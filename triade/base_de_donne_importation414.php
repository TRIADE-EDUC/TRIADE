<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
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
	set_time_limit(3000);
}
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGbasededoni91 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<?php
include_once("librairie_php/db_triade.php");
include_once("librairie_php/timezone.php");
validerequete("menuadmin");
validerequete2($_SESSION["adminplus"]);

$reponse = "";

if (file_exists("./data/fichier_gep/traitement.xls")) {
	$fic_xls="./data/fichier_gep/traitement.xls";
	include_once('./librairie_php/reader.php');
	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('UTF-8');
	$data->read($fic_xls);

	for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
		$classe=$data->sheets[0]['cells'][$i][3];
		$id_classe=recherche_gep_classe($classe);
		if ($id_classe != "") {	$nom_classe=chercheClasse_nom($id_classe); }

		$nom=strtolower(trim(addslashes($data->sheets[0]['cells'][$i][5])));
		$prenom=strtolower(trim(addslashes($data->sheets[0]['cells'][$i][6])));
		$date_naissance=dateFormBase($data->sheets[0]['cells'][$i][12]);
		$cr=verifEleveExist($nom,$prenom,$date_naissance);
		if ($cr == "rien") {
			$reponse.="<span style='font-size:12px;color:#555;'>— <strong>".strtoupper($nom)."</strong> ".ucwords($prenom)."</span><br>";
			continue;
		}else{
			$ideleve=$cr;
		}

		$grille=$data->sheets[0]['cells'][$i][2];
		$sexe=$data->sheets[0]['cells'][$i][7];
		$philo=$data->sheets[0]['cells'][$i][17];
		$lv2=$data->sheets[0]['cells'][$i][18];

		if ($sexe == "M") {
			$key=$nom_classe."_garçon";
			$params[$key].=$ideleve.",";
		}
		if ($sexe == "F") {
			$key=$nom_classe."_fille";
			$params[$key].=$ideleve.",";
		}

		$key=$nom_classe."_".$philo;
		$params[$key].=$ideleve.",";

		$key=$nom_classe."_".$lv2;
		$params[$key].=$ideleve.",";

		$key=$nom_classe."_".$grille;
		$params[$key].=$ideleve.",";

	}

	foreach($params as $key => $value)  {
		$value=preg_replace('/,$/','',$value);
		$params['comment']="";
		$params['liste_eleve']=$value;
		$params['nomgr']="$key";
		create_groupe($params,$anneeScolaire);
	}

	@unlink($fic_xls);

	Pgclose();
?>
<?php if ($reponse) : ?>
<div class="na-card">
  <p style="font-size:12px;color:#888;margin:0 0 6px;">Liste des élèves non trouvés :</p>
  <?php print $reponse ?>
</div>
<?php else : ?>
<div class="na-card">
  <p style="font-size:13px;color:#555;margin:0;">Création des groupes terminée.</p>
</div>
<?php endif; ?>
<div class="na-foot">
  <button type="button" class="btn-enr" onclick="location.href='acces2.php'"><?php print LANGBT41 ?></button>
</div>
<?php
}else{
?>
<div style="margin:10px;padding:10px 14px;background:#ffebee;border:1px solid #ef9a9a;border-radius:6px;font-size:13px;color:#c62828;text-align:center;">
  Fichier non existant — veuillez relancer l'importation depuis l'étape 1.
</div>
<div class="na-foot">
  <button type="button" class="btn-retour" onclick="location.href='base_de_donne_importation411.php'">Retour</button>
</div>
<?php
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
?>
</BODY></HTML>
