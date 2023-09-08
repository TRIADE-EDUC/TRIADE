<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment.class.php,v 1.11 2018-09-21 11:33:09 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/search_universes/search_segment_set.class.php');
require_once($class_path.'/search_universes/search_segment_search_perso.class.php');
require_once($class_path.'/search_universes/search_segment_facets.class.php');
require_once($class_path.'/search_universes/search_segment_search_view.class.php');
require_once($class_path.'/authperso.class.php');
require_once($class_path.'/searcher.class.php');
require_once($class_path.'/translation.class.php');
require_once($include_path.'/templates/search_universes/search_segment.tpl.php');

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
	
	protected $universe_query;
	
	/**
	 * 
	 * @var search_segment_search_result
	 */
	protected $search_result;
	
	protected static $instances;
	
	protected static $segments_labels;
	

	private function __construct($id = 0){
		$this->id = $id+0;
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
				$this->search_perso = new search_segment_search_perso($this->id);
				//search_segment_facets::set_num_segment($this->id);
				$this->facets = new search_segment_facets(); 
			}
		}
	}
	
	public function get_form($ajax = false) {
		global $msg;
		global $charset;
		global $base_path;
		global $search_segment_form;
		global $universe_id;
				
		$html = $search_segment_form;
		$html = str_replace('!!segment_label!!', htmlentities($this->label, ENT_QUOTES, $charset), $html);
		$html = str_replace('!!segment_logo!!', htmlentities($this->logo, ENT_QUOTES, $charset), $html);
		$html = str_replace('!!segment_description!!', htmlentities($this->description, ENT_QUOTES, $charset), $html);
	    $html = str_replace('!!segment_id!!', $this->id, $html);
	    $html = str_replace('!!segment_universe_id!!', $this->num_universe, $html);
	    $html = str_replace('!!universe_query!!', $this->get_human_universe_query(), $html);
	    
		return $html;
	}
	
	public function get_parent_universe_data() {
		global $search_segment_parent_universe;
		if ($this->num_universe != '') {
			$query = "SELECT * FROM search_universes
					WHERE id_search_universe = '" . $this->num_universe . "'";
			$result = pmb_mysql_query($query);
			$universe_data = pmb_mysql_fetch_object($result);
			$html = $search_segment_parent_universe;
			$html = str_replace('!!segment_universe_label!!', $universe_data->search_universe_label, $html);
			$html = str_replace('!!segment_universe_description!!', $universe_data->search_universe_description, $html);
			$html = str_replace('!!segment_universe_id!!', $this->num_universe, $html);
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
		
		$this->label = stripslashes($segment_label);
		$this->description = stripslashes($segment_description);
		$this->template_directory = stripslashes($segment_template_directory);
		if (!empty($segment_type)) {
		    $this->type = $segment_type+0;
		}
		$this->logo = $segment_logo;
		$this->num_universe = $segment_universe_id;
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
				search_segment_logo = "'.$this->logo.'"';
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
	}
	
	public static function delete($id=0) {
		$id += 0;
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
	    search_segment_facets::set_num_segment($this->id);
	    $this->facets = new search_segment_facets();
	    return $this->facets;
	}
	
	public function get_search_perso() {
	    if (isset($this->search_perso)) {
	        return $this->search_perso;
	    }
	    $this->search_perso = new search_segment_search_perso($this->id);
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
	    global $charset;
	    $entities = $this->get_list_entities();
	    $html = '';	    
	    foreach ($entities as $id => $label) {
	        $html.= '<option value="'.$id.'" '.($selected == $id ? 'selected="selected"' : '').'>'.htmlentities($label, ENT_QUOTES, $charset).'</option>';
	    }	    
	    return $html;
	}
	
	public function get_list_entities() {
	    global $msg, $pmb_use_uniform_title, $thesaurus_concepts_active;
	    
	    $entities = array(
	        TYPE_NOTICE => $msg[130],
	        TYPE_AUTHOR => $msg[133] 
	    );
	    
	    if (SESSrights & THESAURUS_AUTH) {
	        $entities[TYPE_CATEGORY] = $msg[134];
	    }
	    $entities[TYPE_PUBLISHER] = $msg[135];
	    $entities[TYPE_COLLECTION] = $msg[136];
	    $entities[TYPE_SUBCOLLECTION] = $msg[137];
	    $entities[TYPE_SERIE] = $msg[333];
	    if ($pmb_use_uniform_title) {
	        $entities[TYPE_TITRE_UNIFORME] = $msg['aut_menu_titre_uniforme'];
	    }
	    $entities[TYPE_INDEXINT] = $msg['indexint_menu'];
	    if ($thesaurus_concepts_active==true && (SESSrights & CONCEPTS_AUTH)) {
	        $entities[TYPE_CONCEPT] = $msg['ontology_skos_menu'];
	    }
	    $authpersos = new authpersos();
	    foreach ($authpersos->get_authpersos() as $authperso) {
	        $entities[$authperso['id']+1000] = $authperso['name'];
	    }
	    return $entities;
	}
	
	protected function get_max_order() {
	    $query = "select max(search_segment_order) as max_order from search_segments where search_segment_num_universe = '".$this->num_universe."'";
	    $result = pmb_mysql_query($query);
	    return pmb_mysql_result($result, 0, 'max_order')+0;
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
		return translation::get_text($this->id, 'search_segments', 'search_segment_label',  $this->label);
	}
	
	public function get_translated_description() {
		return translation::get_text($this->id, 'search_segments', 'search_segment_description',  $this->description);
	}
	
	public function get_display_search() {
		global $charset;
		global $search_type;
		global $base_path;
		global $user_query;
        $display = 	$this->get_form();
		
		search_segment_search_view::set_object_id($this->id);
		search_segment_search_view::set_search_type($search_type);
		search_segment_search_view::set_user_query(stripslashes($user_query));
		search_segment_search_view::set_url_base($base_path.'/index.php?lvl=search_segment&id='.$this->id.'&');
		$display .= search_segment_search_view::get_display_search();
		return $display;
	}
	
	public function get_preview_results() {
	    
	}
	
	public function get_universe_query() {
	    if (!isset($this->universe_query)) {
	        $this->init_universe_query();
	    }
	    return $this->universe_query;
	}
	
	protected function init_universe_query(){
	    global $universe_query;
	    
	    $this->universe_query = ''; 
	    if (isset($universe_query)) {
	        $this->universe_query = $universe_query;
	    }
	    return $this;
	}
	
	public function get_human_universe_query() {
	    if ($this->get_universe_query()) {
	        $search = new search();
	        $search->json_decode_search(stripslashes($this->get_universe_query()));
	        return $search->make_human_query();
	    }
	    return '';    
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
	 * @return search_segment
	 */
	public static function get_instance($id) {
	    if ($id * 1) {
	       if (isset(static::$instances[$id])) {
	           return static::$instances[$id];
	       }
	       static::$instances[$id] = new search_segment($id);
	       return static::$instances[$id];
	    }
	    return null;
	}
	
	public function get_display_results($display_navbar = true) {
	    if (!isset($this->search_result)) {
	        $this->get_search_result();
	    }
	    return $this->search_result->get_display_results($display_navbar);
	}
	
	public function get_nb_results() {
	    if (!isset($this->search_result)) {
	        $this->get_search_result();
	    }
	    return $this->search_result->get_nb_results();
	}
	
	public function get_search_result() {
	    if (isset($this->search_result)) {
	        return $this->search_result;
	    }
	    $this->search_result = new search_segment_search_result($this);
	    return $this->search_result;
	}
	
	public function get_type() {
        return $this->type;
	}
	
	public function get_order(){
	    return $this->order;
	}
	
	public static function get_label_from_id($segment_id) {
	    $segment_id *=1;
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
	            static::$segments_labels[$segment_id] = $row["search_segment_label"];
	            return static::$segments_labels[$segment_id];
	        }
	    }
	    return '';
	}
}