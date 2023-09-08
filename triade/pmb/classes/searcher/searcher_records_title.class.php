<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_records_title.class.php,v 1.4 2016-05-06 14:26:34 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


require_once("$class_path/searcher/searcher_records.class.php");

class searcher_records_title extends searcher_records {
	
	public function __construct($user_query){
		global $mutli_crit_indexation_oeuvre_title;
		parent::__construct($user_query);
		
		$this->field_restrict[]= array(
				'field' => "code_champ",
				'values' => array(1,2,3,4,6,23),
				'op' => "and",
				'not' => false
		);
		
		if($mutli_crit_indexation_oeuvre_title==1){
			$this->field_restrict[]= array(
					'field' => "code_champ",
					'values' => array(26),
					'op' => "or",
					'not' => false,
					'sub'=> array(
						array(
							'sub_field' => "code_ss_champ",
							'values' => 1,
							'op' => "and",
							'not' => false
						),
				)
			);
		}
	}
	
	protected function _get_search_type(){
		return parent::_get_search_type()."_title";
	}
}