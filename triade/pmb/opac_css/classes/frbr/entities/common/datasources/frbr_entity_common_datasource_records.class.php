<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_records.class.php,v 1.2 2018-09-19 13:49:36 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($class_path."/filter_results.class.php");

class frbr_entity_common_datasource_records extends frbr_entity_common_datasource {
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	protected function filter_data_with_access_rights($data) {
	    $implode_data = implode(",", $data);
	    $filter = new filter_results($implode_data);
	    $records_id = $filter->get_results();
	    $data = explode(",",$records_id);
	    return $data;
	}
	
	public function get_datas($datas=array()) {
	    if (isset($datas[0])) {
	        $datas[0] = $this->filter_data_with_access_rights($datas[0]);
	    }
	    $datas = parent::get_datas($datas);
	    return $datas;
	}
}