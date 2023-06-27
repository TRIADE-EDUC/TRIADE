<?php
session_start();
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if ( ($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"droitStageProRead") == 0) ) {
	PgClose();
	header("Location: accespersonneldenied.php?titre=Module Stage Pro.");	
}
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
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
if ($_SESSION["membre"] != "menupersonnel") { validerequete("3"); }
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Publipostage des sociétés" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<form method=post  name="formulaire" action="publipostagesociete_2.php">
<!-- // debut form  -->
<blockquote><BR>
<font class=T2><?php print "Classe" ?> :</font> <select id="idclasse" name="idclasse">
		<option id='select0' value="0" > Toutes les classes</option>
		<?php select_classe() ?>
</select><br /><br />

<font class=T2><?php print "Société" ?> :</font> <select id="type_societe" name="type_societe">
		<option id='select1' value="1" > avec étudiant affecté</option>
		<option id='select1' value="2" > sans étudiant affecté</option>
</select> <br /><br />

<font class=T2><?php print "Type de vignette" ?> :</font> <select id="id_vignette" name="id_vignette">
	       <option id='select0' value='1' >3 colonnes (70x42,3)</option>
               <option id='select0' value='2' >2 colonnes (105x39)</option>
               <option id='select0' value='3' >2 colonnes (105x39) avec marge</option>
</select> 
<br /><br />
<font class=T2><?php print "Ville de la société" ?> : </font> <input type='text' name='ville_societe' size='20' />


<br /><br />
<font class=T2><?php print "Adresse du siege" ?> :</font> <input type=radio name="adr" value="siege" checked='checked' />
<br /><br />
<font class=T2><?php print "Adresse du lieu de stage" ?> :</font> <input type=radio name="adr" value="lieustage" />
<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print VALIDER?>","consult1"); //text,nomInput</script>
</UL></UL></UL>
</form>
</blockquote>
<br /><br /><br />
<!-- // fin form -->
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
// deconnexion en fin de fichier
Pgclose();
?>

</BODY>
</HTML>
