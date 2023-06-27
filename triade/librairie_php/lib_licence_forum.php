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



error_reporting(0);
include_once("../librairie_php/lib_emul_register.php");



if (empty($_SESSION["nom"]))  {
    print "<script type=\"text/javascript\">";
    print "location.href='./acces_refuse.php'";
    print "</script>";
}


include_once("../common/version.php");
include_once("../common/lib_admin.php");
include_once("../common/config8.inc.php");
include_once("../common/lib_ecole.php");
include_once("../librairie_php/langue_forum.php");
include_once("../librairie_php/lib_error_forum.php");
include_once("../common/productId.php");


//------------------------------------------------------------------------------
// construction de la license
// pour Internet explorer
if (preg_match('/msie/i', $_SERVER['HTTP_USER_AGENT']) && !preg_match('/opera/i', $_SERVER['HTTP_USER_AGENT'])) {
print "<div id='menu' class='fond' style='background-image:url(./image/commun/fond_inscrip.jpg);position:absolute;z-index:2;' >";
print "<div class='intitules' url='' align=left>";

print "<br /><img src='./image/commun/logo_triade_licence.gif'>";

print "        <br /><br />Version : <strong>".VERSION."</strong>";
print "        <br /> Tous droits réservés <br />";
print "                Licence d'utilisation : ".LICENCE."<br />";
print "                Product ID = <strong>".PRODUCTID ."</strong>";
print "        <br /><br />";
print "        <textarea cols=55 rows=3 STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'>";

droit();

print "</textarea>";
print "        <hr /><table summary=\"licence\" width=95%><tr><td align=left> <font size=2 >Triade©, 2000 - 2006 </font></td><td align=right><input type=button value='Fermer Fenêtre' onclick='masque_menu()' class='bouton'></td></tr></table>";

print "<br /></div></div>";

print "<script type=\"text/javascript\">";
print "document.getElementById('menu').style.visibility='hidden'";
print "</script>";
}


//------------------------------------------------------------------------------
// declaration de variables
include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../common/config3.inc.php");
include_once("../common/config4.inc.php");
//------------------------------------------------------------------------------



//----------------------------------------------------------------------------
function droit() {
print <<<EOF
Copyright (C) 2000-2004 TAESCH - TRACHET - ORY

Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes de la Licence Publique Générale GNU publiée par la Free Software Foundation.

Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE, ni explicite ni implicite, y compris les garanties de commercialisation ou d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU pour plus de détails.

Vous devez avoir reçu une copie de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.

EOF;
}

include_once("../librairie_php/mactu.php")

//----------------------------------------------------------------

?>
