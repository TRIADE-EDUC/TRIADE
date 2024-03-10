<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET
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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
//error($cnx);
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE11?></font></b></td></tr>
<tr id='cadreCentral0' >
<td ><center>
<br><br>
<?php
$fichier=$_FILES['fichier']['name'];
$type=$_FILES['fichier']['type'];
$tmp_name=$_FILES['fichier']['tmp_name'];
$size=$_FILES['fichier']['size'];
//print $type;

$erreur_fichier="oui";
$anneeScolaire=$_POST["annee_scolaire"];

if ( (!empty($fichier)) &&  ($size <= 2000000)) {
   	if  ( ($type == "application/vnd.ms-excel" ) || ($type == "application/octet-stream") )  {
		$erreur_fichier="non";
		move_uploaded_file($tmp_name,"data/imp_grp.xls");

		$titre=$_POST["saisie_intitule"];
		$fic_xls="data/imp_grp.xls";
		include_once('./librairie_php/reader.php');
		$data = new Spreadsheet_Excel_Reader();
//		$data->setOutputEncoding('CP1251');
		$data->setOutputEncoding('UTF-8');
		$data->read($fic_xls);
		
		$params[nomgr]=trim($_POST["saisie_intitule"]);
		$params[comment]=$_POST["saisie_commentaire"];

		for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
			$nom=trim(strtolower(addslashes($data->sheets[0]['cells'][$i][1])));
			$prenom=trim(strtolower(addslashes($data->sheets[0]['cells'][$i][2])));
			$naissance=trim(dateFormBase($data->sheets[0]['cells'][$i][3]));
			$cr=verifEleveExist($nom,$prenom,$naissance);
			if ($cr == "rien") {
				$nontrouve.="- <b>".strtoupper($nom)."</b> ".ucwords($prenom). "<br />";
			}else{
				$ideleve=$cr;
				$trouve[$ideleve]=$ideleve;
			}
		}

		$params[liste_eleve]=join(",",$trouve);

		@unlink($fic_xls);
		if(verifnomgrp($_POST["saisie_intitule"])) {
			if(create_groupe($params,$anneeScolaire) ){
				print "<font class='T2'>". LANGGRP40 ."</font></center><br>";
				if (trim($nontrouve) != "") {
					print "<font class=T2><ul>".LANGGRP41." : <br>";
					print "<br>$nontrouve<br /><br /></ul>";
				}
			}
		}else{
			print "<font class=T2><center>Groupe déjà créé.</center></font><br>";
		}
	} /// fin du if size et empty
} // fin du if type





if ($erreur_fichier == "oui" ) {
	print "<font class=T2><center>".LANGGRP43.".</center></font>";
}
?>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
