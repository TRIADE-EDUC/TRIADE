<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_indexint_indexer.class.php,v 1.5.4.1 2025/05/30 09:07:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once $class_path . '/sphinx/sphinx_indexer.class.php';

class sphinx_indexint_indexer extends sphinx_authorities_indexer
{
    
    public function __construct()
    {
        global $include_path;
        $this->type = AUT_TABLE_INDEXINT;
        $this->default_index = "indexint";
        parent::__construct();
        $this->filters = ['multi' => ['status', 'num_pclass']];
        $this->setChampBaseFilepath($include_path . "/indexation/authorities/indexint/champs_base.xml");
    }
    
    
    protected function addSpecificsFilters($id, $filters = array())
    {
        $filters = parent::addSpecificsFilters($id, $filters);
        
        //Recuperation du statut
        $query = "select num_pclass, num_statut from indexint join authorities on indexint_id = num_object and type_object = " . $this->type . " where id_authority = " . $id;
        $result = pmb_mysql_query($query);
        $row = pmb_mysql_fetch_object($result);
        if (! array_key_exists('multi', $filters)) {
            $filters['multi'] = array();
        }
        if (is_object($row)) {
            $filters['multi']['num_pclass'] = $row->num_pclass;
            $filters['multi']['status'] = $row->num_statut;
        }
        return $filters;
    }
}