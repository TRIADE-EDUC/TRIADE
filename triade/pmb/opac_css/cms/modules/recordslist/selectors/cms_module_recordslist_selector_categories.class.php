<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_recordslist_selector_categories.class.php,v 1.3.16.1 2025/02/12 12:34:08 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_recordslist_selector_categories extends cms_module_common_selector{

	public function __construct($id=0){
		parent::__construct($id);
		$this->once_sub_selector=true;
	}

	protected function get_sub_selectors(){
		return array(
			"cms_module_common_selector_record_permalink",
			"cms_module_common_selector_record",
			"cms_module_common_selector_env_var"
		);
	}

	public function get_value(){
		//le sous-sélecteur va nous donner la notice...
		if(!$this->value){
			$this->value= array(
					'record' => 0,
					'categories' => ""
			);
			if($this->parameters['sub_selector']) {
				$sub_selector= new $this->parameters['sub_selector']($this->get_sub_selector_id($this->parameters['sub_selector']));
				$sub_selector_value = intval($sub_selector->get_value());
				if( $sub_selector_value){
				    $this->value['record'] = $sub_selector_value;
				    $query = "select GROUP_CONCAT(num_noeud SEPARATOR ',') as categs from notices_categories where notcateg_notice = '".$sub_selector_value."'";
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						$row = pmb_mysql_fetch_object($result);
						$this->value['categories'] = $row->categs;
					}
				}
			}
		}
		return $this->value;
	}
}