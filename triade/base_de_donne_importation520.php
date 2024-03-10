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
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(300);
}
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" onunload="attente_close()"  >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript"<?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGBASE42 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<?php
include_once("librairie_php/db_triade.php");

$fichier=$_FILES["fichier1"]["name"];
$type=$_FILES["fichier1"]["type"];
$tmp_name=$_FILES["fichier1"]["tmp_name"];
//$size=$_FILES["fichier1"]["size"];
if ( (!empty($fichier)) && (($type == "application/octet-stream" ) || ($type == "application/vnd.ms-excel" ))) {
	move_uploaded_file($tmp_name,"data/fichier_gep/$fichier");
	rename("data/fichier_gep/$fichier", "data/fichier_gep/traitement1.xls");
	@unlink("data/fichier_gep/$fichier");
	$fic_xls="data/fichier_gep/traitement1.xls";
	include_once('./librairie_php/reader.php');
	$data = new Spreadsheet_Excel_Reader();
//	$data->setOutputEncoding('CP1251');
	$data->setOutputEncoding('UTF-8');
	$data->read($fic_xls);
	$cnx=cnx();
	$nbeleveaffecte=0;
	for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
		// strtolower(trim(addslashes($data->sheets[0]['cells'][$i][5])));
		//
		// Responsable élève
		// -------------------
		// 1 Civilite
		// 2 Nom Personne
		$params["nomparent"]=strtolower(trim(addslashes($data->sheets[0]['cells'][$i][2])));
		// 3 Prénom Personne
		$params["prenomparent"]=strtolower(trim(addslashes($data->sheets[0]['cells'][$i][3])));
		// 4 Tel personnel
		$params["teltuteur1"]=$teltuteur1=$data->sheets[0]['cells'][$i][4];	
		// 5 Tel portable
		$params["telportable1"]=$telportable1=$data->sheets[0]['cells'][$i][5];
		// 6 Tel professionnel	
		$params["telprofession1"]=$telprofession1=$data->sheets[0]['cells'][$i][6];
		// 7 Email personne	
		$params["email1"]=$emailtuteur1=$data->sheets[0]['cells'][$i][7];
		// 8 Communication adresse

		// 9 Ligne 1 adresse
		$params["adresse1"]=$adresse1=preg_replace('/,/','',trim(addslashes($data->sheets[0]['cells'][$i][9])));
		// 10 Ligne 2 adresse	
		$params["adresse1"].=", ".preg_replace('/,/','',trim(addslashes($data->sheets[0]['cells'][$i][10])));
		// 11 Ligne 3 adresse	
		$params["adresse1"].=", ".preg_replace('/,/','',trim(addslashes($data->sheets[0]['cells'][$i][11])));
		// 12 Ligne 4 adresse	
		// 13 Libelle postal
		$params["ville1"]=$ville1=trim(addslashes($data->sheets[0]['cells'][$i][13]));
		// 14 Code postal
		$params["codepostal1"]=$codePostal=trim(addslashes($data->sheets[0]['cells'][$i][14]));
		// 15 Code departement	
		// 16 Commune etrangere	
		// 17 Ll pays	
		// 18 Code PCS	
		// 19 Ll PCS	
		$params["emploi"]=$emploi=ucfirst(strtolower(trim(addslashes($data->sheets[0]['cells'][$i][19]))));
		// 20 Ll Situation Emploi	
		// 21 Responsable financier
		$respFinancier=trim(addslashes($data->sheets[0]['cells'][$i][21]));
		// 22 Personne à contacter	
		// 23 Destinataire des bourses	
		// 24 Responsable legal	
		$params["responsable"]=$respLegal=trim(addslashes($data->sheets[0]['cells'][$i][24]));
		// 25 Lc parente
		$params["parente"]=$parente=trim(addslashes($data->sheets[0]['cells'][$i][25]));
		// 26 Sexe
		$params["sexe"]=$sexe=trim(addslashes($data->sheets[0]['cells'][$i][26]));  // new
		// 27 Pays Nat.	
		// 28 Elève No Etab	
		// 29 Num. Elève Etab
		//$params["numEleve"]=$numEleve=$data->sheets[0]['cells'][$i][29];	
		// 30 Nom
		// 31 Prénom	
		// 32 Prénom 2	
		// 33 Prénom 3	
		// 34 Date Naissance	
		// 35 Doublement	
		// 36 Id National	
		$params["numEleve"]=$numEleve=$data->sheets[0]['cells'][$i][36];	
		// 37 Date Entrée	
		// 38 Date Sortie	
		// 39 Adhésion Transport	
		// 40 Tel personnel	
		// 41 Tel professionnel	
		// 42 Tel portable	
		// 43 Email eleve
		$params["maileleve"]=$maileleve=trim($data->sheets[0]['cells'][$i][43]);	
		// 44 Date Modification	
		// 45 Autorisation Abs. Perm.	
		// 46 Autorisation Abs. Temp.	
		// 47 Présence Doss. Médic.	
		// 48 Présence Doss. Scol.	
		// 49 Ville Naiss. Etrangère	
		// 50 Commune Naiss.	
		// 51 Pays Naiss.	
		// 52 Code Régime	
		// 53 Lib. Régime	
		$params["regime"]=$regime=trim($data->sheets[0]['cells'][$i][53]);
		// 54 Motif Sortie	
		// 55 Code Circuit	
		// 56 Lib. Circuit	
		// 57 Code Bourse 1	
		// 58 Lib. Bourse 1	
		// 59 Code Bourse 2	
		// 60 Lib. Bourse 2	
		// 61 Code MEF	
		// 62 Lib. MEF	
		// 63 Code Structure	
		// 64 Type Structure	
		// 65 Lib. Structure	
		// 66 Clé Gestion Mat. Enseignée 1	
		// 67 Lib. Mat. Enseignée 1	
		// 68 Code Modalité Elect. 1	
		// 69 Lib. Modalité Elect. 1	
		// 70 Clé Gestion Mat. Enseignée 2	
		// 71 Lib. Mat. Enseignée 2	
		// 72 Code Modalité Elect. 2	
		// 73 Lib. Modalité Elect. 2	
		// 74 Clé Gestion Mat. Enseignée 3	
		// 75 Lib. Mat. Enseignée 3	
		// 76 Code Modalité Elect. 3	
		// 77 Lib. Modalité Elect. 3	
		// 78 Clé Gestion Mat. Enseignée 4	
		// 79 Lib. Mat. Enseignée 4	
		// 80 Code Modalité Elect. 4	
		// 81 Lib. Modalité Elect. 4	
		// 82 Clé Gestion Mat. Enseignée 5	
		// 83 Lib. Mat. Enseignée 5	
		// 84 Code Modalité Elect. 5	
		// 85 Lib. Modalité Elect. 5	
		// 86 Clé Gestion Mat. Enseignée 6	
		// 87 Lib. Mat. Enseignée 6	
		// 88 Code Modalité Elect. 6	
		// 89 Lib. Modalité Elect. 6	
		// 90 Clé Gestion Mat. Enseignée 7	
		// 91 Lib. Mat. Enseignée 7	
		// 92 Code Modalité Elect. 7	
		// 93 Lib. Modalité Elect. 7	
		// 94 Clé Gestion Mat. Enseignée 8	
		// 95 Lib. Mat. Enseignée 8	
		// 96 Code Modalité Elect. 8	
		// 97 Lib. Modalité Elect. 8	
		// 98 Clé Gestion Mat. Enseignée 9	
		// 99 Lib. Mat. Enseignée 9	
		// 100 Code Modalité Elect. 9	
		// 101 Lib. Modalité Elect. 9	
		// 102 Clé Gestion Mat. Enseignée 10	
		// 103 Lib. Mat. Enseignée 10	
		// 104 Code Modalité Elect. 10	
		// 105 Lib. Modalité Elect. 10	
		// 106 Clé Gestion Mat. Enseignée 11	
		// 107 Lib. Mat. Enseignée 11	
		// 108 Code Modalité Elect. 11	
		// 109 Lib. Modalité Elect. 11	
		// 110 Clé Gestion Mat. Enseignée 12	
		// 111 Lib. Mat. Enseignée 12	
		// 112 Code Modalité Elect. 12	
		// 113 Lib. Modalité Elect. 12

		if (($respLegal == 1) || ($respLegal == 2)) {
			$cr=modif_eleve_sconet($params);
			if ($cr) {
				$nbeleveaffecte++;
			}
			unset($params);
		}
	}

			
		
	 	
	Pgclose();	
	@unlink("data/fichier_gep/traitement1.xls");
?>
<br />
<ul>
<font class="T2">- <?php print "Nombre d'éléments mis à jour "  ?> : <?php print $nbeleveaffecte?><br></font>

<br />
<?php
}else {
?>
<br />
<center> <font color=red><?php print LANGbasededon203?></font> <BR><BR>
<?php print LANGDISP26?>
<br /><br />
<?php print "Information Support : $type" ?>
<br /><br />
<input type=button Value="<?php print LANGBT24 ?>" onclick="javascript:history.go(-1)" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"><br />
<br />
</center>
<?php
}
?>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
</BODY></HTML>
