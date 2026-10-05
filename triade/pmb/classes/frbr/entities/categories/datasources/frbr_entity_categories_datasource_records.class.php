<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_categories_datasource_records.class.php,v 1.2 2019/10/21 13:47:50 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_categories_datasource_records extends frbr_entity_common_datasource {
	
	public function __construct($id=0){
		$this->entity_type = 'records';
		parent::__construct($id);
	}
	
	public function get_sub_datasources() {
	    return array(
	        "frbr_entity_categories_datasource_records_link",
	        "frbr_entity_categories_datasource_records_used_in_custom_fields",
	    );
	}
	
	public function get_datas($datas = array()) {
	    return $this->get_sub_datasource_datas($datas);
	}
}