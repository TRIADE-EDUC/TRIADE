<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E.
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) S.A.R.L. T.R.I.A.D.E. 
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
error_reporting(0);
include_once("common/config.inc.php"); // futur : auto_prepend_file
include_once("librairie_php/db_triade.php");
include_once("common/config2.inc.php"); // futur : auto_prepend_file

if (isset($_GET["carnet"])) {
	$idcarnet=$_GET["carnet"];
	$idclasse=$_GET["cls"];
}else{
	$idcarnet=$_POST["idcarnet"];
	$idclasse=$_POST["saisie_classe"];
}

$cnx=cnx();
if ($_SESSION["membre"] == "menuadmin") {
	validerequete("menuadmin");
}else{
	if (CARNETSUIVIPROF == "oui") {
		validerequete("menuprof");
	}else{
		verif_profp_class($_SESSION["id_pers"],$idclasse); 
	}
}


// Sn : variable de Session nom
// Sp : variable de Session prenom
// Sm : variable de Session membre
// Spid : variable de Session pers_id
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);
// données DB utiles pour cette page
// $donne=$mySession[Spid];
?>
<HTML>
<HEAD>
<title>Enseignant - Triade - Compte de <?php print ucwords($mySession[Sp])." ".strtoupper($mySession[Sn])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<?php


?>
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print "Appréciations sur un Carnet de Suivi " ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td>
<!-- // fin  -->
<br />
<font class="T2">&nbsp;&nbsp;<strong>
<?php
print chercheNomCarnet($idcarnet);
?>
</strong>
<br><br />
</font>
<?php
$data=listeCompetence($idcarnet); // id,idcarnet,libelle,ordre
if (count($data) > 0 ) {
	print "<form method='POST' name='formulaire' action='carnet_editer_3.php' onsubmit='return validecompetence()' >";
	print "<table>";
	for($i=0;$i<count($data);$i++) {
		print "<tr>";
		print "<td><font class=T2>".trunchaine($data[$i][2],46)."</font></td>";
		print "<td align=left><input type=radio name='idcompetence' value=\"".$data[$i][0]."\" ></td>";
		print "</tr>";
	}
?>

<tr><td colspan="2" ><br /><br /><font class="T2">Choix de la notation : </font> <select name="notation" >
<option  id="select0" ><?php print LANGCHOIX?></option>
<?php selectTypeNotation($idcarnet) ?>
</select>
</td></tr>

<tr><td colspan="2" ><br /><br /><font class="T2">Choix de la période : </font> <select name="periode" >
<option  id="select0" ><?php print LANGCHOIX?></option>
<?php selectPeriodeCarnet($idcarnet) ?>
</select>
</td></tr>

<tr><td><br /><br />
<input type=hidden name="idcarnet" value="<?php print $idcarnet ?>" />
<input type=hidden name="sClasseGrp" value="<?php print $idclasse ?>" />
<table align=center><tr><td align=center><script language=JavaScript>buttonMagicSubmit("<?php print VALIDER ?>","create"); </script></td><td><script language=JavaScript>buttonMagicRetour2("carnet_editer.php?sClasseGrp=<?php print $idclasse ?>","_parent","Retour")</script></td></tr></table>
</td></tr>
</td></tr></table>
</form>

<?php 
}else{
?>
	<form action='carnet_editer.php' method="get">
	<input type=hidden name="sClasseGrp" value="<?php print $idclasse ?>" />
	<center><font class="T2">Aucune compétence pour ce carnet.</font></center><br /><br />
	<table align=center><tr><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGSTAGE73 ?>","modif");</script></td><td><script language=JavaScript>buttonMagicRetour2("carnet_editer.php?sClasseGrp=<?php print $idclasse ?>","_parent","Retour")</script></td></tr></table>
	</form>
<?php	
}
?>


<br>
<!-- // fin  -->
</td></tr></table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION[membre] == "menuadmin") :
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
</BODY>
</HTML>
<?php @Pgclose() ?>
