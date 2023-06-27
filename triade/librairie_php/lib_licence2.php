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

if (file_exists("./common/lib_patch.php")){
	include_once('./common/lib_patch.php');
	$rev="<br />Rev : <strong>".VERSIONPATCH."</strong>  - <i>".VERSIONMD5."</i>";
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


// pour internet explorer
if (preg_match('/msie/i', $_SERVER['HTTP_USER_AGENT']) && !preg_match('/opera/i', $_SERVER['HTTP_USER_AGENT']))
{
print "<div id='menu' class='fond' style='background-image:url(./image/commun/fond_inscrip.jpg);position:absolute;z-index:2;' >";
print "<div class='intitules' url='' align=left>";
print "<br /><img src='./image/commun/logo_triade_licence.gif'alt='logo' />";
print "        <br /><br />Version : <strong>".VERSION."</strong>";
print "	       $rev";
print "        <br /> Tous droits réservés <br />";
print "                Licence d'utilisation : ".LICENCE."<br />";
print "                Product&nbsp;ID&nbsp;=&nbsp;<font class='T1'>".PRODUCTID."</font>";
print "        <br />";
print "        <textarea cols=55 rows=5 STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'>";
droit();
print "</textarea>";
print "        <hr /><table width=95%><tr><td align=left> <font size=2>Triade©, 2000 - ".date("Y")." </font></td><td align=right><input type=button value='Fermer Fenêtre' onclick='masque_menu()' class='bouton2'></td></tr></table>";

print "<br /></div></div>";
print "\n<script type=\"text/javascript\">";
print "document.getElementById('menu').style.visibility='hidden'";
print "</script>\n";
}

//------------------------------------------------------------------------------
// declaration de variables
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
//------------------------------------------------------------------------------
function droit() {
	print DROITRIADE;
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

print "var GRAPH='".GRAPH."'; ";

if (defined('FOOTERSPECIAL')) { 
	print "var footer=\"".FOOTERSPECIAL."\";";
	print "var footerlien=\"".FOOTERLIEN."\";";
}else{
	print "var footer='';";
	print "var footerlien='';";
}
print "</script>";






?>
