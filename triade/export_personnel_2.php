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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");
validerequete("2");
?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'.js'?>"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
<?php  $today= date ("j M, Y");  ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>
<?php top_h(); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'1.js'?>"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Exportation des données du personnel" ?>  </font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<br />
<ul><font class=T2><?php print "Indiquer l'ordre des colonnes dans votre fichier excel" ?></font></ul>
<br />
<form method="post" action="export_personnel_3.php" >
<font class="T2">
<?php
$nbordre=count($_POST['liste']);
$nbcolplus=$_POST['nbcolplus'];
$saisie_type=$_POST['saisie_type'];

if (isset($_POST['create'])) {
	print "<table width='100%' border=1>";
	print "<tr>";
	$j=0;
	$tab=$_POST['liste'];
	if ($nbcolplus > 0) {
		for($i=0;$i<$nbcolplus;$i++) {
			$tab[]="$i";
			$nbordre++;
		}
	}

	foreach($tab as $key=>$value) {
		print "<td width='33%'>";
		print "<select name='ordre[]'>";
		print "<option value='' >N°</option>";
		for($i=1;$i<=$nbordre;$i++) {
			print "<option value='$i' >$i</option>";
		}
		print "</select>&nbsp;";
		$name="";
		if ($value == "nom") { $name="nom"; }
		if ($value == "prenom") { $name="prénom"; }
		if ($value == "civ_1") { $name="Civivilité"; }
		if ($value == "adr1") { $name="adresse"; }
		if ($value == "code_post_adr1") { $name="CCP"; }
		if ($value == "commune_adr1") { $name="Commune"; }
		if ($value == "tel_port_1") { $name="Tél.&nbsp;port.&nbsp;"; }
		if ($value == "telephone") { $name="Téléphone"; }
		if ($value == "identifiant") { $name="Identifiant"; }
		if ($value == "indice_salaire") { $name="indixe&nbsp;salaire"; }
		if ($value == "code_barre") { $name="Code&nbsp;barre"; }
		if ($value == "email") { $name="Email"; }

		if ($name == "") { $name="<input type=text name='nbcolname[]' size='20' />"; }

		print "<font class='T2'>$name </font></td>";
		$j++;
		if ($j == 3) { print "</tr><tr>"; $j=0; }
		$liste.= $value."%##%";

	}
	print "</tr></table>";
	print "<br>";
	$liste=preg_replace("/%##%$/","",$liste);
	print "<input type='hidden' name='liste' value=\"$liste\" />";
	print "<input type='hidden' name='saisie_type' value=\"$saisie_type\" />";
}
?>
</font>
<br>
<center><input type="submit" value="Suivant -->" class="BUTTON" name="create" /> </center>
</form>
<br>

<br />
<!-- // fin  -->
</td></tr></table>
<BR>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'2.js'?>"> </SCRIPT>
<SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>
</BODY></HTML>
