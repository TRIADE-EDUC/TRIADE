<?php
session_start();
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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_compta.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_comptaSupp.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Echéanciers et versements" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
?>
<br />
<table border=0 align=center width="100%">
<tr>
<form action='comptaconfigclasse.php' method='post'>
<td align=right width=70%><font class="T2"><?php print "Echéanciers et versements par classe" ?> :</font></td>
<td align=left ><script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1 ?>","rien"); //text,nomInput</script>
<?php
$mess="Configuration des modalités de réglement à l\'ensemble d\'une classe.";
$information="Information";
if ((LAN == "oui") && (AGENTWEB == "oui")) {
	$information="Agent Web ".AGENTWEBPRENOM;
	$vocal="M5";
	$vocal=urlencode(stripHTMLtags($vocal));
	$http=protohttps();  // retourne https:// ou http://
	$mess="<iframe width=100 height=100 src=\'${http}www.triade-educ.com/agentweb/agentmel.php?inc=5&m=$vocal\'  MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no align=left ></iframe>".$mess ;
}
?>
<a href='#'  onMouseOver="AffBulle3('<?php print $information ?>','./image/commun/info.jpg','<?php print $mess ?>');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a>
</td>
</tr>
</form>
<tr><td height='20'></td></tr>
<tr>
<form action='comptaconfigmodele.php' method='post'>
<td align=right><font class="T2"><?php print "Configuration des modalités d'échéance" ?> :</font></td>
<td align=left ><script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1 ?>","rien"); //text,nomInput</script>
<?php
$mess="Configuration de modèle de modalité de réglement.";
$information="Information";
if ((LAN == "oui") && (AGENTWEB == "oui")) {
	$information="Agent Web ".AGENTWEBPRENOM;
	$vocal="M7";
	$vocal=urlencode(stripHTMLtags($vocal));
	$http=protohttps();  // retourne https:// ou http://
	$mess="<iframe width=100 height=100 src=\'${http}www.triade-educ.com/agentweb/agentmel.php?inc=5&m=$vocal\'  MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no align=left ></iframe>".$mess ;
}
?><a href='#'  onMouseOver="AffBulle3('<?php print $information ?>','./image/commun/info.jpg','<?php print $mess ?>');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a>
</td>
</form>
</tr>
<tr><td height='20'></td></tr>
<tr>
<form action='comptaconfigeleve0.php' method='post'>
<td align=right><font class="T2"><?php print "Echéanciers et versements par élève" ?> :</font></td>
<td align=left ><script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1 ?>","rien"); //text,nomInput</script>
<?php
$mess="Configuration des modalités de réglement pour un élève.";
$information="Information";
if ((LAN == "oui") && (AGENTWEB == "oui")) {
	$information="Agent Web ".AGENTWEBPRENOM;
	$vocal="M6";
	$vocal=urlencode(stripHTMLtags($vocal));
	$http=protohttps();  // retourne https:// ou http://
	$mess="<iframe width=100 height=100 src=\'${http}www.triade-educ.com/agentweb/agentmel.php?inc=5&m=$vocal\'  MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no align=left ></iframe>".$mess ;
}
?>
<a href='#'  onMouseOver="AffBulle3('<?php print $information ?>','./image/commun/info.jpg','<?php print $mess ?>');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a>
</td>
</tr>
</form>


</form>
<tr><td height='20'></td></tr>
<tr>
<form action='base_de_donne_key.php' method='post'>
<td align=right><font class="T2"><?php print "Supprimer tous les versements effectués" ?> :</font></td>
<td align=left ><script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1 ?>","suppversement"); //text,nomInput</script>
<?php
$mess="Suppression des versements. Ce module est à utiliser lors de votre changement d\'année scolaire.";
$information="ATTENTION";
 

if ((LAN == "oui") && (AGENTWEB == "oui")) {
	$information="Agent Web ".AGENTWEBPRENOM;
	$vocal="M7";
	$vocal=urlencode(stripHTMLtags($vocal));
	$http=protohttps();  // retourne https:// ou http://
//	$mess="<iframe width=100 height=100 src=\'${http}www.triade-educ.com/agentweb/agentmel.php?inc=5&m=$vocal\'  MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no align=left ></iframe>".$mess ;
}
?><a href='#'  onMouseOver="AffBulle3('<?php print $information ?>','./image/commun/warning.jpg','<?php print $mess ?>');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a>

</td>
</tr>
<input type=hidden name="modulepost" value="suppversement" />
</form>
</table>


<br /><br />
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;
?>
 <SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>  
   </BODY></HTML>
