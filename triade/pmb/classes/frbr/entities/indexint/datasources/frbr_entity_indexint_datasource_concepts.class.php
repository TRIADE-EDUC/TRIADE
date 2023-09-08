<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_indexint_datasource_concepts.class.php,v 1.4 2017-09-15 10:06:14 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_indexint_datasource_concepts extends frbr_entity_common_datasource_concept {
	
	public function __construct($id=0){
		$this->entity_type = 'concepts';
		parent::__construct($id);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
		$query = "select distinct index_concept.num_concept as id, index_concept.num_concept as parent FROM index_concept
			WHERE index_concept.type_object = 9 AND index_concept.num_object IN (".implode(',', $datas).")";
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}
}