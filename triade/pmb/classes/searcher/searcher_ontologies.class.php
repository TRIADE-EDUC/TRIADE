<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_ontologies.class.php,v 1.1 2017-05-29 15:23:37 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class searcher_ontologies extends searcher_generic {

	protected $id_ontology= 0;
	
	protected $object_table = '';
	
	public function __construct($user_query,$id_ontology) {
		parent::__construct($user_query);
		$this->object_index_key = "id_item";
		$this->object_words_table = "ontology".$id_ontology."_words_global_index";
		$this->object_fields_table = "ontology".$id_ontology."_fields_global_index";
		$this->object_key = 'id_item';
		$this->id_ontology = $id_ontology;
		
	}
	
	public function _get_search_type(){
		return "ontologies";
	}
	
	protected function get_full_results_query(){
		return 'select distinct '.$this->object_key.' from '.$this->object_words_table;;
	}
	
	protected function _get_ontology_filters(){		
		return [];
	}
	
	protected function _get_sign_elements($sorted=false) {
		$str_to_hash = parent::_get_sign_elements($sorted);
		$str_to_hash .= "&id_ontology=".$this->id_ontology;
		return $str_to_hash;
	}
	
	// à réécrire au besoin...
	protected function _sort($start,$number){
		global $dbh;
		global $last_param, $tri_param, $limit_param;
		if($this->table_tempo != ""){
			$query = "select * from ".$this->table_tempo." order by pert desc limit ".$start.",".$number;
			$res = pmb_mysql_query($query,$dbh);
			if($res && pmb_mysql_num_rows($res)){
				$this->result=array();
				while($row = pmb_mysql_fetch_object($res)){
					$this->result[] = $row->{$this->object_key};
				}
			}
		} else {
			if ($last_param) {
				$query = $this->_get_search_query().' '.$tri_param.' '.$limit_param;
			} else {
				$query = $this->_get_search_query().' '.$this->get_ontology_tri().' limit '.$start.', '.$number;
			}
			$res = pmb_mysql_query($query,$dbh);
			if($res && pmb_mysql_num_rows($res)){
				$this->result=array();
				while($row = pmb_mysql_fetch_object($res)){
					$this->result[] = $row->id_authority;
				}
			}
		}
	}
	
	public function get_ontology_tri() {
		// à surcharger si besoin
		return '';
	}
	
	protected function _sort_result($start,$number){
		if ($this->user_query != '*') {
			$this->_get_pert();
		}
		$this->_sort($start,$number);
	}
	
	public function get_raw_query()
	{
		$this->_analyse();
		return $this->_get_search_query();
	}
	
	public function get_pert_result($query = false) {
		$pert = '';
		if ($this->get_result() && ($this->user_query != '*')) {
			$pert = $this->_get_pert($query);
		}
		if ($query) {
			return $pert;
		}
		return $this->table_tempo;
	}
	
	public function get_results_list_from_search($label, $user_input, $list, $navbar) {
		$template = "
			<br />
			<br />
			<div class='row'>
				<h3>".$this->get_nb_results()." ".$label." ".$user_input."</h3>
			</div>
			<script type='text/javascript' src='./javascript/sorttable.js'></script>
			<table class='sortable'>
				".$list."
			</table>
			<div class='row'>
				".$navbar."
			</div>";
		return $template;
	}
}