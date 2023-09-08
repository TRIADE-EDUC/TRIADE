<?php
session_start();
if (isset($_COOKIE["anneeScolaire"])) $anneeScolaire=$_COOKIE["anneeScolaire"];
include_once("./librairie_php/verifEmailEnregistre.php");
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGABS1?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<BR><UL>

<table border="0" height="120">
<form name='formulaire00'  method='post' action='gestion_abs_retard_codebar.php'>
<tr><td align="left">
<font class="T2"><?php print LANGMESS433 ?> : </font>
</td><td valign=bottom>
<table align=center><tr><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT27bis ?>","rien"); //text,nomInput</script></td></tr></table>
</td></tr>
</form>
<tr><td height=20></td></tr>
<FORM name='formulaire' onsubmit='return valide_consul_classe()' method='post' action='gestion_abs_retard_suite.php'>
<tr><td align="left">
<font class="T2"><?php print ucwords(LANGIMP10)?> : </font><br><br> <Select name='saisie_classe' >
<option value=0 STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
include_once('librairie_php/db_triade.php');

if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
	$profpclasse=$_SESSION["profpclasse"];
	validerequete("menuprof");
}else{
	validerequete("2");
}

$cnx=cnx();
include_once("./librairie_php/ajax.php");
ajax_js();
select_classe2(25);
?>
</select>
</td><td valign=bottom>
<table align=center><tr><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT27bis?>","class"); //text,nomInput</script></td></tr></table>
</td></tr>
</form>

<FORM name=formulaire_5 onsubmit='return valide_consul_classe2()' method="post" action='gestion_abs_retard_suite.php'>
<tr><td align="left"><br><br>
<font class="T2"><?php print ucwords(LANGPROF4)?> : </font><br><br> <Select name='saisie_groupe' >
<option value=0 STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
select_groupe_id();
?>
</select>
</td><td valign=bottom>
<table align=center><tr><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT27bis?>","grp"); //text,nomInput</script></td></tr></table>
</td></tr>
</form>


<form name=formulaire_55 onsubmit='return valide_consul_classe22()' method="post" action='gestion_abs_retard_suite.php'>
<tr><td align="left"><br><br>
<font class="T2"><?php print ucwords(LANGGRP62)?> : </font><br><br> <Select name='saisie_etude' >
<option value=0 STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
select_etude();
?>
</select>
</td><td valign=bottom>
<table align=center><tr><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT27bis?>","etude"); //text,nomInput</script></td></tr></table>
</td></tr>
</form>
</table>

<table>
<FORM name=formulaire_56  method="post" action='gestion_abs_present.php'>
<tr><td align="left"><br><br>
<font class="T2"><?php print LANGMESS434 ?> : </font>
</td><td valign=bottom>
<table align=center><tr><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","rien"); //text,nomInput</script></td></tr></table>
</td></tr>
</form>

</table>
<?php PgClose(); ?>

<?php brmozilla($_SESSION["navigateur"]); ?>
</UL></td></tr></TABLE>
<?php//-----------------------------------------------------------------------?>
<BR><br>
<?php//-----------------------------------------------------------------------?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGABS4bis?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<BR>
<table width="100%" align="center" border="0">
<tr>
<?php
if ((LAN == "oui") && (file_exists("./common/config-sms.php"))) {
	$disabled="";
	$textdisabled="";
}else{
	$disabled="disabled";
	$textdisabled=" / Non Abonn&eacute;";
}
?>
<td width=50% align=right>
<font class="T2"><?php print "Listing absences" ?> :</font>
</td><td align=left><table width="100%"><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGBT28?>","liste_abs.php","_parent","","")</script>
<script language=JavaScript> buttonMagic("<?php print "Envoi Mail" ?>","liste_abs_impr.php","_parent","","")</script>
<form method=post action="sms-abs.php"><script language=JavaScript> buttonMagicSubmit3("<?php print "Envoi SMS $textdisabled "?>","sms","<?php print $disabled?>")</script></form></td></tr></table>
</td></tr>

<tr><td align=right>
<font class="T2"><?php print "Listing retards" ?> :</font>
</td><td align=left><table width="100%"><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGBT28?>","liste_rtd.php","_parent","","")</script>
<script language=JavaScript> buttonMagic("<?php print "Envoi Mail" ?>","liste_rtd_impr.php","_parent","","")</script>
<form method=post action="sms-rtd.php"><script language=JavaScript> buttonMagicSubmit3("<?php print "Envoi SMS $textdisabled "?>","sms","<?php print $disabled?>")</script></form>
</td></tr></table>
</td></tr>

<tr><td align=right>
<font class="T2"><?php print LANGABS68 ?> :</font>
</td><td align=left><table><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGBT28?>","liste_abs_rtd_classe.php","_parent","","")</script>
</td></tr></table>
</td></tr>

<tr><td align=right>
<font class="T2"><?php print LANGMESS436  ?> :</font>
</td><td align=left><table><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGBT28?>","liste_abs_rtd_aucun.php","_parent","","")</script>
</td></tr></table>
</td></tr>

<tr><td align=right>
<font class="T2"><?php print LANGTMESS488 ?> :</font>
</td><td align=left><table><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGBT28?>","liste_rattrapage.php","_parent","","")</script>
<script language=JavaScript> buttonMagic("<?php print "Export rattrapage" ?>","export_rattrapage.php","_parent","","")</script>&nbsp;&nbsp;
</td></tr></table>
</td></tr>

<tr><td align=right>
<font class="T2"><?php print LANGMESS437 ?> :</font>
</td><td align=left><table><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGBT28?>","releve_abs_rtd_classe.php","_parent","","")</script>
</td></tr></table>
</td></tr>

<tr><td align=right>
<font class="T2"><?php print LANGMESS438 ?> :</font>
</td><td align=left><table><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGBT28?>","releve_abs_rtd_semaine_classe.php","_parent","","")</script>
&nbsp;&nbsp;
</td></tr></table>
</td></tr>

<tr><td align=right>
<font class="T2"><?php print LANGMESS439 ?> :</font>
</td><td align=left><table><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGBT28?>","impr_abs_rtd_eleve.php","_parent","","")</script>
&nbsp;&nbsp;
</td></tr></table>
</td></tr>



<tr><td align=right>
<font class="T2"><?php print LANGMESS440 ?> :</font>
</td><td align=left><table><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGBT28?>","listePresent.php","_parent","","")</script>
&nbsp;&nbsp;
</td></tr></table>
</td></tr>


<?php if ($_SESSION['membre'] != "menuprof")  { ?>
<tr><td align=right>
<font class="T2"><?php print LANGMESS441 ?> :</font>
</td><td align=left><table><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGBT28?>","gestion_abs_sconet.php","_parent","","")</script>
<script language=JavaScript> buttonMagic("Import",'base_de_donne_importation700.php','_parent','','')</script>
&nbsp;&nbsp;
</td></tr></table>
</td></tr>
<?php } ?>

<tr><td align=right>
<font class="T2"><?php print LANGABS69 ?> :</font>
</td><td align=left><table><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGBT28?>","cumul_abs_rtd_classe.php","_parent","","")</script>
<script language=JavaScript> buttonMagic("<?php print LANGaffec_cre41?>","cumul_rtd_impr.php","_parent","","")</script>&nbsp;&nbsp;
</td></tr></table>
</td></tr>


<tr><td align=right>
<font class="T2"><?php print LANGMESS442 ?> :</font>
</td><td align=left><table><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGBT28?>","gestion_abs_statistique.php","_parent","","")</script>
&nbsp;&nbsp;
</td></tr></table>
</td></tr>




</table>

<br><br></td></tr>
</table>
<br><br>
<?php//-----------------------------------------------------------------------?>
<form method="post" onsubmit="return valide_recherche_eleve_1()" name="formulaire_1" id="formulaire_1" action="gestion_abs_retard_modif_donne.php">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS443 ?> </font></b></td></tr>
<tr id='cadreCentral0'>
<td ><blockquote><BR>
<table border=0 cellspacing=0>
<tr><td ><font class='T2'>
<?php print LANGMESS444 ?><input type='radio' name='act' onclick="document.getElementById('formulaire_1').action='gestion_abs_retard_planifier.php'" />
<?php print LANGMESS445 ?><input type='radio' name='act' onclick="document.getElementById('formulaire_1').action='gestion_abs_retard_modif_donne.php'"  checked='checked' />
<?php print LANGMESS446 ?><input type='radio' name='act' onclick="document.getElementById('formulaire_1').action='gestion_abs_retard_modif.php'" />
</font></td></tr>
</table><br><br>
<table border=0 cellspacing=0>
<tr><td>
<font class="T2"><?php print LANGBULL3 ?> :</font>
                <select name='anneeScolaire' >
                <?php
                filtreAnneeScolaireSelectNote($anneeScolaire,5);
                ?>
                </select>
</td></tr>
<tr><td height='20'></td></tr>
<tr><td style="padding-top:0px;" nowrap>
<font class="T2"><?php print LANGABS3?> : </font><input type="text" name="saisie_nom_eleve" size="20" id="search" autocomplete="off" onkeyup="searchRequest(this,'eleve','target0','formulaire_1','saisie_nom_eleve')"   style="width:15em" />
</td></tr><tr><td style="padding-top:0px;"><div id="target0" style="width:16em" ></div></td></tr>
</table><br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGMESS447 ?>","rien");</script> 
<font class='T2'><?php print LANGMESS448 ?><input type='radio' name='act' onclick="document.getElementById('formulaire_1').action='gestion_abs_retard_conv.php'" /></font>
</blockquote><br><br>
</td></TR></TABLE>
</form>
<BR>


<?php if ($_SESSION['membre'] != "menuprof") { ?>
<?php//-----------------------------------------------------------------------?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS449 ?> </font></b></td></tr>
<tr id='cadreCentral0'><td valign='top' >
	<table border='0'>


<tr><td align=right>
<font class="T2"><?php print LANGABS70 ?> :</font>
</td><td align=left><table><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGBT28?>","gestion_abs_config.php","_parent","","")</script>
<script language=JavaScript> buttonMagic("<?php print LANGMESS450?>","gestion_abs_config_alerte.php","_parent","","")</script>
&nbsp;&nbsp;</td></tr></table>


<tr><td align=right>
<font class="T2"><?php print LANGMESS451 ?> :</font>
</td><td align=left><table><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGBT28?>","gestion_crenau_config.php","_parent","","")</script>
</td></tr></table>

<?php
if (file_exists("./common/config-sms.php")) {
	include_once("./common/config-sms.php");
	$idsms=SMSKEY;
	$inc=GRAPH;
}
?>
<tr><td align=right valign='top' >
<font class="T2"><?php print LANGMESS452  ?> :</font>
</td><td align=left><table><tr><td>
<script language=JavaScript> buttonMagic("<?php print LANGCONFIG?>","gestion_sms_config.php","_parent","","")</script>
<script language=JavaScript>buttonMagic('<?php print LANGMESS453 ?>','https://support.triade-educ.com/support/sms-compte.php?idsms=<?php print $idsms?>&inc=<?php print $inc ?>','','width=550,height=600','','');</script>&nbsp;&nbsp;
</td></tr></table>


<?php 
     print "</td></tr></table>";
     print "</td></tr></table>";

}else{
     brmozilla($_SESSION["navigateur"]);
}	

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
