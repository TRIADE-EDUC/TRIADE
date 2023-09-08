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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body  id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print "Configuration de la cantine" ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<?php
include_once('./librairie_php/db_triade.php');
$cnx=cnx();
$idpers=$_SESSION["id_pers"];
if ( (verifDroit($idpers,"cantine")) || ($_SESSION["membre"] == "menuadmin" )) { 
if (isset($_GET["idedit"])) {
	$data=recupConfig($_GET["idedit"]); // id,libelle,prix,attribue,indice_salaire,platdefault
	$id=$data[0][0];
	$plat=$data[0][1];
	$prix=$data[0][2];
	$attribue=$data[0][3];
	$indice_salaire=$data[0][4];
	if ($data[0][5] == 1) { $platdefault="checked='checked'"; }else{ $platdefault=""; }
}

?>



<br>
<form method='post' action="cantine_config.php" >
<table align='center'>

<tr><td align='right' ><font class='T2'>Nom du plat :</font></td><td><input type=text name="plat" size='30' value="<?php print $plat ?>" /></td></tr>
<tr><td align='right' ><font class='T2'>Prix du plat :</font></td><td><input type=text name="prix" size='30' value="<?php print $prix ?>" /></td></tr>
<tr><td align='right' ><font class='T2'>Membre concerné :</font></td><td>
<select name='attribue' >
<?php
if ($attribue != "") {
	if ($attribue == 'tous') { $attribuelibelle="tous"; }
	if ($attribue == 'menueleve') { $attribuelibelle="Elève"; }
	if ($attribue == 'menuautre') { $attribuelibelle="Enseignants / Personnels"; }
	if ($attribue == 'menuext') { $attribuelibelle="Extérieurs"; }
	print "<option value='$attribue' id='select0' >$attribuelibelle</option>";
}
?>
<option value='tous' id='select0' >tous</option>
<option value='menueleve' id='select1' >Elève</option>
<option value='menuautre' id='select1' >Enseignants / Personnels</option>
<option value='menuext' id='select1' >Extérieurs</option>
</select>
<tr><td align='right' ><font class='T2'>Indice salaire :</font></td><td>
<select name='indice_salaire' >
<?php
if (($indice_salaire != "") && ($indice_salaire != "0")) {
	print "<option value='$indice_salaire' id='select0' >$indice_salaire</option>";
}
?>
<option value='0' id='select0' >aucun</option>
<?php recupListIndiceSalaire(); ?>
</select>
<!-- <tr><td align='right' ><font class='T2'>Photo / Representation :</font></td><td><input type=file name="photo" /></td></tr> -->
</td></tr>
<tr><td align='right' ><font class='T2'>Plat par défaut :</font></td><td><input type=checkbox name="platdefault" value="1"  <?php print $platdefault ?> /></td></tr>
<tr><td height='20'></td></tr>
<?php if (isset($_GET["idedit"])) { ?>
	<tr><td colspan='2'><script language=JavaScript>buttonMagicSubmit("<?php  print VALIDER ?>","modif"); //text,nomInput</script></td></tr>
<?php }else{ ?>
	<tr><td colspan='2'><script language=JavaScript>buttonMagicSubmit("<?php  print VALIDER ?>","create"); //text,nomInput</script></td></tr>
<?php } ?>
</table>
<input type='hidden' name="id" size='30' value="<?php print $id ?>" />
</form>
<?php
	if (isset($_POST["create"])) { ajoutPlateau($_POST["plat"],$_POST["prix"],$_POST["attribue"],$_POST["indice_salaire"],$_POST["platdefault"]); }
	if (isset($_POST["modif"])) { modifPlateau($_POST["plat"],$_POST["prix"],$_POST["attribue"],$_POST["indice_salaire"],$_POST["id"],$_POST["platdefault"]); }
	if (isset($_GET["idsupp"])) { suppPlateau($_GET["idsupp"]); }

	$data=recupConfigCantine(); // id,libelle,prix,attribue,indice_salaire,platdefault
	
	print "<br><hr>";
	print "<table width='100%' border='1' style='border-collapse: collapse;' >";
	print "<tr>";
	print "<td bgcolor='yellow'><font class='T2'>&nbsp;Nom du plat</font></td>";
	print "<td bgcolor='yellow'><font class='T2'>&nbsp;Prix ".unitemonnaie()."</font></td>";
	print "<td bgcolor='yellow'><font class='T2'>&nbsp;Attribué</font></td>";
	print "<td bgcolor='yellow' width='5%' ><font class='T2'>&nbsp;Indice&nbsp;sal.&nbsp;</font></td>";
	print "<td bgcolor='yellow' width='5%' ><font class='T2'>&nbsp;Action&nbsp;</font></td>";
	print "</tr>";
	for($i=0;$i<count($data);$i++) {
		if ($data[$i][5] == 1) { $platdefaut="&nbsp;<img src='image/on10.gif' title='plateau par défaut' />&nbsp;"; }else{ $platdefaut=""; }
		print "<tr  class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\"  >";
		print "<td><font class='T2'>$platdefaut&nbsp;".$data[$i][1]."</font></td>";
		print "<td><font class='T2'>&nbsp;".affichageFormatMonnaie($data[$i][2])."</font></td>";
		if ($data[$i][3] == "tous") { $attribue="Tous"; }
		if ($data[$i][3] == "menueleve") { $attribue="Elèves"; }
		if ($data[$i][3] == "menuautre") { $attribue="Profs / Pers."; }
		if ($data[$i][3] == "menuext") { $attribue="Exterieurs"; }

		print "<td><font class='T2'>&nbsp;$attribue</font></td>";
		$indice=$data[$i][4];
		if ($data[$i][4] == "0") $indice="<i>aucun</i>";
		print "<td><font class='T2'>&nbsp;$indice</font></td>";
		print "<td>";
		print "&nbsp;<a href='cantine_config.php?idsupp=".$data[$i][0]."' title='Supprimer' ><img src='image/commun/trash.png' border='0' /></a>";
		print "&nbsp;<a href='cantine_config.php?idedit=".$data[$i][0]."' title='Modifier' ><img src='image/commun/editer.gif' border='0' /></a>";
		print "</td>";
		print "</tr>";
	}
	print "</table>";
?>
<br>
<?php }else{ ?>
<br><font class="T2" id="color3"><center><img src="image/commun/img_ssl.gif" align='center' /> Accès réservé</center></font>
<?php } ?>

<br><br>
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
