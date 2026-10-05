<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: external.inc.php,v 1.19 2019/06/20 13:06:32 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/search.class.php");
require_once($class_path."/facettes_external.class.php");

global $external_sources, $field_0_s_2, $es, $ext_type;
if(isset($field_0_s_2) && is_array($field_0_s_2)) {
	$selected_sources = implode(',', $field_0_s_2);
}
global $reinit_facettes_external;
if($reinit_facettes_external) {
	facettes_external::destroy_global_env();
}
global $param_delete_facette, $check_facette;
if(($param_delete_facette) || ($check_facette && is_array($check_facette))) {
	facettes_external::checked_facette_search();
}

if ($ext_type=="multi") $_SESSION["ext_type"]="multi";

if (isset($_SESSION["ext_type"]) && $_SESSION["ext_type"]=="multi") {
	$es=new search("search_fields_unimarc");
	$es->remove_forbidden_fields();
} else {
	$es=new search("search_simple_fields_unimarc");
}
$es->show_results_unimarc("./index.php?lvl=more_results&mode=external","./index.php?search_type_asked=external_search&external_type=simple", true);

//Enregistrement des stats
global $pmb_logs_activate;
if($pmb_logs_activate){
    global $nb_results_tab, $count;
	$nb_results_tab['external'] = $count;
}