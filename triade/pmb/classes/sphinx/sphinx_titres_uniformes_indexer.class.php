<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_titres_uniformes_indexer.class.php,v 1.3 2018-07-16 09:44:44 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once $class_path.'/sphinx/sphinx_indexer.class.php';

class sphinx_titres_uniformes_indexer extends sphinx_authorities_indexer {
	
	public function __construct() {
		global $include_path;
		$this->type = 7;
		$this->default_index = "titres_uniformes";
		parent::__construct();
		$this->filters = ['oeuvre_nature', 'oeuvre_type', 'status'];
		$this->setChampBaseFilepath($include_path."/indexation/authorities/titres_uniformes/champs_base.xml");
	}
	
	protected function addSpecificsFilters($id, $filters =array()){
		$filters = parent::addSpecificsFilters($id, $filters);

		//Récupération du statut
		$query = "select tu_oeuvre_nature, num_statut, tu_oeuvre_type from titres_uniformes join authorities on tu_id = num_object and type_object = ".AUT_TABLE_TITRES_UNIFORMES." where id_authority = ".$id;
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		$filters['oeuvre_nature'] = $row->tu_oeuvre_nature;
		$filters['oeuvre_type'] = $row->tu_oeuvre_type;
		$filters['status'] = $row->num_statut;	
		return $filters;
	}
}