<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_records_list.class.php,v 1.3.6.2.2.1 2025/02/12 12:34:07 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_records_list extends cms_module_common_datasource_list{

	public function __construct($id=0){
		parent::__construct($id);
		$this->limitable = true;
		$this->sortable = true;
		$this->paging = false;
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
		$return = array();
		if (!is_countable($records) || !count($records)) {
		    return false;
		}
		foreach ($records as $key => $record) {
			$records[$key] = intval($record);
		}
		$query = 'select notice_id from notices
				where notice_id in ("'.implode('","', $records).'")
				order by '.$this->parameters["sort_by"].' '.$this->parameters["sort_order"];
		if (!empty($this->parameters['nb_max_elements']) && $this->parameters['nb_max_elements']*1) {
			$query.= ' limit '. (string) intval($this->parameters['nb_max_elements']);
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