<?php
session_start();
include_once("../common/config.inc.php");
include_once("../librairie_php/db_triade.php");
$membre = isset($_SESSION['membre']) ? $_SESSION['membre'] : '';
$isAdmin1 = !empty($_SESSION['admin1']);
if (empty($membre) && !$isAdmin1) { header('Location: ../index1.php'); exit; }

$allowed = array('menuadmin', 'menuscolaire', 'menuprof');
if (!$isAdmin1 && !in_array($membre, $allowed)) {
    header('Location: rooms.php'); exit;
}
if ($isAdmin1) $membre = 'menuadmin';

$cnx = cnx();
include_once("check_abo.php");
include_once("../librairie_php/db_visio.php");
$abo = getAbonnementVisio();
Pgclose();

// --- Lecture des salles depuis les fichiers JSON ---
$dir    = __DIR__ . '/rooms/';
$now    = time();
$today  = date('Y-m-d');

$salles_live    = [];
$salles_today   = [];
$total_messages = 0;
$total_peers    = 0;
$nb_salles_total = 0;

if (is_dir($dir)) {
    foreach (glob($dir . '*.json') as $f) {
        $raw = @file_get_contents($f);
        if (!$raw) continue;
        $d = json_decode($raw, true);
        if (!is_array($d)) continue;

        $code   = basename($f, '.json');
        $mtime  = filemtime($f);
        $msgs = isset($d['msgs']) ? $d['msgs'] : array();
        $nb_msg = count($msgs);
        $total_messages += $nb_msg;
        $nb_salles_total++;

        // Peers actifs (ping < 30s)
        $peers_actifs = array();
        foreach (isset($d['peers']) ? $d['peers'] : array() as $id => $p) {
            if (($now - ($p['ping'] ?? 0)) <= 30) {
                $peers_actifs[] = $p['nom'];
            }
        }

        // Tous les noms qui ont participé
        $noms_participants = array();
        foreach (isset($d['peers']) ? $d['peers'] : array() as $id => $p) {
            if (!empty($p['nom'])) $noms_participants[$p['nom']] = 1;
        }

        $salle = [
            'code'          => $code,
            'peers_actifs'  => $peers_actifs,
            'nb_actifs'     => count($peers_actifs),
            'participants'  => array_keys($noms_participants),
            'nb_msg'        => $nb_msg,
            'mtime'         => $mtime,
            'date'          => date('d/m/Y H:i', $mtime),
        ];

        if (count($peers_actifs) > 0) {
            $salles_live[] = $salle;
            $total_peers  += count($peers_actifs);
        }
        if (date('Y-m-d', $mtime) === $today) {
            $salles_today[] = $salle;
        }
    }
}

// Tri : live en premier, puis par mtime desc
usort($salles_today, function($a, $b) {
    if ($a['nb_actifs'] !== $b['nb_actifs']) return $b['nb_actifs'] - $a['nb_actifs'];
    return $b['mtime'] - $a['mtime'];
});

$plan_label = $abo ? strtoupper(isset($abo['plan']) ? $abo['plan'] : 'starter') : '—';
$max_p      = $abo ? $abo['nb_max_participants'] : 6;
$max_s      = $abo ? $abo['nb_max_salles'] : 2;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard Visio — TRIADE</title>
<link rel="stylesheet" href="../librairie_css/css.css">
<link rel="stylesheet" href="../librairie_css/css-v4.css">
<style>
.dash-wrap  { max-width: 900px; margin: 0 auto; padding: 20px; }
.dash-wrap h2 { color: #080A66; margin-bottom: 20px; }

/* KPI cards */
.kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px; margin-bottom: 28px; }
.kpi-card {
    background: #fff; border-radius: 10px; padding: 18px 16px;
    box-shadow: 0 2px 8px rgba(8,10,102,.1); text-align: center;
}
.kpi-value { font-size: 36px; font-weight: bold; color: #080A66; line-height: 1; }
.kpi-label { font-size: 12px; color: #666; margin-top: 6px; }
.kpi-live  .kpi-value { color: #2ecc71; }
.kpi-warn  .kpi-value { color: #e67e22; }

/* live indicator */
.live-dot {
    display: inline-block; width: 8px; height: 8px;
    background: #2ecc71; border-radius: 50%;
    animation: pulse 1.5s infinite;
    margin-right: 5px; vertical-align: middle;
}
@keyframes pulse {
    0%,100% { opacity: 1; } 50% { opacity: 0.3; }
}

/* Table salles */
.section-title {
    color: #080A66; font-size: 14px; font-weight: bold;
    margin: 24px 0 10px; border-bottom: 2px solid #e0e3f0; padding-bottom: 6px;
}
.vtable { width: 100%; border-collapse: collapse; font-size: 13px; }
.vtable th { background: #f0f2fa; color: #080A66; padding: 8px 10px; text-align: left; }
.vtable td { padding: 8px 10px; border-bottom: 1px solid #eee; vertical-align: top; }
.vtable tr:last-child td { border-bottom: none; }
.vtable tr:hover td { background: #f8f9ff; }

.badge-live   { background: #d4edda; color: #155724; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: bold; }
.badge-vide   { background: #f8d7da; color: #721c24; padding: 2px 8px; border-radius: 10px; font-size: 11px; }
.badge-plan   { background: #e0e3f0; color: #080A66; padding: 3px 10px; border-radius: 10px; font-size: 12px; font-weight: bold; }

.empty-msg { color: #999; font-style: italic; text-align: center; padding: 20px; }

/* Abonnement bar */
.abo-bar {
    display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
    background: #fff; border-radius: 8px; padding: 14px 18px;
    box-shadow: 0 1px 6px rgba(8,10,102,.07); margin-bottom: 24px;
    font-size: 13px;
}
.abo-bar strong { color: #080A66; }

/* refresh */
.refresh-bar { font-size: 12px; color: #999; margin-bottom: 16px; display: flex; gap: 12px; align-items: center; }
.btn-refresh { background: #e8eaf6; border: 1px solid #c5caee; border-radius: 5px; padding: 4px 12px; cursor: pointer; font-size: 12px; color: #080A66; }
.btn-refresh:hover { background: #c5caee; }
</style>
</head>
<body>
<?php include_once("../librairie_php/entete.php"); ?>
<div class="dash-wrap">
    <h2>📊 Tableau de bord Visioconférence</h2>

    <!-- Abonnement -->
    <div class="abo-bar">
        <div><strong>Plan :</strong> <span class="badge-plan"><?php echo $plan_label; ?></span></div>
        <div><strong>Participants max :</strong> <?php echo $max_p; ?></div>
        <div><strong>Salles max :</strong> <?php echo $max_s; ?></div>
        <?php if ($abo && $abo['date_fin']): ?>
        <div><strong>Expire le :</strong> <?php echo date('d/m/Y', strtotime($abo['date_fin'])); ?></div>
        <?php endif; ?>
    </div>

    <!-- Refresh bar -->
    <div class="refresh-bar">
        <span id="refresh-txt">Actualisation auto toutes les 120s</span>
        <button class="btn-refresh" onclick="location.reload()">↻ Actualiser</button>
        <a href="rooms.php" style="color:#080A66;font-size:12px;">→ Voir les salles</a>
        <a href="index.php" style="color:#080A66;font-size:12px;">+ Créer une salle</a>
    </div>

    <!-- KPI -->
    <div class="kpi-grid">
        <div class="kpi-card kpi-live">
            <div class="kpi-value">
                <span class="live-dot"></span><?php echo count($salles_live); ?>
            </div>
            <div class="kpi-label">Salle<?php echo count($salles_live) > 1 ? 's' : ''; ?> en live</div>
        </div>
        <div class="kpi-card kpi-live">
            <div class="kpi-value"><?php echo $total_peers; ?></div>
            <div class="kpi-label">Participant<?php echo $total_peers > 1 ? 's' : ''; ?> connecté<?php echo $total_peers > 1 ? 's' : ''; ?></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value"><?php echo count($salles_today); ?></div>
            <div class="kpi-label">Salle<?php echo count($salles_today) > 1 ? 's' : ''; ?> aujourd'hui</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value"><?php echo $nb_salles_total; ?></div>
            <div class="kpi-label">Salles au total</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value"><?php echo $total_messages; ?></div>
            <div class="kpi-label">Messages échangés</div>
        </div>
        <?php
        $pct_salles = $max_s > 0 ? round((count($salles_live) / $max_s) * 100) : 0;
        $kpi_class  = $pct_salles >= 80 ? 'kpi-warn' : '';
        ?>
        <div class="kpi-card <?php echo $kpi_class; ?>">
            <div class="kpi-value"><?php echo $pct_salles; ?>%</div>
            <div class="kpi-label">Capacité utilisée</div>
        </div>
    </div>

    <!-- Salles en live -->
    <div class="section-title"><span class="live-dot"></span> Salles actives en ce moment</div>
    <?php if (empty($salles_live)): ?>
        <div class="empty-msg">Aucune salle active en ce moment.</div>
    <?php else: ?>
    <table class="vtable">
        <thead>
            <tr>
                <th>Code</th>
                <th>Participants connectés</th>
                <th>Messages</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($salles_live as $s): ?>
        <tr>
            <td><strong style="letter-spacing:2px;color:#080A66;"><?php echo htmlspecialchars($s['code']); ?></strong></td>
            <td>
                <span class="badge-live">● <?php echo $s['nb_actifs']; ?> en ligne</span>
                <span style="margin-left:8px;color:#555;"><?php echo htmlspecialchars(implode(', ', $s['peers_actifs'])); ?></span>
            </td>
            <td><?php echo $s['nb_msg']; ?></td>
            <td>
                <a href="room.php?code=<?php echo urlencode($s['code']); ?>" target="_blank"
                   style="color:#080A66;font-size:12px;">Rejoindre →</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- Salles aujourd'hui -->
    <div class="section-title">📅 Sessions d'aujourd'hui</div>
    <?php if (empty($salles_today)): ?>
        <div class="empty-msg">Aucune session aujourd'hui.</div>
    <?php else: ?>
    <table class="vtable">
        <thead>
            <tr>
                <th>Code</th>
                <th>Statut</th>
                <th>Participants vus</th>
                <th>Messages</th>
                <th>Dernière activité</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($salles_today as $s): ?>
        <tr>
            <td><strong style="letter-spacing:2px;color:#080A66;"><?php echo htmlspecialchars($s['code']); ?></strong></td>
            <td>
                <?php if ($s['nb_actifs'] > 0): ?>
                    <span class="badge-live">● LIVE</span>
                <?php else: ?>
                    <span class="badge-vide">Terminée</span>
                <?php endif; ?>
            </td>
            <td style="color:#555;"><?php echo htmlspecialchars(implode(', ', $s['participants']) ?: '—'); ?></td>
            <td><?php echo $s['nb_msg']; ?></td>
            <td style="color:#888;"><?php echo $s['date']; ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

</div>

<script>
function gererAbo() {
    if (window.parent !== window) {
        window.parent.location.href = './admin/index_acces.php';
    } else {
        window.open('../admin/visio_abo.php', '_blank');
    }
}

// Auto-refresh toutes les 120 secondes
let countdown = 120;
const txt = document.getElementById('refresh-txt');
setInterval(function() {
    countdown--;
    if (countdown <= 0) {
        location.reload();
    } else {
        txt.textContent = 'Actualisation dans ' + countdown + 's';
    }
}, 1000);
</script>
</body>
</html>
