<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_authperso_datasource_works.class.php,v 1.1 2018/03/26 09:31:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_authperso_datasource_works extends frbr_entity_common_datasource {
	
	public function __construct($id=0){
		$this->entity_type = 'works';
		parent::__construct($id);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
		$query = "SELECT oeuvre_event_tu_num as id, oeuvre_event_authperso_authority_num as parent FROM tu_oeuvres_events
			WHERE oeuvre_event_authperso_authority_num IN (".implode(',', $datas).")";
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}
}