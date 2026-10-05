<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alert_see.inc.php,v 1.11 2024/04/16 06:55:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $lvl, $search_type, $id;
global $search;
global $es;

// fonctions de conversion simple2mc
require_once($include_path."/search_queries/specials/combine/search.class.php");

if($lvl == 'etagere_see') {
	// Gestion des alertes à partir d'une étagère
	$mc=combine_search::etagere2mc($id);
} else {
	// Gestion des alertes à partir de la recherche simple
	$mc=combine_search::simple2mc($_SESSION['last_query']);
}

global $field_0_s_4;
if(!is_array($field_0_s_4)) {
    $field_0_s_4 = array();
}
$field_0_s_4[]=serialize(array(
		'serialized_search' => $mc['serialized_search'],
		'search_type' => $mc['search_type']
));

unset($search);

global $search; //On redéclare $search en globale pour la suite
global $op_0_s_4;
$op_0_s_4="EQ";
global $inter_0_s_4;
$inter_0_s_4="";
$search=array();
$search[0]="s_4";

if (isset($_SESSION['opac_view']) && $_SESSION['opac_view']) {
    
    if (!isset($_SESSION['opac_view']) || $_SESSION['opac_view'] == "default_opac" || $_SESSION['opac_view'] == "default") {
        $id_opac = 0;
    } else {
        $id_opac = intval($_SESSION['opac_view']);
    }
    
    $query = "select opac_view_query from opac_views where opac_view_id = ".$id_opac;
	$result = pmb_mysql_query($query);

	if ($result && pmb_mysql_num_rows($result)) {
		$row = pmb_mysql_fetch_object($result);
		$serialized = $row->opac_view_query;
	}

	if (!empty($serialized)) {
		global $field_1_s_4;
		$field_1_s_4[]=serialize(array(
				'serialized_search' => $serialized,
				'search_type' => "search_fields"
		));
		global $op_1_s_4;
		$op_1_s_4="EQ";
		global $inter_1_s_4;
		$inter_1_s_4="and";
		$search[1]="s_4";
	}
}
$es = new search();
$alert_see_mc_values = $es->make_hidden_search_form("./index.php?lvl=more_results","mc_values","",true);