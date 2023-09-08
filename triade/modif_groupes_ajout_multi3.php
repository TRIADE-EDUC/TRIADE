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
<?php include("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
error($cnx);
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGGRP28bis ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top><br>
<?php
validerequete("menuadmin");
// affichage de l'élève (lecture seule)
if (isset($_POST["eid"])) {
	$eid=$_POST["eid"];
	$nomEleve=recherche_eleve_nom($eid);
	$prenomEleve=recherche_eleve_prenom($eid);
	$cnx=cnx();
	print "<font class=T2>";
	print "&nbsp;&nbsp;Validation des groupes de l'élève : <b>".trunchaine($nomEleve." ".$prenomEleve,40)."</b><br /><br />";
	print "</font>";

	$idgrp=$_POST["idgrp"];
	for ($i=0;$i<count($idgrp);$i++) {
		$ajout=1;
		$gid=$_POST["idgrp"][$i];
		if ($gid == "") { continue; }
		$sql="SELECT libelle,liste_elev FROM ${prefixe}groupes WHERE group_id='$gid'";
		$res=execSql($sql);
		$data=chargeMat($res);
		$nomgrp=$data[0][0];
		$liste_eleves=preg_replace('/\{/',"",$data[0][1]);
		$liste_eleves=preg_replace('/\}/',"",$liste_eleves);
		$tabListe=explode(',',$liste_eleves);
		foreach($tabListe as $key=>$value) {
			if  ($value == $eid) { 
				$ajout=0;
				break;
			}
		}
		if ($ajout) {
			if ($liste_eleves != "") {
				$liste_eleves.=",".$eid;
			}else{
				$liste_eleves=$eid;
			}
			$params["liste_eleve"]=$liste_eleves;
			$params["nomgr"]=trim($nomgrp);
			if(modif_group($params)){
				history_cmd($_SESSION["nom"],"MODIFICATION","ajout eleve dans groupe $nomgrp");
			}
		}
	}
}
?>
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
