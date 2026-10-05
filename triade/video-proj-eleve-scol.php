<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 ***************************************************************************/
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta charset="utf-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<link rel="stylesheet" href="librairie_css/css-v4.css">
<link rel="stylesheet" href="librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script language="JavaScript" src="librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/scriptaculous.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<title>Triade Vidéo-Projecteur</title>
<style>
* { box-sizing: border-box; }
html, body { height: 100%; margin: 0; padding: 0; font-family: Electrolize, Trebuchet MS, Arial, sans-serif; background: #fff; overflow-x: hidden; }
.sc-body { padding: 8px 12px; }
.sc-section { margin-bottom: 10px; }
.sc-title { font-size: 10px; font-weight: 700; color: #080A66; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 5px; border-bottom: 1px solid #f0f2fa; padding-bottom: 3px; }
.sc-row { display: flex; align-items: center; justify-content: space-between; font-size: 12px; color: #333; padding: 2px 0; }
.sc-label { color: #555; }
.sc-val { font-weight: 700; color: #080A66; }
.sc-sub { font-size: 11px; color: #777; }
.sc-link { font-size: 11px; color: #080A66; text-decoration: none; margin-left: 6px; }
.sc-link:hover { text-decoration: underline; }
.sc-badge { display: inline-block; font-size: 10px; padding: 1px 7px; border-radius: 10px; background: #f0f2fa; color: #080A66; font-weight: 600; margin-left: 4px; }
.sc-sanction { font-size: 11px; color: #444; display: flex; align-items: center; gap: 5px; padding: 2px 0; }
.sc-sanction select { font-size: 10px; border: 1px solid #dde0f0; border-radius: 3px; padding: 1px 4px; color: #080A66; background: #f8f9ff; }
.sc-empty { font-size: 11px; color: #aaa; font-style: italic; }

/* Popups listing */
.sc-popup {
  position: absolute; top: 10px; left: 10px; right: 10px;
  display: none; z-index: 1000;
  background: #fff; border: 1px solid #CACCEF; border-radius: 7px;
  box-shadow: 0 6px 20px rgba(8,10,102,.18); overflow: hidden;
}
.sc-popup-header { background: #080A66; color: #fff; padding: 6px 12px; font-size: 12px; font-weight: 700; }
.sc-popup-body { padding: 10px 12px; font-size: 11px; color: #333; max-height: 160px; overflow-y: auto; line-height: 1.7; }
.sc-popup-footer { padding: 6px 12px; border-top: 1px solid #f0f2fa; }
</style>
</head>
<body>
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
if ($_SESSION["membre"] == "menupersonnel") {
  if ((!verifDroit($_SESSION["id_pers"],"ficheeleve")) && (!verifDroit($_SESSION["id_pers"],"videoprojo"))) {
    Pgclose(); accesNonReserveFen(); exit();
  }
}else{
  validerequete("3");
}

$ideleve=$_GET["saisie_eleve"];
$idclasse=$_GET["saisie_classe"];
$trim_en_cours=$_GET["trimestre"];

$dateRecup=recupDateTrimByIdclasse($trim_en_cours,$idclasse);
for($j=0;$j<countTriade($dateRecup);$j++) {
  $dateDebut=$dateRecup[$j][0];
  $dateFin=$dateRecup[$j][1];
}
?>

<?php if ($ideleve == ""): ?>
<div class="sc-body"><p class="sc-empty"><i class="bi bi-person-x"></i> <?php print LANGPROJ6 ?></p></div>
<?php else: ?>

<?php
// Absences
$data_2=nombre_abs($ideleve,$dateDebut,$dateFin);
$cumulabs=0; $cumulabsheure=0; $listingAbs="";
for($j=0;$j<countTriade($data_2);$j++) {
  $listingAbs.="&bull; ".dateForm($data_2[$j][1])." — ".$data_2[$j][3]." / ".$data_2[$j][6]."<br>";
  if ($data_2[$j][4] > 0) $cumulabs+=$data_2[$j][4]; else $cumulabsheure+=$data_2[$j][7];
}
// Retards
$data_3=nombre_retard($ideleve,$dateDebut,$dateFin);
$cumulrtds=0; $listingRtd="";
for($j=0;$j<countTriade($data_3);$j++) {
  $listingRtd.="&bull; ".dateForm($data_3[$j][2])." — ".$data_3[$j][4]." / ".$data_3[$j][6]."<br>";
  $nbminute=preg_replace('/mn/','',$data_3[$j][5]);
  if (preg_match('/[0-9]h/',$data_3[$j][5])) {
    list($heure,$minute)=preg_split('/h/',$data_3[$j][5],2);
    $nbminute=$heure*60+$minute;
  }
  $cumulrtds+=$nbminute;
}
// Sanctions
$nb_retenue=0;
$data_ret=affRetenuTotal_par_eleve_trimestre($ideleve,$dateDebut,$dateFin);
if (countTriade($data_ret)>0) $nb_retenue=countTriade($data_ret);
$sanction=array(); $sanctionqui=array();
$data_s=affSanction_par_eleve_trimestre($ideleve,$dateDebut,$dateFin);
for($j=0;$j<countTriade($data_s);$j++) {
  $sanction[$data_s[$j][3]]++;
  $sanctionqui[$data_s[$j][3]]="<option>".$data_s[$j][7]."</option>";
}
ksort($sanction); ksort($sanctionqui);
?>

<!-- Popup listing absences -->
<div id="affabs" class="sc-popup">
  <div class="sc-popup-header"><i class="bi bi-calendar-x"></i> Absences — listing</div>
  <div class="sc-popup-body"><?php print $listingAbs ?: '<em>Aucune absence</em>' ?></div>
  <div class="sc-popup-footer">
    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('affabs').style.display='none'">
      <i class="bi bi-x-lg"></i> <?php print LANGFERMERFEN ?>
    </button>
  </div>
</div>

<!-- Popup listing retards -->
<div id="affrtd" class="sc-popup">
  <div class="sc-popup-header"><i class="bi bi-clock-history"></i> Retards — listing</div>
  <div class="sc-popup-body"><?php print $listingRtd ?: '<em>Aucun retard</em>' ?></div>
  <div class="sc-popup-footer">
    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('affrtd').style.display='none'">
      <i class="bi bi-x-lg"></i> <?php print LANGFERMERFEN ?>
    </button>
  </div>
</div>

<div class="sc-body">

  <!-- Absentéisme -->
  <div class="sc-section">
    <div class="sc-title"><i class="bi bi-calendar-x"></i> Absentéisme</div>
    <div class="sc-row">
      <span class="sc-label">Nb d'absences</span>
      <span>
        <span class="sc-val"><?php print countTriade($data_2) ?></span>
        <?php if (countTriade($data_2)) print "<a href='#' class='sc-link' onclick=\"document.getElementById('affabs').style.display='block';return false;\"><i class='bi bi-list-ul'></i> listing</a>"; ?>
      </span>
    </div>
    <div class="sc-row">
      <span class="sc-label sc-sub">Cumul</span>
      <span class="sc-sub"><b><?php print $cumulabs ?></b> <?php print LANGPROJ18 ?> / <b><?php print $cumulabsheure ?></b> <?php print LANGPROJ18bis ?></span>
    </div>
  </div>

  <!-- Retards -->
  <div class="sc-section">
    <div class="sc-title"><i class="bi bi-clock-history"></i> Retards</div>
    <div class="sc-row">
      <span class="sc-label"><?php print LANGPROJ7 ?></span>
      <span>
        <span class="sc-val"><?php print countTriade($data_3) ?></span>
        <?php if (countTriade($data_3)) print "<a href='#' class='sc-link' onclick=\"document.getElementById('affrtd').style.display='block';return false;\"><i class='bi bi-list-ul'></i> listing</a>"; ?>
      </span>
    </div>
    <div class="sc-row">
      <span class="sc-label sc-sub"><?php print LANGPROJ8 ?></span>
      <span class="sc-sub"><b><?php print $cumulrtds ?></b> <?php print LANGPROJ10 ?></span>
    </div>
  </div>

  <!-- Sanctions -->
  <div class="sc-section">
    <div class="sc-title"><i class="bi bi-exclamation-triangle"></i> <?php print LANGPROJ9 ?></div>
    <div class="sc-row">
      <span class="sc-label"><?php print LANGPROJ11 ?></span>
      <span class="sc-val"><?php print $nb_retenue ?></span>
    </div>
    <?php foreach($sanction as $cle => $value): ?>
    <?php $cat=rechercheCategory($cle); ?>
    <div class="sc-sanction">
      <span class="sc-label" title="<?php print $cat ?>"><?php print trunchaine($cat,18) ?></span>
      <span class="sc-badge"><?php print $value ?></span>
      <select title="<?php print LANGPROJ12 ?>">
        <option><?php print LANGPROJ13 ?></option>
        <?php if (isset($sanctionqui[$cle])) print trunchaine($sanctionqui[$cle],25); ?>
      </select>
    </div>
    <?php endforeach; ?>
  </div>

</div>
<?php endif; ?>
<?php Pgclose(); ?>
</body>
</html>
