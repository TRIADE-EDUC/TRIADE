<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_external.class.php,v 1.3 2021/06/29 14:20:07 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once "$class_path/search_universes/search_segment.class.php";
require_once "$class_path/search_universes/external/search_segment_external_search_result.class.php";
require_once "$class_path/search_universes/external/search_segment_external_sort.class.php";

class search_segment_external extends search_segment {
		
	public function get_search_result() 
	{
	    if (isset($this->search_result)) {
	        return $this->search_result;
	    }
	    $this->search_result = new search_segment_external_search_result($this);
	    return $this->search_result;
	}
	
	public function get_sort() {
	    if (isset($this->sort)) {
	        return $this->sort;
	    }
	    $this->sort = new search_segment_external_sort($this->id);
	    return $this->sort;
	}
	
}