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
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("2");

$nosauvegarde = 1;
$ok      = 0;
$fichier = '';
$colonne = [];

if (isset($_GET["libelle"])) {
    $cnx     = cnx();
    $ok      = 1;
    $libelle = "##struct##".$_GET["libelle"];
    $data    = aff_enr_parametrage($libelle);
    $colonne = unserialize($data[0][1]);
}

if (isset($_POST['create'])) {
    $nosauvegarde = 0;
    $ok      = 1;
    $cnx     = cnx();
    $nbcolname = isset($_POST['nbcolname']) ? $_POST['nbcolname'] : [];
    $tablist   = explode('%##%', $_POST['liste']);

    $i = 0;
    foreach ($_POST['ordre'] as $key => $value) {
        if (is_numeric($tablist[$i])) {
            $o = $tablist[$i];
            $colonne[$value] = stripslashes($nbcolname[$o]);
        } else {
            $colonne[$value] = $tablist[$i];
        }
        $i++;
    }

    for ($i = 1; $i <= countTriade($colonne); $i++) {
        $ordre = $colonne[$i].";";
    }
    $ordre = preg_replace('/:$/', "", $ordre);
}

if ($ok == 1) {
    require_once "./librairie_php/class.writeexcel_workbook.inc.php";
    require_once "./librairie_php/class.writeexcel_worksheet.inc.php";

    $fichier = "./data/fichier_ASCII/export_".$_SESSION["id_pers"].".xls";
    @unlink($fichier);

    $workbook  = new writeexcel_workbook($fichier);
    $worksheet1 = $workbook->addworksheet('Listing');
    $worksheet1->set_selection('A0');

    $header = $workbook->addformat();
    $header->set_color('white');
    $header->set_align('center');
    $header->set_align('vcenter');
    $header->set_pattern();
    $header->set_fg_color('blue');

    $center = $workbook->addformat();
    $center->set_align('left');

    $j = 0;
    for ($i = 1; $i <= countTriade($colonne); $i++) {
        $titre = $colonne[$i];
        $worksheet1->write(0, $j, "$titre", $header);
        $j++;
    }

    $datalisting = listingEleve();

    $ii = 1;
    for ($i = 0; $i < countTriade($datalisting); $i++) {
        $a = 1;
        for ($j = 0; $j < countTriade($colonne); $j++) {
            $donnee = "";
            if ($colonne[$a] == "nom")                          { $donnee = $datalisting[$i][1]; }
            if ($colonne[$a] == "prenom")                       { $donnee = $datalisting[$i][2]; }
            if ($colonne[$a] == "classe")                       { $donnee = chercheClasse_nom($datalisting[$i][3]); }
            if ($colonne[$a] == "lv1")                          { $donnee = $datalisting[$i][4]; }
            if ($colonne[$a] == "lv2")                          { $donnee = $datalisting[$i][5]; }
            if ($colonne[$a] == "option")                       { $donnee = $datalisting[$i][6]; }
            if ($colonne[$a] == "regime")                       { $donnee = $datalisting[$i][7]; }
            if ($colonne[$a] == "date_naissance")               { $donnee = dateForm($datalisting[$i][8]); }
            if ($colonne[$a] == "lieu_naissance")               { $donnee = $datalisting[$i][9]; }
            if ($colonne[$a] == "nationalite")                  { $donnee = $datalisting[$i][10]; }
            if ($colonne[$a] == "civ_1")                        { $donnee = civ($datalisting[$i][13]); }
            if ($colonne[$a] == "nomtuteur")                    { $donnee = $datalisting[$i][14]; }
            if ($colonne[$a] == "prenomtuteur")                 { $donnee = $datalisting[$i][15]; }
            if ($colonne[$a] == "adr1")                         { $donnee = $datalisting[$i][16]; }
            if ($colonne[$a] == "code_post_adr1")               { $donnee = $datalisting[$i][17]; }
            if ($colonne[$a] == "commune_adr1")                 { $donnee = $datalisting[$i][18]; }
            if ($colonne[$a] == "tel_port_1")                   { $donnee = $datalisting[$i][19]; }
            if ($colonne[$a] == "civ_2")                        { $donnee = civ($datalisting[$i][20]); }
            if ($colonne[$a] == "nomtuteur_2")                  { $donnee = $datalisting[$i][21]; }
            if ($colonne[$a] == "prenomtuteur_2")               { $donnee = $datalisting[$i][22]; }
            if ($colonne[$a] == "adr2")                         { $donnee = $datalisting[$i][23]; }
            if ($colonne[$a] == "code_post_adr2")               { $donnee = $datalisting[$i][24]; }
            if ($colonne[$a] == "commune_adr2")                 { $donnee = $datalisting[$i][25]; }
            if ($colonne[$a] == "tel_port_2")                   { $donnee = $datalisting[$i][26]; }
            if ($colonne[$a] == "telephone")                    { $donnee = $datalisting[$i][27]; }
            if ($colonne[$a] == "profession_pere")              { $donnee = $datalisting[$i][28]; }
            if ($colonne[$a] == "tel_prof_pere")                { $donnee = $datalisting[$i][29]; }
            if ($colonne[$a] == "profession_mere")              { $donnee = $datalisting[$i][30]; }
            if ($colonne[$a] == "tel_prof_mere")                { $donnee = $datalisting[$i][31]; }
            if ($colonne[$a] == "nom_etablissement")            { $donnee = $datalisting[$i][32]; }
            if ($colonne[$a] == "numero_etablissement")         { $donnee = $datalisting[$i][33]; }
            if ($colonne[$a] == "code_postal_etablissement")    { $donnee = $datalisting[$i][34]; }
            if ($colonne[$a] == "commune_etablissement")        { $donnee = $datalisting[$i][35]; }
            if ($colonne[$a] == "numero_eleve")                 { $donnee = $datalisting[$i][36]; }
            if ($colonne[$a] == "photo")                        { $donnee = $datalisting[$i][37]; }
            if ($colonne[$a] == "email")                        { $donnee = $datalisting[$i][38]; }
            if ($colonne[$a] == "email_eleve")                  { $donnee = $datalisting[$i][39]; }
            if ($colonne[$a] == "email_resp_2")                 { $donnee = $datalisting[$i][40]; }
            if ($colonne[$a] == "class_ant")                    { $donnee = $datalisting[$i][41]; }
            if ($colonne[$a] == "annee_ant")                    { $donnee = $datalisting[$i][42]; }
            if ($colonne[$a] == "numero_gep")                   { $donnee = $datalisting[$i][44]; }
            if ($colonne[$a] == "valid_forward_mail_eleve")     { $donnee = strtolower($datalisting[$i][45]); }
            if ($colonne[$a] == "tel_eleve")                    { $donnee = $datalisting[$i][46]; }
            if ($colonne[$a] == "valid_forward_mail_parent")    { $donnee = strtolower($datalisting[$i][47]); }
            if ($colonne[$a] == "sexe")                         { $donnee = $datalisting[$i][48]; }
            if ($colonne[$a] == "code_barre")                   { $donnee = recupIdCodeBar($datalisting[$i][0], "menueleve"); }
            if ($colonne[$a] == "adresse_eleve")                { $donnee = $datalisting[$i][50]; }
            if ($colonne[$a] == "ccp_eleve")                    { $donnee = $datalisting[$i][51]; }
            if ($colonne[$a] == "commune_eleve")                { $donnee = $datalisting[$i][52]; }
            if ($colonne[$a] == "pays_eleve")                   { $donnee = $datalisting[$i][53]; }
            if ($colonne[$a] == "email_eleve_pro")              { $donnee = $datalisting[$i][54]; }
            if ($colonne[$a] == "annee_scolaire")               { $donnee = $datalisting[$i][55]; }
            if ($colonne[$a] == "information")                  { $donnee = $datalisting[$i][56]; }
            if ($colonne[$a] == "tel_fixe_eleve")               { $donnee = $datalisting[$i][57]; }
            $worksheet1->write_string($ii, $j, utf8_decode("$donnee"), $center);
            $a++;
        }
        $ii++;
    }

    $workbook->close();
}
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

  <?php if ($ok == 1 && $fichier): ?>
  <!-- Téléchargement -->
  <div class="card">
    <div class="card-header" style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#2e7d32">
      <i class="bi bi-check-circle-fill"></i> Fichier généré avec succès
    </div>
    <div class="card-body" style="padding:10px 14px">
      <p style="font-size:12px;color:#555;margin:0 0 10px">Votre fichier Excel est prêt. Cliquez pour le télécharger.</p>
      <button type="button"
              onclick="open('visu_document.php?fichier=<?php print urlencode($fichier) ?>','_blank','')"
              class="btn btn-primary" style="background:#2e7d32;border-color:#2e7d32;display:inline-flex;align-items:center;gap:8px">
        <i class="bi bi-file-earmark-excel"></i> Récupérer l'exportation
      </button>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($nosauvegarde == 0): ?>
  <!-- Sauvegarde de structure -->
  <div class="card" style="border-top:3px solid #3949ab">
    <div class="card-header" style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#080A66">
      <i class="bi bi-bookmark-plus"></i> Sauvegarder la structure d'exportation
    </div>
    <div class="card-body" style="padding:10px 14px">
      <p style="font-size:11px;color:#888;margin:0 0 10px;font-style:italic">
        Récupérez d'abord votre fichier Excel, puis nommez et sauvegardez cette structure pour la réutiliser.
      </p>
      <?php
      $colonne_ser = serialize($colonne);
      $colonne_ser = preg_replace("/'/","&#146;",$colonne_ser);
      ?>
      <form method="post" action="export.php" name="formulaire" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
        <input type="hidden" name="structure" value="<?php print $colonne_ser ?>">
        <label style="font-size:12px;font-weight:600;color:#333;white-space:nowrap">Nom de la structure :</label>
        <input type="text" name="nom_structure" maxlength="200"
               style="border:1px solid #c5cae9;border-radius:6px;padding:6px 10px;font-size:12px;flex:1;min-width:120px">
        <button type="submit" name="savestructure" class="btn btn-primary" style="padding:6px 16px">
          <i class="bi bi-bookmark-check"></i> Sauvegarder
        </button>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <div>
    <script language=JavaScript>buttonMagicRetour("export.php","_self")</script>
  </div>

</div>

<!-- // fin -->
<?php Pgclose(); ?>
</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
