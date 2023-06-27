<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_facets.class.php,v 1.8 2018-04-27 10:18:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/templates/search_universes/search_segment_facets.tpl.php');

class search_segment_facets {
	
	protected $num_segment;
	
	protected $segment_type;
	
	protected $facets;
		
	public function __construct($num_segment = 0){
		$this->num_segment = $num_segment+0;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$this->facets = array();
		$this->order = 0;
		if ($this->num_segment) {
			$query = "SELECT num_facet FROM search_segments_facets WHERE num_search_segment = '".$this->num_segment."' order by search_segment_facet_order";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while($row = pmb_mysql_fetch_assoc($result)) {
					$this->facets[] = $row['num_facet'];
				}
			}
		}
	}

	public function get_facets() {
	    if (!isset($this->facets)) {
	        $this->facets = array();
	    }
		return $this->facets;
	}
	
	public function get_form($type = 0) {
	    global $msg, $charset, $base_url;
	    global $segment_facets_list_form, $segment_facets_list_form_line;
	    global $sub;
	    
	    $list = "";
	    $query = "SELECT * FROM facettes WHERE facette_type = '".entities::get_string_from_const_type($this->segment_type)."' ORDER BY facette_order, facette_name";
	    $result = pmb_mysql_query($query);
	    $i = 0;
	    while ($row = pmb_mysql_fetch_assoc($result)) {
	        if ($i % 2) $pair_impair = "even"; else $pair_impair = "odd";
	        $line = $segment_facets_list_form_line;
	        $line = str_replace('!!facet_class!!', $pair_impair, $line);
	        $line = str_replace('!!facet_type!!', 'segment_facets[]', $line);
	        $line = str_replace('!!facet_checked!!', (in_array($row['id_facette'], $this->facets) ? "checked='checked'" : ""), $line);
	        $line = str_replace('!!facet_id!!', $row['id_facette'], $line);
	        $line = str_replace('!!facet_name!!', htmlentities($row['facette_name'], ENT_QUOTES, $charset), $line);
	        $line = str_replace('!!facet_link!!', $base_url."/admin.php?categ=opac&sub=".$sub."&action=edit&id=".$row['id_facette'], $line);
	        $list.= $line;
	        $i++;
	    }
	    $segment_facets_list_form = str_replace('!!facets_list!!', $list, $segment_facets_list_form);
	    $segment_facets_list_form = str_replace('!!segment_id!!', $this->num_segment, $segment_facets_list_form);
	    $segment_facets_list_form = str_replace('!!segment_type!!', entities::get_string_from_const_type($this->segment_type), $segment_facets_list_form);
	    return $segment_facets_list_form;
	}
		
	public function set_properties_from_form(){
	    global $segment_facets;	    
	    $this->facets = array();
	    if (!empty($segment_facets)) {
	        $this->facets = $segment_facets;
	    }
	}
	
	public function save() {
		static::delete($this->num_segment);
		foreach($this->facets as $order=>$num_facet) {
			$query = 'INSERT INTO search_segments_facets SET
				num_search_segment = '.$this->num_segment.',
				num_facet = "'.$num_facet.'",
				search_segment_facet_order = "'.$order.'"';
			pmb_mysql_query($query);
		}
	}
	
	public static function delete($id=0) {
		$id += 0;
		if (!$id) {
		    return;
		}
		$query = "delete from search_segments_facets where num_search_segment = ".$id;
		pmb_mysql_query($query);
	}

	public static function on_delete_facet($id=0) {
	    $id += 0;
	    if (!$id) {
	        return;
	    }
	    $query = "delete from search_segments_facets where num_facet = ".$id;
	    pmb_mysql_query($query);
	}
	
	public function add_facet($num_facet) {
	    $num_facet  += 0;
	    if ($num_facet) {
	        $this->get_facets();
	        $this->facets[] = $num_facet;
	    }
	}
	
	public function set_segment_type($segment_type) {
		$this->segment_type = $segment_type+0;
	}
}