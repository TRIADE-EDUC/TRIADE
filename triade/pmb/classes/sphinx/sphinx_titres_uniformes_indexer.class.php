<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_titres_uniformes_indexer.class.php,v 1.6.4.1 2025/05/30 09:07:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once $class_path.'/sphinx/sphinx_indexer.class.php';

class sphinx_titres_uniformes_indexer extends sphinx_authorities_indexer
{
    
    public function __construct()
    {
        global $include_path;
        $this->type = 7;
        $this->default_index = "titres_uniformes";
        parent::__construct();
        $this->filters = ['multi' => ['oeuvre_nature', 'oeuvre_type', 'status']];
        $this->setChampBaseFilepath($include_path . "/indexation/authorities/titres_uniformes/champs_base.xml");
    }
    
    
    protected function addSpecificsFilters($id, $filters = array())
    {
        $filters = parent::addSpecificsFilters($id, $filters);
        
        //Recuperation du statut
        $query = "select tu_oeuvre_nature, num_statut, tu_oeuvre_type from titres_uniformes join authorities on tu_id = num_object and type_object = " . AUT_TABLE_TITRES_UNIFORMES .
        " where id_authority = " . $id;
        $result = pmb_mysql_query($query);
        $row = pmb_mysql_fetch_object($result);
        if (! array_key_exists('multi', $filters)) {
            $filters['multi'] = array();
        }
        if (is_object($row)) {
            $filters['multi']['oeuvre_nature'] = $row->tu_oeuvre_nature;
            $filters['multi']['oeuvre_type'] = $row->tu_oeuvre_type;
            $filters['multi']['status'] = $row->num_statut;
        }
        return $filters;
    }
}