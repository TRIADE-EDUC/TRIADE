<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: change_db.php,v 1.2 2016-08-25 15:19:58 jpermanne Exp $

// définition du minimum nécéssaire
$base_path     = ".";                            
$base_auth     = "";
$base_title    = "";    
$base_noheader = 1;
$base_nocheck  = 1;
$base_nobody   = 1;

require_once ("$base_path/includes/init.inc.php");

if ((in_array($selected_db,$_tableau_databases)) && (pmb_mysql_select_db($selected_db, $dbh))) {
	$pmb_nb_documents=(@pmb_mysql_result(pmb_mysql_query("select count(*) from notices",$dbh),0,0))*1;
	$pmb_opac_url=(@pmb_mysql_result(pmb_mysql_query("select valeur_param from parametres where type_param='pmb' and sstype_param='opac_url'",$dbh),0,0));
	$pmb_bdd_version=(@pmb_mysql_result(pmb_mysql_query("select valeur_param from parametres where type_param='pmb' and sstype_param='bdd_version'",$dbh),0,0));
	$pmb_login_message=(@pmb_mysql_result(pmb_mysql_query("select valeur_param from parametres where type_param='pmb' and sstype_param='login_message'",$dbh),0,0));
	echo json_encode(array(
			'bdd'=>$selected_db,
			'nb_docs'=>$pmb_nb_documents,
			'opac_url'=>$pmb_opac_url,
			'bdd_version'=>$pmb_bdd_version,
			'login_message'=>$pmb_login_message
	));
}