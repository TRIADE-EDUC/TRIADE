<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// | creator : PMB Services                                                   |
// +-------------------------------------------------+
// $Id: tache_docnum.php,v 1.7 2018/02/26 17:01:59 apetithomme Exp $

// définition du minimum nécéssaire 
$base_path     = ".";                            
$base_auth     = ADMINISTRATION_AUTH; //"CIRCULATION_AUTH";  
$base_title    = "";    
$base_noheader = 1;
$base_nobody   = 1;


require_once ("$base_path/includes/init.inc.php");  
require_once ("$include_path/explnum.inc.php");  
require_once ($class_path."/upload_folder.class.php"); 

$resultat = pmb_mysql_query("SELECT id_tache_docnum, tache_docnum_nomfichier, tache_docnum_mimetype, tache_docnum_data, tache_docnum_extfichier, 
			tache_docnum_repertoire, tache_docnum_path, concat(repertoire_path,tache_docnum_path,tache_docnum_nomfichier) as path
			FROM taches_docnum left join upload_repertoire on repertoire_id=tache_docnum_repertoire WHERE id_tache_docnum = '$tache_docnum_id' ", $dbh);

$nb_res = pmb_mysql_num_rows($resultat) ;

if (!$nb_res) {
	header("Location: images/mimetype/unknown.gif");
	exit ;
	} 
	
$ligne = pmb_mysql_fetch_object($resultat);

if (($ligne->tache_docnum_data)||($ligne->tache_docnum_path)) {
	if ($ligne->tache_docnum_path) {
		$up = new upload_folder($ligne->tache_docnum_repertoire);
		$path = $up->repertoire_path.$ligne->tache_docnum_path.$ligne->tache_docnum_nomfichier.".".$ligne->tache_docnum_extfichier;
		$path = str_replace("//","/",$path);
		$path=$up->encoder_chaine($path);
		$fo = fopen($path,'rb');
		$ligne->tache_docnum_data=fread($fo,filesize($path));
		fclose($fo);
	}
	
	create_tableau_mimetype() ;
	$name=$_mimetypes_bymimetype_[$ligne->tache_docnum_mimetype]["plugin"] ;
	if ($name) {
		$type = "" ;
		// width='700' height='525' 
		$name = " name='$name' ";
	} else $type="type='$ligne->tache_docnum_mimetype'" ;
	if ($_mimetypes_bymimetype_[$ligne->tache_docnum_mimetype]["embeded"]=="yes") {
		print "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body><EMBED src=\"./doc_num_data.php?explnum_id=$explnum_id\" $type $name controls='console' ></EMBED></body></html>" ;
		exit ;
	}
	
	$nomfichier="";
	if ($ligne->tache_docnum_nomfichier) {
		$nomfichier=$ligne->tache_docnum_nomfichier;
	}
	elseif ($ligne->tache_docnum_extfichier)
		$nomfichier="pmb".$ligne->id_tache_docnum.".".$ligne->tache_docnum_extfichier;
	if ($nomfichier) header('Content-Disposition: inline; filename="'.$nomfichier.'"');
	
	header("Content-Type: ".$ligne->tache_docnum_mimetype);
	print $ligne->tache_docnum_data;
	exit ;
}
	
if ($ligne->tache_docnum_mimetype=="URL") {
	if ($ligne->tache_docnum_url) header("Location: $ligne->tache_docnum_url");
	exit ;
}

//if($ligne->explnum_path){
//	$up = new upload_folder($ligne->repertoire_id);
//	$path = str_replace("//","/",$ligne->path);
//	$path=$up->encoder_chaine($path);
//	$fo = fopen($path,'rb');
//	header("Content-Type: ".$ligne->explnum_mimetype);
//	fpassthru($fo);
//	exit;
	
//}
