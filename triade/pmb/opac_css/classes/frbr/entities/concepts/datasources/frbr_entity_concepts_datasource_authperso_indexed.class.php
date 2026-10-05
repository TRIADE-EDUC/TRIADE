<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_concepts_datasource_authperso_indexed.class.php,v 1.1 2019/09/19 08:36:25 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_concepts_datasource_authperso_indexed extends frbr_entity_common_datasource {
	
	public function __construct($id=0){
		$this->entity_type = 'authperso';
		parent::__construct($id);
	}
	
	public function set_authperso_id($id) {
	    $this->parameters->authperso_id = intval($id);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
		$query = "select distinct index_concept.num_object as id, index_concept.num_concept as parent FROM index_concept
			WHERE index_concept.type_object = 12 AND index_concept.num_concept IN (".implode(',', $datas).")";
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}
}