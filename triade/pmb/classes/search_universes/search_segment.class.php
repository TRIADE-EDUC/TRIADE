<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment.class.php,v 1.43.2.6.2.1 2025/02/12 12:34:08 dbellamy Exp $

use Pmb\Common\Helper\HelperEntities;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path.'/search_universes/search_segment_set.class.php');
require_once($class_path.'/search_universes/search_segment_search_perso.class.php');
require_once($class_path.'/search_universes/search_segment_facets.class.php');
require_once($class_path.'/interface/interface_form.class.php');
require_once($class_path.'/authperso.class.php');
require_once($class_path.'/translation.class.php');
require_once($include_path.'/templates/search_universes/search_segment.tpl.php');
require_once "$class_path/search_universes/search_segment_sort.class.php";
require_once "$class_path/search_universes/search_segment_external.class.php";

class search_segment {

	protected $id;

	protected $label;

	protected $description;

	protected $template_directory;

	protected $num_universe;

	protected $type;

	protected $order;

	protected $logo;

	protected $set;

	protected $parameters;

	protected $search_class;

	protected static $first_entity_type;

	protected static $handler;

	protected $search_perso;

	protected $facets;

	protected $sort;

	protected $segment_sort;

	protected $search_universes_associate;

	protected $rmc_enabled = 0;

	/**
	 * Parametres additionnels pour le formulaire
	 *
	 * @var array|null
	 */
	protected $search_segment_data = null;

	public function __construct($id = 0){
		$this->id = intval($id);
		$this->fetch_data();
	}

	protected function fetch_data() {
		$this->label = '';
		$this->description = '';
		$this->template_directory = '';
		$this->num_universe = 0;
		$this->type = 0;
		$this->order = 0;
		$this->logo = '';
		if ($this->id) {
			$query = "SELECT * FROM search_segments
						WHERE id_search_segment = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_assoc($result);
				$this->label = $row["search_segment_label"];
				$this->description = $row["search_segment_description"];
				$this->template_directory = $row["search_segment_template_directory"];
				$this->num_universe = $row["search_segment_num_universe"];
				$this->type = $row["search_segment_type"];
				$this->order = $row["search_segment_order"];
				$this->logo = $row["search_segment_logo"];
				$this->set = new search_segment_set($this->id);
				$this->sort = new search_segment_sort($this->id);
				$this->search_perso = new search_segment_search_perso($this->id);
				$this->search_perso->set_segment_type($this->type);
				$this->facets = new search_segment_facets($this->id);
				$this->facets->set_segment_type($this->type);
				$this->rmc_enabled = $row["search_segment_rmc_enabled"];
				if (isset($row["search_segment_data"])) {
    				$this->search_segment_data = encoding_normalize::json_decode(stripslashes($row["search_segment_data"]), true);
				}
				$this->get_search_universes_associate();
			}
		}
	}

	public function get_form($ajax = false) {
		global $msg;
		global $charset;
		global $base_path;
		global $search_segment_content_form;
		global $universe_id;
		global $pmb_opac_url;

		$content_form = $search_segment_content_form;
		$content_form = str_replace('!!segment_label!!', htmlentities($this->label, ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!segment_logo!!', htmlentities($this->logo, ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!segment_description!!', htmlentities($this->description, ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!segment_type!!', $this->get_list_entity_options($this->type), $content_form);
		$content_form = str_replace('!!segment_rmc_enabled!!', (($this->rmc_enabled) ? 'checked':''), $content_form);

		$interface_form = new interface_admin_segment_form('search_segment_form');
		$interface_form->set_duplicable(true);
		if($this->id){
			$interface_form->set_label($msg['search_segment_edit']);
			$content_form = str_replace('!!segment_universe_id!!', $this->num_universe, $content_form);
			$content_form = str_replace('!!segment_facets_form!!', ($this->type != TYPE_CONCEPT ? $this->facets->get_form() : ''), $content_form);

			$content_form = str_replace('!!segment_search_universes!!', $this->get_universes_form(), $content_form);
			// recherche prédéfinie
			$content_form = str_replace('!!segment_search_perso_form!!', $this->search_perso->get_form(entities::get_string_from_const_type($this->type)), $content_form);
// 			$content_form = str_replace('!!segment_search_perso_form!!', "", $content_form);

			$content_form = str_replace('!!segment_filter_form!!', $this->get_filter_form(), $content_form);
			$content_form = str_replace('!!segment_sort_form!!', $this->get_sort()->get_form(), $content_form);
			$content_form = str_replace('!!segment_type_readonly!!', 'disabled', $content_form);
			$content_form = str_replace('!!segment_id_field!!',  $interface_form->get_display_field_text($msg['search_segment_id'], $this->id), $content_form);
			$content_form = str_replace('!!segment_permalink_field!!', $interface_form->get_display_field_url($msg['search_segment_url'], $pmb_opac_url."index.php?lvl=search_segment&id=".$this->id), $content_form);
			if ($this->get_default_segment_from_universe() == $this->id) {
			    $content_form = str_replace('!!checked!!', 'checked', $content_form);
			} else {
			    $content_form = str_replace('!!checked!!', '', $content_form);
			}
			if ($this->hide_segment_search()) {
			    $content_form = str_replace('!!hide_segment_checked!!', 'checked', $content_form);
			} else {
			    $content_form = str_replace('!!hide_segment_checked!!', '', $content_form);
			}
		    $interface_form->set_object_id($this->id);
		} else {
			$interface_form->set_label($msg['search_segment_create']);
			$content_form = str_replace('!!segment_universe_id!!', $universe_id, $content_form);
			$content_form = str_replace('!!segment_facets_form!!', '', $content_form);
			$content_form = str_replace('!!segment_search_universes!!', '', $content_form);
			$content_form = str_replace('!!segment_search_perso_form!!', '', $content_form);
			$content_form = str_replace('!!segment_set_form!!', '', $content_form);
			$content_form = str_replace('!!segment_sort_form!!', '', $content_form);
			$content_form = str_replace('!!segment_type_readonly!!', '', $content_form);
			$content_form = str_replace('!!checked!!', '', $content_form);
			$content_form = str_replace('!!hide_segment_checked!!', '', $content_form);
			$content_form = str_replace('!!segment_id_field!!','', $content_form);
			$content_form = str_replace('!!segment_permalink_field!!','', $content_form);
			$content_form = str_replace('!!segment_filter_form!!', "", $content_form);
		}
		$content_form = str_replace('!!segment_id!!', $this->id, $content_form);
		$namespace = HelperEntities::get_entities_namespace();
		$classname = "";
		if(array_key_exists($this->type, $namespace)) {
			$classname = "search_segment_" . $namespace[$this->type];
		}
		if (class_exists($classname)) {
			$content_form = str_replace('!!segment_additional!!', call_user_func([$classname, "get_additional"], $this->search_segment_data), $content_form);
		} else {
		    $content_form = str_replace('!!segment_additional!!', "", $content_form);
		}
		$interface_form->set_url_base($base_path."/admin.php?categ=search_universes&sub=segment");
		$interface_form->set_content_form($content_form);
		$interface_form->set_table_name('search_segments');
		if ($ajax) {
		    $interface_form->set_url_base($base_path."/ajax.php?module=admin&categ=search_universes&sub=segment");
			$universes = search_universe::get_universe_list();
			$interface_form->set_universe_select_data($universes, $this->num_universe);
		    return $interface_form->get_display_ajax();
		}
		return $interface_form->get_display();
	}

	public function set_properties_from_form(){
		global $segment_label;
		global $segment_description;
		global $segment_rmc_enabled;
		global $segment_template_directory;
		global $segment_type;
		global $segment_logo;
		global $segment_universe_id;
		global $search_universes_associate;
		global $hide_segment_search;

		$this->label = stripslashes($segment_label);
		$this->description = stripslashes($segment_description);
		$this->rmc_enabled = (isset($segment_rmc_enabled)? 1:0);
		$this->template_directory = stripslashes($segment_template_directory);
		if (!empty($segment_type)) {
		    $this->type = intval($segment_type);
		}

		$this->logo = $segment_logo;
		$this->num_universe = $segment_universe_id;
		if (is_object($this->sort) && method_exists($this->sort, 'get_sort_from_form')){
    		$this->segment_sort = $this->sort->get_sort_from_form();

    		//Globalisation des traductions
    		$languages = translation::get_languages();
    		if (!empty($languages)) {
    		    foreach ($languages as $language) {
    		        if (!empty($language['is_current_lang'])) {
    		            $segment_sort = 'segment_sort_calculated';
    		            global ${$segment_sort};
    		            ${$segment_sort} = $this->segment_sort;
    		        } else {
    		            $segment_sort = $language['code'].'_segment_sort_calculated';
    		            global ${$segment_sort};
    		            ${$segment_sort} = $this->sort->get_sort_from_form($language['code']);
    		        }
    		    }
    		}
		}

		$this->search_universes_associate = array();
		if(!empty($search_universes_associate) && is_array($search_universes_associate)) {
		    $this->search_universes_associate = $search_universes_associate;
		}
		$this->search_segment_data = [];
		$namespace = HelperEntities::get_entities_namespace();
		if (
			class_exists("search_segment_" . $namespace[$this->type]) &&
			method_exists("search_segment_" . $namespace[$this->type], "get_properties_from_form")
		) {
			$classname = "search_segment_" . $namespace[$this->type];
			$this->search_segment_data = call_user_func([$classname, "get_properties_from_form"]);
		}
		$this->search_segment_data["hide_segment_search"] = intval($hide_segment_search);
	}

	public function save() {
	    global $segment_default;

		if($this->id){
			$query = 'UPDATE ';
			$query_clause = ' WHERE id_search_segment = '.$this->id;
		}else{
			$query = 'INSERT INTO ';
			$query_clause = '';
			$this->order = $this->get_max_order() + 1;
		}
		$query .= ' search_segments SET
				search_segment_label = "'.addslashes($this->label).'",
				search_segment_description = "'.addslashes($this->description).'",
				search_segment_template_directory = "'.addslashes($this->template_directory).'",
				search_segment_num_universe = "'.$this->num_universe.'",
				search_segment_type = "'.$this->type.'",
				search_segment_order = "'.$this->order.'",
				search_segment_logo = "'.$this->logo.'",
				search_segment_sort = "'.$this->segment_sort.'",
                search_segment_rmc_enabled = '.$this->rmc_enabled.',
                search_segment_data = "' . addslashes(json_encode($this->search_segment_data)) . '"';
		pmb_mysql_query($query.$query_clause);
		if(!$this->id){
			$this->id = pmb_mysql_insert_id();
		}

	    search_universe::update_default_segment($this->num_universe, $this->id, isset($segment_default));

		$this->get_facets();
		$this->facets->set_properties_from_form();
		$this->facets->save();

		$this->get_search_perso();
		$this->search_perso->set_properties_from_form();
		$this->search_perso->save();

		$query = "DELETE FROM search_segments_associated_universes WHERE num_segment='" . $this->id . "'";
		pmb_mysql_query($query);

		$index = count($this->search_universes_associate);
		for ($i = 0; $i < $index; $i++) {
			//On se tire si on associe notre univers à notre univers
			//Possible en cas de duplication vers un autre univers par ex
			if($this->search_universes_associate[$i] == $this->num_universe) {
				continue;
			}
		    $query = "INSERT INTO search_segments_associated_universes SET num_segment = '" . $this->id ."', num_universe = '" . $this->search_universes_associate[$i] ."'";
		    pmb_mysql_query($query);
		}
		$translation = new translation($this->id, "search_segments");
		$translation->update("segment_label", "segment_label");
		$translation->update_text("segment_description");
		$translation->update_text("segment_sort", "segment_sort_calculated");
	}

	public static function delete($id=0) {
	    $id = intval($id);
	    if (!$id) {
	    	return;
	    }
	    translation::delete($id, "search_segments");
		$query = "delete from search_segments where id_search_segment = ".$id;
		pmb_mysql_query($query);
		search_segment_facets::delete($id);
		search_segment_search_perso::delete();
		$query = "UPDATE search_universes SET search_universe_default_segment = 0 WHERE search_universe_default_segment = $id";
		pmb_mysql_query($query);

		return true;
	}

	public static function get_entities_list_form($selected = '') {
		global $search_segment_type_option, $class_path;
		$dirs = array_filter(glob('./classes/search_universes/entity/*'), 'is_dir');
		$entities_list_form = "";
		foreach ($dirs as $dir) {
			if(basename($dir) != "CVS"){
				$entity_class_name = self::build_class_path($dir);
				$builded_class = $class_path.'/search_universes/entity/'.basename($dir).'/'.$entity_class_name.'.class.php';
				require_once($builded_class);
				if(class_exists($entity_class_name)){
					if (empty(self::$first_entity_type)) {
						self::$first_entity_type = basename($dir);
					}
					$entity_option = str_replace("!!segment_type_value!!", basename($dir), $search_segment_type_option);
					$entity_option = str_replace("!!segment_type_name!!", ($entity_class_name::get_name() ? $entity_class_name::get_name() : basename($dir)), $entity_option);
					$entity_option = str_replace("!!segment_selected_type!!", (basename($dir) == basename($selected) ? "selected='selected'" : ""), $entity_option);
					$entities_list_form .= $entity_option;
				}
			}
		}
		return $entities_list_form;
	}

	public static function build_class_path($dir){
		if(!$dir){
			return '';
		}
		$pieces = explode('/', $dir);
		if (!count($pieces) || count($pieces) < 2) {
			return '';
		}
		return implode('_', array_slice($pieces, 2));
	}

	public function get_num_universe() {
		return $this->num_universe;
	}

	public function get_set_form() {
		$entity_class_name = $this->get_entity_class_name();
		$handler =  $entity_class_name::get_set_handler();
		return $handler::get_filter_form();
	}

	public function get_entity_class_name() {
		if (empty($this->type)) {
			if (empty(self::$first_entity_type)) {
				return '';
			}
			$this->type = self::$first_entity_type;
		}
		$entity_class_name =  "search_universes_entity_".$this->type;
		return $entity_class_name;
	}

	protected function get_set_handler() {
		$entity_class_name = $this->get_entity_class_name();
		$handler = $entity_class_name::get_set_handler($this->id, $this->parameters);
		return $handler;
	}

	protected function get_predefined_handler() {
		$entity_class_name = $this->get_entity_class_name();
		$handler = $entity_class_name::get_predefined_search_handler($this->id, $this->parameters);
		return $handler;
	}

	public function init_type() {
		global $segment_entity_type;
		if (empty($segment_entity_type)) {
			return '';
		}
		return $segment_entity_type;
	}

	public function get_facets() {
	    if (isset($this->facets)) {
	        return $this->facets;
	    }
	    $this->facets = new search_segment_facets($this->id);
	    $this->facets->set_segment_type($this->type);
	    return $this->facets;
	}

	public function get_search_perso() {
	    if (isset($this->search_perso)) {
	        return $this->search_perso;
	    }
	    $this->search_perso = new search_segment_search_perso($this->id);
	    $this->search_perso->set_segment_type($this->type);
	    return $this->search_perso;
	}

	public function get_set() {
	    if (isset($this->set)) {
	        return $this->set;
	    }
	    $this->set = new search_segment_set($this->id);
	    return $this->set;
	}

	public function get_list_entity_options($selected = 0) {
	    global $charset, $msg;
	    $entities = $this->get_list_entities();
	    $html = '';
	    foreach ($entities as $type => $entity) {
	        switch (true) {
	            case $type == 'authperso':
	                $html .= "<optgroup label='".htmlentities($msg['authperso_multi_search_title'], ENT_QUOTES, $charset)."'>";
	                break;
	            case $type != 'default':
	                $html .= "<optgroup label='".htmlentities($type, ENT_QUOTES, $charset)."'>";
	                break;
	            default:
	                break;
	        }
	        foreach ($entity as $id => $label) {
    	        $html.= "<option value='$id'".($selected == $id ? "selected='selected'" : "").">".htmlentities($label, ENT_QUOTES, $charset)."</option>";
	        }
	        if ($type != 'default') {
	            $html .= "</optgroup>";
	        }
	    }
	    return $html;
	}

	public function get_list_entities() {
	    global $msg, $pmb_use_uniform_title, $thesaurus_concepts_active;
	    global $animations_active;

	    $entities = array('default' => array(
	        TYPE_NOTICE => $msg[130],
	        TYPE_AUTHOR => $msg[133]
	    ));

	    if (SESSrights & THESAURUS_AUTH) {
	        $entities['default'][TYPE_CATEGORY] = $msg[134];
	    }
	    $entities['default'][TYPE_PUBLISHER] = $msg[135];
	    $entities['default'][TYPE_COLLECTION] = $msg[136];
	    $entities['default'][TYPE_SUBCOLLECTION] = $msg[137];
	    $entities['default'][TYPE_SERIE] = $msg[333];
	    if ($pmb_use_uniform_title) {
	        $entities['default'][TYPE_TITRE_UNIFORME] = $msg['aut_menu_titre_uniforme'];
	    }
	    $entities['default'][TYPE_INDEXINT] = $msg['indexint_menu'];
	    if ($thesaurus_concepts_active==true && (SESSrights & CONCEPTS_AUTH)) {
	        $entities['default'][TYPE_CONCEPT] = $msg['ontology_skos_menu'];
	    }

	    $entities['default'][TYPE_EXTERNAL] = $msg['facettes_external_records'];

	    $authpersos = new authpersos();
	    foreach ($authpersos->get_authpersos() as $authperso) {
	        $entities['authperso'][$authperso['id']+1000] = $authperso['name'];
	    }
	    $ontologies = new ontologies();
	    $entities = array_merge($entities,$ontologies->get_available_segments());

 	    $entities['default'][TYPE_CMS_EDITORIAL] = $msg['cms_menu_editorial'];

	    if ($animations_active) {
	        $entities['default'][TYPE_ANIMATION] = $msg['animation_base_title'];
	    }
	    return $entities;
	}

	protected function get_max_order() {
	    $query = "select max(search_segment_order) as max_order from search_segments where search_segment_num_universe = '".$this->num_universe."'";
	    $result = pmb_mysql_query($query);
	    return intval(pmb_mysql_result($result, 0, 'max_order'));
	}

	public function get_id() {
	    return $this->id;
	}

	public function get_translated_label() {
		return translation::get_translated_text($this->id, 'search_segments', 'segment_label',  $this->label);
	}

	public function get_translated_description() {
		return translation::get_translated_text($this->id, 'search_segments', 'segment_description',  $this->description);
	}

	protected function get_default_segment_from_universe() {
	    if ($this->get_num_universe()) {
	        $universe = new search_universe($this->num_universe);
	        return $universe->get_default_segment();
	    }
	    return 0;
	}

	public function get_sort() {
	    if (isset($this->sort)) {
	        return $this->sort;
	    }
	    $this->sort = new search_segment_sort($this->id);
	    return $this->sort;
	}

	protected function get_filter_form(){
	    global $search_segment_filter_form;
	    $html = $search_segment_filter_form;
	    $html = str_replace('!!segment_set_form!!', $this->get_set()->get_form(), $html);

        return $html;
	}

	/**
	 *
	 * @param int $id
	 * @return search_segment|search_segment_external
	 */
	public static function get_instance($id) {
	    $id = intval($id);

	    $query = "SELECT * FROM `search_segments` WHERE id_search_segment = $id";
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_assoc($result);
    	    if ($row['search_segment_type'] == TYPE_EXTERNAL) {
    	        return new search_segment_external($id);
    	    }
	    }
	    return new search_segment($id);
	}

	public static function update_order($segments, $id, $order) {

	    $tmp = array();
	    foreach ($segments as $key => $segment) {
	        if($segment['id_search_segment'] == $id){
	            $old_order = $key;
	        }
	        $tmp[] = $segment['id_search_segment'];
	    }

	    $out = array_splice($tmp, $old_order, 1);
	    array_splice($tmp, $order, 0, $out);


	    foreach($tmp as $order => $segment) {
	        $query = 'UPDATE search_segments SET
				search_segment_order = '.$order.' WHERE id_search_segment = '.$segment;
	        pmb_mysql_query($query);
	    }
	}

	public function get_universes_form()
	{
	    global $search_segment_universes_form, $search_segment_universe_row;
	    $universes = search_universe::get_universe_list();

	    $form = $search_segment_universes_form;
	    $temp = "";

	    foreach ($universes as $universe)
	    {
	        if($universe->get_id() != $this->get_num_universe())
	        {
	            $row = $search_segment_universe_row;
	            $row = str_replace('!!name_universe!!', $universe->get_label(), $row);
	            $row = str_replace('!!universe_value!!', $universe->get_id(), $row);
	            $checked = "";
	            if(in_array($universe->get_id(), $this->search_universes_associate)) {
	                $checked = "checked";
	            }
	            $row = str_replace('!!checked!!', $checked, $row);
	            $temp .= $row;
	        }
	    }

	    $form = str_replace('!!universe_list!!', $temp, $form);
	    return $form;
	}

	public function get_search_universes_associate()
	{
	    if(!isset($this->search_universes_associate)) {
	        $this->search_universes_associate = array();
	        $query = "SELECT num_universe FROM search_segments_associated_universes WHERE num_segment = '" . $this->id . "'";
	        $result = pmb_mysql_query($query);

	        if(pmb_mysql_num_rows($result)){
	            while ($row = pmb_mysql_fetch_assoc($result)) {
	                $this->search_universes_associate[] = intval($row['num_universe']);
	            }
	        }
	    }
	    return $this->search_universes_associate;
	}

	/**
	 * Retourne les parametres additionnels pour le formulaire
	 *
	 * @param array $search_segments_data
	 * @return string
	 */
    public static function get_additional($search_segments_data)
    {
        return "";
    }

	/**
	 * Duplique le segment dans le ou les univers passes en parametre
	 */
	public function duplicate($universes = array())
	{
		global $segment_search_perso;

		$duplicate = clone $this;
		$facets = $this->facets->get_facets();
		$set = $this->get_set();
		$sortQuery = "SELECT search_segment_sort FROM search_segments WHERE id_search_segment = '{$this->get_id()}'";
		$segmentSort = pmb_mysql_result(pmb_mysql_query($sortQuery), 0, 0);
		$segment_search_perso = $this->search_perso->get_search_perso();
		$duplicate->set_segment_sort();
		$duplicate->id = 0;
		$duplicate->facets = null;
		$duplicate->set = null;

		foreach($universes as $universeId) {
			global $segment_facets;
			$segment_facets = $facets;
			$duplicate->num_universe = $universeId;
			$duplicate->segment_sort = $segmentSort;
			$duplicate->save();
			$newSet = new search_segment_set($duplicate->get_id());
			$newSet->set_data_set($set->get_data_set());
			$newSet->update();
			$newSearchPerso = new search_segment_search_perso($duplicate->get_id());
			$newSearchPerso->set_search_perso($segment_search_perso);
			$newSearchPerso->save();

			$duplicate->id = 0;
			$duplicate->facets = null;
			$duplicate->set = null;
		}
		return $duplicate;
	}

	public function set_segment_sort($sort = "")
	{
		$this->segment_sort = $sort;
	}

	public function hide_segment_search() {
	    if (!empty($this->search_segment_data) && !empty($this->search_segment_data['hide_segment_search'])) {
	        return true;
	    }
	    return false;
	}
}