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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Permission Cahier de textes </title>
</head>
<body id='bodyfond2'>
<?php include("./librairie_php/lib_licence.php"); ?>
<center>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();


// module de modification
if(isset($_POST["create"])) {
	$liste=$_POST["saisie_recherche_final"];
	$params["liste_prof"]=$liste;
	$params["nomgrp"]=trim($_POST["saisie_intitule"]);
	if(modif_perm_cdt($params) ){
		history_cmd($_SESSION["nom"],"MODIFICATION","Permisssion $nomgrp");
        	alertJs("Permission attribuée.");
	}
}
// fin de la modif groupe



$libelle=$_POST["saisie_intitule"];
$sql="SELECT libelle,text FROM ${prefixe}parametrage WHERE libelle='$libelle'";
$res=execSql($sql);
$data=chargeMat($res);
$nomgrp=$data[0][0];
$liste_prof=preg_replace('/\{/',"",$data[0][1]);
$liste_prof=preg_replace('/\}/',"",$liste_prof);
if ($liste_prof != "") {
	$sql="SELECT nom,prenom FROM ${prefixe}personnel where pers_id IN ($liste_prof)";
	$res=execSql($sql);
	$data=chargeMat($res);
}
?>
<font class=T2><?php print "Liste des enseignants" ?></font>
<br><br>
<table border="1" width=99% bordercolor="#000000">
<TR>
<TD bgcolor="yellow" ><B><?php print LANGNA1 ?></B></TD>
<TD bgcolor="yellow" ><B><?php print LANGNA2 ?></B></TD>
</tr>
<?php
// debut for
for($i=0;$i<count($data);$i++) { ?>
<tr class="tabnormal" onmouseover="this.className='tabover2'" onmouseout="this.className='tabnormal'">
	<td ><?php print ucwords($data[$i][0])?></td>
	<td ><?php print ucwords($data[$i][1])?></td>
</tr>
<?php
} // fin for
?>
</table>
<BR><BR>
<table align=center><tr><td>
<script language=JavaScript>buttonMagicFermeture()</script></td>
</tr></table>
</center>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
