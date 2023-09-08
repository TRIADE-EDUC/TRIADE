<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_authors_indexer.class.php,v 1.4 2018-07-16 09:44:44 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once $class_path.'/sphinx/sphinx_authorities_indexer.class.php';

class sphinx_authors_indexer extends sphinx_authorities_indexer {
	
	public function __construct() {
		global $include_path;
		$this->type = AUT_TABLE_AUTHORS;
		$this->default_index = "authors";
		parent::__construct();
		$this->filters = ['status', 'author_type'];
		$this->setChampBaseFilepath($include_path."/indexation/authorities/authors/champs_base.xml");
	}
	
	protected function addSpecificsFilters($id, $filters =array()){
		$filters = parent::addSpecificsFilters($id, $filters);

		//Récupération du statut
		$query = "select author_type, num_statut from authors join authorities on author_id = num_object and type_object = ".$this->type." where id_authority = ".$id;
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		$filters['author_type'] = $row->author_type;
		$filters['status'] = $row->num_statut;
		return $filters;
	}
}