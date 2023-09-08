<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_records_list.class.php,v 1.2 2018-01-08 14:52:37 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_records_list extends cms_module_common_datasource_list{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->limitable = true;
		$this->sortable = true;
	}
	
	/*
	 * On défini les critères de tri utilisable pour cette source de donnée
	 */
	protected function get_sort_criterias() {
		return array (
			"date_parution",
			"notice_id",
			"index_sew"
		);
	}
	
	protected function sort_records($records) {
		$return = array('records' => array());
		if(!count($records)) {
			return $return;
		}
		if (empty($this->parameters["sort_by"])) {
			$return["records"] = $records;
			return $return;
		}
		$query = 'select notice_id from notices
				where notice_id in ('.implode(',', $records).')
				order by '.$this->parameters["sort_by"].' '.$this->parameters["sort_order"];
		if ($this->parameters['nb_max_elements']*1) {
			$query.= ' limit '.$this->parameters['nb_max_elements']*1;
		}
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result) > 0) {
			$return["title"] = "Liste de notices";
			while($row = pmb_mysql_fetch_object($result)){
				$return["records"][] = $row->notice_id;
			}
		}
		return $return;
	}
}