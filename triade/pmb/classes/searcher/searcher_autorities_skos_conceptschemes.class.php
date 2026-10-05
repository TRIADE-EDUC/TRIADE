<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_autorities_skos_conceptschemes.class.php,v 1.2 2022/01/14 13:51:45 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class searcher_autorities_skos_conceptschemes extends searcher_autorities {

	public function __construct($user_query){
		parent::__construct($user_query);
		$this->object_key = "id_item";
		$this->object_index_key= "id_item";
		$this->object_words_table = "skos_words_global_index";
		$this->object_fields_table = "skos_fields_global_index";
		$this->field_restrict[]= array(
				'field' => "code_champ",
				'values' => array(100),
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
 			$query = "select ".$this->table_tempo.".".$this->object_key." from ".$this->table_tempo." join ".$this->object_fields_table." on ".$this->table_tempo.".".$this->object_key." = ".$this->object_fields_table.".".$this->object_index_key." where code_champ= 100 order by pert desc,".$this->object_fields_table.".".$this->object_fields_value." asc limit ".$start.",".$number;
 		} else {
 			$query = "select ".$this->object_key." from ".$this->object_fields_table." where code_champ= 100 and ".$this->object_fields_table.".".$this->object_index_key." in (".$this->get_result().") order by ".$this->object_fields_table.".".$this->object_fields_value." asc limit ".$start.",".$number;
 		}
 		$result = pmb_mysql_query($query) or die( pmb_mysql_error());
 		if(pmb_mysql_num_rows($result)){
 			$this->result=array();
 			while($row = pmb_mysql_fetch_object($result)){
 				$this->result[] = $row->{$this->object_key};
 			}
 		}
 	}
	
	protected function get_full_results_query(){
		
		$query = "select ".$this->object_key." from ".$this->object_fields_table." where code_champ = 100";	
		return $query;	
	}
}