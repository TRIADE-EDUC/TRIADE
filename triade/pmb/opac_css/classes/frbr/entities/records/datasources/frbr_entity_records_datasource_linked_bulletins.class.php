<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_records_datasource_linked_bulletins.class.php,v 1.1 2022/06/27 14:39:30 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_records_datasource_linked_bulletins extends frbr_entity_common_datasource_records {
	
	public function __construct($id=0){
		$this->entity_type = 'records';
		parent::__construct($id);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
		$query = "SELECT DISTINCT bulletin_notice AS parent, num_notice AS id FROM analysis
			JOIN bulletins ON bulletin_id = analysis_bulletin
			WHERE analysis_notice IN (".implode(',', $datas).")";
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}
}