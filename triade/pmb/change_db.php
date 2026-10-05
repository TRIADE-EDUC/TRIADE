<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: change_db.php,v 1.4 2023/04/06 14:55:09 dbellamy Exp $

// définition du minimum nécéssaire
$base_path     = ".";                            
$base_auth     = "";
$base_title    = "";    
$base_noheader = 1;
$base_nocheck  = 1;
$base_nobody   = 1;

if(!empty($_POST['selected_db'])) {
    $database = $_POST['selected_db'];
}
require_once ("$base_path/includes/init.inc.php");

global $selected_db, $_tableau_databases;

if ((in_array($selected_db,$_tableau_databases)) && (pmb_mysql_select_db($selected_db))) {
	$pmb_nb_documents=intval(pmb_mysql_result(pmb_mysql_query("select count(*) from notices"),0,0));
	$pmb_opac_url=(pmb_mysql_result(pmb_mysql_query("select valeur_param from parametres where type_param='pmb' and sstype_param='opac_url'"),0,0));
	$pmb_bdd_version=(pmb_mysql_result(pmb_mysql_query("select valeur_param from parametres where type_param='pmb' and sstype_param='bdd_version'"),0,0));
	$pmb_login_message=(pmb_mysql_result(pmb_mysql_query("select valeur_param from parametres where type_param='pmb' and sstype_param='login_message'"),0,0));
	echo json_encode(array(
			'bdd'=>$selected_db,
			'nb_docs'=>$pmb_nb_documents,
			'opac_url'=>$pmb_opac_url,
			'bdd_version'=>$pmb_bdd_version,
			'login_message'=>$pmb_login_message
	));
}