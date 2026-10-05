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
  <b><font id='menumodule1'>Exportation des données du personnel</font></b>
</td></tr>
<tr id='cadreCentral0'>
<td>
<!-- // debut -->

<div style="display:flex;flex-direction:column;gap:12px;padding:10px 6px">

<form method="post" action="export_personnel_2.php">

  <div class="card">
    <div class="card-header" style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#080A66">
      <i class="bi bi-check2-square"></i> Sélectionner les colonnes à exporter
    </div>
    <div class="card-body" style="padding:10px 14px;display:flex;flex-direction:column;gap:12px">

      <!-- Filtre type -->
      <div class="toolbar" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
        <label class="form-label" style="margin:0;font-weight:600;font-size:12px">Type de membre :</label>
        <select name="saisie_type" class="form-control" style="width:auto">
          <option value="0"><?php print LANGCHOIX ?></option>
          <option value="ENS">Enseignant</option>
          <option value="ADM">Direction</option>
          <option value="TUT">Tuteur de stage</option>
          <option value="PER">Personnel</option>
          <option value="MVS">Vie Scolaire</option>
        </select>
      </div>

      <!-- Champs -->
      <div class="exp-chk-grid">
        <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="civ_1"> Civilité</label>
        <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="nom"> Nom</label>
        <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="prenom"> Prénom</label>
        <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="adr1"> Adresse</label>
        <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="code_post_adr1"> Code postal</label>
        <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="commune_adr1"> Commune</label>
        <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="tel_port_1"> Tél. portable</label>
        <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="email"> Email</label>
        <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="telephone"> Téléphone</label>
        <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="identifiant"> Identifiant</label>
        <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="indice_salaire"> Indice salaire</label>
        <label class="exp-chk-item"><input type="checkbox" name="liste[]" value="code_barre"> Code barre</label>
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
