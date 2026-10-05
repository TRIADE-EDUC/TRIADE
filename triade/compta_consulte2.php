<?php
session_start();
if (isset($_POST["anneescolairefiltre"])) {
    setcookie("anneeScolaire", $_POST["anneescolairefiltre"], time()+36000*24*30);
    $anneescolairefiltre = $_POST["anneescolairefiltre"];
}
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/alertify.min.css">
<link rel="stylesheet" href="./librairie_css/alertify.default.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_compta.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]"; ?></title>
<style>
.cc2-wrap { padding:0 0 16px; }

/* Carte générique */
.cc2-card { background:#fff; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,.08); margin-bottom:14px; overflow:hidden; }
.cc2-card-header { background:linear-gradient(90deg,#080A66,#1e4d8c); color:#fff; padding:9px 14px; font-size:12px; font-weight:700; font-family:Electrolize,'Trebuchet MS',Arial; letter-spacing:.5px; text-transform:uppercase; display:flex; align-items:center; justify-content:space-between; }
.cc2-card-header-badge { background:rgba(255,255,255,.22); border-radius:20px; padding:2px 10px; font-size:11px; }

/* Fiche élève */
.cc2-student { display:flex; gap:14px; padding:14px 16px; align-items:flex-start; }
.cc2-photo img { border-radius:8px; border:2px solid #e8eaf6; display:block; }
.cc2-info { flex:1; }
.cc2-info-name { font-size:16px; font-weight:800; color:#080A66; margin-bottom:8px; }
.cc2-info-name a { color:#080A66; text-decoration:none; border-bottom:2px solid #c5cae9; }
.cc2-info-name a:hover { border-color:#080A66; }
.cc2-info-grid { display:grid; grid-template-columns:1fr 1fr; gap:4px 16px; font-size:12px; }
.cc2-info-item { display:flex; gap:6px; }
.cc2-info-label { font-weight:600; color:#1a237e; white-space:nowrap; }
.cc2-info-val { color:#333; }
.cc2-year-row { margin-top:10px; display:flex; align-items:center; gap:8px; font-size:12px; }
.cc2-year-label { font-weight:600; color:#1a237e; }
.cc2-year-select { padding:4px 10px; border:1px solid #c5cae9; border-radius:6px; font-size:12px; background:#fff; }

/* Légende statuts */
.cc2-legend { display:flex; gap:16px; align-items:center; padding:8px 14px; background:#f5f7ff; border-top:1px solid #e8eaf6; font-size:11px; color:#555; flex-wrap:wrap; }
.cc2-legend-item { display:flex; align-items:center; gap:5px; }

/* Résumé financier */
.cc2-summary { display:flex; gap:24px; padding:12px 16px; background:#eef1ff; flex-wrap:wrap; border-top:1px solid #c5cae9; }
.cc2-summary-item { font-size:13px; }
.cc2-summary-label { font-weight:600; color:#1a237e; display:block; font-size:11px; text-transform:uppercase; letter-spacing:.4px; }
.cc2-summary-val { font-weight:800; color:#080A66; font-size:15px; }

/* Boutons */
.cc2-btn-row { padding:10px 14px 4px; display:flex; gap:10px; }

/* Sélection homonymes */
.cc2-select-btn { padding:4px 12px; background:#080A66; color:#fff; border:none; border-radius:6px; font-size:12px; cursor:pointer; }
.cc2-select-btn:hover { background:#1e4d8c; }

/* Actions dans le tableau */
.cc2-actions { display:flex; align-items:center; gap:6px; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx = cnx();

if (isset($_POST["anneescolairefiltre"])) $anneescolairefiltre = $_POST["anneescolairefiltre"];
if (isset($_GET["anneescolairefiltre"]))  $anneescolairefiltre = $_GET["anneescolairefiltre"];
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'"; ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'"; ?>></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td><b><font id='menumodule1'>Modifier un versement</font></b></td></tr>
<tr id='cadreCentral0'><td valign='top' style="padding:8px 4px;">

<div class="cc2-wrap">
<?php
if (!isset($_GET["ideleve"])) {
    $nomEleve = $_POST["saisie_nom_eleve"];
    $sql = "SELECT elev_id,nom,prenom,classe FROM {$prefixe}eleves WHERE nom='$nomEleve'";
    $res  = execSql($sql);
    $data = ChargeMat($res);

    if (countTriade($data) > 1) {
        /* ── Plusieurs élèves avec ce nom ── */
?>
<div class="cc2-card">
  <div class="cc2-card-header">
    Plusieurs élèves trouvés
    <span class="cc2-card-header-badge"><?php print countTriade($data) ?> résultats</span>
  </div>
  <table class="cc-table" width="100%">
  <thead>
    <tr class="cc-thead-row">
      <th class="cc-th">Nom Prénom</th>
      <th class="cc-th">Classe</th>
      <th class="cc-th" style="width:120px; text-align:center;">Sélection</th>
    </tr>
  </thead>
  <tbody>
  <?php
  for ($i = 0; $i < countTriade($data); $i++) {
      print "<tr class='cc-tr-data'>";
      print "<td class='cc-td'>".strtoupper($data[$i][1])." ".ucfirst($data[$i][2])."</td>";
      print "<td class='cc-td'>".chercheClasse_nom($data[$i][3])."</td>";
      print "<td class='cc-td' style='text-align:center;'>";
      print "<button class='cc2-select-btn' onclick=\"open('compta_consulte2.php?ideleve=".$data[$i][0]."&anneescolairefiltre=".($anneescolairefiltre ?? '')."','_parent','')\">Sélectionner</button>";
      print "</td></tr>";
  }
  ?>
  </tbody>
  </table>
</div>
<?php
    } else {
        $ideleve = $data[0][0];
    }
} else {
    $ideleve = $_GET["ideleve"];
}

if (!empty($ideleve) && $ideleve > 0) {
    $nomeleve    = recherche_eleve_nom($ideleve);
    $prenomeleve = recherche_eleve_prenom($ideleve);
    $idclasse    = chercheClasseEleve($ideleve);
    $classe      = chercheClasse_nom($idclasse);
    $montantBourse         = montantBourse($ideleve);
    $montantIndemniteStage = montantIndemniteStage($ideleve);
    $nbmoisindemnite       = nbmoisindemnite($ideleve);
?>

<!-- ── Fiche élève ── -->
<div class="cc2-card">
  <div class="cc2-card-header">
    Dossier élève
  </div>
  <div class="cc2-student">
    <div class="cc2-photo">
      <img src="image_trombi.php?idE=<?php print $ideleve ?>" border=0 width="70">
    </div>
    <div class="cc2-info">
      <div class="cc2-info-name">
        <a href="edit_eleve.php?eid=<?php print $ideleve ?>" title="Accès à sa fiche">
          <?php print strtoupper($nomeleve) ?> <?php print ucfirst($prenomeleve) ?>
        </a>
      </div>
      <div class="cc2-info-grid">
        <div class="cc2-info-item">
          <span class="cc2-info-label">Classe :</span>
          <span class="cc2-info-val"><?php print ucwords($classe) ?></span>
        </div>
        <div class="cc2-info-item">
          <span class="cc2-info-label">Boursier :</span>
          <span class="cc2-info-val"><?php print etatBoursier($ideleve) ?> (<?php print $montantBourse ?>)</span>
        </div>
        <div class="cc2-info-item">
          <span class="cc2-info-label">Indemn. stage :</span>
          <span class="cc2-info-val"><?php print $montantIndemniteStage ?> <i>(<?php print $nbmoisindemnite ?> mois)</i></span>
        </div>
      </div>
      <form method='get' action='compta_consulte2.php' style="margin:0;">
        <div class="cc2-year-row">
          <span class="cc2-year-label">Année scolaire :</span>
          <select name='anneescolairefiltre' onchange="this.form.submit();" class="cc2-year-select">
            <?php filtreAnneeScolaireSelect($anneescolairefiltre); ?>
          </select>
          <input type='hidden' name="ideleve" value="<?php print $ideleve ?>">
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ── Tableau des versements ── -->
<div class="cc2-card">
  <div class="cc2-card-header">Versements</div>
  <table class="cc-table" width="100%" style="border-collapse:collapse;">
  <thead>
    <tr class="cc-thead-row">
      <th class="cc-th" style="width:100px;">Date d'appel</th>
      <th class="cc-th" style="width:35%;">Versement</th>
      <th class="cc-th" style="text-align:right; width:110px;">Montant dû</th>
      <th class="cc-th" style="text-align:center;">Effectué / Détail</th>
    </tr>
  </thead>
  <tbody>
<?php
  $dataV = recupConfigVersement($idclasse, $anneescolairefiltre);
  if ($dataV == "") { $dataV = array(); }
  $dataVE = recupConfigVersementEleve($ideleve, $anneescolairefiltre);
  if ($dataVE == "") { $dataVE = array(); }
  $dataV = array_merge($dataV, $dataVE);

  for ($j = 0; $j < countTriade($dataV); $j++) {
      $id = $dataV[$j][0];
      $dateversement = $dataV[$j][4];
      list($anneeversement, $moisverserment, $jourversement) = preg_split('/-/', $dateversement);
      $dateversement = "$anneeversement$moisverserment$jourversement";

      if (verifcomptaExclu($id, $ideleve)) {
          $s = "<s>"; $ss = "</s>"; $exclu = true;
      } else {
          $s = ""; $ss = ""; $exclu = false;
      }

      $data           = recupInfoVersement($ideleve, $id);
      $dateVersement  = $data[0][3];
      $anneescolaireversement = $data[0][5];
      $idvers         = $data[0][1];
      if ($dateVersement != "") { $dateVersement = dateForm($dateVersement); }
      $montantVers  = number_format($data[0][2], 2, '.', '');
      $modepaiement = nl2br($data[0][4]);
      $dateVersOr   = $dataV[$j][4];
      $montantavers = $dataV[$j][3];

      print "<tr class='cc-tr-data'>";
      print "<td class='cc-td'>&nbsp;$s".dateForm($dataV[$j][4])."$ss&nbsp;</td>";
      print "<td class='cc-td'>&nbsp;$s".$dataV[$j][2]."$ss</td>";
      print "<td class='cc-td' style='text-align:right;'><b>$s".affichageFormatMonnaie($dataV[$j][3])."$ss</b></td>";
      print "<td class='cc-td'><div class='cc2-actions'>";

      if (!$exclu) {
          if ($dateVersement != "") {
              $lien = "compta_modif.php?idvers=$idvers&date=".$data[0][3]."&ideleve=$ideleve";
          } else {
              $lien = "compta_ajout2.php?ideleve=$ideleve&anneescolaire=$anneescolairefiltre";
          }
          print "<a href='$lien'><img src='image/commun/editer.gif' border='0' title='Modifier'></a>";
          print "<a href='#' onmouseover=\"AffBulle3('Informations / Détails','./image/commun/info.jpg','$listeHoraire'); searchVersement('$ideleve','$id','$dateVersOr','$montantavers');\" onmouseout=\"HideBulle();\"><img src='image/commun/show.png' border='0' title='Détails'></a>";
      } else {
          print "<a href='#' onmouseover=\"AffBulle3('Informations / Détails','./image/commun/info.jpg','<b>Versement exonéré</b>');\" onmouseout=\"HideBulle();\"><img src='image/commun/show.png' border='0'></a>";
      }

      $dateduJour = date("Ymd");
      $dateVersOr = preg_replace('/-/', "", $dateVersOr);

      if (!$exclu) {
          if (($montantVers == "0.00") && ($dateduJour > $dateVersOr)) {
              print "<img src='image/commun/important.png' border='0' alt='Retard paiement' title='Retard paiement'>";
          }
          if (($montantVers < $dataV[$j][3]) && ($dateduJour > $dateVersOr) && ($montantVers != "0.00")) {
              print "<img src='image/commun/warning.gif' border='0' alt='Paiement incomplet' title='Paiement incomplet'>";
          }
          if ($montantVers >= $dataV[$j][3]) {
              print "<img src='image/commun/valid.gif' border='0' alt='Paiement effectué' title='Paiement effectué'>";
          }
      }

      print "</div></td></tr>";

      if (!$exclu) {
          $montantScol  += $dataV[$j][3];
          $montantregle += $montantVers;
      }
  }
  $montantScol  = affichageFormatMonnaie($montantScol);
  $montantregle = affichageFormatMonnaie($montantregle);
?>
  </tbody>
  </table>

  <!-- Légende -->
  <div class="cc2-legend">
    <div class="cc2-legend-item"><img src='image/commun/valid.gif' border='0'> Paiement effectué</div>
    <div class="cc2-legend-item"><img src='image/commun/warning.gif' border='0'> Paiement incomplet</div>
    <div class="cc2-legend-item"><img src='image/commun/important.png' border='0'> Retard paiement</div>
  </div>

  <!-- Résumé financier -->
  <?php $unite = unitemonnaie(); $bilanfinancier = $montantScol - $montantBourse - ($montantIndemniteStage * $nbmoisindemnite); ?>
  <div class="cc2-summary">
    <div class="cc2-summary-item">
      <span class="cc2-summary-label">Montant scolarité</span>
      <span class="cc2-summary-val"><?php print $montantScol ?> <?php print $unite ?></span>
    </div>
    <div class="cc2-summary-item">
      <span class="cc2-summary-label">Montant réglé</span>
      <span class="cc2-summary-val"><?php print $montantregle ?> <?php print $unite ?></span>
    </div>
    <div class="cc2-summary-item">
      <span class="cc2-summary-label">Bilan financier</span>
      <span class="cc2-summary-val"><?php print affichageFormatMonnaie($bilanfinancier) ?> <?php print $unite ?></span>
    </div>
  </div>
</div>

<!-- Bouton retour -->
<div class="cc2-btn-row">
  <script language=JavaScript>buttonMagicRetour("compta_consulte.php","_self");</script>
</div>

<?php } ?>
</div>

</td></tr></table>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
} else {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
}
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
