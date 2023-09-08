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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type='text/javascript' src="./librairie_php/server.php?client=Util,main,dispatcher,httpclient,request,json,loading,iframe"></script>
<script type='text/javascript' src="./librairie_php/auto_server.php?client=all&stub=livesearch"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGTITRE24?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
     <!-- // fin  -->
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
include_once("./librairie_php/ajax.php");
ajax_js();
?>
<?php//-----------------------------------------------------------------------?>
<form method=post onsubmit="return valide_recherche_eleve()" action="gestion_dispence_suite.php" name="formulaire">
<blockquote><BR>
<table border=0 cellspacing=0><tr><td style="padding-top:0px;" nowrap><font class=T2>
<?php print LANGABS3?> :</font> <input type="text" name="saisie_nom_eleve" size="20" id="search" autocomplete="off" onkeyup="searchRequest(this,'eleve','target0','formulaire','saisie_nom_eleve')"   style="width:15em" />
</td></tr><tr><td style="padding-top:0px;"><div id="target0" style="width:13.5em" ></div></td></tr>
</table>
<UL><UL><UL><script language=JavaScript>buttonMagicSubmit("<?php print LANGDISP20?>","rien"); //text,nomInput</script>
</UL></UL></UL>
 </blockquote>
</form>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
</UL>
<!-- // fin  -->
</table>
<BR><BR>
<?php//-----------------------------------------------------------------------?>
<?php//-----------------------------------------------------------------------?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGTITRE25?></font></b></td>
</tr>
<tr  id='cadreCentral0' >
<td >
     <!-- // fin  -->
<form method=post onsubmit="return valide_recherche_eleve_1()" action="gestion_dispence_modif.php" name="formulaire_1">
<blockquote><BR>
<table border=0 cellspacing=0><tr><td style="padding-top:0px;" nowrap><font class=T2>
<?php print LANGABS3?> :</font> <input type="text" name="saisie_nom_eleve" size="20" id="search" autocomplete="off" onkeyup="searchRequest(this,'eleve','target1','formulaire_1','saisie_nom_eleve')"   style="width:15em" />
</td></tr><tr><td style="padding-top:0px;"><div id="target1" style="width:13.5em" ></div></td></tr>
</table>
<UL><UL><UL><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT33?>","rien"); //text,nomInput</script>
</UL></UL></UL>
 </blockquote>
</form>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
</UL>
</table><BR><BR>
<?php//-----------------------------------------------------------------------?>
<?php//-----------------------------------------------------------------------?>
<!-- // fin  -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGTITRE26?> </font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
     <!-- // fin  -->
<form method=post onsubmit="return valide_recherche_eleve_2()" action="gestion_dispence_supp.php" name="formulaire_2">
<blockquote><BR>
<table border=0 cellspacing=0><tr><td style="padding-top:0px;" nowrap><font class=T2>
<?php print LANGABS3?> :</font> <input type="text" name="saisie_nom_eleve" size="20" id="search" autocomplete="off" onkeyup="searchRequest(this,'eleve','target2','formulaire_2','saisie_nom_eleve')"   style="width:15em" />
</td></tr><tr><td style="padding-top:0px;"><div id="target2" style="width:13.5em" ></div></td></tr>
</table><UL><UL><UL><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT32?>","rien"); //text,nomInput</script>
</UL></UL></UL>
 </blockquote>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
</form>
<?php//-----------------------------------------------------------------------?>
<?php//-----------------------------------------------------------------------?>
</UL>
<!-- // fin  -->
</td></tr></table>
     <?php
      // Test du membre pour savoir quel fichier JS je dois executer
      if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
      print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
      print "</SCRIPT>";
      else :
      print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
      print "</SCRIPT>";

      top_d();

      print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
      print "</SCRIPT>";
      endif ;
     ?>
   </BODY></HTML>
