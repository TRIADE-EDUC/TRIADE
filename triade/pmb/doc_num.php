<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// | creator : Yves PRATTER                                                   |
// +-------------------------------------------------+
// $Id: doc_num.php,v 1.33 2023/01/03 15:10:50 dgoron Exp $

use Pmb\Digitalsignature\Models\DocnumCertifier;

// définition du minimum nécéssaire 
$base_path     = ".";                            
$base_auth     = ""; //"CIRCULATION_AUTH";  
$base_title    = "";    
$base_noheader = 1;
// $base_nocheck  = 1;
$base_nobody   = 1;
$base_nosession   = 1;

global $class_path, $include_path, $msg, $charset, $explnum_id;
global $gestion_acces_active, $gestion_acces_user_notice, $PMBuserid;
global $rights, $dom_1, $pmb_digital_signature_activate, $get_sign;

require_once ("$base_path/includes/init.inc.php");
require_once ("$include_path/explnum.inc.php");  
require_once ($class_path."/explnum.class.php"); 

//gestion des droits
require_once($class_path."/acces.class.php");

$explnum_id = intval($explnum_id);
$explnum = new explnum($explnum_id);

if (!$explnum->explnum_id) {
	header("Location: images/mimetype/unknown.gif");
	exit ;
}

$id_for_rigths = $explnum->explnum_notice;
if($explnum->explnum_bulletin != 0){
	//si bulletin, les droits sont rattachés à la notice du bulletin, à défaut du pério...
	$req = "select bulletin_notice,num_notice from bulletins where bulletin_id =".$explnum->explnum_bulletin;
	$res = pmb_mysql_query($req);
	if(pmb_mysql_num_rows($res)){
		$row = pmb_mysql_fetch_object($res);
		$id_for_rigths = $row->num_notice;
		if(!$id_for_rigths){
			$id_for_rigths = $row->bulletin_notice;
		}
	}
}

//droits d'acces utilisateur/notice
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	$rights = $dom_1->getRights($PMBuserid,$id_for_rigths);
} else {
	$dom_1=null;
	$rights = 0;
}

if( $rights & 4 || (is_null($dom_1))){
    if($pmb_digital_signature_activate && $get_sign) {
        $certifier = new DocnumCertifier($explnum);
        if($certifier->checkSignExists()) {
            $path = $certifier->getCmsFilePath();
            header("Content-Disposition: attachment; filename=sign_docnum_" . $explnum->explnum_id . ".cms");
            print file_get_contents($path);
            exit;
        }
    }
	if (!($file_loc = $explnum->get_is_file())) {
		$content = $explnum->get_file_content();
	} else {
		$content = '';
	}
	if($file_loc || $content ) {
		create_tableau_mimetype();
		$name=$_mimetypes_bymimetype_[$explnum->explnum_mimetype]["plugin"] ;
		if ($name) {
			// width='700' height='525' 
			$name = " name='$name' ";
		}
		$type="type='".$explnum->explnum_mimetype."'" ;
		if ($_mimetypes_bymimetype_[$explnum->explnum_mimetype]["embeded"]=="yes") {
			print "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body><EMBED src=\"./doc_num_data.php?explnum_id=$explnum_id\" $type $name controls='console' ></EMBED></body></html>" ;
			exit ;
		}

		$file_name = $explnum->get_file_name();
		session_write_close();
		pmb_mysql_close();		
		if ($file_name) header('Content-Disposition: inline; filename="'.$file_name.'"');
		if($explnum->explnum_mimetype == 'text/html') {
			header("Content-Type: ".$explnum->explnum_mimetype." charset=".$charset);
		} else {
			header("Content-Type: ".$explnum->explnum_mimetype);
		}
		if($content){
			print $content;
		}elseif($file_loc){
			readfile($file_loc);
		}
		exit;
	}
	if ($explnum->explnum_mimetype=="URL") {
		if ($explnum->explnum_url) header("Location: ".$explnum->explnum_url);
		exit ;
	}
}else{
	print $msg["forbidden_docnum"];
}