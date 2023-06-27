<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_universe.class.php,v 1.14 2018-09-21 11:33:09 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/translation.class.php');
require_once($include_path.'/templates/search_universes/search_universe.tpl.php');
require_once($class_path.'/search_view.class.php');
require_once($class_path."/more_results.class.php");
//require_once($class_path."/search_universes/search_universes_history.class.php");

class search_universe {
	
	protected $id;
	
	protected $label;
	
	protected $description;
	
	protected $template_directory;
	
	protected $opac_views;
	
	protected $segments;
	
	protected $default_segment;
	
	protected static $universes_labels;

	public function __construct($id = 0){
		$this->id = $id+0;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$this->label = '';
		$this->description = '';
		$this->template_directory = '';
		$this->opac_views = array();
		$this->default_segment = array();
		if ($this->id) {
			$query = "
			    SELECT * FROM search_universes
				WHERE id_search_universe = '".$this->id."'
			";
			
			$result = pmb_mysql_query($query);
			if ($result) {
				$row = pmb_mysql_fetch_assoc($result);
				$this->label = $row["search_universe_label"];
				$this->description = $row["search_universe_description"];
				$this->template_directory = $row["search_universe_template_directory"];
				$this->opac_views = ( $row["search_universe_opac_views"] ? explode(',', $row["search_universe_opac_views"]) : array());
				$this->default_segment = $row["search_universe_default_segment"];
			}
		}
	}
	
	public function get_label() {
		return $this->label;
	}
	
	public function get_translated_label() {
		return translation::get_text($this->id, 'search_universes', 'search_universe_label',  $this->label);
	}
	
	public function get_description() {
		return $this->description;
	}
	
	public function get_translated_description() {
		return translation::get_text($this->id, 'search_universes', 'search_universe_description',  $this->description);
	}
		
	public function get_template_directory() {
		return $this->template_directory;
	}
		
	public function get_opac_views() {
		return $this->opac_views;
	}
	
    public function get_form() {
		global $msg;
		global $charset;
		global $search_universe_form;
		global $search_universe_segment_list;
		global $universe_query;
		
		$html = $search_universe_form;
		$html = str_replace('!!universe_id!!', $this->id, $html);
		$html = str_replace('!!universe_label!!', htmlentities($this->label, ENT_QUOTES, $charset), $html);
		$html = str_replace('!!universe_description!!', htmlentities($this->description, ENT_QUOTES, $charset), $html);
		$html = str_replace('!!universe_segment_list!!', $search_universe_segment_list, $html);
		$html = str_replace('!!last_query!!', $this->get_search_universes_history_last_query(), $html);
		$html = str_replace('!!default_segment!!', $this->get_default_segment(), $html);
		$html = str_replace('!!universe_segments_form!!', $this->get_segments_form(), $html);
		$html = str_replace('!!search_index!!', $this->get_search_universes_history(), $html);	
		
		return $html;
	}
	
	public function get_segments_list($segment_id = 0){
		global $search_universe_segment_list;
		global $msg;
		global $universe_query;
		
		$segment_list = "<h4 class='new_search_segment_title'><span class='fa fa-search'></span> ". $msg["search_segment_new_search"] ." \"". $this->get_universe_query()."\"</h4>";
		$segment_list .= $search_universe_segment_list;
		$segment_list = str_replace('!!universe_segments_form!!', $this->get_segments_form($segment_id), $segment_list);
		$segment_list = str_replace('!!universe_query!!', ($universe_query ? $universe_query : ''), $segment_list);
		return $segment_list;
	}
	
	public function set_properties_from_form(){
		global $universe_label;	
		global $universe_description;	
		global $universe_template_directory;
		global $universe_opac_views;
		
		$this->label = stripslashes($universe_label);
		$this->description = stripslashes($universe_description);
		$this->template_directory = stripslashes($universe_template_directory);
		$this->opac_views = array();
		
		if (isset($universe_opac_views)) {
    		if (!in_array('', $universe_opac_views)) {
    		    $this->opac_views = $universe_opac_views;
    		}
		}		
	}
	
	public function save() {
		if($this->id){
			$query = 'update ';
			$query_clause = ' where id_search_universe = '.$this->id;
		}else{
			$query = 'insert into ';
			$query_clause = '';
		}
		$query .= ' search_universes set
			search_universe_label = "'.addslashes($this->label).'",
			search_universe_description = "'.addslashes($this->description).'",
			search_universe_template_directory = "'.addslashes($this->template_directory).'",
			search_universe_opac_views = "'.implode(',', $this->opac_views).'"';
		pmb_mysql_query($query.$query_clause);
		if(!$this->id){
			$this->id = pmb_mysql_insert_id();			
		}		
	}
	
	public static function delete($id) {
		$id += 0;
		$query = "delete from search_universes where id_search_universe = ".$id;
		pmb_mysql_query($query);
	}
	
	public function get_segments() {
		if (!isset($this->segments)) {
			$this->segments = array();
			$query = "SELECT id_search_segment FROM search_segments
						WHERE search_segment_num_universe = '".$this->id."'
						ORDER BY search_segment_order";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while($row = pmb_mysql_fetch_assoc($result)) {
					$this->segments[] = search_segment::get_instance($row['id_search_segment']);
				}
			}
		}
		return $this->segments;		
	}
	
	public function get_opac_views_form() {
		global $opac_opac_view_activate;
		global $search_universe_opac_views;
		
		$form = '';
		if($opac_opac_view_activate) {
			$form = $search_universe_opac_views;
			$opac_views = new opac_views();
			$form = str_replace("!!opac_views_selector!!", opac_views::get_selector('universe_opac_views', $this->opac_views), $form);
		}
		return $form;
	}
	
	public function get_segments_form($segment_id = 0) {
		global $search_universe_segments_form_row;
		global $search_universe_segment_logo;
		global $charset;
		
		$segments_form = "";
		$segments = $this->get_segments();		
		
		if (is_array($segments) && count($segments)) {
			foreach ($segments as $segment) {
				$segment_form = str_replace("!!segment_label!!", htmlentities(stripslashes($segment->get_label()), ENT_QUOTES, $charset), $search_universe_segments_form_row); 
				$segment_form = str_replace("!!segment_description!!", $segment->get_description(), $segment_form);
				if($segment->get_logo()){
					$segment_form = str_replace("!!segment_logo!!", $search_universe_segment_logo, $segment_form);
					$segment_form = str_replace("!!segment_logo!!", $segment->get_logo(), $segment_form);
				}
				$segment_form = str_replace("!!segment_logo!!", '', $segment_form);
				
				$segment_form = str_replace("!!segment_id!!", $segment->get_id(), $segment_form);
				$segment_form = str_replace("!!segment_selected!!", ($segment_id == $segment->get_id() ? 'selected="selected"' : '' ), $segment_form);
				$segments_form .= $segment_form;
			}
		}
		return $segments_form;
	}
	
	public function get_id() {
	    return $this->id;
	}
	
	public function get_display_segments() {
        $this->get_segments();
        return $this->segments;
	}

	public function get_result_from_segments(){
	    $result_tab = array();
	    $this->get_segments();
	    foreach($this->segments as $segment){
	        $set = $segment->get_set();
	        if ($set->get_data_set()) {
	            $result_tab[] = $set->make_search(); 	            
	        }
	        //$segment->get_preview_results();
	    }
	    $query = "SELECT * FROM ". implode(', ', $result_tab);
	    $result = pmb_mysql_query($query);
	    
	    $row = pmb_mysql_fetch_all($result);
	    
	}
	
	public function rec_history() {
        global $search_type;
        global $search_index;
        $search_type = 'search_universes';
        
        rec_history();
        
        return $search_index;
	}
	
	public function get_search_universes_history() {
	    global $universe_history;
	    if (!empty($universe_history)) {
	        return $universe_history;	            
	    }
	    return '';
	}
	
	public function get_search_universes_history_last_query() {
	    global $universe_history;
	    global $user_query;
	    if (!empty($universe_history)) {
	        $n = $universe_history;
	        if (!empty($_SESSION["search_universes".$n]["universe_query"])) {
	            return $_SESSION["search_universes".$n]["universe_query"];
	        }
	    }
	    if (!empty($user_query)) {
	        return $user_query;
	    }
	    return '';
	}
	
	public static function get_label_from_id($universe_id) {
	    $universe_id *=1;
	    if ($universe_id) {
    	    if (isset(static::$universes_labels[$universe_id])) {
    	        return static::$universes_labels[$universe_id];
    	    }
    	    if (!isset(static::$universes_labels)) {
    	        static::$universes_labels = array();
    	    }
    	    $query = "
			    SELECT search_universe_label FROM search_universes
				WHERE id_search_universe = '".$universe_id."'
			";
    	    $result = pmb_mysql_query($query);
    	    if ($result) {
    	        $row = pmb_mysql_fetch_assoc($result);
    	        static::$universes_labels[$universe_id] = $row["search_universe_label"];
    	        return static::$universes_labels[$universe_id];
    	    }
	    }
	    return '';
	}
	
	public function get_default_segment() {
	    return $this->default_segment;
	}
	
	
	public function get_universe_query() {
	    global $search_index;
	    if (!empty($search_index)) {
	        $n = $search_index;
	        if (!empty($_SESSION["search_universes".$n]["universe_query"])) {
	            return $_SESSION["search_universes".$n]["universe_query"];
	        }
	    }
	    return '*';
	}
}