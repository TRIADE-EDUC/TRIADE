<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment.class.php,v 1.52.2.3 2024/11/14 15:36:50 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Autocomplete\Controller\AutocompleteController;

// require_once($class_path.'/search_universes/search_segment_set.class.php');
// require_once($class_path.'/search_universes/search_segment_search_perso.class.php');
// require_once($class_path.'/search_universes/search_segment_facets.class.php');
// require_once($class_path.'/search_universes/search_segment_search_view.class.php');
// require_once($class_path.'/authperso.class.php');
// require_once($class_path.'/searcher.class.php');
// require_once($class_path.'/translation.class.php');
require_once($include_path.'/templates/search_universes/search_segment.tpl.php');
// require_once "$class_path/search_universes/search_segment_sort.class.php";
// require_once "$class_path/search_universes/external/search_segment_external.class.php";

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

	/**
	 *
	 * @var search_segment_search_result
	 */
	protected $search_result;

	protected static $instances;

	protected static $current_instance;

	protected static $segments_labels;

	protected $segment_sort;

	protected $rmc_enabled;

	protected $search_universes_associate;

	/**
	 * Parametres additionnels pour le formulaire
	 *
	 * @var array|null
	 */
	protected $search_segment_data;

	protected $sort;

	private function __construct($id = 0){
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
				pmb_mysql_free_result($result);

				$this->label = $row["search_segment_label"];
				$this->description = $row["search_segment_description"];
				$this->template_directory = $row["search_segment_template_directory"];
				$this->num_universe = $row["search_segment_num_universe"];
				$this->type = $row["search_segment_type"];
				$this->order = $row["search_segment_order"];
				$this->logo = $row["search_segment_logo"];
				$this->rmc_enabled = $row["search_segment_rmc_enabled"];
				$this->set = new search_segment_set($this->id);
				$this->sort = $this->get_sort();
				$this->search_perso = new search_segment_search_perso($this->id);
// 				search_segment_facets::set_num_segment($this->id);
				$this->facets = search_segment_facets::get_instance('', $this->id);
				$this->get_search_universes_associate();
				if (isset($row["search_segment_data"])) {
				    $this->search_segment_data = encoding_normalize::json_decode(stripslashes($row["search_segment_data"]), true);
				}
			}
		}
	}

	public function get_form($ajax = false) {
		global $msg;
		global $charset;
		global $base_path;
		global $search_segment_form;
		global $universe_id;
		$universe = new search_universe($this->num_universe);
		$html = $search_segment_form;
		$html = str_replace('!!universe_label!!', htmlentities($universe->get_translated_label(), ENT_QUOTES, $charset), $html);
		$html = str_replace('!!segment_universe_id!!', $this->num_universe, $html);
		$html = str_replace('!!segment_label!!', htmlentities($this->get_translated_label(), ENT_QUOTES, $charset), $html);
		$html = str_replace('!!segment_logo!!', htmlentities($this->logo, ENT_QUOTES, $charset), $html);
		$html = str_replace('!!segment_description!!', htmlentities($this->get_translated_description(), ENT_QUOTES, $charset), $html);
	    $html = str_replace('!!segment_id!!', $this->id, $html);
	    $html = str_replace('!!segment_universe_id!!', $this->num_universe, $html);
	    $html = str_replace('!!last_query!!', htmlentities(stripslashes(search_universe::$start_search["query"]), ENT_QUOTES, $charset), $html);

		return $html;
	}

	public function get_parent_universe_data() {
		global $charset;
		global $search_segment_parent_universe;
		if ($this->num_universe != '') {
		    $universe = new search_universe($this->num_universe);
			$html = $search_segment_parent_universe;
			$html = str_replace('!!segment_universe_label!!', htmlentities($universe->get_translated_label(), ENT_QUOTES, $charset), $html);
			$html = str_replace('!!segment_universe_description!!', htmlentities($universe->get_translated_description(), ENT_QUOTES, $charset), $html);
			$html = str_replace('!!segment_universe_id!!', $this->num_universe, $html);
		    $html = str_replace('!!get_parameters!!', search_universe::get_parameters(), $html);
			//$html = str_replace('!!last_query!!', search_universe::$start_search["query"], $html);
			$html = str_replace('!!last_query!!', htmlentities(stripslashes(search_universe::$start_search["query"]), ENT_QUOTES, $charset), $html);
			return $html;
		}
		return '';
	}

	public function set_properties_from_form(){
		global $segment_label;
		global $segment_num_universe;
		global $segment_description;
		global $segment_template_directory;
		global $segment_type;
		global $segment_logo;
		global $segment_universe_id;
		global $segment_sort;
		global $search_universes_associate;

		$this->label = stripslashes($segment_label);
		$this->description = stripslashes($segment_description);
		$this->template_directory = stripslashes($segment_template_directory);
		if (!empty($segment_type)) {
		    $this->type = intval($segment_type);
		}
		$this->logo = $segment_logo;
		$this->num_universe = $segment_universe_id;
		$this->segment_sort = $segment_sort;

		$this->search_universes_associate = array();
		if(!empty($search_universes_associate) && is_array($search_universes_associate)) {
		    $this->search_universes_associate = $search_universes_associate;
		}
	}

	public function save() {

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
				search_segment_sort = "'.$this->segment_sort.'"';
		pmb_mysql_query($query.$query_clause);
		if(!$this->id){
			$this->id = pmb_mysql_insert_id();
		}

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
		$query = "delete from search_segments where id_search_segment = ".$id;
		pmb_mysql_query($query);
		search_segment_facets::delete($id);
		search_segment_search_perso::delete();
		return true;
	}

	public static function get_entities_list_form($selected = '') {
		global $msg, $search_segment_type_option, $class_path, $include_path;
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
	    $this->facets = search_segment_facets::get_instance("", $this->id);
	    $this->facets->set_num_segment($this->id);
	    return $this->facets;
	}

	public function get_search_perso() {
	    if (isset($this->search_perso)) {
	        return $this->search_perso;
	    }
	    $this->search_perso = new search_segment_search_perso($this->id);
	    return $this->search_perso;
	}

	/**
	 * Retourne l'objet search_segment_set
	 *
	 * @return search_segment_set
	 */
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
	            case $type == 'connectors':
	                $html .= "<optgroup label='".$msg['facettes_external_records']."'>";
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
	        $entities['default'][TYPE_ANIMATION] = "animations";
	    }
	    return $entities;
	}

	protected function get_max_order() {
	    $query = "select max(search_segment_order) as max_order from search_segments where search_segment_num_universe = '".$this->num_universe."'";
	    $result = pmb_mysql_query($query);
	    $max_order = pmb_mysql_result($result, 0, 'max_order');
	    return intval($max_order);
	}

	public function get_id() {
	    return $this->id;
	}

	public function get_label() {
	    return $this->label;
	}

	public function get_logo() {
	    return $this->logo;
	}

	public function get_description() {
	    return $this->description;
	}

	public function get_translated_label() {
		return translation::get_translated_text($this->id, 'search_segments', 'segment_label',  $this->label);
	}

	public function get_translated_description() {
		return translation::get_translated_text($this->id, 'search_segments', 'segment_description',  $this->description);
	}

	public function get_display_search_with_results() {
	    $display = 	$this->get_form();

	    $display = str_replace("!!search_segment_result!!", $this->get_display_results(), $display);
	    $display = str_replace("!!search_segment!!", $this->get_display_search_view(), $display);
	    return $display;
	}

	public function get_display_search() {
        $display = 	$this->get_form();
        $display = str_replace("!!search_segment!!", $this->get_display_search_view(), $display);
		$display = str_replace("!!search_segment_result!!", "", $display);
		return $display;
	}

	private function get_display_search_view() {
	    global $search_segment_type;
	    global $base_path;
	    global $user_query;

	    if ($this->hide_segment_search()) {
	        return "";
	    }
	    if (empty($search_segment_type)) {
	        $search_segment_type = "simple_search";
	    }

	    search_segment_search_view::set_object_id($this->id);
	    search_segment_search_view::set_segment($this);
	    search_segment_search_view::set_search_type($search_segment_type);
	    search_segment_search_view::set_user_query($user_query);
	    search_segment_search_view::set_url_base($base_path.'/index.php?lvl=search_segment&id='.$this->id.search_universe::get_segments_dynamic_params().'&');
	    return search_segment_search_view::get_display_search();
	}

	public function get_preview_results() {

	}

	public function get_rebound_form() {
	    global $search_segment_rebound_form;

	    $html = $search_segment_rebound_form;
	    $html = str_replace('!!segment_id!!', $this->id, $html);
	    $html = str_replace('!!universe_id!!', $this->num_universe, $html);

	    return $html;
	}

	/**
	 *
	 * @param int $id
	 * @return search_segment|search_segment_external
	 */
	public static function get_instance($id) {
        $id = intval($id);

        if (isset(static::$instances[$id])) {
           static::$current_instance = static::$instances[$id];
           return static::$instances[$id];
        }

        $type = 0;
        $query = "SELECT search_segment_type FROM search_segments WHERE id_search_segment = $id";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_assoc($result);
			pmb_mysql_free_result($result);
            $type = $row['search_segment_type'];
        }

        switch ($type){
            case TYPE_EXTERNAL:
                static::$instances[$id] = new search_segment_external($id);
                break;
            default:
                static::$instances[$id] = new search_segment($id);
                break;
        }
        if (empty(static::$current_instance)) {
            static::$current_instance = static::$instances[$id];
        }
        return static::$instances[$id];
	}

	/**
	 *
	 * @return search_segment|NULL
	 */
	public static function get_current_instance() {
	    if (!empty(static::$current_instance)) {
	        return static::$current_instance;
	    }
	    return null;
	}

	public function get_display_results() {
	    if (!isset($this->search_result)) {
	        $this->get_search_result();
	    }
	    // Permet de ne pas afficher le selecteur de tri pour les types de segment non triables... pour le moment
	    $display_navbar = true;
	    $display_sort_selector = true;
	    if ($this->get_type() == TYPE_EXTERNAL) {
	        $display_sort_selector = false;
	    }

	    return $this->search_result->get_display_results($display_navbar, $display_sort_selector);
	}

	public function get_nb_results($ajax_mode = false, $is_sub_rmc = search_segment_search_result::IS_NOT_SUB_RMC) {
	    if (!isset($this->search_result)) {
	        $this->get_search_result();
	    }
	    return $this->search_result->get_nb_results($ajax_mode, $is_sub_rmc);
	}

	public function get_search_result() {
	    if (isset($this->search_result)) {
	        return $this->search_result;
	    }
	    $this->search_result = new search_segment_search_result($this);
	    return $this->search_result;
	}

	public function get_search_result_table() {
	    if (!isset($this->search_result)) {
	        $this->get_search_result();
	    }
	    return $this->search_result->get_searcher_table();
	}

	public function get_type() {
        return $this->type;
	}

	public function get_order(){
	    return $this->order;
	}

	public static function get_label_from_id($segment_id) {
	    $segment_id = intval($segment_id);
	    if ($segment_id) {
	        if (isset(static::$segments_labels[$segment_id])) {
	            return static::$segments_labels[$segment_id];
	        }
	        if (!isset(static::$segments_labels)) {
	            static::$segments_labels = array();
	        }
	        $query = "
			    SELECT search_segment_label FROM search_segments
				WHERE id_search_segment = '".$segment_id."'
			";
	        $result = pmb_mysql_query($query);
	        if ($result) {
	            $row = pmb_mysql_fetch_assoc($result);
	            static::$segments_labels[$segment_id] = translation::get_translated_text($segment_id, 'search_segments', 'segment_label',  $row["search_segment_label"]);
	            return static::$segments_labels[$segment_id];
	        }
	    }
	    return '';
	}
	public function get_sort() {
	    if (isset($this->sort)) {
	        return $this->sort;
	    }
	    $this->sort = new search_segment_sort($this->id);
	    return $this->sort;
	}

	public function get_opac_search_instance() {
	    if($this->get_type() == TYPE_NOTICE){
	        return new search('search_fields');
	    }elseif(($this->get_type() == TYPE_CMS_EDITORIAL)){
	        return new search('search_fields_cms_editorial');
	    } elseif($this->get_type() == TYPE_EXTERNAL) {
	        return new search('search_fields_unimarc');
	    } else {
	        return new search_authorities('search_fields_authorities');
	    }
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


	public function get_universe_associate_list()
	{
	    global $search_segment_universe_associate_list;
	    global $search_segment_universe_associate_form_row;

	    $universe_associat = $this->get_search_universes_associate();
	    if (count($universe_associat) <= 0) {
	        return "";
	    }

	    $universe_associat_list = "";
	    $content = "";
	    $index = count($universe_associat);

	    for ($i = 0; $i < $index; $i++) {
	        $universe = new search_universe($universe_associat[$i]);
	        $row = $search_segment_universe_associate_form_row;
	        $row = str_replace('!!universe_associate_label!!', $universe->get_translated_label(), $row);
	        $row = str_replace('!!universe_associate_id!!', $universe->get_id(), $row);
	        $row = str_replace('!!search_form_hidden!!', $universe->get_hidden_form(), $row);
	        $content .= $row;
	    }
	    $universe_associat_list = str_replace('!!universes_associated_form!!', $content, $search_segment_universe_associate_list);

	    return $universe_associat_list;
	}

	/**
	 * rmc disponible
	 * @return boolean
	 */
	public function has_rmc_enabled() {
        $universe = new search_universe($this->num_universe);
        return $universe->has_rmc_enabled();
	}

	/**
	 * autocompletion disponible
	 * @return boolean
	 */
	public function is_autocomplete() {
        $universe = new search_universe($this->num_universe);
        return $universe->is_autocomplete();
	}

	/**
	 * determine l'utilisation d'un champ dynamique
	 * @return number
	 */
	public function use_dynamic_field() {
	    if (strpos($this->get_set()->get_data_set(), "s_12") !== false) {
	        return 1;
	    }
	    return 0;
	}

	/**
	 * Retourne les parametres additionnels pour le formulaire
	 *
	 * @return array|null
	 */
	public function get_search_segment_data() {
	    return $this->search_segment_data;
	}

	public function get_formated_type() {
	    switch ($this->type) {
	        case TYPE_NOTICE :
	        case TYPE_EXTERNAL :
	        case TYPE_CMS_EDITORIAL :
	        case TYPE_ANIMATION :
	            return $this->type;
	        default:
	            if ($this->type > 10000) {
	                return $this->type;
	            }
	            return TYPE_AUTHORITY;

	    }
	}

	private function hide_segment_search() {
	    if (!empty($this->search_segment_data) && !empty($this->search_segment_data['hide_segment_search'])) {
	        return true;
	    }
	    return false;
	}
}