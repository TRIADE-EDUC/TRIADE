<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_recordslist_selector_collection.class.php,v 1.3.16.1 2025/02/12 12:34:08 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_recordslist_selector_collection extends cms_module_common_selector{

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
				'collection' => 0
			);
			if($this->parameters['sub_selector']) {
				$sub_selector= new $this->parameters['sub_selector']($this->get_sub_selector_id($this->parameters['sub_selector']));
				$sub_selector_value = intval($sub_selector->get_value());
				if($sub_selector_value){
				    $this->value['record'] = $sub_selector_value;
				    $query = "select coll_id from notices where notice_id = '".$sub_selector_value."'";
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						$row = pmb_mysql_fetch_object($result);
						$this->value['collection'] = $row->coll_id;
					}
				}
			}
		}
		return $this->value;
	}
}