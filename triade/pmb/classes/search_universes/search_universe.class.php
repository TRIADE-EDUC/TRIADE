<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_universe.class.php,v 1.31.2.1 2024/10/28 13:43:42 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
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
	
	protected $rmc_enabled = 0;
	
	protected $universe_settings;

	public function __construct($id = 0){
		$this->id = intval($id);
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
				$this->rmc_enabled = $row["search_universe_rmc_enabled"];
				$this->universe_settings = encoding_normalize::json_decode($row["search_universe_settings"]);
			}
		}
	}
	
	public function get_label() {
		return $this->label;
	}
	
	public function get_translated_label() {
		return translation::get_translated_text($this->id, 'search_universes', 'universe_label',  $this->label);
	}
	
	public function get_description() {
		return $this->description;
	}
	
	public function get_translated_description() {
		return translation::get_translated_text($this->id, 'search_universes', 'universe_description',  $this->description);
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
		global $pmb_opac_url;
		
		$content_form = $search_universe_content_form;
		$content_form = str_replace('!!universe_label!!', htmlentities($this->label, ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!universe_description!!', htmlentities($this->description, ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!universe_opac_views!!', $this->get_opac_views_form(), $content_form);
		$content_form = str_replace('!!universe_rmc_enabled!!', (($this->rmc_enabled) ? 'checked':''), $content_form);
		$content_form = str_replace('!!universe_perio_enabled!!', (!empty($this->universe_settings->perio_enabled) ? 'checked':''), $content_form);
		$content_form = str_replace('!!universe_autocomplete!!', (!empty($this->universe_settings->autocomplete) ? 'checked':''), $content_form);
		
		$interface_form = new interface_admin_universe_form('search_universe_form');
		$interface_form->set_duplicable(true);
		if($this->id){
			$interface_form->set_label($msg['search_universe_edit']);
			$content_form = str_replace('!!universe_segments_form!!', $this->get_segments_form(), $content_form);
			$content_form = str_replace('!!universe_id_field!!', $interface_form->get_display_field_text($msg['search_universe_id'], $this->id), $content_form);
			$content_form = str_replace('!!universe_permalink_field!!', $interface_form->get_display_field_url($msg['search_universe_url'], $pmb_opac_url."index.php?lvl=search_universe&id=".$this->id), $content_form);
		} else {
			$interface_form->set_label($msg['search_universe_create']);
			$content_form = str_replace('!!universe_segments_form!!', '', $content_form);
    		$content_form = str_replace('!!universe_id_field!!','', $content_form);
    		$content_form = str_replace('!!universe_permalink_field!!','', $content_form);
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
		global $universe_rmc_enabled;
		global $universe_template_directory;
		global $universe_opac_views;
		global $universe_perio_enabled;
		global $universe_autocomplete;
		$this->label = stripslashes($universe_label);
		$this->description = stripslashes($universe_description);
		$this->rmc_enabled = (isset($universe_rmc_enabled)? 1:0);
		if(empty($this->universe_settings)) {
			$this->universe_settings = new stdClass();
		}
		$this->universe_settings->perio_enabled = (isset($universe_perio_enabled)? 1:0);
		$this->universe_settings->autocomplete = (isset($universe_autocomplete)? 1:0);
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
			search_universe_opac_views = "'.implode(',', $this->opac_views).'",
            search_universe_rmc_enabled = '.$this->rmc_enabled.',
		    search_universe_settings = "'. addslashes(encoding_normalize::json_encode($this->universe_settings)).'"';
		pmb_mysql_query($query.$query_clause);
		if(!$this->id){
			$this->id = pmb_mysql_insert_id();			
		}
		$translation = new translation($this->id, "search_universes");
		$translation->update("universe_label");
		$translation->update_text("universe_description");
	}
	
	public static function delete($id) {
		$id = intval($id);
		if (!$id) {
		    return;
		}
		translation::delete($id, "search_universes");
		$query = "delete from search_universes where id_search_universe = ".$id;
		pmb_mysql_query($query);
		$query = "SELECT id_search_segment FROM search_segments
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
						WHERE search_segment_num_universe = '".$this->id."' ORDER BY search_segment_order, search_segment_label";
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
			foreach ($segments as $key => $segment) {
			    
			    $entities_label = entities::get_entities_labels();
			    if (!empty($entities_label[$segment["search_segment_type"]])) {
			        $type = $entities_label[$segment["search_segment_type"]];
			    }else if ($segment["search_segment_type"] > 10000 ){
			        $type = 'onto';
			    }else{
                    $type = authpersos::get_name(($segment["search_segment_type"]-1000));
			    }
			    
		        $even_odd = "odd";
			    if ($key % 2) {
			        $even_odd = "even";
			    }
			    
				$segment_form = str_replace("!!segment_label!!", htmlentities(stripslashes($segment["search_segment_label"]), ENT_QUOTES, $charset), $search_universe_segment); 
				$segment_form = str_replace("!!segment_logo!!", ($segment["search_segment_logo"] ? "<img width='30px' height='30px' src='".$segment["search_segment_logo"]."' alt='".$segment["search_segment_label"]."'/>" : ''), $segment_form);
				$segment_form = str_replace("!!segment_type!!", htmlentities(stripslashes($type ?? ""), ENT_QUOTES, $charset), $segment_form);
				$segment_form = str_replace("!!segment_id!!", $segment["id_search_segment"], $segment_form);
				$segment_form = str_replace("!!even_odd!!", $even_odd, $segment_form);
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
	
	public static function update_default_segment($univers_id, $segment_id, $update_value) {
	    $univers_id = intval($univers_id);
	    $segment_id = intval($segment_id);
	    
	    if ($update_value && $univers_id && $segment_id) {
	        $query = "UPDATE search_universes SET search_universe_default_segment = '".$segment_id."' WHERE id_search_universe = '".$univers_id."'";
	        pmb_mysql_query($query);
	    }else if($univers_id && $segment_id){
	        $query = "UPDATE search_universes SET search_universe_default_segment = 0 WHERE id_search_universe = '".$univers_id."' and search_universe_default_segment = '".$segment_id."'";
	        pmb_mysql_query($query);
	    }
	}
	
    /**
     * Retourne tous les univers
     * @return search_universe[]
     */
	public static function get_universe_list()
	{
	    $universes = array();
	    
	    $query = "SELECT DISTINCT id_search_universe FROM search_universes JOIN search_segments ON search_segment_num_universe = id_search_universe";
	    $result = pmb_mysql_query($query);
	    
	    if(pmb_mysql_num_rows($result)) {
	        while ($row = pmb_mysql_fetch_assoc($result)) {
	            $universes[] = new search_universe($row['id_search_universe']);
	        }
	    }
	    return $universes;
	}

	public function set_id($id = 0)
	{
		$this->id = $id;
	}

	/**
	 * Duplique l'univers et tous ses segments
	 */
	public function duplicate()
	{
		$duplicate = clone $this;
		$duplicate->id = 0;
		$duplicate->save();
		if($duplicate->id) {
			//On duplique les segments qu'on rattache au nouvel univers
			$universeToDuplicateArray = array($duplicate->id);
			$this->get_segments();
			foreach($this->segments as $segment) {
				$segmentInstance = search_segment::get_instance($segment["id_search_segment"]);
				$segmentInstance->duplicate($universeToDuplicateArray);
			}
		}
		return $duplicate;
	}
}