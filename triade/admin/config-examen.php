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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php include("./librairie_php/lib_licence.php"); ?>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" >
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Gestion des examens</font></b></td></tr>
<tr id='cadreCentral0'><td valign=top>
<!-- // debut de la saisie -->
<blockquote>
<BR>
<?php
if (isset($_GET['id'])) {
	$id=$_GET['id'];
	include_once("../common/config.inc.php");
	include_once("../librairie_php/db_triade.php");
	$cnx=cnx();
	$data=rechercheExamenConfig($id);
	// id, libelle , coef
	$libelle=$data[0][1];
	$coef=$data[0][2];
	Pgclose(); 
}
?>
<form method=post name="formulaire">
<table>
Nom de l'examen : <input type='text' name='examen' maxlength='20' value="<?php print $libelle ?>"  /><br><br>
Coef de l'examen : <input type='text' name='coef' size='4' value="<?php print $coef ?>"  />&nbsp;<i>(champs vide, laisser libre aux enseignants)</i>
</table>
<br>
</blockquote>
<ul>
<input type='hidden' name='id' value='<?php print $id ?>' /> 
<?php if (isset($_GET['id'])) {  ?>
	<script language=JavaScript>buttonMagicSubmit("<?php print "Modifier"?>","modifier"); //text,nomInput</script>
	<script language=JavaScript>buttonMagicSubmit("<?php print "Supprimer"?>","supp"); //text,nomInput</script>
	&nbsp;&nbsp;<input type='button' onClick="open('config-examen.php','_parent','')"  value="Nouveau" class="button" />
<?php  }else{  ?>
	<script language=JavaScript>buttonMagicSubmit("<?php print "Enregistrer"?>","create"); //text,nomInput</script>
<?php } ?>
<BR><br><br>
</ul>
</form>
<?php

if (isset($_POST["supp"])) {
	include_once("../common/config.inc.php");
        include_once("../librairie_php/db_triade.php");
        $cnx=cnx();
	suppExamenConfig($_POST["id"]);
	Pgclose();
}

if (isset($_POST["modifier"])) {
	include_once("../common/config.inc.php");
	include_once("../librairie_php/db_triade.php");
	$cnx=cnx();
	$examen=$_POST['examen'];
	$coefexamen=$_POST['coef'];
	$id=$_POST['id'];
	modifExamenConfig($examen,$coefexamen,$id);	
	Pgclose(); 
}

if (isset($_POST["create"])){
	include_once("../common/config.inc.php");
	include_once("../librairie_php/db_triade.php");
	$cnx=cnx();
	$examen=$_POST['examen'];
	$coefexamen=$_POST['coef'];
	createExamenConfig($examen,$coefexamen);	
	Pgclose();
}
?>

</td></tr></table>
<br><br>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Liste des examens </font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<table width=100%>
<?php
include_once('../librairie_php/db_triade.php');
include_once('../librairie_php/langue.php');
$cnx=cnx();
$data=recupExamenConfig();
for($i=0;$i<count($data);$i++) {
	print "<tr class='tabnormal' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\">\n";
	print "<td >".$data[$i][1]." - &nbsp;Coef : ".$data[$i][2]."</td>\n";
	print "<td width=5><input type=button class=button value=\"Visualiser / Modifier\" onclick=\"open('config-examen.php?id=".$data[$i][0]."','_parent','');\" ></td>\n";
	print "</tr>\n";
}
?>
</table>

<!-- // fin de la saisie -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
