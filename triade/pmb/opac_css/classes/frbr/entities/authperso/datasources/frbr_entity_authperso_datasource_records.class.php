<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_authperso_datasource_records.class.php,v 1.3 2020/09/22 12:09:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_authperso_datasource_records extends frbr_entity_common_datasource_records {
	
	public function __construct($id=0){
		$this->entity_type = 'records';
		parent::__construct($id);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
		$query = "SELECT notice_authperso_notice_num as id, notice_authperso_authority_num as parent FROM notices_authperso
			WHERE notice_authperso_authority_num IN (".implode(',', $datas).")";
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}
}