<?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>
<HTML>
<HEAD>
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");
include_once("../common/config6.inc.php");
include_once("../librairie_php/db_visio.php");

$cnx = cnx();
$abo = getAbonnementVisio();

// --- Lecture des salles depuis les fichiers JSON ---
$dir    = realpath(__DIR__ . '/../visio/rooms') . '/';
$now    = time();
$today  = date('Y-m-d');

$salles_live     = array();
$salles_today    = array();
$total_messages  = 0;
$total_peers     = 0;
$nb_salles_total = 0;

if (is_dir($dir)) {
    foreach (glob($dir . '*.json') as $f) {
        $raw = @file_get_contents($f);
        if (!$raw) continue;
        $d = json_decode($raw, true);
        if (!is_array($d)) continue;

        $code   = basename($f, '.json');
        $mtime  = filemtime($f);
        $msgs   = isset($d['msgs']) ? $d['msgs'] : array();
        $nb_msg = count($msgs);
        $total_messages += $nb_msg;
        $nb_salles_total++;

        $peers_actifs = array();
        foreach (isset($d['peers']) ? $d['peers'] : array() as $id => $p) {
            if (($now - (isset($p['ping']) ? $p['ping'] : 0)) <= 30) {
                $peers_actifs[] = isset($p['nom']) ? $p['nom'] : '?';
            }
        }

        $noms_participants = array();
        foreach (isset($d['peers']) ? $d['peers'] : array() as $id => $p) {
            if (!empty($p['nom'])) $noms_participants[$p['nom']] = 1;
        }

        $salle = array(
            'code'         => $code,
            'peers_actifs' => $peers_actifs,
            'nb_actifs'    => count($peers_actifs),
            'participants' => array_keys($noms_participants),
            'nb_msg'       => $nb_msg,
            'mtime'        => $mtime,
            'date'         => date('d/m/Y H:i', $mtime),
        );

        if (count($peers_actifs) > 0) {
            $salles_live[] = $salle;
            $total_peers  += count($peers_actifs);
        }
        if (date('Y-m-d', $mtime) === $today) {
            $salles_today[] = $salle;
        }
    }
}

usort($salles_today, function($a, $b) {
    if ($a['nb_actifs'] !== $b['nb_actifs']) return $b['nb_actifs'] - $a['nb_actifs'];
    return $b['mtime'] - $a['mtime'];
});

$plan_label = $abo ? strtoupper(isset($abo['plan']) ? $abo['plan'] : 'starter') : '—';
$max_p      = $abo ? $abo['nb_max_participants'] : 6;
$max_s      = $abo ? $abo['nb_max_salles'] : 2;
$pct_salles = $max_s > 0 ? round((count($salles_live) / $max_s) * 100) : 0;
?>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<title>Triade — Tableau de bord Visio</title>
<style>
.kpi-grid { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:20px; }
.kpi-card {
    flex:1; min-width:120px; background:#fff; border:1px solid #c5caee;
    border-radius:10px; padding:14px 12px; text-align:center;
}
.kpi-value { font-size:30px; font-weight:bold; color:#080A66; line-height:1; }
.kpi-label { font-size:11px; color:#666; margin-top:5px; }
.kpi-live .kpi-value { color:#2ecc71; }
.kpi-warn .kpi-value { color:#e67e22; }
.live-dot {
    display:inline-block; width:7px; height:7px;
    background:#2ecc71; border-radius:50%;
    animation:pulse 1.5s infinite; margin-right:4px; vertical-align:middle;
}
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.3} }
.section-title {
    color:#080A66; font-size:13px; font-weight:bold;
    margin:18px 0 8px; border-bottom:2px solid #e0e3f0; padding-bottom:5px;
}
.abo-bar {
    display:flex; gap:14px; flex-wrap:wrap; align-items:center;
    background:#f0f2fa; border:1px solid #c5caee; border-radius:8px;
    padding:10px 14px; margin-bottom:16px; font-size:12px;
}
.badge-plan { background:#e0e3f0; color:#080A66; padding:2px 10px; border-radius:10px; font-weight:bold; font-size:11px; }
.badge-live { background:#d4edda; color:#155724; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:bold; }
.badge-vide { background:#f8d7da; color:#721c24; padding:2px 8px; border-radius:10px; font-size:11px; }
.empty-msg  { color:#999; font-style:italic; text-align:center; padding:16px; font-size:12px; }
.refresh-bar { font-size:11px; color:#999; margin-bottom:14px; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Tableau de bord — Visioconférence</font></b></td></tr>
<tr id='cadreCentral0'><td>

<div style="padding:10px 14px;">

    <!-- Abonnement -->
    <div class="abo-bar">
        <span>Plan : <span class="badge-plan"><?php echo $plan_label; ?></span></span>
        <span>Participants max : <strong><?php echo $max_p; ?></strong></span>
        <span>Salles max : <strong><?php echo $max_s; ?></strong></span>
        <?php if ($abo && $abo['date_fin']): ?>
        <span>Expire : <strong><?php echo date('d/m/Y', strtotime($abo['date_fin'])); ?></strong></span>
        <?php endif; ?>
        <span style="margin-left:auto;">
            <a href="visio_abo.php" style="color:#080A66;font-size:11px;">&#9881; Abonnement</a>
            &nbsp;|&nbsp;
            <a href="visio_rooms.php" style="color:#080A66;font-size:11px;">Salles actives</a>
        </span>
    </div>

    <!-- Refresh -->
    <div class="refresh-bar">
        <span id="refresh-txt">Actualisation auto toutes les 15s</span>
        &nbsp;|&nbsp;
        <a href="visio_dashboard.php" style="color:#080A66;">&#8635; Actualiser</a>
    </div>

    <!-- KPI -->
    <div class="kpi-grid">
        <div class="kpi-card kpi-live">
            <div class="kpi-value"><span class="live-dot"></span><?php echo count($salles_live); ?></div>
            <div class="kpi-label">Salle<?php echo count($salles_live)>1?'s':''; ?> en live</div>
        </div>
        <div class="kpi-card kpi-live">
            <div class="kpi-value"><?php echo $total_peers; ?></div>
            <div class="kpi-label">Participant<?php echo $total_peers>1?'s':''; ?> connecté<?php echo $total_peers>1?'s':''; ?></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value"><?php echo count($salles_today); ?></div>
            <div class="kpi-label">Sessions aujourd'hui</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value"><?php echo $nb_salles_total; ?></div>
            <div class="kpi-label">Salles au total</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value"><?php echo $total_messages; ?></div>
            <div class="kpi-label">Messages échangés</div>
        </div>
        <div class="kpi-card <?php echo $pct_salles>=80?'kpi-warn':''; ?>">
            <div class="kpi-value"><?php echo $pct_salles; ?>%</div>
            <div class="kpi-label">Capacité utilisée</div>
        </div>
    </div>

    <!-- Salles live -->
    <div class="section-title"><span class="live-dot"></span> Salles actives en ce moment</div>
    <?php if (empty($salles_live)): ?>
        <div class="empty-msg">Aucune salle active en ce moment.</div>
    <?php else: ?>
    <table class="cc-data-table">
        <thead><tr class="cc-thead-row">
            <th class="cc-th">Code</th>
            <th class="cc-th">Participants</th>
            <th class="cc-th">Messages</th>
            <th class="cc-th">Action</th>
        </tr></thead>
        <tbody>
        <?php foreach ($salles_live as $s): ?>
        <tr class="cc-tr-data">
            <td class="cc-td"><strong style="letter-spacing:2px;color:#080A66;"><?php echo htmlspecialchars($s['code']); ?></strong></td>
            <td class="cc-td">
                <span class="badge-live">&#9679; <?php echo $s['nb_actifs']; ?> en ligne</span>
                <span style="margin-left:6px;color:#555;font-size:11px;"><?php echo htmlspecialchars(implode(', ', $s['peers_actifs'])); ?></span>
            </td>
            <td class="cc-td"><?php echo $s['nb_msg']; ?></td>
            <td class="cc-td">
                <a href="../visio/room.php?code=<?php echo urlencode($s['code']); ?>" target="_blank" class="cc-btn-consult">Rejoindre</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- Sessions aujourd'hui -->
    <div class="section-title">&#128197; Sessions d'aujourd'hui</div>
    <?php if (empty($salles_today)): ?>
        <div class="empty-msg">Aucune session aujourd'hui.</div>
    <?php else: ?>
    <table class="cc-data-table">
        <thead><tr class="cc-thead-row">
            <th class="cc-th">Code</th>
            <th class="cc-th">Statut</th>
            <th class="cc-th">Participants vus</th>
            <th class="cc-th">Messages</th>
            <th class="cc-th">Dernière activité</th>
        </tr></thead>
        <tbody>
        <?php foreach ($salles_today as $s): ?>
        <tr class="cc-tr-data">
            <td class="cc-td"><strong style="letter-spacing:2px;color:#080A66;"><?php echo htmlspecialchars($s['code']); ?></strong></td>
            <td class="cc-td">
                <?php if ($s['nb_actifs']>0): ?>
                    <span class="badge-live">&#9679; LIVE</span>
                <?php else: ?>
                    <span class="badge-vide">Terminée</span>
                <?php endif; ?>
            </td>
            <td class="cc-td" style="color:#555;font-size:11px;"><?php echo htmlspecialchars(implode(', ', $s['participants']) ?: '—'); ?></td>
            <td class="cc-td"><?php echo $s['nb_msg']; ?></td>
            <td class="cc-td" style="color:#888;font-size:11px;"><?php echo $s['date']; ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

</div>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>

<script>
let countdown = 15;
const txt = document.getElementById('refresh-txt');
setInterval(function() {
    countdown--;
    if (countdown <= 0) { location.reload(); }
    else { txt.textContent = 'Actualisation dans ' + countdown + 's'; }
}, 1000);
</script>
</body>
</HTML>
