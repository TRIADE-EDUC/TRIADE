<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
//------------------------------------------------------------------------------
// section pour un acces non autorise




include_once("./librairie_php/lib_emul_register.php");

if (  (empty($_SESSION["nom"])) && (empty($_SESSION["membre"])) )  {
    header("location: ./acces_refuse.php");
    exit;
}

if (file_exists("./common/lib_triade_interne.php")) {
        if (file_exists("../../../common/config-all-site.php")) {
                include_once("../../../common/config-all-site.php");
        }
}



if (file_exists("./common/version.php")) include_once("./common/version.php");
if (file_exists("./common/lib_admin.php")) include_once("./common/lib_admin.php");
if (file_exists("./common/lib_ecole.php")) include_once("./common/lib_ecole.php");
if (file_exists("./common/config2.inc.php")) include_once("./common/config2.inc.php");
if (file_exists("./common/config3.inc.php")) include_once("./common/config3.inc.php");
if (file_exists("./common/config4.inc.php")) include_once("./common/config4.inc.php");
if (file_exists("./common/config8.inc.php")) include_once("./common/config8.inc.php");
if (file_exists("./librairie_php/timezone.php")) include_once("./librairie_php/timezone.php");
if (file_exists("./librairie_php/langue.php")) include_once("./librairie_php/langue.php");
if (file_exists("./librairie_php/lib_error.php")) include_once("./librairie_php/lib_error.php");
if (file_exists("./common/productId.php")) include_once("./common/productId.php");

// -----------------------------------------------------------------------------
// syxtaxe d'utilisation
// verifplus("menuadmin",$_SESSION[id_pers],$_SESSION[membre]);
// verifplus("menuparent",$_SESSION[id_pers],$_SESSION[membre]);
// verifplus("menuprof",$_SESSION[id_pers],$_SESSION[membre]);
// verifplus("menuscolaire",$_SESSION[id_pers],$_SESSION[membre]);
// verifplus("menudeux",$_SESSION[id_pers],$_SESSION[membre]);
// scolaire et admin
function verifplus($verifplus,$idpers,$idmembre) {
	if ($verifplus == "menudeux") {
		if (($idmembre == "menuadmin") || ($idmembre == "menuscolaire")) {
			$blackliste=0;
		}else{
			$blackliste=1;
		}
	}else {
		if ($idmembre != $verifplus) {
			$blackliste=1;
		}else{
			$blackliste=0;
		}
	}

	if ($blackliste == 1) {
    		print "<script type=\"text/javascript\">";
	    	print "location.href=\"./blacklist.php\"";
 		print "</script>";
		exit;
	}
}


// -----------------------------------------------------------------------------

function testadminplus() {
	if (empty($_SESSION["adminplus"])) {
	    print "<script type=\"text/javascript\">";
	    print "location.href=\"./affectation_creation_key.php\"";
	    print "</script>";
	    exit;
	}
}


//  brmozilla($_SESSION[navigateur]);
function brmozilla($navig) {
	if ($navig == "NONIE") {
		print "<br />";
	}
}

if (file_exists("./common/lib_patch.php")){
	include_once('./common/lib_patch.php');
	$rev="<br>Rev : <em>".VERSIONPATCH."</em>";
}

//------------------------------------------------------------------------------
// declaration de variables
include_once("./common/config.inc.php");
if (defined('FOOTERSPECIAL')) { $footer=FOOTERSPECIAL; }else{ $footer=''; }
//------------------------------------------------------------------------------
if  (preg_match('/http/',FORUM)) {
	print "<script type=\"text/javascript\"> var forum='".FORUM."'; forumtarget='target=_blank' </script>";
}else{
	print "<script type=\"text/javascript\"> var forum='forum.php'; forumtarget='' </script>";
}


include_once("mactu.php");

//----------------------------------------------------------------

?>
