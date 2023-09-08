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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return valide_consul_classe()" name="formulaire" action="gestion_examen_impr_brevet_techno22012.php" >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print "Fiche Brevet série Technologique"?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
<blockquote><BR>
<font class=T2><?php print LANGPROFG?> :</font> 
<select id="saisie_classe" name="saisie_classe">
<option id='select0' ><?php print LANGCHOIX?></option>
<?php
select_classe(); // creation des options
?>
</select>
<br><br>
<?php 

$datap=config_param_visu("viescolaire");
$viescolaire=$datap[0][0];
if ($viescolaire == "1") $viescolaire="checked='checked'";
$datap=config_param_visu("examenEPS");
$examenEPS=$datap[0][0];
if ($examenEPS == "oui") $examenEPS="checked='checked'";
$datap=config_param_visu("examenPREV_SANTE_ENV");
$examenPREV_SANTE_ENV=$datap[0][0];
if ($examenPREV_SANTE_ENV == "oui") $examenPREV_SANTE_ENV="checked='checked'";
$datap=config_param_visu("recupNoteNotanet");
$recupNoteNotanet=$datap[0][0];
if ($recupNoteNotanet == "1") $recupNoteNotanet="checked='checked'";
if ($recupNoteNotanet == "0") $recupNoteNotanet="";
?>
<font class='T2'>Nature de l'option : <select name='option'>
				      <option value=""  id='select0' ><?php print LANGCHOIX ?></option>
				      <option value="agricole"  id='select1' >Agricole</option></select>
<br>
</font>
<br>

<font class=T2>Afficher la matière "Vie scolaire" : </font><input type='checkbox' name="viescolaire" value='1' <?php print $checkviescolaire ?> /> <i>(oui)</i>
<br><br>
<font class=T2>Prendre seulement examen Brevet EPS : </font><input type='checkbox' name="examenEPS" value='oui' <?php print $examenEPS ?> /> <i>(oui)</i>
<br><br>
<font class=T2>Prendre seulement examen Brevet PREV. SANTE ENV. : </font><input type='checkbox' name="examenPREV_SANTE_ENV" value='oui' <?php print $examenPREV_SANTE_ENV ?> /> <i>(oui)</i>
<br><br>
<font class=T2>Prise en compte des notes correctifs Notanet : <input type='checkbox' name="recupNoteNotanet" value='1' <?php print $recupNoteNotanet ?> /> <i>(oui)</i>
<br><br><br>

<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print VALIDER ?>","create"); //text,nomInput</script>
<input type=hidden name="typebull" value="brevetcollege" />
<form>
<br><br>
<!-- // fin form -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
