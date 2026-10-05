<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_doc_data.php,v 1.8 2022/02/10 08:18:42 dgoron Exp $

$base_path=".";
require_once($base_path."/includes/init.inc.php");

global $css, $explnumdoc_id;

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

if ($css=="") $css=1;

// si paramétrage authentification particulière et pour la re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');
$explnumdoc_id = intval($explnumdoc_id);
$resultat = pmb_mysql_query("SELECT explnum_doc_nomfichier, explnum_doc_mimetype, explnum_doc_data, explnum_doc_extfichier
			FROM explnum_doc WHERE id_explnum_doc = '$explnumdoc_id' ");
$nb_res = pmb_mysql_num_rows($resultat) ;

if (!$nb_res) {
	exit ;
} 
	
$ligne = pmb_mysql_fetch_object($resultat);
if ($ligne->explnum_doc_data) {
	header("Content-Type: ".$ligne->explnum_doc_mimetype);
	header("Content-Length: ".$ligne->taille);
	print $ligne->explnum_doc_data;
	exit ;
} else print "ERROR".pmb_mysql_error() ;
?>