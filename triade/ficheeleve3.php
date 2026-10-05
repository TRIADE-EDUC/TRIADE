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
<?php include_once("./common/config5.inc.php"); include_once("./common/config2.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta charset="utf-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/scriptaculous.js"></script>
<script type="text/javascript" src="./librairie_js/ajaxNoteVisu.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<style>
#coulBar0 { background-image: none; }
/* Override ancien jaune → design 2026 */
td[bgcolor='yellow'], th[bgcolor='yellow'] {
  background: #f0f2fa !important;
  color: #080A66 !important;
  font-weight: 700 !important;
  font-size: 11px !important;
  padding: 7px 10px !important;
}
/* Override ancien hover tableau */
tr.tabnormal, tr.tabnormal2 { background: #fff; }
tr.tabnormal:hover, tr.tabnormal2:hover, tr.tabover { background: #f5f7ff; }
/* Moderniser bordures anciennes tables */
table[border="1"] { border-collapse: collapse !important; }
table[border="1"] td, table[border="1"] th { border: 1px solid #dde0f0 !important; }
/* Badge status admin */
.fe-status-wrap { display:flex; flex-wrap:wrap; gap:6px; align-items:center; margin:8px 0 4px; }
.fe-status-warn { display:inline-flex; align-items:center; gap:5px; background:#fff3e0; border:1px solid #ffb74d; color:#e65100; border-radius:5px; padding:5px 10px; font-size:11px; font-weight:700; }
.fe-status-lock { display:inline-flex; align-items:center; gap:5px; background:#fce4e4; border:1px solid #ef9a9a; color:#c62828; border-radius:5px; padding:5px 10px; font-size:11px; font-weight:700; }
/* Message accès refusé */
.fe-no-access { text-align:center; padding:20px; font-size:12px; color:#888; font-style:italic; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
include_once("librairie_php/recupnoteperiode.php");
$cnx=cnx();
validerequete("7");
if ($_SESSION["membre"] == "menupersonnel") {
	if ((!verifDroit($_SESSION["id_pers"],"ficheeleve")) && (!verifDroit($_SESSION["id_pers"],"AESH"))) {
		Pgclose();
		accesNonReserveFen();
		exit();
	}
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<?php
$id_eleve=$_GET["eid"];
$saisie_classe=$_GET["idclasse"];
$_SESSION["pageretour"]="ficheeleve3.php?eid=$id_eleve&idclasse=$saisie_classe";

if (trim($saisie_classe) == "") {
	$saisie_classe=chercheIdClasseDunEleve($id_eleve);
}

$disabledSMS="disabled";
$showSMS=false;
$smsActif=false;

if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
	$showSMS=true;
	if ((LAN == "oui") && (file_exists("./common/config-sms.php"))) {
		$disabledSMS="";
		$smsActif=true;
	}
}

$disabledprojo="1";
if (( ((defined("NOTEELEVEVISU")) && (NOTEELEVEVISU == "oui")) && ($_SESSION["membre"] == "menuprof")) || ($_SESSION["membre"] == "menuadmin")||($_SESSION["membre"]=="menupersonnel")) {
		$disabledprojo="0";
}
if ($saisie_classe == "") $saisie_classe=chercheIdClasseDunEleve($id_eleve);
?>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%"  bgcolor="#0B3A0C"  height="1130">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print LANGPROF26 ?> <font id="color2" ><?php print recherche_eleve($id_eleve);?></font></B></font></td></tr>
<tr id='cadreCentral0' valign='top' ><td>
<br>

<?php
/* ── Calcul des variables admin (avant l'HTML) ── */
$img = $img2 = $brimg = '';
$bouton = $bouton2 = $bouton33 = '';
$inactifval = $probaval = $etatmessagerie = '';

if ($_SESSION["membre"] == "menuadmin") {
	if (isset($_GET["val"])) { inactifEleve($id_eleve,$_GET["val"]); }
	$inactif=getInactifEleve($id_eleve);
	if ($inactif == "1") {
		$bouton=LANGMESS255;
		$inactifval="0";
		$img="<span class='fe-status-warn'><i class='bi bi-exclamation-triangle-fill'></i><b>".LANGMESST393."</b></span>";
	}else{
		$bouton=LANGMESS254;
		$inactifval="1";
	}

	if (isset($_GET["proba"])) { ProbatoireEleve($id_eleve,$_GET["proba"]); }
	$inactifProba=getProbaEleve($id_eleve);
	if ($inactifProba == "1") {
		$bouton2=LANGMESST395;
		$probaval="0";
		$img2="<span class='fe-status-warn'><i class='bi bi-exclamation-triangle-fill'></i><b>".LANGMESST394."</b></span>";
	}else{
		$bouton2=LANGMESST396;
		$probaval="1";
	}

	$etatmessagerie="inactif";
	$bouton33="Bloquer messagerie";
	$libelle="acces_mail_$id_eleve";
	if (isset($_GET['messagerie'])) {
		$valeur=$_GET['messagerie'];
		$info=dateDMY();
		if ($valeur == "inactif") {
			enr_parametrage($libelle,$valeur,$info);
			$bouton33="Débloquer messagerie";
			$etatmessagerie="actif";
		}else{
			supp_parametrage($libelle);
		}
	}

	$data33=aff_structure($libelle);
	$valeur=$data33[0][1];
	if ($valeur == "inactif") {
		$bouton33="Débloquer messagerie";
		$etatmessagerie="actif";
		$img2.="<span class='fe-status-lock'><i class='bi bi-lock-fill'></i>Messagerie bloquée</span>";
	}

	if (($img != "") || ($img2 != "")) {
		$brimg="1";
	}
}
?>

<!-- Barre d'actions -->
<div class="toolbar flex-wrap" style="margin-bottom:8px; align-items:center;">
  <form method="post" action="ficheeleve2.php" style="display:inline; margin:0; line-height:0;">
    <script language=JavaScript>buttonMagicSubmit("<?php print LANGMESS250 ?>","rien");</script>
    <input type="hidden" name="sClasseGrp" value="<?php print $saisie_classe?>" />
    <input type="hidden" name="anneeScolaire" value="<?php print anneeScolaireViaIdClasse($saisie_classe) ?>" />
  </form>
<?php if ($showSMS): ?>
  <?php if ($smsActif): ?>
  <form method='get' action="sms-mess.php" style="display:inline; margin:0; line-height:0;">
    <script language=JavaScript>buttonMagicSubmit3("<?php print LANGMESS251 ?>","sms","")</script>
    <input type='hidden' name="eid" value="<?php print $id_eleve ?>" />
  </form>
  <?php else: ?>
  <input type="button" value="<?php print LANGMESS251 ?>" class="button"
    onclick="alertify.error('Le service SMS n\'est pas activé sur cet établissement.');">
  <?php endif; ?>
<?php endif; ?>
<?php if ($disabledprojo == "0"): ?>
  <script language=JavaScript>buttonMagic2("<?php print LANGPROF38 ?>","profpprojo.php?fiche=1&idClasse=<?php print $saisie_classe?>","video","width=800,height=600,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes","0");</script>
<?php else: ?>
  <input type="button" value="<?php print LANGPROF38 ?>" class="btn btn-primary"
    onclick="alertify.error('Ce service n\'est pas activé. Veuillez contacter votre administrateur TRIADE.');">
<?php endif; ?>
  <?php if (($_SESSION["membre"] == "menuadmin") || ((defined("VIESCOLAIREMODIFETUDIANT")) && (VIESCOLAIREMODIFETUDIANT == "oui") && ($_SESSION["membre"] == "menuscolaire"))) { ?>
  <form method='get' action="modif_eleve.php" style="display:inline; margin:0; line-height:0;">
    <script language=JavaScript>buttonMagicSubmit3("<?php print LANGMESS252 ?>","fiche","")</script>
    <input type='hidden' name="eid" value="<?php print $id_eleve ?>" />
  </form>
  <?php } ?>
  <?php if ((defined("CARNETSUIVIPROF")) && (CARNETSUIVIPROF == "oui") && ($_SESSION["membre"] == "menuprof")) { ?>
    <script language=JavaScript>buttonMagic("<?php print LANGMESST392 ?>","carnet_editer.php?sClasseGrp=<?php print $saisie_classe?>","_parent","","");</script>
  <?php } ?>
  <?php if ($_SESSION["membre"] == "menuadmin") { ?>
    <script language=JavaScript>buttonMagic2('<?php print $bouton?>','ficheeleve3.php?eid=<?php print $id_eleve?>&val=<?php print $inactifval?>','_self','')</script>
    <script language=JavaScript>buttonMagic2('<?php print $bouton2 ?>','ficheeleve3.php?eid=<?php print $id_eleve?>&proba=<?php print $probaval?>','_self','')</script>
    <script language=JavaScript>buttonMagic2('<?php print "Affecter stage"?>','gestion_stage_affec_eleve_2.php?id=<?php print $id_eleve?>&idclasse=<?php print $saisie_classe ?>','_self','')</script>
    <script language=JavaScript>buttonMagic2('<?php print "Impression"?>','impressionficheeleve.php?id=<?php print $id_eleve?>&idclasse=<?php print $saisie_classe ?>','_self','')</script>
    <script language=JavaScript>buttonMagic2('<?php print $bouton33 ?>','ficheeleve3.php?eid=<?php print $id_eleve?>&messagerie=<?php print $etatmessagerie ?>','_self','')</script>
  <?php } ?>
</div>
<?php if ($brimg): ?>
<div class="fe-status-wrap"><?php echo $img.' '.$img2; ?></div>
<?php endif; ?>

<div class="fe-main-tabs">
  <div class="fe-main-tab active" data-panel="fe-panel-0"><i class="bi bi-person-lines-fill"></i> <?php print LANGMESS259 ?></div>
  <div class="fe-main-tab" data-panel="fe-panel-1"><i class="bi bi-journal-bookmark-fill"></i> <?php print LANGMESS260 ?></div>
  <div class="fe-main-tab" data-panel="fe-panel-2"><i class="bi bi-calendar2-check"></i> <?php print LANGMESS261 ?></div>
  <div class="fe-main-tab" data-panel="fe-panel-3"><i class="bi bi-shield-exclamation"></i> <?php print LANGMESS262 ?></div>
  <div class="fe-main-tab" data-panel="fe-panel-4"><i class="bi bi-star-half"></i> Savoir &amp; Etre</div>
  <div class="fe-main-tab" data-panel="fe-panel-5"><i class="bi bi-clock-history"></i> Classes ant.</div>
</div>
<div class="fe-panels">

  <?php // Renseignements ?>
  <div id="fe-panel-0" class="fe-panel active">
<?php
$sql=<<<EOF
SELECT
	elev_id,
	nom,
	prenom,
	c.libelle,
	lv1,
	lv2,
	`option`,
	regime,
	date_naissance,
	lieu_naissance,
	nationalite,
	passwd,
	passwd_eleve,
	civ_1,
	nomtuteur,
	prenomtuteur,
	adr1,
	code_post_adr1,
	commune_adr1,
	tel_port_1,
	civ_2,
	nom_resp_2,
	prenom_resp_2,
	adr2,
	code_post_adr2,
	commune_adr2,
	tel_port_2,
	telephone,
	profession_pere,
	tel_prof_pere,
	profession_mere,
	tel_prof_mere,
	nom_etablissement,
	numero_etablissement,
	code_postal_etablissement,
	commune_etablissement,
	numero_eleve,
	email,
	email_eleve,
	class_ant,
	annee_ant,
	tel_eleve,
	email_resp_2,
	sexe,
	code_compta,
	information,
	adr_eleve,
	commune_eleve,
	ccp_eleve,
	tel_fixe_eleve,
	boursier,
	montant_bourse,
	indemnite_stage,
	emailpro_eleve,
	rangement,
	cdi,
	bde,
	situation_familiale,
	annee_scolaire,
	serie_bac,
	annee_bac,
	departement_bac,
	departementnais,
	ine
FROM
	{$prefixe}eleves, {$prefixe}classes c
WHERE
	elev_id='$id_eleve'
AND	c.code_class=classe

EOF;
$res=execSql($sql);
$data=chargeMat($res);

$idEleve=$id_eleve;
$nom=$data[0][1];
$prenom=$data[0][2];
$classe=$data[0][3];
$boursier=($data[0][50] == 0) ? LANGNON : LANGOUI ;
$lv1=$data[0][4];
$lv2=$data[0][5];
$option=$data[0][6];
$regime=$data[0][7];
$date_naissance=$data[0][8];
$lieu_naissance=$data[0][9];
$nationalite=$data[0][10];
$numero_eleve=$data[0][36];

/*
13	civ_1,
14	nomtuteur,
15	prenomtuteur,
16	adr1,
17	code_post_adr1,
18	commune_adr1,
19	tel_port_1,
20	civ_2,
21	nom_resp_2,
22	prenom_resp_2,
23	adr2,
24	code_post_adr2,
25	commune_adr2,
26	tel_port_2,
27	telephone,
28	profession_pere,
29	tel_prof_pere,
30	profession_mere,
31	tel_prof_mere,
 */
$civ_1=$data[0][13];
$nomtuteur=$data[0][14];
$prenomtuteur=$data[0][15];
$adr1=$data[0][16];
$code_post_adr1=$data[0][17];
$commune_adr1=$data[0][18];
$tel_port_1=$data[0][19];
$civ_2=$data[0][20];
$nom_resp_2=$data[0][21];
$prenom_resp_2=$data[0][22];
$adr2=$data[0][23];
$code_post_adr2=$data[0][24];
$commune_adr2=$data[0][25];
$tel_port_2=$data[0][26];
$telephone=$data[0][27];
$profession_pere=$data[0][28];
$tel_prof_pere=$data[0][29];
$profession_mere=$data[0][30];
$tel_prof_mere=$data[0][31];

/*
37	email,
38	email_eleve,
39	class_ant,
40	annee_ant,
41	tel_eleve,
42	email_resp_2,
43	sexe,
44	code_compta,
45	information,
46	adr_eleve,
47	commune_eleve,
48	ccp_eleve,
49	tel_fixe_eleve,
50	boursier,
51	montant_bourse,
52	indemnite_stage,
53	emailpro_eleve
54	rangement
55 	cdi,
56	bde,
57	situation_familiale
*/
$email=$data[0][37];
$email_eleve=$data[0][38];
$class_ant=$data[0][39];
$annee_ant=$data[0][40];
$tel_eleve=$data[0][41];
$email_resp_2=$data[0][42];
$sexe=$data[0][43];
$code_compta=$data[0][44];
$information=$data[0][45];
$adr_eleve=$data[0][46];
$commune_eleve=$data[0][47];
$ccp_eleve=$data[0][48];
$tel_fixe_eleve=$data[0][49];
$emailpro_eleve=$data[0][53];
$rangement=$data[0][54];
$cdi=($data[0][55] == 0) ? LANGNON : LANGOUI ;
$bde=($data[0][56] == 0) ? LANGNON : LANGOUI ;
$lv2=($lv2 == "NULL") ? "" : $lv2 ;
$lv1=($lv1 == "NULL") ? "" : $lv1 ;
$situation_familiale=$data[0][57];
$annee_scolaire_eleve=$data[0][58];
$serie_bac=$data[0][59];
$annee_bac=$data[0][60];
$departement_bac=$data[0][61];
$departementnais=$data[0][62];
$ine=$data[0][63];

?>

<div class="fe-info-wrap">
<div class="fe-photo-card">
	<img src="image_trombi.php?idE=<?php print $id_eleve ?>" border='0' style='box-shadow:0 4px 12px rgba(0,0,0,0.3);border-radius:8px'><br><br>[ <a href="#" class="bouton2" onclick="open('photoajouteleve.php?ideleve=<?php print $idEleve?>','photo','width=450,height=310')"><?php print LANGPER30 ?></a> ]
</div><table class="fe-info-table">
<tr class="fe-tab-tr"><td class="fe-label"><?php print LANGMESS270 ?></td><td class="fe-value"><b><?php print $nom ?></b></td></tr>
<tr class="fe-tab-tr"><td class="fe-label"><?php print LANGMESS271 ?></td><td class="fe-value"><?php print $prenom ?></td></tr>
<tr class="fe-tab-tr"><td class="fe-label"><?php print LANGMESS272 ?></td><td class="fe-value" title="<?php print $classe?>"><?php print trunchaine($classe,35) ?></td></tr>
<tr class="fe-tab-tr"><td class="fe-label">Année Scolaire :</td><td class="fe-value"><?php print $annee_scolaire_eleve ?></td></tr>
<tr class="fe-tab-tr"><td class="fe-label"><?php print LANGMESS273 ?></td><td class="fe-value"><?php print dateForm($date_naissance) ?></td></tr>
<tr class="fe-tab-tr"><td class="fe-label"><?php print LANGMESS274 ?></td><td class="fe-value"><?php print $nationalite ?></td></tr>
<tr class="fe-tab-tr"><td class="fe-label"><?php print LANGMESS275 ?></td><td class="fe-value"><?php print $lieu_naissance ?></td></tr>
<tr class="fe-tab-tr"><td class="fe-label">Département de naissance :</td><td class="fe-value"><?php print $departementnais ?></td></tr>
<tr class="fe-tab-tr"><td class="fe-label"><?php print LANGMESS276 ?></td><td class="fe-value"><?php print $boursier ?>&nbsp;/&nbsp;CDI&nbsp;:&nbsp;<?php print $cdi ?>&nbsp;/&nbsp;BDE&nbsp;:&nbsp;<?php print $bde ?></td></tr>
<tr class="fe-tab-tr"><td class="fe-label"><?php print LANGMESS277 ?></td><td class="fe-value"><?php print $numero_eleve ?></td></tr>
<tr class="fe-tab-tr"><td class="fe-label">Numéro INE :</td><td class="fe-value"><?php print $ine ?></td></tr>
<tr class="fe-tab-tr"><td class="fe-label"><?php print LANGMESS278 ?></td><td class="fe-value"><?php print $lv1 ?></td></tr>
<tr class="fe-tab-tr"><td class="fe-label"><?php print LANGMESS279 ?></td><td class="fe-value"><?php print $lv2 ?></td></tr>
<tr class="fe-tab-tr"><td class="fe-label"><?php print LANGMESS280 ?></td><td class="fe-value"><?php print $option ?></td></tr>
<tr class="fe-tab-tr"><td class="fe-label"><?php print LANGMESS281 ?></td><td class="fe-value"><?php print $regime ?></td></tr>
<tr class="fe-tab-tr"><td class="fe-label"><?php print LANGMESS282 ?></td><td class="fe-value"><?php print $rangement ?></td></tr>
<?php
$texte=recupIdCodeBar($id_eleve,"menueleve");
if ($texte != "") {
	$infoT=LANGASS39;
	print "<tr class='fe-tab-tr'><td class='fe-label'>$infoT :</td><td class='fe-value'><img src='./codebar/image.php?code=code39&text=$texte' /></td></tr>";
}
?>
</table>
</div>
	<hr style="border:none;border-top:1px solid #dde0f0;margin:12px 0">
<div class="fe-sub-section">
<div class="fe-sub-tabs">
  <div class="fe-sub-tab active" data-sub="fe-sub-0"><i class="bi bi-person-fill"></i> <?php print preg_replace('/^info\.?\s*/i','',LANGMESS264) ?></div>
  <div class="fe-sub-tab" data-sub="fe-sub-1"><i class="bi bi-person-fill"></i> <?php print preg_replace('/^info\.?\s*/i','',LANGMESS265) ?></div>
  <div class="fe-sub-tab" data-sub="fe-sub-2"><i class="bi bi-mortarboard-fill"></i> <?php print preg_replace('/^info\.?\s*/i','',LANGMESS266) ?></div>
  <div class="fe-sub-tab" data-sub="fe-sub-3"><i class="bi bi-briefcase-fill"></i> Tuteur stage</div>
  <div class="fe-sub-tab" data-sub="fe-sub-4"><i class="bi bi-clock-history"></i> Historie Stage</div>
  <div class="fe-sub-tab" data-sub="fe-sub-5"><i class="bi bi-archive-fill"></i> <?php print LANGMESS267 ?></div>
  <div class="fe-sub-tab" data-sub="fe-sub-6"><i class="bi bi-heart-pulse-fill"></i> Médic.</div>
  <div class="fe-sub-tab" data-sub="fe-sub-7">Info.</div>
</div>
  <div id="fe-sub-0" class="fe-sub-panel active">
		<table class="fe-tab-table">
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS283 ?></td><td class="fe-tab-value"><b><?php print civ($civ_1)." ".$nomtuteur." ".$prenomtuteur ?></b></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS284 ?></td><td class="fe-tab-value"><?php print $situation_familiale ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS285 ?></td><td class="fe-tab-value"><?php print $adr1 ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS287 ?></td><td class="fe-tab-value"><?php print $code_post_adr1 ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS288 ?></td><td class="fe-tab-value"><?php print $commune_adr1 ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS289 ?></td><td class="fe-tab-value"><?php print $email ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS290 ?></td><td class="fe-tab-value"><?php print "$telephone / $tel_port_1" ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS291 ?></td><td class="fe-tab-value"><?php print $profession_pere ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS292 ?></td><td class="fe-tab-value"><?php print $tel_prof_pere ?></td></tr>
		</table>
		</div>
  <div id="fe-sub-1" class="fe-sub-panel">
		<table class="fe-tab-table">
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS283 ?></td><td class="fe-tab-value"><b><?php print civ($civ_2)." ".$nom_resp_2." ".$prenom_resp_2 ?></b></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS284 ?></td><td class="fe-tab-value"><?php print $situation_familiale ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS285 ?></td><td class="fe-tab-value"><?php print $adr2 ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS287 ?></td><td class="fe-tab-value"><?php print $code_post_adr2 ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS288 ?></td><td class="fe-tab-value"><?php print $commune_adr2 ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS289 ?></td><td class="fe-tab-value"><?php print $email_resp_2 ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS290 ?></td><td class="fe-tab-value"><?php print "$tel_port_2" ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS291 ?></td><td class="fe-tab-value"><?php print $profession_mere ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS292 ?></td><td class="fe-tab-value"><?php print $tel_prof_mere ?></td></tr>
		</table>
		</div>
  <div id="fe-sub-2" class="fe-sub-panel">
		<table class="fe-tab-table">
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS283 ?></td><td class="fe-tab-value"><b><?php print $nom." ".$prenom ?></b></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS285 ?></td><td class="fe-tab-value"><b><?php print $adr_eleve ?></b></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS287 ?></td><td class="fe-tab-value"><?php print $code_post_adr1 ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS288 ?></td><td class="fe-tab-value"><?php print $commune_adr1 ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS289 ?></td><td class="fe-tab-value"><?php print "$email_eleve / $emailpro_eleve" ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS290 ?></td><td class="fe-tab-value"><?php print "$tel_eleve / $tel_fixe_eleve" ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS293 ?></td><td class="fe-tab-value"><?php print $sexe ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS294 ?></td><td class="fe-tab-value"><?php print $class_ant ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label">Série du bac :</td><td class="fe-tab-value"><?php print $serie_bac ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label">Année du bac :</td><td class="fe-tab-value"><?php print $annee_bac ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label">Département du bac :</td><td class="fe-tab-value"><?php print $departement_bac ?></td></tr>
		</table>
		</div>
  <div id="fe-sub-3" class="fe-sub-panel">
                <?php
		$dataTuteur=recupInfoTuteurStage($id_eleve);
		// nom,prenom,civ,email,adr,code_post,commune,tel,tel_port,id_societe_tuteur
		$nomTuteurStage=$dataTuteur[0][0];
		$prenomTuteurStage=$dataTuteur[0][1];
		$civTuteur=civ($dataTuteur[0][2]);
		$societeTuteurStage=recherche_entr_nom_via_id($dataTuteur[0][9]);
		$adr_TuteurStage=$dataTuteur[0][4];
		$ccp_TuteurStage=$dataTuteur[0][5];
		$commune_TuteurStage=$dataTuteur[0][6];
		$email_TuteurStage=$dataTuteur[0][3];
		$tel_TuteurStage=$dataTuteur[0][7];
		$telPort_TuteurStage=$dataTuteur[0][8];
		?>
		<table class="fe-tab-table">
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS283 ?></td><td class="fe-tab-value"><b><?php print $civTuteur." ".$nomTuteurStage." ".$prenomTuteurStage ?></b></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGAGENDA61 ?>&nbsp;:</td><td class="fe-tab-value"><b><?php print $societeTuteurStage ?></b></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGAGENDA63 ?>&nbsp;:</td><td class="fe-tab-value"><b><?php print $adr_TuteurStage ?></b></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS287 ?></td><td class="fe-tab-value"><?php print $ccp_TuteurStage ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS288 ?></td><td class="fe-tab-value"><?php print $commune_TuteurStage ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS289 ?></td><td class="fe-tab-value"><?php print "$email_TuteurStage" ?></td></tr>
		<tr class="fe-tab-tr"><td class="fe-tab-label"><?php print LANGMESS290 ?></td><td class="fe-tab-value"><?php print "$tel_TuteurStage / $telPort_TuteurStage" ?></td></tr>
                </table>
                </div>

  <div id="fe-sub-4" class="fe-sub-panel">
		<?php
		$data=recherche_stage_historique($id_eleve); //e.nom,s.nomprenomeleve,s.classeeleve,s.periodestage
		?>
		<table class="table" style="font-size:11px">
		<thead><tr>
		  <th class="cc-th" style="width:5%">Période</th>
		  <th class="cc-th">Classe</th>
		  <th class="cc-th"><?php print LANGSTAGE39?></th>
		</tr></thead>
		<tbody>
		<?php
		for($i=0;$i<countTriade($data);$i++) {
		        $nom_entreprise=$data[$i][0];
		        $periode=preg_replace('/ /','&nbsp;',$data[$i][3]);
		        $classe=$data[$i][2];
		        print "<tr class='cc-tr-data'>";
		        print "<td>&nbsp;$periode&nbsp;</td>";
		        print "<td>&nbsp;$classe&nbsp;</td>";
		        print "<td>&nbsp;<a href='gestion_stage_ent_visu_rech_nom.php?recherche=$nom_entreprise' title='Consulter'>$nom_entreprise</a>&nbsp;</td>";
		        print "</tr>";
		}
		?>
		</tbody></table>
		</div>

  <div id="fe-sub-5" class="fe-sub-panel">
		<?php
		$databull=recupArchiveBulletin($idEleve); //  ideleve,anneescolaire,trimestre,date,classe,file
		?>
		<table class="table" style="font-size:11px">
		<thead><tr>
		  <th class="cc-th"><?php print LANGMESS295 ?></th>
		  <th class="cc-th"><?php print LANGELE4 ?></th>
		  <th class="cc-th"><?php print LANGMESS296 ?></th>
		  <th class="cc-th"><?php print LANGMESS297 ?></th>
		  <th class="cc-th"><?php print LANGMESS298 ?></th>
		</tr></thead>
		<tbody>
		<?php

		if (isset($_GET["supp"])) {
			if ($_SESSION["membre"] == "menuadmin") {
				if (file_exists($ficsupp)) @unlink($ficsupp);
			}
		}

		for($j=0;$j<countTriade($databull);$j++) {
			$fichierarc=$databull[$j][5];
			$fichierarc=preg_replace('/\'/',"",$fichierarc);

			if (file_exists($fichierarc)) {
				$lien="<a href='visu_document.php?fichier=$fichierarc' target='_blank' ><img src='image/commun/download.png' title='".LANGTELECHARGE."' border='0' /></a>";
				if ($_SESSION["membre"] == "menuadmin") $lien.="&nbsp;<a href='ficheeleve3.php?eid=$idEleve&idclasse=$saisie_classe&supp=$fichierarc' ><img src='image/commun/trash.png' title='".LANGBT50."' border='0' /></a>";
				print '<tr class="cc-tr-data">';
				print '<td>'.$databull[$j][1].'</td>';
				print '<td>'.preg_replace('/_/','&nbsp;',$databull[$j][4]).'</td>';
				print '<td>'.$databull[$j][2].'</td>';
				print '<td>'.$lien.'</td>';
				print '<td>'.dateForm($databull[$j][3]).'</td>';
				print '</tr>';
			}else{
				suppArchiveBulletinEleve($fichierarc);
			}
		}
		?>
		</tbody></table>
		</div>

		<?php // info medic ?>
  <div id="fe-sub-6" class="fe-sub-panel">
		<?php
		if ( ((defined("INFOMEDIC")) && (INFOMEDIC == "oui")) || ($_SESSION["membre"] == "menuadmin" ) || ($_SESSION["membre"] == "menupersonnel") ) {
			print "<div style='font-size:12px;font-weight:700;color:#080A66;margin-bottom:8px'>".LANGPROF29." :</div>";
			$data=profPmedAff($idEleve);
			// id,date,ideleve,nomProf,commentaire
			print "<table class='table' style='font-size:11px'>";
			print "<thead><tr>
				<th class='cc-th' style='width:5%'>".LANGTE7."</th>
				<th class='cc-th' style='width:30%'>".LANGMESST397."</th>
				<th class='cc-th'>".LANGSTAGE37."</th>
				</tr></thead><tbody>";
			for($i=0;$i<countTriade($data);$i++) { ?>
				<tr class="cc-tr-data">
				<td valign='top'><?php print dateForm($data[$i][1]) ?></td>
				<td valign='top'><?php print $data[$i][3]?></td>
				<td valign='top'><?php print stripslashes($data[$i][4])?>&nbsp;&nbsp;</td>
				</tr>
			<?php
			}
			print "</tbody></table>";
		}else{ ?>
			<div class="fe-no-access"><?php print LANGMESS308 ?></div>
		<?php
		}
		?>
		</div>
  <div id="fe-sub-7" class="fe-sub-panel">
		<?php
		if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menupersonnel")){
			print "<br><script language=JavaScript>buttonMagic('".LANGMESS309."','profpcomplement.php?eid=$idEleve','_parent','','');</script><br>";
		}
		?>
		<br>
		<?php
			$data=profPinfoAff($idEleve);
		// id,date,idEleve,nomProf,commentaire
			print "<table class='table' style='font-size:11px'>";
			print "<thead><tr>
				<th class='cc-th' style='width:5%'>".LANGTE7."</th>
				<th class='cc-th' style='width:30%'>".LANGMESST397."</th>
				<th class='cc-th'>".LANGSTAGE37."</th>
				</tr></thead><tbody>";
			for($i=0;$i<countTriade($data);$i++) {
			?>
				<tr class="cc-tr-data">
				<td valign='top'><?php print dateForm($data[$i][1]) ?></td>
				<td valign='top'><?php print $data[$i][5]?></td>
				<td valign='top'><?php print $data[$i][4]?>&nbsp;&nbsp;</td>
				</tr>
			<?php
			}
		?>
		</tbody></table>
		</div>
</div>
  </div>


  <div id="fe-panel-1" class="fe-panel">
<?php
	if (  (((defined("NOTEELEVEVISU")) && (NOTEELEVEVISU == "oui")) && ($_SESSION["membre"] == "menuprof"))
		|| ( ($_SESSION["membre"] == "menuprof") && (ENTRETIENPROF == "oui") )
		|| ($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menupersonnel")
		|| (($_SESSION["membre"] == "menuscolaire") && (VIESCOLAIRENOTEENSEIGNANT == "oui"))
	   ) { ?>
		<div id='visunote'></div>
		<div style="margin:6px 0">
		<form method="get" action="entretien2.php" style="display:inline">
		<script language=JavaScript>buttonMagicSubmit("<?php print LANGMESS310 ?>","rien");</script>
		<input type="hidden" name="idclasse" value="<?php print $saisie_classe?>" />
		<input type="hidden" name="eid" value="<?php print $id_eleve?>" />
		</form>
		</div>

		<script>ajaxVisuNote('<?php print $id_eleve ?>','<?php print $saisie_classe ?>','','')</script>
<?php	}else{ ?>
		<div class="fe-no-access"><?php print LANGMESS308 ?></div>
<?php   } ?>
  </div>


  <?php // vie scolaire ?>
  <div id="fe-panel-2" class="fe-panel">
		<?php if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") ) {  ?>
			<div class="toolbar" style="margin-bottom:8px">
			<form method="post" action="gestion_abs_retard_planifier.php" style="display:inline">
			<script language=JavaScript>buttonMagicSubmit("<?php print LANGMESS311 ?>","rien");</script>
			<input type="hidden" name="saisie_nom_eleve" value="<?php print recherche_eleve_nom($id_eleve) ?>"  />
			</form>
			<form method="post" action="gestion_abs_retard_modif_donne.php" style="display:inline">
			<script language=JavaScript>buttonMagicSubmit("<?php print LANGMESS312 ?>","rien");</script>
			<input type="hidden" name="saisie_nom_eleve" value="<?php print recherche_eleve_nom($id_eleve) ?>"  />
			</form>
			<form method="post" action="gestion_abs_retard_modif.php" style="display:inline">
			<script language=JavaScript>buttonMagicSubmit("<?php print LANGMESS313 ?>","rien");</script>
			<input type="hidden" name="saisie_nom_eleve" value="<?php print recherche_eleve_nom($id_eleve) ?>"  />
			</form>
			</div>
		<?php }
			if ((ACCESPROFVISUABSRTD == "oui") ||  ($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") ||  ($_SESSION["membre"] == "menupersonnel")) {
				$sql="SELECT c.libelle,e.nom,e.prenom,e.elev_id FROM {$prefixe}eleves e, {$prefixe}classes c WHERE e.elev_id='$idEleve' AND c.code_class=e.classe ORDER BY c.libelle, e.nom, e.prenom";
				$res=execSql($sql);
				$data=chargeMat($res);
				for($i=0;$i<countTriade($data);$i++) { ?>
					<table border="0" width="100%" style="margin-bottom:4px">
					<tr>
					<td bgcolor="#FFFFFF" width=55%><?php print LANGTP1 ?> : <B><?php print ucwords(trim($data[$i][1]))?></b></td>
					<td bgcolor="#FFFFFF"><?php print LANGCALEN7 ?> : <font color=red><?php print trim($data[$i][0])?></font>
					</td></tr>
					<tr>
					<td bgcolor="#FFFFFF"><?php print LANGTP2 ?> : <b><?php print ucwords(trim($data[$i][2]))?></b></td>
					<td bgcolor="#FFFFFF"> <?php print LANGABS62 ?></td>
					</tr>
					</table>
					<table border="1" width="100%" style="border-collapse:collapse;margin-bottom:6px">
					<thead><TR>
					<TH class="cc-th" width=15%><?php print LANGABS13 ?></TH>
					<TH class="cc-th" width=20%><?php print LANGPARENT17 ?></TH>
					<TH class="cc-th" width=15%><?php print LANGABS60 ?></TH>
					<TH class="cc-th" width=20%><?php print LANGABS12 ?></TH>
					</TR></thead>
					<tbody>
					<?php
					$data_2=affRetard($data[$i][3]);
					// elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier, heure_saisie, creneaux

					for($j=0;$j<countTriade($data_2);$j++) {
						list($creneaux,$debcre,$fincre)=preg_split('/#/',$data_2[$j][10]);
						$matiere=chercheMatiereNom($data_2[$j][7]);
						if (($matiere == "") || ($matiere < 0)) { $matiere="";  } ?>
							<TR class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
							<form name="formulaire_<?php print $i.$j?>" >
							<TD align=center valign=top><?php print date_jour(dateForm($data_2[$j][2])); ?>  <br>
							<?php  print dateForm($data_2[$j][2])?>
							</td>
							<TD  align=center valign=top><?php print timeForm($data_2[$j][1])." ".$creneaux ?> <br> <?php print "  ($debcre - $fincre)" ?> <br>(<?php print trunchaine($matiere,11) ?>) </td>
							<TD  align=center valign=top>
							<select name="saisie_duree_<?php print $i?>" >
							<option STYLE='color:#000066;background-color:#FCE4BA'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							</select>
							<input type=hidden onfocus=this.blur() name="saisie_duree_retourner_<?php print $i?>" value="<?php print $data_2[$j][5]?>"  >
							<?php
							$yy=$data_2[$j][5];
							if ($data_2[$j][5] == 0) { $yy="???"; }
							?>
							<script langage=Javascript>
							chargement_pendant('<?php print trim($yy)?>','<?php print $i?>','<?php print $i.$j?>');
							</script>
							</td>
							<TD  valign=top>
							<?php
							$motiftext=$data_2[$j][6] ;
							if ($data_2[$j][6] == "inconnu") { $motiftext=LANGINCONNU; }
							$motiftext=preg_replace('/"/'," ",$motiftext);
							?>
							<input type=text name="saisie_modif_<?php print $i?>" value="<?php print $motiftext ?>" size=10 readonly >
							(&nbsp;<input type=checkbox name="saisie_justifier_<?php print $i?>" value="1" disabled <?php if ($data_2[$j][8] == 1) { print "checked='checked'"; } ?> > Justifié)
							</td>
							</form>
							</TR>
						<?php
    					}
						?>
						</tbody></table>
						<br>
						<table border="1" width="100%" style="border-collapse:collapse;margin-bottom:12px">
						<thead><TR>
						<TH class="cc-th" width=15%><?php print LANGPARENT8 ?></TH>
						<TH class="cc-th" width=15%><?php print LANGABS60 ?></TH>
						<TH class="cc-th" width=20%>&nbsp;<?php
						if ($_SESSION["membre"] == "menuprof") {
							print "Créneau&nbsp;";
						}else{
							print LANGGRP29bis."&nbsp;";
						}
						?></TH>
						<TH class="cc-th" width=20%><?php print LANGABS12 ?></TH>
						</TR></thead>
						<tbody>
						<?php
						$data_3=affAbsence($data[$i][3]);
						//    elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif,  duree_heure, id_matiere, time, justifier, heure_saisie, heuredabsence, creneaux
						for($j=0;$j<countTriade($data_3);$j++) {
							list($creneaux,$debcre,$fincre)=preg_split('/#/',$data_3[$j][13]);
						?>
						<TR class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
						<form  name="formulaire_3_<?php print $i.$j?>" >
						<TD  align=center valign=top><?php print date_jour(dateForm($data_3[$j][1])); ?><br><?php print dateForm($data_3[$j][1])?></td>
						<TD  align=center valign=top>
						<select name="saisie_duree_<?php print $i?>"  >
						<option STYLE='color:#000066;background-color:#FCE4BA'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						</select>
						<input type=hidden onfocus=this.blur() name="saisie_duree_retourner_<?php print $i?>" value="<?php print $data_3[$j][4]?>"  >
						<?php
						$yy=$data_3[$j][4]." J";
						if ($data_3[$j][4] == 0) { $yy="???"; }
						if ($data_3[$j][4] == -1) { $yy=$data_3[$j][7]."H"; }
						?>
						<script langage=Javascript>
						chargement_pendant_jour('<?php print trim($yy)?>','<?php print $i?>','<?php print $i.$j?>');
						</script>
						<TD align=center valign=top>
						<?php
						if ($_SESSION["membre"] == "menuprof") {
							print "$creneaux ($debcre - $fincre)";
						}else{
							print dateForm($data_3[$j][2])?> <br> <?php if (($data_3[$j][11] != "") && ($data_3[$j][11] != "00:00:00") ){ print timeForm($data_3[$j][11]);
						}
					}
					?>
					</td>
					<TD valign=top>
					<?php $motiftext=$data_3[$j][6];
      				if ($data_3[$j][6] == "inconnu") { $motiftext=LANGINCONNU; }
      				$motiftext=preg_replace('/"/'," ",$motiftext);
					?>
					<input type=text name="saisie_modif_<?php print $i?>" value="<?php print $motiftext ?>" size=10 readonly >
					(&nbsp;<input type=checkbox name="saisie_justifier_<?php print $i?>" value="1" disabled <?php if ($data_3[$j][10] == 1) { print "checked='checked'"; } ?> > Justifié)
					</td>
					<input type='hidden' name=saisie_eleve_id_2 value="<?php print $data[$i][3]?>">
					<input type='hidden' name=saisie_date_ret_2 value="<?php print $data_3[$j][1]?>">
					<input type='hidden' name=saisie_nom_eleve value="<?php print $data[$i][1]?>">
					<input type='hidden' name=saisie_id_champ value="<?php print $i?>">
					<input type='hidden' name=saisie_time value="<?php print $data_3[$j][9]?>">
					<input type='hidden' name=saisie_matiere value="<?php print $data_3[$j][8]?>">
					</form>
					</td>
					</TR>
<?php 			}
					  ?>
				</tbody></table>
		<?php } ?>
	<?php }else{ ?>
		<div class="fe-no-access"><?php print LANGMESS308 ?></div>
	<?php
	     }
	?>
  </div>

  <div id="fe-panel-3" class="fe-panel">
	<table border="1" width="100%" style="border-collapse:collapse;margin-bottom:12px">
	<thead>
	<TR><td colspan=4 bgcolor=#FFFFFF align=center style="font-size:12px;font-weight:600;padding:6px"> <?php print LANGPARENT15 ?></td></TR>
	<TR>
	<TH class="cc-th" width=5%><?php print ucwords(LANGPROFK) ?></TH>
	<TH class="cc-th"><?php print LANGDISC57?></TH>
	</TR>
	</thead>
	<tbody>
<?php

if (($_SESSION["membre"] == "menuprof") || ($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") || ($_SESSION["membre"] == "menupersonnel") ) {
	$data_2=affSanction_par_eleve($idEleve);
}


// id,id_eleve,motif,id_category,date_saisie,origin_saisie,signature_parent,attribuer_par,devoir_a_faire
// $data : tab bidim - soustab 3 champs

for($j=0;$j<countTriade($data_2);$j++)
        {
		$raison=$data_2[$j][8];
		$raison=preg_replace('/\r\n/',"<br />",$raison);
		$raison=preg_replace('/\n/',"<br />",$raison);

?>
	<TR  class="tabnormal" onMouseOver="this.className='tabover'" onMouseOut="this.className='tabnormal'">

	<TD align=center valign="top" width=10% ><?php print dateForm($data_2[$j][4])?></td>
	<TD valign=top>
	&nbsp;<?php print ucwords(LANGDISC20) ?>: <font color=red><b><?php print rechercheCategory($data_2[$j][3])?></b></font> <br />
	&nbsp;<?php print ucwords(LANGPARENT15) ?>: <b><?php print $data_2[$j][2]?></b><br />
	&nbsp;<?php print LANGABS12 ?> : <?php print $data_2[$j][9] ?><br>
	&nbsp;<?php print LANGDISC9 ?> : <?php print trim($data_2[$j][7]) ?><br>
	&nbsp;<?php print LANGMESS98 ?> : <?php print $data_2[$j][8]?>
	</td>

	</TR>

<?php

}

?>
	</tbody></table>

	<table border="1" width="100%" style="border-collapse:collapse">
	<thead>
	<TR><td colspan=3 bgcolor=#FFFFFF align=center style="font-size:12px;font-weight:600;padding:6px"> <?php print LANGPARENT16 ?></td></TR>
	<TR>
	<TH class="cc-th" width=10%><?php print LANGPARENT16 ?></TH>
	<TH class="cc-th"><?php print LANGDISP2 ?></TH>
	</TR>
	</thead>
	<tbody>
<?php
if (($_SESSION["membre"] == "menuparent") || ($_SESSION["membre"] == "menueleve") || ($_SESSION["membre"] == "menututeur") ) {
	$data_2= affRetenuTotal_par_eleve($_SESSION["id_pers"]);
}

if (($_SESSION["membre"] == "menuprof") || ($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") || ($_SESSION["membre"] == "menupersonnel") ) {
	$data_2= affRetenuTotal_par_eleve($_GET["eid"]);
}


// id_elev,date_de_la_retenue,heure_de_la_retenue,date_de_saisie,origi_saisie,id_category,retenue_effectuer,motif,attribuer_par,signature_parent,duree_retenu,devoir_a_faire
// $data : tab bidim - soustab 3 champs

for($j=0;$j<countTriade($data_2);$j++) {
?>
        <TR  class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
        <form method=POST>
	<TD align=center valign=top><?php print dateForm($data_2[$j][1])?><br><?php print LANGPARENT17 ?><br><?php print $data_2[$j][2]?>
	<br> (<?php print timeForm($data_2[$j][10]) ?>) </td>
	<TD valign=top>
	&nbsp;<?php print ucwords(LANGDISC20) ?>: <font color=red><b><?php print rechercheCategory($data_2[$j][5])?></b></font> <br />
	&nbsp;<?php print ucwords(LANGPARENT15) ?>: <b><?php print $data_2[$j][7]?></b><br />
	&nbsp;<?php print LANGPARENT18 ?> :
		<?php
		if ($data_2[$j][6] != 1 ) {
			print "<b><font color=red>".ucwords(LANGNON)."</font></b>";
		}else {
			print ucwords(LANGOUI);
		}
		?>
	<br />&nbsp;<?php print LANGABS12 ?> : <?php print $data_2[$j][12] ?>
	<br />&nbsp;<?php print LANGMESS98 ?> : <?php print $data_2[$j][11]?>
	<br />&nbsp;<?php print LANGDISC9 ?> : <?php print ucwords($data_2[$j][8])?> - <?php print LANGTE12 ?> <?php print dateForm($data_2[$j][3]) ?>
	</td>
        </form>
        </TR>
<?php }

?>
	</tbody></table>
  </div>

  <?php // savoir etre ?>
  <div id="fe-panel-4" class="fe-panel">
	<table border="1" width='100%' style="border-collapse:collapse">
	<thead><tr>
	  <th class="cc-th">Date</th>
	  <th class="cc-th">Ponctualité</th>
	  <th class="cc-th">Motivation</th>
	  <th class="cc-th">Dynamisme</th>
	</tr></thead>
	<tbody>
<?php
$anneeScolaire=anneeScolaireViaIdClasse($saisie_classe);
$dataInfo=recupSavoirEtre($id_eleve,$saisie_classe,$anneeScolaire);
for($j=0;$j<countTriade($dataInfo);$j++) {
	$ponct=stripslashes($dataInfo[$j][0]);
	$motiv=stripslashes($dataInfo[$j][1]);
	$dynam=stripslashes($dataInfo[$j][2]);
	$id=$dataInfo[$j][3];
	$date=dateForm($dataInfo[$j][4]);
	$motiv=preg_replace('/"/',"&quot;",$motiv);
	$dynam=preg_replace('/"/',"&quot;",$dynam);
	$ponct=preg_replace('/"/',"&quot;",$ponct);
	print "<tr class='cc-tr-data'>";
	print "<td width='10%' valign='top'>$date</td>";
	print "<td width='30%' valign='top'>$ponct</td>";
	print "<td width='30%' valign='top'>$motiv</td>";
	print "<td width='30%' valign='top'>$dynam</td>";
	print "</tr>";
}
?>
	</tbody></table>
  </div>


  <div id="fe-panel-5" class="fe-panel">
  		<?php if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")|| ($_SESSION["membre"] == "menupersonnel")) {  ?>
  			<table border="1" width='100%' style="border-collapse:collapse">
  			<thead>
			<?php
			print "<tr><th class='cc-th' style='width:5%'>Année Scolaire</th><th class='cc-th'>Classe</th></tr>";
			?>
			</thead>
			<tbody>
			<?php
			$data=listingHistoClasseEleve($idEleve); // annee, classe
			for ($i=0; $i<countTriade($data);$i++) {
				print "<tr class='cc-tr-data'><td>&nbsp;".$data[$i][0]."&nbsp;</td><td>&nbsp;".$data[$i][1]."</td></tr>";
			}
   			?>
  			</tbody></table>
		<?php }else{ ?>
			<div class="fe-no-access"><?php print LANGMESS308 ?></div>
		<?php } ?>
	</div>






</div>




<script>
(function(){
  document.querySelectorAll('.fe-main-tab').forEach(function(tab){
    tab.addEventListener('click', function(){
      document.querySelectorAll('.fe-main-tab').forEach(function(t){ t.classList.remove('active'); });
      document.querySelectorAll('.fe-panel').forEach(function(p){ p.classList.remove('active'); });
      tab.classList.add('active');
      var panel = document.getElementById(tab.dataset.panel);
      if (panel) panel.classList.add('active');
    });
  });
  document.querySelectorAll('.fe-sub-tab').forEach(function(tab){
    tab.addEventListener('click', function(){
      document.querySelectorAll('.fe-sub-tab').forEach(function(t){ t.classList.remove('active'); });
      document.querySelectorAll('.fe-sub-panel').forEach(function(p){ p.classList.remove('active'); });
      tab.classList.add('active');
      var panel = document.getElementById(tab.dataset.sub);
      if (panel) panel.classList.add('active');
    });
  });
})();
</script>

	<br><br>
</td></tr>
</td></tr></table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") ):
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
print "</SCRIPT>";
else :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
print "</SCRIPT>";
top_d();
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
print "</SCRIPT>";
endif ;
?>
<?php @Pgclose() ?>
</BODY>
</HTML>
