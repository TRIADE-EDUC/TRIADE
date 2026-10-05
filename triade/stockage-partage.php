<?php
session_start();
error_reporting(0);

if (empty($_SESSION["nom"])) {
    header('Location: acces_refuse.php');
    exit;
}

if (file_exists("./common/config.inc.php")) include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/lib_licence.php");
$cnx = cnx();

function mimetype($fichier, $quoi) {
    global $mess, $HTTP_USER_AGENT;
    if (!preg_match("/MSIE/i", $HTTP_USER_AGENT)) { $client = "netscape.gif"; } else { $client = "html.gif"; }
    if (is_dir($fichier))                              { $image = "dossier.gif"; $nom_type = $mess[8]; }
    elseif (preg_match('/\.mid$/i', $fichier))         { $image = "mid.gif";    $nom_type = $mess[9]; }
    elseif (preg_match('/\.txt$/i', $fichier))         { $image = "txt.gif";    $nom_type = $mess[10]; }
    elseif (preg_match('/\.sql$/i', $fichier))         { $image = "txt.gif";    $nom_type = $mess[10]; }
    elseif (preg_match('/\.js$/i', $fichier))          { $image = "js.gif";     $nom_type = $mess[11]; }
    elseif (preg_match('/\.gif$/i', $fichier))         { $image = "gif.gif";    $nom_type = $mess[12]; }
    elseif (preg_match('/\.jpg$/i', $fichier))         { $image = "jpg.gif";    $nom_type = $mess[13]; }
    elseif (preg_match('/\.html$/i', $fichier))        { $image = $client;      $nom_type = $mess[14]; }
    elseif (preg_match('/\.htm$/i', $fichier))         { $image = $client;      $nom_type = $mess[15]; }
    elseif (preg_match('/\.rar$/i', $fichier))         { $image = "rar.gif";    $nom_type = $mess[60]; }
    elseif (preg_match('/\.gz$/i', $fichier))          { $image = "zip.gif";    $nom_type = $mess[61]; }
    elseif (preg_match('/\.tgz$/i', $fichier))         { $image = "zip.gif";    $nom_type = $mess[61]; }
    elseif (preg_match('/\.z$/i', $fichier))           { $image = "zip.gif";    $nom_type = $mess[61]; }
    elseif (preg_match('/\.ra$/i', $fichier))          { $image = "ram.gif";    $nom_type = $mess[16]; }
    elseif (preg_match('/\.ram$/i', $fichier))         { $image = "ram.gif";    $nom_type = $mess[17]; }
    elseif (preg_match('/\.rm$/i', $fichier))          { $image = "ram.gif";    $nom_type = $mess[17]; }
    elseif (preg_match('/\.pl$/i', $fichier))          { $image = "pl.gif";     $nom_type = $mess[18]; }
    elseif (preg_match('/\.zip$/i', $fichier))         { $image = "zip.gif";    $nom_type = $mess[19]; }
    elseif (preg_match('/\.wav$/i', $fichier))         { $image = "wav.gif";    $nom_type = $mess[20]; }
    elseif (preg_match('/\.php$/i', $fichier))         { $image = "php.gif";    $nom_type = $mess[21]; }
    elseif (preg_match('/\.phtml$/i', $fichier))       { $image = "php.gif";    $nom_type = $mess[22]; }
    elseif (preg_match('/\.exe$/i', $fichier))         { $image = "exe.gif";    $nom_type = $mess[50]; }
    elseif (preg_match('/\.bmp$/i', $fichier))         { $image = "bmp.gif";    $nom_type = $mess[56]; }
    elseif (preg_match('/\.png$/i', $fichier))         { $image = "gif.gif";    $nom_type = $mess[57]; }
    elseif (preg_match('/\.css$/i', $fichier))         { $image = "css.gif";    $nom_type = $mess[58]; }
    elseif (preg_match('/\.mp3$/i', $fichier))         { $image = "mp3.gif";    $nom_type = $mess[59]; }
    elseif (preg_match('/\.xls$/i', $fichier))         { $image = "xls.gif";    $nom_type = $mess[64]; }
    elseif (preg_match('/\.doc$/i', $fichier))         { $image = "doc.gif";    $nom_type = $mess[65]; }
    elseif (preg_match('/\.pdf$/i', $fichier))         { $image = "pdf.gif";    $nom_type = $mess[79]; }
    elseif (preg_match('/\.mov$/i', $fichier))         { $image = "mov.gif";    $nom_type = $mess[80]; }
    elseif (preg_match('/\.avi$/i', $fichier))         { $image = "avi.gif";    $nom_type = $mess[81]; }
    elseif (preg_match('/\.mpg$/i', $fichier))         { $image = "mpg.gif";    $nom_type = $mess[82]; }
    elseif (preg_match('/\.mpeg$/i', $fichier))        { $image = "mpeg.gif";   $nom_type = $mess[83]; }
    elseif (preg_match('/\.swf$/i', $fichier))         { $image = "flash.gif";  $nom_type = $mess[91]; }
    else                                               { $image = "defaut.gif"; $nom_type = $mess[23]; }
    if ($quoi == "image") { return $image; } else { return $nom_type; }
}

function taille($fichier) {
    global $size_unit;
    $taille = filesize($fichier);
    if ($taille >= 1073741824)  { $taille = round($taille / 1073741824 * 100) / 100 . " G" . $size_unit; }
    elseif ($taille >= 1048576) { $taille = round($taille / 1048576 * 100)    / 100 . " M" . $size_unit; }
    elseif ($taille >= 1024)    { $taille = round($taille / 1024 * 100)       / 100 . " K" . $size_unit; }
    else                        { $taille = $taille . " " . $size_unit; }
    if ($taille == 0) { $taille = "-"; }
    return $taille;
}

$idpers = $_SESSION["id_pers"];
$membre = $_SESSION["membre"];

$data = recupListFichierPartager($idpers, $membre);
// colonnes : fichier, chemin, membreIdProprio, membreIdAutorise, idclasse, membresource, idsource, id
$nb = countTriade($data);
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="CacheControl" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<title>TRIADE-BOX &mdash; Partage</title>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<style>
body { background:#f0f2fa; margin:0; padding:16px; font-family:'Trebuchet MS',Arial,sans-serif; }
.sp-wrap { max-width:760px; margin:0 auto; }

.sp-header {
    background:linear-gradient(135deg,#0d1b4a 0%,#1e4d8c 100%);
    border-radius:12px; padding:14px 20px;
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:18px; box-shadow:0 4px 16px rgba(0,0,0,.18);
}
.sp-header-title {
    color:#fff; font-family:Electrolize,'Trebuchet MS',Arial;
    font-size:15px; font-weight:800; letter-spacing:1px; text-transform:uppercase;
}
.sp-header-nav { display:flex; gap:10px; align-items:center; }
.sp-btn-nav {
    background:rgba(255,255,255,.15); color:#fff; border:1px solid rgba(255,255,255,.3);
    border-radius:8px; padding:6px 14px; font-size:12px; font-weight:700;
    cursor:pointer; text-decoration:none; white-space:nowrap;
    display:inline-flex; align-items:center; gap:6px;
}
.sp-btn-nav:hover { background:rgba(255,255,255,.25); color:#fff; }
.sp-btn-nav.active { background:#fff; color:#0d1b4a; border-color:#fff; }

.sp-card {
    background:#fff; border-radius:12px;
    box-shadow:0 2px 10px rgba(0,0,0,.08); overflow:hidden;
}
.sp-card-header {
    background:linear-gradient(90deg,#080A66,#1e4d8c);
    color:#fff; padding:10px 16px;
    font-size:12px; font-weight:700; font-family:Electrolize,'Trebuchet MS',Arial;
    letter-spacing:.5px; text-transform:uppercase;
    display:flex; align-items:center; justify-content:space-between;
}
.sp-badge {
    background:rgba(255,255,255,.25); border-radius:20px;
    padding:2px 10px; font-size:11px; font-weight:700;
}
.sp-empty {
    text-align:center; padding:40px 20px; color:#888; font-size:13px;
}
.sp-empty-icon { font-size:40px; margin-bottom:8px; }
</style>
</head>
<body>

<div class="sp-wrap">

  <!-- ── Header ── -->
  <div class="sp-header">
    <div class="sp-header-title">TRIADE-BOX</div>
    <div class="sp-header-nav">
      <a href="stockage.php" class="sp-btn-nav">
        <img src="image/stockage/hdd.gif" alt="" style="height:16px;vertical-align:middle;">
        Mon stockage
      </a>
      <a href="stockage-partage.php" class="sp-btn-nav active">
        <img src="image/stockage/reseau.png" alt="" style="height:16px;vertical-align:middle;">
        Partage
      </a>
    </div>
  </div>

  <!-- ── Liste fichiers partagés ── -->
  <div class="sp-card">
    <div class="sp-card-header">
      Fichiers partagés avec moi
      <span class="sp-badge"><?php echo $nb; ?> fichier<?php echo $nb > 1 ? 's' : ''; ?></span>
    </div>

    <?php if ($nb == 0): ?>
    <div class="sp-empty">
      <div class="sp-empty-icon">&#128193;</div>
      Aucun fichier partagé avec vous.
    </div>
    <?php else: ?>
    <table class="cc-data-table">
      <thead>
        <tr class="cc-thead-row">
          <th class="cc-th" style="width:32px;"></th>
          <th class="cc-th">Fichier</th>
          <th class="cc-th" style="width:90px;">Taille</th>
          <th class="cc-th" style="width:120px;">Modifi&eacute; le</th>
          <th class="cc-th cc-th-center" style="width:80px;">T&eacute;l&eacute;charger</th>
        </tr>
      </thead>
      <tbody>
      <?php for ($i = 0; $i < $nb; $i++):
          $fichier      = $data[$i][0];
          $chemin       = $data[$i][1];
          $membresource = $data[$i][5];
          $idsource     = $data[$i][6];
          $id           = $data[$i][7];
          $path         = "./data/stockage/$membresource/$idsource/$chemin";

          if (!file_exists($path)) {
              suppFichierPartager($id);
              continue;
          }
          $tmp = filemtime($path);
      ?>
        <tr class="cc-tr-data">
          <td class="cc-td cc-td-center">
            <img src="image/stockage/<?php echo mimetype($path, "image"); ?>" style="width:20px;height:20px;vertical-align:middle;">
          </td>
          <td class="cc-td"><?php echo htmlspecialchars($fichier); ?></td>
          <td class="cc-td"><?php echo taille($path); ?></td>
          <td class="cc-td" style="font-size:11px;"><?php echo date("d/m/Y H:i", $tmp); ?></td>
          <td class="cc-td cc-td-center">
            <a href="stockage-download.php?id=<?php echo $id; ?>" target="_blank" title="T&eacute;l&eacute;charger">
              <img src="./image/stockage/download.gif" alt="DL" style="vertical-align:middle;">
            </a>
          </td>
        </tr>
      <?php endfor; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>

</div>

<?php Pgclose(); ?>
</body>
</html>
