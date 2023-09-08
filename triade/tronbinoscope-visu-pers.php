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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
if ($_SESSION["membre"] == "menupersonnel") {
	if (!verifDroit($_SESSION["id_pers"],"trombinoscopeRead")){
		validerequete("2");
	}
}else{
	validerequete("2");
	$visu=1;
	$visu2=1;
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTRONBI11?></font></b></td></tr>
<tr id='cadreCentral0' >
<td valign='top'>
<br><br>
<form method='POST' >
&nbsp;&nbsp;&nbsp;<font class="T2"><?php print "Type membre " ?>  :</font> <select name="saisie_type" onChange="this.form.submit()" >
     
    <option id='select0' value='0' <?php print ($_POST["saisie_type"] == 0) ? "selected='selected'" : "" ?> ><?php print LANGCHOIX?></option>
    <option id='select1' value="ENS" <?php print ($_POST["saisie_type"] == "ENS") ? "selected='selected'" : "" ?> ><?php print "Enseignant"?></option>
    <option id='select1' value="ADM" <?php print ($_POST["saisie_type"] == "ADM") ? "selected='selected'" : "" ?> ><?php print "Direction"?></option>
    <option id='select1' value="TUT" <?php print ($_POST["saisie_type"] == "TUT") ? "selected='selected'" : "" ?> ><?php print "Tuteur de stage"?></option>
    <option id='select1' value="PER" <?php print ($_POST["saisie_type"] == "PER") ? "selected='selected'" : "" ?> ><?php print "Personnel"?></option>
    <option id='select1' value="MVS" <?php print ($_POST["saisie_type"] == "MVS") ? "selected='selected'" : "" ?> ><?php print "Vie Scolaire"?></option>
</select><br><br>
</form>

<!-- // debut form  -->
<?php
$sqlsuite="(type_pers='ENS' OR type_pers='ADM' OR type_pers='PER' OR type_pers='MVS')";
if (isset($_POST["saisie_type"])) {
	if ($_POST["saisie_type"] != "0") $sqlsuite=" type_pers='".$_POST["saisie_type"]."' ";
}

$sql="SELECT pers_id,nom,prenom FROM ${prefixe}personnel WHERE $sqlsuite AND  offline='0' ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);

print "<table width='100%'>";
if( count($data) <= 0 )	{
	print("<tr id='cadreCentral0' ><td align=center valign=center>"."AUCUN COMPTE DE DISPONIBLE"."</td></tr>");
} else {
	print "<tr>";
	$j=0;
	for($i=0;$i<count($data);$i++) {
		$j++;
	?>	
		<td align=center><img src="image_trombi.php?idP=<?php print $data[$i][0]?>" border=0 /><br><?php print recherche_personne($data[$i][0])?></td>
<?php
		if ($j == 3) { print "</tr><tr>"; $j=0; }
	}
}
print "</tr></table>";
Pgclose();
?>
<br>
<table align=center><tr><td align=center><script language=JavaScript>buttonMagic("<?php print "Imprimer au format PDF" ?>","tronbinoscope-pers-impr-pdf.php?saisie_type=<?php print $_POST["saisie_type"] ?>","impr","width=800,height=600,scrollbars=yes,menubar=yes","") </script></tr></td></table>
<br>
</td></tr></table>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
?>
</BODY>
</HTML>
