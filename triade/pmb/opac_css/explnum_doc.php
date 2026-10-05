<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_doc.php,v 1.11 2022/01/05 15:25:28 dgoron Exp $

$base_path=".";
require_once($base_path."/includes/init.inc.php");

global $explnumdoc_id, $_mimetypes_bymimetype_, $css, $charset;

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

if ($css=="") $css=1;

require_once ("./includes/explnum.inc.php");  

// si paramétrage authentification particulière et pour la re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');
$explnumdoc_id = intval($explnumdoc_id);
$resultat = pmb_mysql_query("SELECT * FROM explnum_doc WHERE id_explnum_doc = '$explnumdoc_id' ");
$nb_res = pmb_mysql_num_rows($resultat) ;

if (!$nb_res) {
	header("Location: images/mimetype/unknown.gif");
	exit ;
	} 
	
$ligne = pmb_mysql_fetch_object($resultat);
if ($ligne->explnum_doc_data) {
	create_tableau_mimetype() ;
	$name=$_mimetypes_bymimetype_[$ligne->explnum_mimetype]["plugin"] ;
	if ($name) {
		$type = "" ;
		// width='700' height='525' 
		$name = " name='$name' ";
	} else $type="type='$ligne->explnum_mimetype'" ;
	if ($_mimetypes_bymimetype_[$ligne->explnum_mimetype]["embeded"]=="yes") {
		print "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body><EMBED src=\"./explnum_doc_data.php?explnumdoc_id=$explnumdoc_id\" $type $name controls='console' ></EMBED></body></html>" ;
		exit ;
	}
	
	$nomfichier="";
	if ($ligne->explnum_doc_nomfichier) {
		$nomfichier=$ligne->explnum_doc_nomfichier;
	}
	elseif ($ligne->explnum_doc_extfichier)
		$nomfichier="pmb".$ligne->explnum_id.".".$ligne->explnum_doc_extfichier;
	if ($nomfichier) header('Content-Disposition: inline; filename="'.$nomfichier.'"');
	
	header("Content-Type: ".$ligne->explnum_doc_mimetype);
	print $ligne->explnum_doc_data;
	exit ;
}
if ($ligne->explnum_doc_mimetype=="URL") {
	if($ligne->explnum_doc_url){
		header("Location: $ligne->explnum_doc_url");
	}
	exit ;
}
	
?>