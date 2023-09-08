<?php
//----------------------------------------------------------------------------
// module pour affichage de la license
// pour Internet Explorer
include_once("../common/version.php");
include_once("../common/lib_admin.php");
include_once("../common/lib_ecole.php");
include_once("../common/config-md5.php");
include_once("../librairie_php/licence_triade.php");

if (file_exists("../common/lib_patch.php")){
//	include_once('../common/lib_patch.php');
	$rev="<br>Rev : <i>".VERSIONPATCH."</i>  - <i>".VERSIONMD5."</i>";
}


if (preg_match('/msie/', $_SERVER['HTTP_USER_AGENT']) && !preg_match('/opera/', $_SERVER['HTTP_USER_AGENT']))
{

print "<div id='menu' class='fond'  style='background-image:url(/".REPECOLE."/".REPADMIN."/image/fond_inscrip.jpg);position:absolute;z-index:2;'>";
print "<div class='intitules' url='' align=left>";

print "<BR><img src='/".REPECOLE."/".REPADMIN."/image/logo_triade_licence.gif'>";

print "        <BR><BR>Version : <b>".VERSION."</b>";
print 		$rev;
print "        <BR> Tous droits réservés <BR>";
print "                Licence d'utilisation accordée à : ADMINISTRATION<BR>";
print "                Product&nbsp;ID&nbsp;=&nbsp;<font class=T1>".PRODUCTID."</font>";
print "        <BR><BR>";
print "        <textarea cols=55 rows=3 STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'>";

droit();

print "</textarea>";
print "        <HR><table width=95%><tr><TD align=left> <font size=2 > T.R.I.A.D.E. © </FONT></TD><TD align=right><input type=button value='Fermer Fenêtre' onclick='masque_menu()' class='bouton2' ></TD></TR></table>";

print "<BR></div>";
print "</div>";
print "<script>";
print "document.getElementById('menu').style.visibility='hidden'";
print "</script>";
}
//----------------------------------------------------------------------------
function droit() {
	print DROITRIADE; 
}

define("PIEDPAGE","<p>La <b>T</b>ransparence et la <b>R</b>apidité de l'<b>I</b>nformatique <b>A</b>u service <b>D</b>e l'<b>E</b>nseignement<br> Pour visualiser ce site de façon optimale : résolution minimale 800x600 - T.R.I.A.D.E &copy;  - Tous droits réservés</p>");

?>
