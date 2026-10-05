<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
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
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print $_SESSION['nom']." ".$_SESSION['prenom'] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
	$profpclasse=$_SESSION["profpclasse"];
	validerequete("menuprof");
}else{
	validerequete("2");
}
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Exportation des rattrapages</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<form method="post" name="formulaire" action="export_rattrapage.php">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGDISC47 ?> :</span>
    <input type="text" name="saisie_date_debut" class="cc-select" style="width:90px;" value="<?php print isset($_POST["saisie_date_debut"]) ? $_POST["saisie_date_debut"] : "" ?>" onKeyPress="onlyChar(event)">
    <?php include_once("librairie_php/calendar.php"); calendar("id1","document.formulaire.saisie_date_debut",$_SESSION["langue"],"0"); ?>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGDISC48 ?> :</span>
    <input type="text" name="saisie_date_fin" class="cc-select" style="width:90px;" value="<?php print isset($_POST["saisie_date_fin"]) ? $_POST["saisie_date_fin"] : "" ?>" onKeyPress="onlyChar(event)">
    <?php calendar("id2","document.formulaire.saisie_date_fin",$_SESSION["langue"],"0"); ?>
  </div>
</div>
<br>
<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:0 5px;">
  <button type="button" class="btn-retour" onclick="open('gestion_abs_retard.php','_parent','')">Retour</button>
  <span style="display:flex;"><script language=JavaScript>buttonMagicSubmit3("Exportation","export","");</script></span>
</div>
<br><br>
</form>

<?php
if (isset($_POST["export"])) {
	print "<hr style='border:none;border-top:1px solid #c5cae9;margin:8px 5px;'>";
	$dateDebut=$_POST["saisie_date_debut"];
	$dateFin=$_POST["saisie_date_fin"];

	require_once "./librairie_php/class.writeexcel_workbook.inc.php";
	require_once "./librairie_php/class.writeexcel_worksheet.inc.php";

	$fichier="./data/fichier_ASCII/export_rattrapage".$_SESSION["id_pers"].".xls";
	@unlink($fichier);

	$workbook = new writeexcel_workbook($fichier);
	$worksheet1 =& $workbook->addworksheet('Listing');

	$header =& $workbook->addformat();
	$header->set_color('white');
	$header->set_align('center');
	$header->set_align('vcenter');
	$header->set_pattern();
	$header->set_fg_color('blue');

	$center =& $workbook->addformat();
	$center->set_align('left');

	$worksheet1->set_selection('A0');
	$intituleeleve=ucwords(INTITULEELEVE);

	$worksheet1->write(0, 0, "Classe", $header);
	$worksheet1->write(0, 1, "$intituleeleve", $header);
	$worksheet1->write(0, 2, "Absent le", $header);
	$worksheet1->write(0, 3, "Durée", $header);
	$worksheet1->write(0, 4, "Motif", $header);
	$worksheet1->write(0, 5, "Rattrapage le Date ", $header);
	$worksheet1->write(0, 6, "Rattrapage le Heure ", $header);
	$worksheet1->write(0, 7, "Rattrapage Durée ", $header);
	$worksheet1->write(0, 8, "Rattrapage Effectué ", $header);
	$worksheet1->write(0, 9, "Rattrapage le Date ", $header);
	$worksheet1->write(0, 10, "Rattrapage le Heure ", $header);
	$worksheet1->write(0, 11, "Rattrapage Durée ", $header);
	$worksheet1->write(0, 12, "Rattrapage Effectué ", $header);
	$worksheet1->write(0, 13, "Rattrapage le Date ", $header);
	$worksheet1->write(0, 14, "Rattrapage le Heure ", $header);
	$worksheet1->write(0, 15, "Rattrapage Durée ", $header);
	$worksheet1->write(0, 16, "Rattrapage Effectué ", $header);

	$data=recupRattrapage(dateFormBase($dateDebut),dateFormBase($dateFin));
	for($i=0;$i<countTriade($data);$i++) {
		$id=$data[$i][0];
		$date=dateForm($data[$i][1]);
		$heure_depart=dateForm($data[$i][2]);
		$duree=timeForm($data[$i][3]);
		$ref_id_absrtd=$data[$i][4];
		$valider=$data[$i][5];
		$tab[$ref_id_absrtd][$id]="$date#$heure_depart#$duree#$valider";
	}

	$A=1;
	foreach($tab as $key=>$value) {
		$ref_id_absrtd=$key;
		$info=recupInfoRattrapageAbs($ref_id_absrtd);
		$ideleve=$info[0][0];
		$absle=dateForm($info[0][1]);
		$duree=$info[0][2];
		$motif=$info[0][3];
		$idmatiere=$info[0][4];
		$justifier=$info[0][5];
		$creneaux=$info[0][6];

		if ($duree == "-1") {
			$duree=$info[0][8]."h";
		}elseif((preg_match('/h/',$duree)) || (preg_match('/mn/',$duree))) {
			// rien
		}else{
			$duree="${duree} j";
		}

		$classe=chercheClasse_nom(chercheClasseEleve($ideleve));
		$intituleeleve=rechercheEleveNomPrenom($ideleve);

		$worksheet1->write($A, 0, "$classe", $center);
		$worksheet1->write($A, 1, "$intituleeleve", $center);
		$worksheet1->write($A, 2, "$absle", $center);
		$worksheet1->write($A, 3, "$duree", $center);
		$worksheet1->write($A, 4, "$motif", $center);

		$B=5;
		foreach($value as $key2=>$value2) {
			list($date,$heure_depart,$duree,$valider)=preg_split('/#/',$value2);
			$date=dateForm($date);
			$heure_depart=timeForm($heure_depart);
			$duree=timeForm($duree);
			$worksheet1->write($A, $B, "$date", $center); $B++;
			$worksheet1->write($A, $B, "$heure_depart", $center); $B++;
			$worksheet1->write($A, $B, "$duree", $center); $B++;
			$valider = ($valider == 1) ? "oui" : "non";
			$worksheet1->write($A, $B, "$valider", $center); $B++;
		}
		$A++;
	}

	$workbook->close();

	print "<div style='text-align:center;padding:8px 5px;'>";
	print "<button type='button' class='btn-enr' onclick=\"open('visu_document.php?fichier=$fichier','_blank','');\">Récupération de l'exportation</button>";
	print "</div>";
}
?>

</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
Pgclose();
?>
</BODY></HTML>
