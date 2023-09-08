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
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_discipline.js"></script>
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
<?php//-----------------------------------------------------------------------?>
<?php//-----------------------------------------------------------------------?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGDISC58 ?> </font></b></td>
</tr><tr  id='cadreCentral0' >
<td >
<!-- // fin  -->
<BR>
<table border=0 width=100%><tr><td colspan=2>
<?php
include_once('librairie_php/db_triade.php');
validerequete("2");
$cnx=cnx();
include_once("./librairie_php/ajax.php");
ajax_js();
// ------------------------------------------
?>

</td></tr>
<tr><td align=right>
<FORM name=formulaire onsubmit='return valide_consul_classe()' method=post action='gestion_discipline_ajout.php'>
<font class="T2"><?php print LANGELE4 ?> :</font> <Select name='saisie_classe' >
                          <option value=0 STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
<?php
select_classe2(20);
Pgclose();
?>
</select></td><td align=left><table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGDISC38 ?>","rien"); //text,nomInput</script>
</td></tr></table>
</td></tr></table>
<?php brmozilla($_SESSION["navigateur"]); ?>
</UL></td></tr></TABLE>
</form>
<?php//-----------------------------------------------------------------------?>
<BR>
<?php//-----------------------------------------------------------------------?>
<form method=post name="formulaire_1">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGDISC39 ?></font></b></td></tr>
<tr  id='cadreCentral0' >
<td>
<center>
<table border=0><tr><td align="right">
	<font class="T2"><?php print LANGDISC40 ?> :</font>
	</td><td align=left><table align=center><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGBT28?>","gestion_discipline_retenue_non_fait.php","_parent","","")</script>
<script language=JavaScript> buttonMagic("<?php print Courrier?>","liste_retenu_impr.php","_parent","","")</script>&nbsp;&nbsp;
</td></tr></table>
</td></tr>
<tr><td align="right">
<font class="T2"><?php print LANGDISC41 ?> :</font>
</td><td align=left><table><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGBT28?>","gestion_discipline_calendrier.php","_parent","","")</script>
</td></tr></table>
<tr><td align="right">
<font class="T2"><?php print LANGDISC42 ?> :</font>
</td><td align=left><table><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGBT28?>","gestion_discipline_non_aff.php","_parent","","")</script>
</td></tr></table>

<tr><td align="right">
<font class="T2"><?php print LANGDISC44 ?> :</font>
</td><td align=left><table><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGBT28?>","gestion_discipline_supprimer.php","_parent","","")</script>
</td></tr></table>

<tr><td align="right">
<font class="T2"><?php print CUMUL03 ?> :</font>
</td><td align=left><table><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGBT28?>","cumul_disci_classe.php","_parent","","")</script>
</td></tr></table>
<tr><td align="right">
<font class="T2"><?php print LANGDISC43 ?> :</font>
</td><td align=left><table><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGBT28?>","gestion_discipline_config.php","_parent","","")</script>
</td></tr></table>


</td></TR></TABLE>

</center>
</td></TR></TABLE>
</form>
<?php//-----------------------------------------------------------------------?>
<BR>
<?php//-----------------------------------------------------------------------?>
<form method=post onsubmit="return valide_recherche_eleve_2()" action="gestion_discipline_modif.php" name="formulaire_2">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGDISC54 ?> </font></b></td>
     </tr>
     <tr  id='cadreCentral0' >
     <td >
<blockquote><BR>
<table border=0 cellspacing=0><tr><td style="padding-top:0px;" nowrap>
<font class="T2"><?php print LANGABS3?> : </font><input type="text" name="saisie_nom_eleve" size="20" id="search" autocomplete="off" onkeyup="searchRequest(this,'eleve','target0','formulaire_2','saisie_nom_eleve')"   style="width:15em" />
</td></tr><tr><td style="padding-top:0px;"><div id="target0" style="width:13.5em" ></div></td></tr>
</table>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","rien"); //text,nomInput</script>
</UL></UL></UL>
 </blockquote>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
    </td></TR>
</TABLE>
</form>

<?php//-----------------------------------------------------------------------?>
<br>
<?php//-----------------------------------------------------------------------?>
<form method=post onsubmit="return valide_recherche_eleve_4()" action="gestion_discipline_modif_sanc.php" name="formulaire_4">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Modifier les disciplines d'un éléve " ?> </font></b></td>
     </tr>
     <tr  id='cadreCentral0' >
     <td >
<blockquote><BR>
<table border=0 cellspacing=0><tr><td style="padding-top:0px;" nowrap>
<font class="T2"><?php print LANGABS3?> : </font><input type="text" name="saisie_nom_eleve" size="20" id="search" autocomplete="off" onkeyup="searchRequest(this,'eleve','target4','formulaire_4','saisie_nom_eleve')"   style="width:15em" />
</td></tr><tr><td style="padding-top:0px;"><div id="target4" style="width:13.5em" ></div></td></tr>
</table>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","rien"); //text,nomInput</script>
</UL></UL></UL>
 </blockquote>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
    </td></TR>
</TABLE>
</form>

<?php//-----------------------------------------------------------------------?>
<BR>
<?php//-----------------------------------------------------------------------?>
<form method=post onsubmit="return valide_recherche_eleve_3()" action="gestion_discipline_supp.php" name="formulaire_3">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS47 ?> </font></b></td></tr>
<tr  id='cadreCentral0' >
<td >
<blockquote><BR>
<table border=0 cellspacing=0><tr><td style="padding-top:0px;" nowrap>
<font class="T2"><?php print LANGABS3?> : </font><input type="text" name="saisie_nom_eleve" size="20" id="search" autocomplete="off" onkeyup="searchRequest(this,'eleve','target1','formulaire_3','saisie_nom_eleve')"   style="width:15em" />
</td></tr><tr><td style="padding-top:0px;"><div id="target1" style="width:13.5em" ></div></td></tr>
</table>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGDISC56 ?>","rien"); //text,nomInput</script>
</UL></UL></UL>
 </blockquote>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
    </td></TR>
</form>
<?php//-----------------------------------------------------------------------?>
<?php//-----------------------------------------------------------------------?>
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
<?php include_once("./librairie_php/finbody.php"); ?>
   </BODY></HTML>
