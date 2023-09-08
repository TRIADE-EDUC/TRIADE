<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_collections_datasource_subcollections.class.php,v 1.2 2017-06-02 09:52:43 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_collections_datasource_subcollections extends frbr_entity_common_datasource {
	
	public function __construct($id=0){
		$this->entity_type = 'subcollections';
		parent::__construct($id);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
		$query = "select distinct sub_coll_id as id, sub_coll_parent as parent FROM sub_collections
			WHERE sub_coll_parent IN (".implode(',', $datas).")";
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}
}