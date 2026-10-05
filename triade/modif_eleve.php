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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<meta charset="utf-8">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verifEmail.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/scriptaculous.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<style>
#coulBar0 { background-image: none; }
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
if ($_SESSION["membre"] == "menupersonnel") {
	$cnx=cnx();
	if (!verifDroit($_SESSION["id_pers"],"ficheeleve")) {
		accesNonReserveFen();
		exit();
	}
	Pgclose();
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMODIF1 ?></font></B></td></tr>
<tr id='cadreCentral0' >
<td >
<!-- // fin  -->
<div style="padding:6px 12px">
<?php
//debut if pour affichage ou non du formulaire
if(!isset($_POST["update"])) {
?>

<?php
$eid=$_GET["eid"];
// récupération des données pour les mettres dans les values des champs du formulaire
$sql=<<<EOF

SELECT
        elev_id,
        trim(nom),
        trim(prenom),
        trim(classe),
        trim(lv1),
        trim(lv2),
        trim(`option`),
        trim(regime),
        date_naissance,
	trim(nationalite),
        trim(passwd),
	trim(passwd_eleve),
	trim(civ_1),
        trim(nomtuteur),
        trim(prenomtuteur),
        trim(adr1),
        trim(code_post_adr1),
	trim(commune_adr1),
	trim(civ_2),
	trim(nom_resp_2),
	trim(prenom_resp_2),
        trim(adr2),
        trim(code_post_adr2),
        trim(commune_adr2),
        trim(telephone),
        trim(profession_pere),
        trim(tel_prof_pere),
        trim(profession_mere),
        trim(tel_prof_mere),
        trim(nom_etablissement),
        trim(numero_etablissement),
        trim(code_postal_etablissement),
        trim(commune_etablissement),
	trim(numero_eleve),
	trim(email),
	trim(email_eleve),
	c.libelle,
        trim(class_ant),
	annee_ant,
	trim(tel_eleve),
	trim(lieu_naissance),
	trim(tel_port_1),
	trim(tel_port_2),
	email_resp_2,
	sexe,
	code_compta,
	information,
	adr_eleve,
	commune_eleve,
	ccp_eleve,
	tel_fixe_eleve,
	pays_eleve,
	boursier,
	montant_bourse,
	indemnite_stage,
	nbmoisindemnite,
	emailpro_eleve,
	rangement,
	cdi,
	bde,
	situation_familiale,
	ine
	
FROM
        {$prefixe}eleves, {$prefixe}classes c
WHERE
        elev_id='$eid'
AND     c.code_class=classe

EOF;
$res=execSql($sql);
$data=chargeMat($res);

/*
0 elev_id,
1 trim(nom),
2 trim(prenom),
3  trim(classe),
4  trim(lv1),
5  trim(lv2),
6  trim(option),
7  trim(regime),
8  date_naissance,
9  trim(nationalite),
10 trim(passwd),
11 trim(passwd_eleve),
12 trim(civ_1),
13 trim(nomtuteur),
14 trim(prenomtuteur),
15 trim(adr1),
16 trim(code_post_adr1),
17 trim(commune_adr1),
18 trim(civ_2),
19 trim(nom_resp_2),
20 trim(prenom_resp_2),
21 trim(adr2),
22 trim(code_post_adr2),
23 trim(commune_adr2),
24 trim(telephone),
25 trim(profession_pere),
26 trim(tel_prof_pere),
27 trim(profession_mere),
28 trim(tel_prof_mere),
29 trim(nom_etablissement),
30 trim(numero_etablissement),
31 trim(code_postal_etablissement),
32 trim(commune_etablissement),
33 trim(numero_eleve),
34 trim(email),
35 trim(email_eleve),
36 c.libelle,
37 trim(class_ant),
38 annee_ant,
39 tel_eleve
40 lieu_naissance,
41 tel_port_1,
42 tel_port_2,
43 email_tuteur_2
44 sexe
45 code_compta
46 Information
47 adr_eleve
48 commune_eleve
49 ccp_eleve
50 tel_fixe_eleve
51 pays
52 boursier,
53 montant_bourse,
54 indemnite_stage
55 nbmoisindemnite
56 emailpro_eleve
57 rangement
58 cdi
59 bde
60 situation_familiale
61 ine
*/
?>


<form method=post onsubmit="return valide_modif_eleve()" name="formulaire">

<!-- Card 1 : Identité & Scolarité -->
<div class="card" style="margin:8px 0">
  <div class="card-header"><span class="card-title"><i class="bi bi-person-fill"></i> <?php print LANGMODIF2 ?></span></div>
  <div style="padding:10px 14px">
    <div class="form-row"><label><?php print LANGEL1 ?></label><input type="text" name="saisie_nom" value="<?php print $data[0][1]?>"></div>
    <div class="form-row"><label><?php print LANGEL2 ?></label><input type="text" name="saisie_prenom" value="<?php print $data[0][2]?>"></div>
    <div class="form-row"><label><?php print LANGEL3 ?></label>
      <input type=hidden readonly name="saisie_classe" value='<?php print $data[0][3]?>'>
      <input type=text readonly value='<?php print chercheClasse_nom($data[0][3])?>' style="font-size:12px;color:#333;background:#f0f2fa;border:1px solid #c5cae9;border-radius:4px;padding:3px 7px">
    </div>
    <div class="form-row"><label><?php print LANGEL4."/Spé" ?></label>
      <select name="saisie_lv1">
        <option selected value="<?php print $data[0][4]?>"><?php print $data[0][4]?></option>
        <?php select_matiere_pour_lvo(); ?><option value=''></option>
      </select></div>
    <div class="form-row"><label><?php print LANGEL5."/Spé" ?></label>
      <select name="saisie_lv2">
        <option selected value="<?php print $data[0][5]?>"><?php print $data[0][5]?></option>
        <?php select_matiere_pour_lvo(); ?><option value=''></option>
      </select></div>
    <div class="form-row"><label><?php print LANGEL6 ?></label>
      <select name="saisie_option">
        <option selected value="<?php print $data[0][6]?>"><?php print $data[0][6]?></option>
        <?php select_matiere_pour_lvo(); ?>
      </select></div>
    <div class="form-row"><label><?php print LANGEL7 ?></label>
      <select name="saisie_regime">
        <?php if ($data[0][7] != "") { print "<option value='".$data[0][7]."'>".$data[0][7]."</option>"; } ?>
        <option value=""><?php print LANGCHOIX ?></option>
        <option value="Interne"><?php print LANGELE7 ?></option>
        <option value="Demi-pension"><?php print LANGELE8 ?></option>
        <option value="Externe"><?php print LANGELE9 ?></option>
        <optgroup label="Personnalisé"><?php selectRegime(); ?>
      </select> [<a href="regime_ajout.php">ajouter</a>]</div>
    <div class="form-row"><label>Rangement / Info.</label><input type="text" name="saisie_rangement" maxlength='200' value='<?php print $data[0][57]?>'></div>
  </div>
</div>

<!-- Card 2 : Situation -->
<div class="card" style="margin:8px 0">
  <div class="card-header"><span class="card-title"><i class="bi bi-clipboard-data"></i> Situation</span></div>
  <div style="padding:10px 14px">
    <?php
    $checkedM=""; $checkedF="";
    if (trim(strtoupper($data[0][44])) == "M") $checkedM="checked='checked'";
    if (trim(strtoupper($data[0][44])) == "F") $checkedF="checked='checked'";
    ?>
    <div class="form-row"><label>Sexe</label>
      <label style="font-weight:400">M <input type="radio" name="saisie_sexe" value="m" <?php print $checkedM ?>></label>&nbsp;&nbsp;
      <label style="font-weight:400">F <input type="radio" name="saisie_sexe" value="f" <?php print $checkedF ?>></label></div>
    <?php
    $checkedOui=""; $checkedNon="";
    if (trim($data[0][52]) == "1") $checkedOui="checked='checked'"; else $checkedNon="checked='checked'";
    ?>
    <div class="form-row"><label>Boursier</label>
      <label style="font-weight:400">Oui <input type="radio" name="saisie_boursier" value="1" <?php print $checkedOui ?>></label>&nbsp;&nbsp;
      <label style="font-weight:400">Non <input type="radio" name="saisie_boursier" value="0" <?php print $checkedNon ?>></label></div>
    <div class="form-row"><label>Montant de la bourse</label><input type="text" name="saisie_montant_bourse" value="<?php print affichageFormatMonnaie($data[0][53])?>"></div>
    <?php
    $checkedBDEOui=""; $checkedBDENon="";
    if (trim($data[0][59]) == "1") $checkedBDEOui="checked='checked'"; else $checkedBDENon="checked='checked'";
    ?>
    <div class="form-row"><label>Inscription au BDE</label>
      <label style="font-weight:400">Oui <input type="radio" name="saisie_bde" value="1" <?php print $checkedBDEOui ?>></label>&nbsp;&nbsp;
      <label style="font-weight:400">Non <input type="radio" name="saisie_bde" value="0" <?php print $checkedBDENon ?>></label></div>
    <?php
    $checkedCDIOui=""; $checkedCDINon="";
    if (trim($data[0][58]) == "1") $checkedCDIOui="checked='checked'"; else $checkedCDINon="checked='checked'";
    ?>
    <div class="form-row"><label>Inscription à la bibliothèque</label>
      <label style="font-weight:400">Oui <input type="radio" name="saisie_cdi" value="1" <?php print $checkedCDIOui ?>></label>&nbsp;&nbsp;
      <label style="font-weight:400">Non <input type="radio" name="saisie_cdi" value="0" <?php print $checkedCDINon ?>></label></div>
    <div class="form-row"><label>Indemnité de stage</label>
      <input type="text" name="saisie_indemnite_stage" value="<?php print affichageFormatMonnaie($data[0][54])?>" style="width:80px">
      <span style="font-size:11px;color:#555">&nbsp;/ mois.&nbsp; Nb mois : <input type="text" name="saisie_nbmoisindemnite_stage" value="<?php print $data[0][55]?>" style="width:40px"></span></div>
  </div>
</div>

<!-- Card 3 : Données personnelles -->
<div class="card" style="margin:8px 0">
  <div class="card-header"><span class="card-title"><i class="bi bi-card-text"></i> Données personnelles</span></div>
  <div style="padding:10px 14px">
    <div class="form-row"><label><?php print LANGEL8 ?></label>
      <input type="text" name="saisie_date_naissance" value="<?php print dateForm($data[0][8])?>">
      <?php include_once("librairie_php/calendar.php"); calendar('id1','document.formulaire.saisie_date_naissance',$_SESSION["langue"],"1"); ?>
    </div>
    <div class="form-row"><label><?php print LANGEL9 ?></label><input type="text" name="saisie_nationalite" value="<?php print $data[0][9]?>" maxlength='20'></div>
    <div class="form-row"><label><?php print LANGEDIT6 ?></label><input type="text" name="saisie_lieunais" value="<?php print $data[0][40]?>" maxlength='40'></div>
    <div class="form-row"><label><?php print ucwords(LANGIMP52) ?></label>
      <input type=button onclick="open('modif_eleve_pass_eleve.php?ideleve=<?php print $eid;?>','pass','width=450,height=350')" value='<?php print LANGELE30 ?>' class="btn btn-secondary btn-sm"></div>
    <div class="form-row"><label><?php print LANGEL30 ?></label>
      <input type="passwd" name="saisie_numnational" value="<?php print $data[0][33]?>" maxlength=20 <?php if (AUTOINE == "oui") print "readonly style='background:#f0f2fa'" ?>>
      <?php if (AUTOINE == "oui") print " <small style='color:#888'>(Numéro automatisé)</small>"; ?></div>
    <div class="form-row"><label>Numéro INE</label><input type="passwd" name="saisie_ine" value="<?php print $data[0][61]?>" maxlength=20></div>
    <div class="form-row"><label>Code comptabilité</label><input type="text" name="saisie_codecompta" maxlength=30 value="<?php print $data[0][45]?>"></div>
  </div>
</div>

<!-- Card 4 : Coordonnées de l'élève -->
<div class="card" style="margin:8px 0">
  <div class="card-header"><span class="card-title"><i class="bi bi-geo-alt"></i> Coordonnées de l'élève</span></div>
  <div style="padding:10px 14px">
    <div class="form-row"><label>Adresse</label><input type="text" name="saisie_adr_eleve" maxlength=100 value="<?php print $data[0][47]?>"></div>
    <div class="form-row"><label><?php print LANGELE15 ?></label><input type="text" name="saisie_code_post_adr_eleve" maxlength=15 value="<?php print $data[0][49]?>"></div>
    <div class="form-row"><label><?php print LANGELE16 ?></label><input type="text" name="saisie_commune_adr_eleve" maxlength=40 value="<?php print $data[0][48]?>"></div>
    <div class="form-row"><label>Pays</label><input type="text" name="saisie_pays_eleve" maxlength=50 value="<?php print $data[0][51]?>"></div>
    <div class="form-row"><label>Téléphone</label><input type="text" name="saisie_tel_fixe_eleve" maxlength=25 value="<?php print $data[0][50]?>"></div>
    <div class="form-row"><label><?php print LANGEDIT20." ".LANGTITRE40 ?></label><input type="text" name="saisie_portable_eleve" maxlength=18 value="<?php print $data[0][39]?>"></div>
    <div class="form-row"><label><?php print LANGELE244." ".LANGTITRE40 ?></label>
      <input type="text" name="saisie_email_eleve" id="saisie_email_eleve" maxlength='150' value="<?php print $data[0][35]?>" onBlur="verifMailExist(this.value,'rtaff1','saisie_email_eleve','<?php print $eid ?>')"><span id='rtaff1'></span></div>
    <div class="form-row"><label><?php print LANGELE244 ?> universitaire</label>
      <input type="text" name="saisie_emailpro_eleve" id="saisie_emailpro_eleve" maxlength='150' value="<?php print $data[0][56]?>" onBlur="verifMailExist(this.value,'rtaff2','saisie_emailpro_eleve','<?php print $eid ?>')"><span id='rtaff2'></span></div>
    <div class="form-row" style="align-items:flex-start"><label>Information</label><textarea name="saisie_info_eleve" cols=40 rows=3><?php print preg_replace('/\<br \/\>/','',nl2br($data[0][46]))?></textarea></div>
  </div>
</div>

<!-- Card 5 : Famille -->
<div class="card" style="margin:8px 0">
  <div class="card-header"><span class="card-title"><i class="bi bi-people"></i> <?php print LANGMODIF3 ?></span></div>
  <div style="padding:10px 14px">
    <div class="form-row"><label>Situation Familiale</label>
      <select name='situation_familiale'>
        <option value=''></option>
        <option value='<?php print LANGSITU1 ?>' <?php if ($data[0][60]==LANGSITU1) print "selected" ?>><?php print LANGSITU1 ?></option>
        <option value='<?php print LANGSITU2 ?>' <?php if ($data[0][60]==LANGSITU2) print "selected" ?>><?php print LANGSITU2 ?></option>
        <option value='<?php print LANGSITU3 ?>' <?php if ($data[0][60]==LANGSITU3) print "selected" ?>><?php print LANGSITU3 ?></option>
        <option value='<?php print LANGSITU4 ?>' <?php if ($data[0][60]==LANGSITU4) print "selected" ?>><?php print LANGSITU4 ?></option>
        <option value='<?php print LANGSITU5 ?>' <?php if ($data[0][60]==LANGSITU5) print "selected" ?>><?php print LANGSITU5 ?></option>
        <option value='<?php print LANGSITU6 ?>' <?php if ($data[0][60]==LANGSITU6) print "selected" ?>><?php print LANGSITU6 ?></option>
        <option value='<?php print LANGSITU7 ?>' <?php if ($data[0][60]==LANGSITU7) print "selected" ?>><?php print LANGSITU7 ?></option>
      </select></div>
    <div class="form-row"><label><?php print LANGEL21 ?></label><input type="text" name="saisie_telephone" maxlength='18' value="<?php print $data[0][24]?>"></div>
    <div class="form-row"><label><?php print LANGEL22 ?></label><input type="text" name="saisie_profession_pere" maxlength='30' value="<?php print $data[0][25]?>"></div>
    <div class="form-row"><label><?php print LANGEL23 ?></label><input type="text" name="saisie_tel_prof_pere" maxlength='18' value="<?php print $data[0][26]?>"></div>
    <div class="form-row"><label><?php print LANGEL24 ?></label><input type="text" name="saisie_profession_mere" maxlength='30' value="<?php print $data[0][27]?>"></div>
    <div class="form-row"><label><?php print LANGEL25 ?></label><input type="text" name="saisie_tel_prof_mere" maxlength='18' value="<?php print $data[0][28]?>"></div>
  </div>
</div>

<!-- Card 6 : Responsable 1 -->
<div class="card" style="margin:8px 0">
  <div class="card-header"><span class="card-title"><i class="bi bi-person"></i> Responsable 1</span></div>
  <div style="padding:10px 14px">
    <div class="form-row"><label>Civ 1</label>
      <select name="saisie_civ1">
        <?php if (trim($data[0][12]) != "") { print "<option value='".$data[0][12]."'>".civ($data[0][12])."</option>"; } listingCiv(); ?>
      </select></div>
    <div class="form-row"><label><?php print LANGEL11 ?></label><input type="text" name="saisie_nomtuteur" value="<?php print $data[0][13]?>" maxlength=30></div>
    <div class="form-row"><label><?php print LANGEL12 ?></label><input type="text" name="saisie_prenomtuteur" value="<?php print $data[0][14]?>" maxlength=30></div>
    <div class="form-row"><label><?php print LANGEL14 ?></label><input type="text" name="saisie_adr1" value="<?php print $data[0][15]?>" maxlength=100></div>
    <div class="form-row"><label><?php print LANGEL15 ?></label><input type="text" name="saisie_code_post_adr1" value="<?php print $data[0][16]?>" maxlength=15></div>
    <div class="form-row"><label><?php print LANGEL16 ?></label><input type="text" name="saisie_commune_adr1" value="<?php print $data[0][17]?>" maxlength=40></div>
    <div class="form-row"><label><?php print LANGEDIT2 ?></label><input type="text" name="saisie_tel_port_1" value="<?php print $data[0][41]?>" maxlength=25></div>
    <div class="form-row"><label><?php print LANGELE244 ?> 1</label>
      <input type="text" name="saisie_email" id="saisie_email" value="<?php print $data[0][34]?>" maxlength=150 onBlur="verifMailExist(this.value,'rtaff3','saisie_email','<?php print $eid ?>')"><span id='rtaff3'></span></div>
    <div class="form-row"><label><?php print ucwords(LANGIMP51) ?> 1</label>
      <input type=button onclick="open('modif_eleve_pass.php?ideleve=<?php print $eid;?>','pass','width=400,height=350')" value='<?php print LANGELE30 ?>' class="btn btn-secondary btn-sm"></div>
  </div>
</div>

<!-- Card 7 : Responsable 2 -->
<div class="card" style="margin:8px 0">
  <div class="card-header"><span class="card-title"><i class="bi bi-person"></i> Responsable 2</span></div>
  <div style="padding:10px 14px">
    <div class="form-row"><label>Civ 2</label>
      <select name="saisie_civ2">
        <?php if (trim($data[0][18]) != "") { print "<option value='".$data[0][12]."'>".civ($data[0][18])."</option>"; } listingCiv(); ?>
      </select></div>
    <div class="form-row"><label><?php print LANGEDIT4 ?></label><input type="text" name="saisie_nomtuteur2" value="<?php print $data[0][19]?>" maxlength=30></div>
    <div class="form-row"><label><?php print LANGEDIT5 ?></label><input type="text" name="saisie_prenomtuteur2" maxlength=50 value="<?php print $data[0][20]?>"></div>
    <div class="form-row"><label><?php print LANGEL18 ?></label><input type="text" name="saisie_adr2" value="<?php print $data[0][21]?>" maxlength=100></div>
    <div class="form-row"><label><?php print LANGEL19 ?></label><input type="text" name="saisie_code_post_adr2" value="<?php print $data[0][22]?>" maxlength=15></div>
    <div class="form-row"><label><?php print LANGEL20 ?></label><input type="text" name="saisie_commune_adr2" value="<?php print $data[0][23]?>" maxlength=40></div>
    <div class="form-row"><label><?php print LANGEDIT9 ?></label><input type="text" name="saisie_tel_port_2" value="<?php print $data[0][42]?>" maxlength=25></div>
    <div class="form-row"><label><?php print LANGELE244 ?> 2</label>
      <input type="text" name="saisie_email_2" id='saisie_email_2' value="<?php print $data[0][43]?>" maxlength=150 onBlur="verifMailExist(this.value,'rtaff4','saisie_email_2','<?php print $eid ?>')"><span id='rtaff4'></span></div>
    <div class="form-row"><label><?php print ucwords(LANGIMP51) ?> 2</label>
      <input type=button onclick="open('modif_eleve_pass.php?ideleve=<?php print $eid;?>&p2=P2','pass','width=400,height=350')" value='<?php print LANGELE30 ?>' class="btn btn-secondary btn-sm"></div>
  </div>
</div>

<!-- Card 8 : Établissement scolaire précédent -->
<div class="card" style="margin:8px 0">
  <div class="card-header"><span class="card-title"><i class="bi bi-building"></i> <?php print ucwords(LANGELE25) ?></span></div>
  <div style="padding:10px 14px">
    <div class="form-row"><label>Établissement</label><input type="text" name="saisie_nom_etablissement" value="<?php print $data[0][29]?>" maxlength=30></div>
    <div class="form-row"><label><?php print LANGELE27 ?></label><input type="text" name="saisie_numero_etablissement" value="<?php print $data[0][30]?>" maxlength=30></div>
    <div class="form-row"><label><?php print LANGbasededoni41 ?></label><input type="text" name="saisie_classe_ant" value="<?php print $data[0][37]?>" maxlength=30></div>
    <div class="form-row"><label><?php print LANGbasededoni42 ?></label><input type="text" name="saisie_annee_ant" value="<?php print $data[0][38]?>"></div>
    <div class="form-row"><label><?php print LANGEL28 ?></label><input type="text" name="saisie_code_postal_etablissement" value="<?php print $data[0][31]?>" maxlength=6></div>
    <div class="form-row"><label><?php print LANGEL29 ?></label><input type="text" name="saisie_commune_etablissement" value="<?php print $data[0][32]?>" maxlength=30></div>
  </div>
</div>

<div class="toolbar" style="margin:10px 0">
  <script language=JavaScript>buttonMagicSubmit("<?php print LANGABS45?>","update");</script>
  &nbsp;&nbsp;
  <script language=JavaScript>buttonMagicPrecedent2();</script>
</div>
</form>
<?php
// fin du if pour affichage ou non du formulaire
}

if(isset($_POST["update"])) {
	$eid=$_GET["eid"];

	// création du tableau de hash contenant les paramètres de la fonction modif_eleve
	$params['ne']=		trim(strtolower($_POST["saisie_nom"]));
	$params['pe']=		trim($_POST["saisie_prenom"]);
	$params['ce']=		trim($_POST["saisie_classe"]);
	$params['lv1']=		trim(strtolower($_POST["saisie_lv1"]));
	$params['lv2']=		trim(strtolower($_POST["saisie_lv2"]));
	$params['option']=	trim(strtolower($_POST["saisie_option"]));
	$params['regime']=	trim(strtolower($_POST["saisie_regime"]));
	$params['naiss']=		$_POST["saisie_date_naissance"];
	$params['nat']=		trim(strtolower($_POST["saisie_nationalite"]));
	$params['nt']=		trim($_POST["saisie_nomtuteur"]);
	$params['pt']=		trim($_POST["saisie_prenomtuteur"]);
	$params['adr1']=		trim($_POST["saisie_adr1"]);
	$params['cpadr1']=	$_POST["saisie_code_post_adr1"];
	$params['commadr1']=  	trim($_POST["saisie_commune_adr1"]);
	$params['adr2']=		trim($_POST["saisie_adr2"]);
	$params['cpadr2']=	$_POST["saisie_code_post_adr2"];
	$params['commadr2']=	trim($_POST["saisie_commune_adr2"]);
	$params['tel']=		$_POST["saisie_telephone"];
	$params['profp']=		trim($_POST["saisie_profession_pere"]);
	$params['telprofp']=	$_POST["saisie_tel_prof_pere"];
	$params['profm']=		trim($_POST["saisie_profession_mere"]);
	$params['telprofm']=	$_POST["saisie_tel_prof_mere"];
	$params['nomet']=		trim($_POST["saisie_nom_etablissement"]);
	$params['numet']=		$_POST["saisie_numero_etablissement"];
	$params['cpet']=		$_POST["saisie_code_postal_etablissement"];
	$params['commet']=	trim($_POST["saisie_commune_etablissement"]);
	$params['numero_eleve']=	$_POST["saisie_numnational"];
	$params['email']=		$_POST["saisie_email"];
	$params['classe_ant']=	$_POST["saisie_classe_ant"];
	$params['annee_ant']=	$_POST["saisie_annee_ant"];
	$params['civ1']=		$_POST["saisie_civ1"];
	$params['civ2']=		$_POST["saisie_civ2"];
	$params['tel_eleve']=	$_POST["saisie_portable_eleve"];
	$params['mail_eleve']=	$_POST["saisie_email_eleve"];
	$params['mailpro_eleve']=	$_POST["saisie_emailpro_eleve"];
	$params['nom_resp2']=	trim($_POST["saisie_nomtuteur2"]);
	$params['prenom_resp2']=	trim($_POST["saisie_prenomtuteur2"]);
	$params['lieunais']=	trim($_POST["saisie_lieunais"]);
	$params['tel_port_1']=	$_POST["saisie_tel_port_1"];
	$params['tel_port_2']=	$_POST["saisie_tel_port_2"];
	$params['email_2']=	$_POST["saisie_email_2"];
	$params['codecompta']=    $_POST["saisie_codecompta"];
	$params['sexe']=    	$_POST["saisie_sexe"];
	$params['information']=  	$_POST["saisie_info_eleve"];
	$params['adr_eleve']= 	trim($_POST["saisie_adr_eleve"]);
	$params['commune_eleve']= trim($_POST["saisie_commune_adr_eleve"]);
	$params['ccp_eleve']= 	$_POST["saisie_code_post_adr_eleve"];
	$params['tel_fixe_eleve']=$_POST["saisie_tel_fixe_eleve"];
	$params['pays_eleve']=    trim($_POST["saisie_pays_eleve"]);
	$params['boursier']=	$_POST["saisie_boursier"];
	$params['boursier_montant']=$_POST["saisie_montant_bourse"];
	$params['indemnite_stage']=$_POST["saisie_indemnite_stage"];
	$params['nbmoisindemnite_stage']=$_POST["saisie_nbmoisindemnite_stage"];
	$params['rangement']=$_POST["saisie_rangement"];
	$params['cdi']=$_POST["saisie_cdi"];
	$params['bde']=$_POST["saisie_bde"];
	$params['situation_familiale']=trim($_POST["situation_familiale"]);
	$params['saisie_ine']=trim($_POST["saisie_ine"]);

	// trim et strtolower des values du hash params
	foreach($params as $key => $value) {
		strtolower($value);
		trim($value);
	}

	$cr=modif_eleve($eid,$params);

        if($cr){
        	alertJs(LANGALERT1);
		$nomElve=strtolower($_POST["saisie_nom"]);
		history_cmd($_SESSION["nom"],"Modification","Elève: $nomElve");
	}
}
?>

<?php
// si mise à jour on affiche les données modifiées via un reload vers consult_eleve.php
if($cr) {
	print("<script>window.location.replace('edit_eleve.php?eid=$eid');</script>");
}
?>

     </div>
     <!-- // fin  -->
     </td></tr></table>

     <SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php Pgclose() ?>
</BODY>
</HTML>
