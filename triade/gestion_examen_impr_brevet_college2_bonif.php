<?php
session_start();
error_reporting(0);
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
include_once("./librairie_php/lib_licence.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(900);
}
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
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();"  >
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGBULL5?> </font></b></td></tr>
<tr  id='cadreCentral0' >
<td >
<!-- // fin  --><br> <br>
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
if ($_SESSION["membre"] == "menuprof") {
	verif_profp_class($_SESSION["id_pers"],$_POST["saisie_classe"]);
}else{
	validerequete("2");
}
$bull="";
$typebull=$_POST["typebull"];
//---------------------------------------------- SERIE BREVET
if ($typebull == "brevetcollege") {   // brevet série college
	validerequete("menuadmin"); 
	$bull="brevet_construction_bonif.php"; 
	$retour="gestion_examen_impr_brevet_college_bonif.php";  
}
//-----------------------------------------------
?>
<form method=post action="<?php print $bull ?>" onSubmit="document.formulaire5.rien.disabled=true;"  name="formulaire5" >
<input type='hidden' name='examen' value='<?php print $typebull ?>' />
<center>
<select name="type_pdf" id="saisie_classe">
<option value="global" id='select1'><?php print LANGPDF1 ?></option>
<option value="pers" id='select1'><?php print LANGPDF2 ?></option>
<!-- <option value="mail" id='select1'>Un envoi par email par bulletin</option> -->
</select>
<br><br>
<font class=T2>Type bulletin : </font> 
<select name="type_colonne" id="saisie_col">
<option value="LV2" id='select1'><?php print "LV2" ?></option>
<!-- <option value="DP6H" id='select1'><?php print "DP6H" ?></option> -->
</select>
<br><br>
<font class=T2>Note arrondi au demi-point supérieur : </font><input type="checkbox" name=arrondi value="1" checked="checked" /> (oui)
<br><br>
<input type=hidden name='saisie_classe' value="<?php print $_POST["saisie_classe"];?>" >
<input type=hidden name='annee_scolaire' value="<?php print $_POST["annee_scolaire"];?>" >
<input type=hidden name='NoteUsa' value="<?php print $_POST["NoteUsa"];?>" >
<input type=hidden name='typetrisem' value="<?php print $_POST["typetrisem"];?>" >


<table border=0 align=center width="250"><tr><td align="center">
<script language=JavaScript>buttonMagicRetour("<?php print $retour ?>","_parent")</script>
<script language=JavaScript>buttonMagicSubmit3("<?php print LANGBULL6 ?>","rien","onclick='this.value=\"<?php print LANGBT5 ?>\";AfficheAttente()'");</script>&nbsp;&nbsp;
</td></tr></table>
</form>

<!-- // fin  -->
</td></tr></table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION["membre"] == "menuadmin") :
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
// deconnexion en fin de fichier

attente();
Pgclose();
?>
</BODY></HTML>
