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
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Liste Elève </title>
</head>
<body id='bodyfond2' >
<center>
<?php
include("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
if ($_SESSION["membre"] == "menuprof") {
	$saisie_classe=$_GET["sClasseGrp"];
	$cnx=cnx();
	verif_profp_class($_SESSION["id_pers"],$saisie_classe);
	$nomClasse=chercheClasse_nom($saisie_classe);
	$donne=$_SESSION["id_suppleant"];
	$sql="SELECT idprof,idclasse FROM ${prefixe}prof_p WHERE idprof='$donne'";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	// patch pour problème sous-matière à 0
	for($i=0;$i<count($data);$i++){
		$nomclasse=chercheClasse($data[$i][1]);
		$nomclasse=$nomclasse[0][1];
		$option.="<option STYLE='color:#000066;background-color:#CCCCFF' value='".$data[$i][1]."'>$nomclasse</option>\n";
	}
	// fin patch
	freeResult($curs);
	unset($curs);


}else{
	$saisie_classe="";
	validerequete("menuadmin");
	$cnx=cnx();
}

$gid=$_GET['gid'];
$sql="SELECT libelle,liste_elev FROM ${prefixe}groupes WHERE group_id='$gid'";

$res=execSql($sql);
$data=chargeMat($res);
$nomgrp=$data[0][0];
?>
<p><font class=T2><?php print LANGGRP32 ?> <font color="red"><b><?php print $nomgrp?></b></font></font></p>
<br>
<br>
<form method="post" name="formulaire" action="modif_groupe_ajout3.php"  onsubmit="return valide_supp_choix('saisie_classe','une classe')" >
<input type=hidden name='gid' readonly  value="<?php print $_GET["gid"]; ?>">
<input type=hidden name='sClasseGrp' readonly  value="<?php print $_GET["sClasseGrp"]; ?>">
<font class=T2>
<?php print LANGGRP33 ?> : <br><br><br>
<table><tr><td>
<font class=T2><?php print LANGGRP34 ?></font><select name="saisie_classe">
<option   STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
if ($_SESSION["membre"] == "menuprof") {
	print $option;
}else{
	select_classe(); // creation des options
}
Pgclose();
?>
</select> </td><td>
<script language=JavaScript>buttonMagicSubmit("<?php print VALIDER ?>","rien"); //text,nomInput</script>
</td></tr></table>
</font>
</form>
</BODY>
</HTML>
