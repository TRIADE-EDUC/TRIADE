<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_collections_indexer.class.php,v 1.1 2017-04-25 07:04:13 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once $class_path.'/sphinx/sphinx_indexer.class.php';

class sphinx_collections_indexer extends sphinx_authorities_indexer {
	
	public function __construct() {
		global $include_path;
		$this->type = AUT_TABLE_COLLECTIONS;
		$this->default_index = "collections";
		parent::__construct();
		$this->setChampBaseFilepath($include_path."/indexation/authorities/collections/champs_base.xml");
	}
	
	protected function addSpecificsFilters($id, $filters =array()){
		$filters = parent::addSpecificsFilters($id, $filters);

		//Récupération du statut
		$query = "select num_statut from authorities where id_authority = ".$id." and type_object = ".$this->type;
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		$filters['status'] = $row->num_statut;
		return $filters;
	}
}