<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_authors_datasource_records.class.php,v 1.2 2017-06-02 09:52:43 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_authors_datasource_records extends frbr_entity_common_datasource {
	
	public function __construct($id=0){
		$this->entity_type = 'records';
		parent::__construct($id);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
		$query = "select distinct responsability_notice as id, responsability_author as parent FROM responsability
			WHERE responsability_author IN (".implode(',', $datas).")";
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}
}