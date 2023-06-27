<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_indexint_indexer.class.php,v 1.2 2018-07-16 09:44:44 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once $class_path.'/sphinx/sphinx_indexer.class.php';

class sphinx_indexint_indexer extends sphinx_authorities_indexer {
	
	public function __construct() {
		global $include_path;
		$this->type = AUT_TABLE_INDEXINT;
		$this->default_index = "indexint";
		parent::__construct();
		$this->filters = ['status', 'num_pclass'];
		$this->setChampBaseFilepath($include_path."/indexation/authorities/indexint/champs_base.xml");
	}
	
	protected function addSpecificsFilters($id, $filters =array()){
		$filters = parent::addSpecificsFilters($id, $filters);

		//Récupération du statut
		$query = "select num_pclass, num_statut from indexint join authorities on indexint_id = num_object and type_object = ".$this->type." where id_authority = ".$id;
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		$filters['num_pclass'] = $row->num_pclass;
		$filters['status'] = $row->num_statut;
		return $filters;
	}
}