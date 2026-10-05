<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
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
//------------------------------------------------------------



if (file_exists("lib_error.php")) include_once("lib_error.php");
if (file_exists("lib_emul_register.php")) include_once("lib_emul_register.php");
if (file_exists("./common/version.php")) include_once("./common/version.php");
if (file_exists("./common/lib_admin.php")) include_once("./common/lib_admin.php");
if (file_exists("./common/lib_ecole.php")) include_once("./common/lib_ecole.php");
if (file_exists("./common/config2.inc.php")) include_once("./common/config2.inc.php");
if (file_exists("./common/config3.inc.php")) include_once("./common/config3.inc.php");
if (file_exists("./common/config4.inc.php")) include_once("./common/config4.inc.php");
if (file_exists("./common/config8.inc.php")) include_once("./common/config8.inc.php");
if (file_exists("./common/config-md5.php")) include_once("./common/config-md5.php");
if (file_exists("./common/productId.php")) include_once("./common/productId.php");
if (file_exists("./common/config-module.php")) include_once("./common/config-module.php");


include_once("lib_error.php");
include_once("licence_triade.php");

$VERSIONPATCH="";
$VERSIONMD5="";

if (file_exists("./common/lib_patch.php")){
	include_once('./common/lib_patch.php');
	if (defined('VERSIONPATCH')) $VERSIONPATCH=VERSIONPATCH;
	if (defined('VERSIONMD5')) $VERSIONPATCH=VERSIONMD5;
	$rev="<br />Rev : <strong>".$VERSIONPATCH."</strong>  - <i>".$VERSIONMD5."</i>";
}

if (file_exists("./common/lib_triade_interne.php")) {
        if (file_exists("../../../common/config-all-site.php")) {
                include_once("../../../common/config-all-site.php");
        }
}

if (file_exists("./common/config-fen.php")) include_once("./common/config-fen.php");


if (!defined('INTITULEDIRECTION')) { define("INTITULEDIRECTION","direction"); }
if (!defined('INTITULEELEVE')) { define("INTITULEELEVE","élève"); }
if (!defined('LARGEURFEN')) { define("LARGEURFEN","780"); }
if (!defined('INTITULECLASSE')) { define("INTITULECLASSE","classe"); }
if (!defined('INTITULEENSEIGNANT')) { define("INTITULEENSEIGNANT","enseignant"); }


//------------------------------------------------------------------------------
// declaration de variables
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
//------------------------------------------------------------------------------
function droit() {
	if (defined('DROITRIADE')) print DROITRIADE;
}


include_once("mactu.php");


print "<script type=\"text/javascript\" >";
print "var preinscription='".PREINSCRIPTION."';";
print "var largeurfen='".LARGEURFEN."';";
print "if (screen.width >= 800) { largeurfen='780'; }";
print "if (screen.width >= 1024) { largeurfen='900'; }";
print "if (screen.width >= 1200) { largeurfen='1000'; }";

print "var INTITULEDIRECTION='".ucfirst(INTITULEDIRECTION)."'; ";
print "var INTITULEELEVE='".ucfirst(TextNoAccent(INTITULEELEVE))."'; ";
print "var INTITULEENSEIGNANT='".ucfirst(TextNoAccent(INTITULEENSEIGNANT))."'; ";


print "var GRAPH='GRAPH'; ";
print "var banniere='BANNIEREDISPO';";

if (defined("GRAPH")) 		print "var GRAPH='".GRAPH."'; ";
if (defined("BANNIEREDISPO"))	print "var banniere='".BANNIEREDISPO."';";
if (defined("BANNIEREHAUTEUR"))	print "var bannierehauteur='".BANNIEREHAUTEUR."';";

if (defined('FOOTERSPECIAL')) { 
	print "var footer=\"".FOOTERSPECIAL."\";";
	print "var footerlien=\"".FOOTERLIEN."\";";
}else{
	print "var footer='';";
	print "var footerlien='';";
}
print "</script>";






?>
