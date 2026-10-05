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
 ***************************************************************************/
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<style>
.exp-chk-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:4px}
.exp-chk-item{display:flex;align-items:center;gap:6px;padding:5px 8px;background:#f5f7ff;border:1px solid #e4e9f8;border-radius:5px;font-size:11px;cursor:pointer;transition:background .15s}
.exp-chk-item:hover{background:#eef0fb;border-color:#c5cae9}
.exp-chk-item input{accent-color:#080A66;cursor:pointer;flex-shrink:0}
.exp-section-title{font-size:11px;font-weight:700;color:#080A66;padding:4px 0 2px;border-bottom:1px solid #e4e9f8;margin-bottom:6px;display:flex;align-items:center;gap:5px}
</style>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("2");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'>Exportation des données <?php print INTITULEELEVE ?></font></b>
</td></tr>
<tr id='cadreCentral0'>
<td>
<!-- // debut -->

<div style="display:flex;flex-direction:column;gap:12px;padding:10px 6px">

<form method="post" action="export_eleve_2.php">

  <div class="card">
    <div class="card-header" style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#080A66">
      <i class="bi bi-check2-square"></i> Sélectionner les colonnes à exporter
    </div>
    <div class="card-body" style="padding:10px 14px;display:flex;flex-direction:column;gap:10px">

      <!-- Identité -->
      <div>
        <div class="exp-section-title"><i class="bi bi-person"></i> Identité <?php print INTITULEELEVE ?></div>
        <div class="exp-chk-grid">
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="nom"> Nom</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="prenom"> Prénom</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="classe"> Classe</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="date_naissance"> Date naissance</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="sexe"> Sexe</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="lieu_naissance"> Lieu naissance</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="nationalite"> Nationalité</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="numero_eleve"> INE / N° <?php print INTITULEELEVE ?></label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="annee_scolaire"> Année scolaire</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="regime"> Régime</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="lv1"> Langue vivante 1</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="lv2"> Langue vivante 2</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="option"> Option</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="information"> Informations</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="code_barre"> Code barre</label>
        </div>
      </div>

      <!-- Coordonnées élève -->
      <div>
        <div class="exp-section-title"><i class="bi bi-house"></i> Coordonnées <?php print INTITULEELEVE ?></div>
        <div class="exp-chk-grid">
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="adresse_eleve"> Adresse</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="ccp_eleve"> Code postal</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="commune_eleve"> Commune</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="pays_eleve"> Pays</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="tel_fixe_eleve"> Tél. fixe</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="tel_eleve"> Tél. portable</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="email_eleve"> Email</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="email_eleve_pro"> Email universitaire</label>
        </div>
      </div>

      <!-- Responsable 1 -->
      <div>
        <div class="exp-section-title"><i class="bi bi-people"></i> Responsable 1</div>
        <div class="exp-chk-grid">
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="civ_1"> Civilité tuteur 1</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="nomtuteur"> Nom tuteur 1</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="prenomtuteur"> Prénom tuteur 1</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="adr1"> Adresse tuteur 1</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="code_post_adr1"> Code postal tuteur 1</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="commune_adr1"> Commune tuteur 1</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="tel_port_1"> Tél. portable tuteur 1</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="email"> Email tuteur 1</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="profession_pere"> Profession père</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="tel_prof_pere"> Tél. prof. père</label>
        </div>
      </div>

      <!-- Responsable 2 -->
      <div>
        <div class="exp-section-title"><i class="bi bi-people"></i> Responsable 2</div>
        <div class="exp-chk-grid">
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="civ_2"> Civilité tuteur 2</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="nomtuteur_2"> Nom tuteur 2</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="prenomtuteur_2"> Prénom tuteur 2</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="adr2"> Adresse tuteur 2</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="code_post_adr2"> Code postal tuteur 2</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="commune_adr2"> Commune tuteur 2</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="tel_port_2"> Tél. portable tuteur 2</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="email_resp_2"> Email tuteur 2</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="telephone"> Téléphone</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="profession_mere"> Profession mère</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="tel_prof_mere"> Tél. prof. mère</label>
        </div>
      </div>

      <!-- Établissement précédent -->
      <div>
        <div class="exp-section-title"><i class="bi bi-building"></i> Établissement précédent</div>
        <div class="exp-chk-grid">
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="nom_etablissement"> Nom établissement</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="numero_etablissement"> N° établissement</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="code_postal_etablissement"> Code postal étab.</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="commune_etablissement"> Commune étab.</label>
          <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="class_ant"> Classe antérieure</label>
        </div>
      </div>

      <!-- Colonnes supplémentaires -->
      <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-top:1px solid #e4e9f8;font-size:12px;color:#555">
        <i class="bi bi-plus-circle" style="color:#3949ab"></i>
        Colonnes supplémentaires vides :
        <input type="text" size="3" name="nbcolplus" value="0"
               style="border:1px solid #c5cae9;border-radius:4px;padding:3px 6px;font-size:12px;width:50px;text-align:center">
        <span style="font-size:11px;color:#888;font-style:italic">nombre de colonnes à ajouter</span>
      </div>

    </div>
  </div>

  <div style="display:flex;gap:8px;padding:4px 0">
    <button type="submit" name="create" class="btn btn-primary">
      Suivant <i class="bi bi-arrow-right"></i>
    </button>
    <script language=JavaScript>buttonMagicRetour("export.php","_self")</script>
  </div>

</form>

</div>

<!-- // fin -->
<?php Pgclose(); ?>
</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
