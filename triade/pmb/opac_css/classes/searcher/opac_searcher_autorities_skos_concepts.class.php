<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: opac_searcher_autorities_skos_concepts.class.php,v 1.5 2021/12/27 08:17:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/searcher/opac_searcher_autorities.class.php");

class opac_searcher_autorities_skos_concepts extends opac_searcher_autorities {

	public function __construct($user_query){
		parent::__construct($user_query);
		$this->object_key = "id_item";
		$this->object_index_key= "id_item";
		$this->object_words_table = "skos_words_global_index";
		$this->object_fields_table = "skos_fields_global_index";
		$this->field_restrict[]= array(
				'field' => "code_champ",
				'values' => array(1,2,3),
				'op' => "and",
				'not' => false
		);
	}
	
	public function _get_search_type(){
		return parent::_get_search_type()."_concepts";
	}
	
 	// à réécrire au besoin...
 	protected function _sort($start,$number){
 		if($this->table_tempo != ""){
 			$query = "select * from ".$this->table_tempo." join ".$this->object_fields_table." on ".$this->table_tempo.".".$this->object_key." = ".$this->object_fields_table.".".$this->object_index_key." where code_champ= 1 order by pert desc,".$this->object_fields_table.".".$this->object_fields_value." asc limit ".$start.",".$number;
 			$result = pmb_mysql_query($query);
 			if(pmb_mysql_num_rows($result)){
 				$this->result=array();
 				while($row = pmb_mysql_fetch_object($result)){
 					$this->result[] = $row->{$this->object_key};
 				}
 			}
 		}
 	}
 	
 	protected static function _get_typdoc_filter($on_notice=false){
 		global $typdoc;
 		$return ="";
 		if ($typdoc){
			$return = " join index_concept on type_object = 1 and num_concept = id_item join notices on num_object = notice_id and typdoc = '".$typdoc."'";
 		}
 		return $return;
 	}
 	
 	protected function _filter_results(){
 		global $concept_scheme;
 		
 		$query = "";

 		if (($concept_scheme !== null) && ($concept_scheme*1 === 0)) {
 			// On cherche dans les concepts sans schéma
 			$query = "select ".$this->object_key." from ".$this->object_fields_table." where ".$this->object_key." not in (select ".$this->object_key." from ".$this->object_fields_table." where code_champ = 4) and code_champ = 1 and ".$this->object_key." in (".$this->objects_ids.")";
 		} else if ($concept_scheme && ($concept_scheme != -1)) {
 			// On cherche dans un schema en particulier
 			$query = "select ".$this->object_key." from ".$this->object_fields_table." where code_champ = 4 and authority_num = ".($concept_scheme*1)." and ".$this->object_key." in (".$this->objects_ids.")";
 		}
 		// Pas de filtre si on cherche dans tous les schémas
 		if ($query) {
 			$result = pmb_mysql_query($query);
 			$this->objects_ids ="";
 			if($result && pmb_mysql_num_rows($result)){
 				while($row = pmb_mysql_fetch_object($result)){
 					if($this->objects_ids) $this->objects_ids.= ",";
 					$this->objects_ids.= $row->{$this->object_key};
 				}
 			}
 		}
	}

	protected function get_full_results_query(){
		global $concept_scheme;
		$query = "select ".$this->object_key." from ".$this->object_fields_table." where code_champ = 1";
		if ($concept_scheme*1 === 0) {
			// On cherche dans les concepts sans schéma
			$query.= " and ".$this->object_key." not in (select ".$this->object_key." from ".$this->object_fields_table." where code_champ = 4)";
		} else if ($concept_scheme && ($concept_scheme != -1)) {
			// On cherche dans un schema en particulier
			$query = " and ".$this->object_key." in (select ".$this->object_key." from ".$this->object_fields_table." where code_champ = 4 and authority_num = ".($concept_scheme*1);
		}
		return $query;	
	}
	
	
	protected function _get_sign($sorted=false) {
		global $concept_scheme;
		$str_to_hash = parent::_get_sign($sorted);
		$str_to_hash .= "&concept_scheme=".$concept_scheme;
		return md5($str_to_hash);
	}
}