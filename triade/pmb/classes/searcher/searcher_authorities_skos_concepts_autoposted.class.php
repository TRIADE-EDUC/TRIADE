<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_authorities_skos_concepts_autoposted.class.php,v 1.3 2018/01/24 15:53:00 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/skos/skos_concept.class.php");
require_once($class_path."/onto/common/onto_common_uri.class.php");

class searcher_authorities_skos_concepts_autoposted extends searcher_autorities {
	public $user_query;

	public function __construct($user_query){
		parent::__construct($user_query);
		$this->user_query = $user_query;
		$this->object_key = "uri_id";
		$this->object_fields_table = "onto_uri";
	}
	
	public function _get_search_type(){
		return parent::_get_search_type()."_concepts";
	}
	
	public function get_full_query(){
		$query = "";
		if (is_numeric($this->user_query)) {
			$uri = onto_common_uri::get_uri($this->user_query);
			$broad_paths = skos_concept::get_broad_paths($uri);
			$narrow_paths = skos_concept::get_narrow_paths($uri);
			
			$ids = $this->user_query;
			
			//broad
			foreach ($broad_paths as $broad_path) {
				$broad_path = substr($broad_path,0, -1);
				$ids.= ','.str_replace('/', ',', $broad_path);
			}
			//narrow
			foreach ($narrow_paths as $narrow_path) {
				$narrow_path = substr($narrow_path,0,-1);
				$ids.= ',';
				$ids.= str_replace('/', ',', $narrow_path);
			}
			$query = "
					SELECT DISTINCT id_authority 
					FROM authorities 
					WHERE authorities.num_object IN (".$ids.")
					AND type_object = 10";
		}
		return $query;	
	}
}