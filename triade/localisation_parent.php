<?php
session_start();
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./leaflet/leaflet.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./leaflet/leaflet.js"></script>
<title>Triade &mdash; Localisation</title>
<style>
.lp-wrap { max-width:820px; margin:0 auto; padding:0 0 20px; }

.lp-header {
    background:linear-gradient(135deg,#0d1b4a 0%,#1e4d8c 100%);
    border-radius:12px; padding:14px 20px;
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:16px; box-shadow:0 4px 16px rgba(0,0,0,.18);
}
.lp-header-title {
    color:#fff; font-family:Electrolize,'Trebuchet MS',Arial;
    font-size:15px; font-weight:800; letter-spacing:1px; text-transform:uppercase;
}
.lp-header-sub { color:#b0c4de; font-size:12px; margin-top:3px; }

.lp-card {
    background:#fff; border-radius:12px;
    box-shadow:0 2px 10px rgba(0,0,0,.08); overflow:hidden; margin-bottom:14px;
}
.lp-card-header {
    background:linear-gradient(90deg,#080A66,#1e4d8c);
    color:#fff; padding:10px 16px;
    font-size:12px; font-weight:700; font-family:Electrolize,'Trebuchet MS',Arial;
    letter-spacing:.5px; text-transform:uppercase;
    display:flex; align-items:center; justify-content:space-between;
}
.lp-badge {
    background:rgba(255,255,255,.25); border-radius:20px;
    padding:2px 10px; font-size:11px; font-weight:700;
}

#lp-map { height:420px; width:100%; }

.lp-history { padding:0; }
.lp-history table { width:100%; border-collapse:collapse; }
.lp-no-data {
    text-align:center; padding:40px 20px; color:#888; font-size:13px;
}
.lp-no-data-icon { font-size:40px; margin-bottom:8px; }

.lp-legend {
    padding:10px 16px; font-size:11px; color:#555;
    border-top:1px solid #e8eaf6;
    display:flex; align-items:center; gap:14px; flex-wrap:wrap;
}
.lp-dot { width:12px; height:12px; border-radius:50%; display:inline-block; vertical-align:middle; margin-right:4px; }
.lp-dot-recent { background:#2e7d32; }
.lp-dot-old    { background:#1565c0; }
.lp-dot-last   { background:#c62828; }
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre'].".js'"; ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre']."1.js'"; ?>></SCRIPT>

<?php
include_once("librairie_php/db_triade.php");
$cnx = cnx();

$idEleve = intval($_SESSION["id_pers"]);
$nomEleve = '';
$prenomEleve = '';
$gpsData = [];
$nbPoints = 0;
$points = [];

function safeChargeMat($res) {
    if (!$res || !is_object($res) || DB::isError($res)) return [];
    return chargeMat($res);
}

$eleveData   = safeChargeMat(execSql(
    "SELECT nom, prenom FROM {$prefixe}eleves WHERE elev_id='$idEleve' LIMIT 1"
));
$nomEleve    = isset($eleveData[0][0]) ? strtoupper(stripslashes($eleveData[0][0]))  : '';
$prenomEleve = isset($eleveData[0][1]) ? ucfirst(stripslashes($eleveData[0][1])) : '';

$gpsData  = safeChargeMat(execSql(
    "SELECT date_gps, heure_gps, latitude, longitude, precision_m
     FROM {$prefixe}gps_eleve
     WHERE id_eleve='$idEleve'
     ORDER BY date_gps DESC, heure_gps DESC
     LIMIT 200"
));
$nbPoints = countTriade($gpsData);

for ($i = 0; $i < $nbPoints; $i++) {
    $points[] = [
        'date'      => $gpsData[$i][0],
        'heure'     => substr($gpsData[$i][1], 0, 5),
        'lat'       => floatval($gpsData[$i][2]),
        'lng'       => floatval($gpsData[$i][3]),
        'precision' => ($gpsData[$i][4] !== null && $gpsData[$i][4] !== '') ? floatval($gpsData[$i][4]) : null,
    ];
}
?>

<div class="lp-wrap">

  <!-- ── Header ── -->
  <div class="lp-header">
    <div>
      <div class="lp-header-title">&#128205; Localisation</div>
      <div class="lp-header-sub">
        <?php echo htmlspecialchars("$prenomEleve $nomEleve"); ?>
        &mdash; 48 derni&egrave;res heures
      </div>
    </div>
    <div style="color:#b0c4de;font-size:11px;text-align:right;">
      <?php echo $nbPoints; ?> point<?php echo $nbPoints > 1 ? 's' : ''; ?> GPS
    </div>
  </div>

  <?php if ($nbPoints == 0): ?>
  <!-- ── Aucune donnée ── -->
  <div class="lp-card">
    <div class="lp-card-header">Carte</div>
    <div class="lp-no-data">
      <div class="lp-no-data-icon">&#128205;</div>
      Aucune position GPS disponible pour les 48 derni&egrave;res heures.<br>
      <span style="font-size:11px;color:#aaa;">L'application mobile doit &ecirc;tre active sur le t&eacute;l&eacute;phone de l'&eacute;l&egrave;ve.</span>
    </div>
  </div>

  <?php else: ?>
  <!-- ── Carte ── -->
  <div class="lp-card">
    <div class="lp-card-header">
      Carte
      <span class="lp-badge">derniers 2 jours</span>
    </div>
    <div id="lp-map"></div>
    <div class="lp-legend">
      <span><span class="lp-dot lp-dot-last"></span> Derni&egrave;re position</span>
      <span><span class="lp-dot lp-dot-recent"></span> Aujourd'hui</span>
      <span><span class="lp-dot lp-dot-old"></span> Hier</span>
    </div>
  </div>

  <!-- ── Historique ── -->
  <div class="lp-card">
    <div class="lp-card-header">
      Historique des positions
      <span class="lp-badge"><?php echo $nbPoints; ?> point<?php echo $nbPoints > 1 ? 's' : ''; ?></span>
    </div>
    <div class="lp-history">
      <table>
        <thead>
          <tr class="cc-thead-row">
            <th class="cc-th" style="width:110px;">Date</th>
            <th class="cc-th" style="width:70px;">Heure</th>
            <th class="cc-th">Latitude</th>
            <th class="cc-th">Longitude</th>
            <th class="cc-th" style="width:90px;">Pr&eacute;cision</th>
            <th class="cc-th cc-th-center" style="width:80px;">Carte</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($points as $p): ?>
          <tr class="cc-tr-data">
            <td class="cc-td"><?php echo date('d/m/Y', strtotime($p['date'])); ?></td>
            <td class="cc-td"><?php echo htmlspecialchars($p['heure']); ?></td>
            <td class="cc-td" style="font-family:monospace;font-size:11px;text-align:center;"><?php echo number_format($p['lat'], 6); ?></td>
            <td class="cc-td" style="font-family:monospace;font-size:11px;text-align:center;"><?php echo number_format($p['lng'], 6); ?></td>
            <td class="cc-td">
              <?php if ($p['precision'] !== null): ?>
              <span style="font-size:11px;">&plusmn;&nbsp;<?php echo round($p['precision']); ?> m</span>
              <?php else: ?>
              <span style="color:#bbb;font-size:11px;">—</span>
              <?php endif; ?>
            </td>
            <td class="cc-td cc-td-center">
              <a href="#lp-map"
                 onclick="centerMap(<?php echo $p['lat']; ?>, <?php echo $p['lng']; ?>)"
                 title="Voir sur la carte"
                 style="color:#080A66;font-size:14px;">&#128205;</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

<script>
var lpPoints = <?php echo json_encode($points); ?>;
var lpMap = null;

document.addEventListener('DOMContentLoaded', function() {
    if (!lpPoints || lpPoints.length === 0) return;

    var last = lpPoints[0];
    lpMap = L.map('lp-map').setView([last.lat, last.lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
    }).addTo(lpMap);

    var today = new Date().toISOString().slice(0, 10);

    // Tracé du chemin
    var latlngs = lpPoints.map(function(p) { return [p.lat, p.lng]; });
    L.polyline(latlngs, {color: '#1565c0', weight: 2, opacity: 0.5}).addTo(lpMap);

    // Marqueurs
    lpPoints.forEach(function(p, idx) {
        var isLast    = (idx === 0);
        var isToday   = (p.date === today);
        var color     = isLast ? '#c62828' : (isToday ? '#2e7d32' : '#1565c0');
        var radius    = isLast ? 9 : 6;
        var popupText = '<b>' + p.date.split('-').reverse().join('/') + ' ' + p.heure + '</b>';
        if (p.precision !== null) popupText += '<br>&plusmn;&nbsp;' + Math.round(p.precision) + ' m';

        var circle = L.circleMarker([p.lat, p.lng], {
            radius: radius,
            fillColor: color,
            color: '#fff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.9
        }).addTo(lpMap);
        circle.bindPopup(popupText);

        if (isLast) {
            circle.openPopup();
        }

        // Cercle de précision pour la dernière position
        if (isLast && p.precision !== null) {
            L.circle([p.lat, p.lng], {
                radius: p.precision,
                fillColor: '#c62828',
                color: '#c62828',
                weight: 1,
                opacity: 0.3,
                fillOpacity: 0.1
            }).addTo(lpMap);
        }
    });
});

function centerMap(lat, lng) {
    if (lpMap) {
        lpMap.setView([lat, lng], 16);
    }
}
</script>

  <?php endif; ?>

</div>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    echo "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
} else {
    echo "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    echo "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
}
Pgclose();
?>
</body></html>
