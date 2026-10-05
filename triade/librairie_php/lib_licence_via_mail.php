<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
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



include_once("./lib_emul_register.php");


include_once("./common/version.php");
include_once("./common/lib_admin.php");
include_once("./common/lib_ecole.php");
include_once("./common/config2.inc.php");
include_once("./common/config8.inc.php");
include_once("timezone.php");
include_once("langue.php");
include_once("lib_error.php");
include_once("./common/productId.php");
//include_once("./sound/lib_sound.php"); sonore_action();


// -----------------------------------------------------------------------------
// -----------------------------------------------------------------------------
// syxtaxe d'utilisation
// verifplus("menuadmin",$_SESSION[id_pers],$_SESSION['membre']);
// verifplus("menuparent",$_SESSION[id_pers],$_SESSION['membre']);
// verifplus("menuprof",$_SESSION[id_pers],$_SESSION['membre']);
// verifplus("menuscolaire",$_SESSION[id_pers],$_SESSION['membre']);
// verifplus("menudeux",$_SESSION[id_pers],$_SESSION['membre']);
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
	    	print "location.href='./blacklist.php'";
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




//------------------------------------------------------------------------------
// declaration de variables
include_once("./common/config.inc.php");
//------------------------------------------------------------------------------

//----------------------------------------------------------------------------
function droit() {
print <<<EOF
Copyright (C) 2000-2007 S.A.R.L. - T.R.I.A.D.E.

Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes de la Licence Publique Générale GNU publiée par la Free Software Foundation.

Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE, ni explicite ni implicite, y compris les garanties de commercialisation ou d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU pour plus de détails.

Vous devez avoir reçu une copie de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.

EOF;
}

include_once("mactu.php")

print "var versiontriade='".VERSION."'; ";
print "var versionpatch='".VERSIONPATCH."'; ";
print "var productId='".PRODUCTID."'; ";

//----------------------------------------------------------------

?>
