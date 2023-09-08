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
$anneeScolaire=$_COOKIE["anneeScolaire"];
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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Evalution et notation du jury</font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
$data=visu_param();
// nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement,$anneeScolaire
for($i=0;$i<count($data);$i++) {
	$nom_etablissement=trim($data[$i][0]);
	$adresse=trim($data[$i][1]);
	$postal=trim($data[$i][2]);
	$ville=trim($data[$i][3]);
	$tel=trim($data[$i][4]);
	$mail=trim($data[$i][5]);
	$directeur_etablissement=trim($data[$i][6]);
	$urlsite=trim($data[$i][7]);
	$accademie=trim($data[$i][8]);
	$pays=trim($data[$i][9]);
	$departement=trim($data[$i][10]);
}
?>

<form method=post  name="formulaire" action="gestion_examen_jury2.php" enctype="multipart/form-data">
<table width='100%' border='0'>
<tr><td height='20'></td></tr>
<tr><td align='right' ><font class='T2'>&nbsp;&nbsp;Année :</td><td><input type='text' name='annee' id='annee' size='40' value="<?php print $anneeScolaire ?>" /></td></tr>
<tr><td height='20'></td></tr>
<tr><td align='right' ><font class='T2'>&nbsp;&nbsp;Titre :</td><td><input type='text' name='titre' id='titre' size='40' value="" /></td></tr>
<tr><td height='20'></td></tr>
<tr><td align='right' ><font class='T2'>&nbsp;&nbsp;Directeur :</td><td>
<select name='directeur' id='directeur'>
<option id='select0' value=''><?php print LANGCHOIX ?></option>
<?php print select_personne_2('ENS','30'); ?>
</select>

</td></tr>
<tr><td height='10'></td></tr>
<tr><td align='right' ><font class='T2'>&nbsp;&nbsp;Composition du jury:</td><td><br><input type='text' name='jury' id='jury' size='40' /><br><i>(séparer par une vigule)</i></td></tr>
<tr><td height='10'></td></tr>
<tr><td align='right' ><font class='T2'>&nbsp;&nbsp;Sujet : </td><td><input type='text' name='sujet' id='sujet' size='40' /></td></tr>
<tr><td height='20'></td></tr>
<tr><td align='right' ><font class='T2'>&nbsp;&nbsp;Auteur(s) : </td><td><input type='text' name='auteur' id='auteur' size='40' /></td></tr>
<tr><td height='20'></td></tr>
<tr><td align='center' colspan='2'><font class='T2'>&nbsp;&nbsp;Appréciation et Evaluation du jury : <br />
<textarea type='text' name='commentaire' id='commentaire' cols=100 rows=25 ></textarea></td></tr>
<tr><td height='20'></td></tr>
<tr><td align='right' ><font class='T2'>&nbsp;&nbsp;Notation : </td><td><input type='text' name='note' id='note' size='2' /> /20</td></tr>
<tr><td height='20'></td></tr>
<tr><td height='40' colspan='2'>
<table align=center><tr><td>
<script language=JavaScript>buttonMagic("<?php print LANGCIRCU14 ?>","Javascript:history.go(-2)","_parent","","");</script>
<script language=JavaScript>buttonMagicSubmit3("<?php print LANGENR ?>","rien",""); //text,nomInput</script>&nbsp;&nbsp;
</td></tr></table>
</td></tr>
</table>
</form>

<?php
Pgclose();
include_once("./librairie_php/lib_conexpersistant.php");
connexpersistance();
?>
<!-- // fin form -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
