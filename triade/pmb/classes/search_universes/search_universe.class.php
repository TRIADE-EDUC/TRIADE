<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_universe.class.php,v 1.13 2018-07-26 09:45:31 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/interface/interface_form.class.php');
require_once($class_path.'/opac_views.class.php');
require_once($class_path.'/translation.class.php');
require_once($include_path.'/templates/search_universes/search_universe.tpl.php');

class search_universe {
	
	protected $id;
	
	protected $label;
	
	protected $description;
	
	protected $template_directory;
	
	protected $opac_views;
	
	protected $segments;
	
	protected $default_segment;

	public function __construct($id = 0){
		$this->id = $id+0;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$this->label = '';
		$this->description = '';
		$this->template_directory = '';
		$this->opac_views = array();
		$this->default_segment = 0;
		if ($this->id) {
			$query = "SELECT * FROM search_universes
						WHERE id_search_universe = '".$this->id."'";
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
	
	public function get_form($ajax = false) {
		global $msg;
		global $base_path;
		global $charset;
		global $search_universe_content_form;
		
		$content_form = $search_universe_content_form;
		$content_form = str_replace('!!universe_label!!', htmlentities($this->label, ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!universe_description!!', htmlentities($this->description, ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!universe_opac_views!!', $this->get_opac_views_form(), $content_form);
		
		$interface_form = new interface_form('search_universe_form');
		if($this->id){
			$interface_form->set_label($msg['search_universe_edit']);
			$content_form = str_replace('!!universe_segments_form!!', $this->get_segments_form(), $content_form);
		} else {
			$interface_form->set_label($msg['search_universe_create']);
			$content_form = str_replace('!!universe_segments_form!!', '', $content_form);
		}
		$content_form = str_replace('!!universe_id!!', $this->id, $content_form);
		
		$interface_form->set_object_id($this->id);
		$interface_form->set_url_base($base_path."/admin.php?categ=search_universes&sub=universe");
		$interface_form->set_content_form($content_form);
		$interface_form->set_table_name('search_universes');
		if ($ajax) {
		    return $interface_form->get_display_ajax();
		}
		return $interface_form->get_display();
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
		if (!$id) {
		    return;
		}
		$query = "delete from search_universes where id_search_universe = ".$id;
		pmb_mysql_query($query);
		$query = "SELECT id_search_segment FROM search_segments
						JOIN search_universes ON id_search_universe = search_segment_num_universe
						WHERE search_segment_num_universe = '".$id."'";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {				
			while ($row = pmb_mysql_fetch_object($result)) {
				search_segment::delete($row->id_search_segment);
			}
		}		
		return true;		
	}
	
	public function get_segments() {
		if (!isset($this->segments)) {
			$this->segments = array();
			$query = "SELECT * FROM search_segments
						JOIN search_universes ON id_search_universe = search_segment_num_universe
						WHERE search_segment_num_universe = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
			    while ($row = pmb_mysql_fetch_assoc($result)) {
			        $this->segments[] = $row;
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
	
	public function get_segments_form() {
		global $search_universe_segment;		
		global $search_universe_segments_form;		
		global $charset;
		
		$segments_form = "";
		$segments = $this->get_segments();		
		
		if (is_array($segments) && count($segments)) {
			foreach ($segments as $segment) {
				$segment_form = str_replace("!!segment_label!!", htmlentities(stripslashes($segment["search_segment_label"]), ENT_QUOTES, $charset), $search_universe_segment); 
				$segment_form = str_replace("!!segment_logo!!", ($segment["search_segment_logo"] ? "<img width='30px' height='30px' src='".$segment["search_segment_logo"]."' alt='".$segment["search_segment_label"]."'/>" : ''), $segment_form);
				$segment_form = str_replace("!!segment_id!!", $segment["id_search_segment"], $segment_form);
				$segments_form .= $segment_form;
			}
		}
		
		$html = str_replace("!!universe_segments!!", $segments_form, $search_universe_segments_form);
		$html = str_replace("!!universe_id!!", $this->id, $html);
		
		return $html;
	}
	
	public function get_id() {
	    return $this->id;
	}
	
	public function get_default_segment() {
	    return $this->default_segment;
	}
	
	public static function update_default_segment($univers_id, $segment_id) {
	    $univers_id *= 1;
	    $segment_id *= 1;
	    if ($univers_id && $segment_id) {
	        $query = "UPDATE search_universes SET search_universe_default_segment = '".$segment_id."' WHERE id_search_universe = '".$univers_id."'";
	        pmb_mysql_query($query);
	    }
	}
}