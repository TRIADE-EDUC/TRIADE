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
<title>Triade - Liste Elève </title>
</head>
<body id='bodyfond2'>
<?php include("./librairie_php/lib_licence.php"); ?>
<center>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");

if ($_SESSION["membre"] == "menuprof") {
	$saisie_classe=$_POST["sClasseGrp"];
	$cnx=cnx();
	verif_profp_class($_SESSION["id_pers"],$saisie_classe);
	$nomClasse=chercheClasse_nom($saisie_classe);
}else{
	$saisie_classe="";
	validerequete("menuadmin");
	$cnx=cnx();
}


$gid=$_POST["gid"];
$sql="SELECT libelle,liste_elev FROM ${prefixe}groupes WHERE group_id='$gid'";
$res=execSql($sql);
$data=chargeMat($res);
$nomgrp=$data[0][0];

// module de modification
if(isset($_POST["create"])) {
	$liste=$_POST["saisie_recherche_final"];
	$params["liste_eleve"]=$liste;
	$params["nomgr"]=trim($_POST["saisie_intitule"]);
	if( modif_group($params) ):
		history_cmd($_SESSION["nom"],"MODIFICATION","ajout eleve dans groupe $nomgrp");
        	alertJs(LANGGRP37);
	else:
       		error(0);
	endif;
}
// fin de la modif groupe



$gid=$_POST["gid"];
$sql="SELECT libelle,liste_elev FROM ${prefixe}groupes WHERE group_id='$gid'";
$res=execSql($sql);
$data=chargeMat($res);
$nomgrp=$data[0][0];
$liste_eleves=preg_replace('/\{/',"",$data[0][1]);
$liste_eleves=preg_replace('/\}/',"",$liste_eleves);
if ($liste_eleves != "") {

	$sql="SELECT nom,prenom,libelle,elev_id FROM ${prefixe}eleves, ${prefixe}classes where classe=code_class AND elev_id IN ($liste_eleves)";
	$res=execSql($sql);
	$data=chargeMat($res);
	$pasdeleve="non";

}else {

	$pasdeleve="oui";

}
?>

<font class=T2><?php print LANGGRP38 ?><font color="red"><b><?php print $nomgrp?></b></font></font>
<br>
<form method=post name="formulaire" >
<input type=hidden name='saisie_intitule' readonly  value="<?php print trim($nomgrp)?>">
<table border="1" width=99% bordercolor="#000000">
<TR>
<TD bgcolor="yellow" ><B><?php print LANGNA1 ?></B></TD>
<TD bgcolor="yellow" ><B><?php print LANGNA2 ?></B></TD>
</tr>
<?php
// debut for
if ( $pasdeleve != "oui" ) {
	for($i=0;$i<count($data);$i++) {
?>
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
</form>
<?php
}else {
	print "</table>";
	print "<br>";
	print LANGGRP39;
	print "<br>";
	print "<br>";
	print "<br>";
	print "<br>";
	print "<table align=center><tr><td>";
	print "<script language=JavaScript>buttonMagicFermeture()</script></td>";
	print "</tr></table>";
}
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
