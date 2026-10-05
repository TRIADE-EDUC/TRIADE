<?php
session_start();
error_reporting(0);

// ─── Actions avec redirect/header — AVANT tout output ────────────────────────
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    include_once("../common/config.inc.php");
    $conn = mysqli_connect(HOST, USER, PWD, DB);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="triade-msn-'.date('Ymd').'.csv"');
    echo "\xEF\xBB\xBF";
    $q = mysqli_query($conn, "
        SELECT u.fname, u.lname, u.email, u.status, u.update_sync,
               (SELECT COUNT(*) FROM ".PREFIXE."messages WHERE outgoing_msg_id = u.unique_id) as nb_envoyes,
               (SELECT COUNT(*) FROM ".PREFIXE."messages WHERE incoming_msg_id  = u.unique_id) as nb_recus,
               CASE WHEN p.pers_id IS NOT NULL THEN 'Personnel' ELSE 'Eleve' END as profil
        FROM ".PREFIXE."users u
        LEFT JOIN ".PREFIXE."personnel p ON LOWER(TRIM(p.email)) COLLATE utf8_general_ci = LOWER(TRIM(u.email)) COLLATE utf8_general_ci
        ORDER BY u.fname, u.lname
    ");
    echo "Profil;Prénom;Nom;Email;Statut;Dernière activité;Messages envoyés;Messages reçus\n";
    while ($r = mysqli_fetch_assoc($q)) {
        $last = $r['update_sync'] ? date('d/m/Y H:i', $r['update_sync']) : '-';
        echo $r['profil'].';'.$r['fname'].';'.$r['lname'].';'.$r['email'].';'
             .$r['status'].';'.$last.';'.$r['nb_envoyes'].';'.$r['nb_recus']."\n";
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include_once("../common/config.inc.php");
    $conn = mysqli_connect(HOST, USER, PWD, DB);
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $uid = (int)($_POST['uid'] ?? 0);
        if ($uid > 0) {
            mysqli_query($conn, "DELETE FROM ".PREFIXE."messages WHERE incoming_msg_id=$uid OR outgoing_msg_id=$uid");
            mysqli_query($conn, "DELETE FROM ".PREFIXE."users WHERE unique_id=$uid");
        }
    }
    if ($action === 'reset_status') {
        $t = time() - 1000;
        mysqli_query($conn, "UPDATE ".PREFIXE."users SET status='Offline now' WHERE update_sync < $t");
    }
    header("Location: gestionintramsn.php");
    exit;
}

// ─── Page normale — lib_licence + db en premier (pattern ges_blacklist.php) ──
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4-2.css">
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<title>Triade — Gestion TRIADE-MSN</title>
<style>
.msn-card      { background:#fff; border:1px solid #c5cae9; border-radius:10px;
                 padding:14px 20px; min-width:130px; flex:1; text-align:center; }
.msn-card-val  { font-size:32px; font-weight:800; color:#080A66;
                 font-family:Electrolize,Trebuchet MS,Arial; }
.msn-card-lbl  { font-size:11px; color:#666; margin-top:4px;
                 font-family:Electrolize,Trebuchet MS,Arial; }
.msn-card-online .msn-card-val { color:#2e7d32; }
.msn-section   { background:#080A66; color:#fff; border-radius:8px 8px 0 0;
                 padding:7px 14px; font-size:12px; font-weight:700;
                 font-family:Electrolize,Trebuchet MS,Arial; margin-top:16px; }
.msn-badge     { display:inline-block; border-radius:10px; padding:1px 8px;
                 font-size:10px; font-weight:700; font-family:Electrolize,Trebuchet MS,Arial; }
.msn-badge-on   { background:#e8f5e9; color:#2e7d32; border:1px solid #a5d6a7; }
.msn-badge-off  { background:#fafafa; color:#999;    border:1px solid #e0e0e0; }
.msn-badge-pers { background:#e8eaf6; color:#3949ab; border:1px solid #9fa8da; }
.msn-badge-elev { background:#fff3e0; color:#e65100; border:1px solid #ffcc80; }
.msn-bar-wrap  { display:flex; align-items:center; gap:8px; margin-bottom:6px; }
.msn-bar-name  { min-width:120px; font-size:11px; color:#333; text-align:right;
                 font-family:Electrolize,Trebuchet MS,Arial; }
.msn-bar       { height:18px; background:linear-gradient(90deg,#3949ab,#080A66);
                 border-radius:4px; min-width:4px; }
.msn-bar-val   { font-size:11px; color:#555; font-family:Electrolize,Trebuchet MS,Arial; }
.msn-filter-bar { display:flex; gap:8px; align-items:center; flex-wrap:wrap;
                  padding:8px 10px; background:#f0f2fa; border:1px solid #c5cae9; }
.msn-filter-bar label { font-size:11px; font-weight:700; color:#080A66; cursor:pointer;
                  font-family:Electrolize,Trebuchet MS,Arial; }
.msn-td-sub     { font-size:11px !important; color:#555 !important; }
</style>
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");
?>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Gestion TRIADE-MSN</font></b></td></tr>
<tr id='cadreCentral0'><td valign="top">

<?php
// ─── DB + queries ICI (dans le body, pattern ges_blacklist.php) ───────────────
$cnx  = cnx();
$conn = mysqli_connect(HOST, USER, PWD, DB);
$threshold = time() - 1000;

function msn_count($conn, $sql) {
    $r = mysqli_query($conn, $sql);
    return ($r && ($row = mysqli_fetch_assoc($r))) ? (int)$row['n'] : 0;
}

// Vérification existence des tables MSN
$has_users = (bool)mysqli_fetch_assoc(mysqli_query($conn, "SHOW TABLES LIKE '".PREFIXE."users'"));
$has_msg   = $has_users && (bool)mysqli_fetch_assoc(mysqli_query($conn, "SHOW TABLES LIKE '".PREFIXE."messages'"));

$total_users  = $has_users ? msn_count($conn, "SELECT COUNT(*) n FROM ".PREFIXE."users") : 0;
$online_users = $has_users ? msn_count($conn, "SELECT COUNT(*) n FROM ".PREFIXE."users WHERE update_sync > $threshold") : 0;
$total_msg    = $has_msg   ? msn_count($conn, "SELECT COUNT(*) n FROM ".PREFIXE."messages") : 0;
$total_conv   = $has_msg   ? msn_count($conn, "SELECT COUNT(DISTINCT CONCAT(LEAST(outgoing_msg_id,incoming_msg_id),'-',GREATEST(outgoing_msg_id,incoming_msg_id))) n FROM ".PREFIXE."messages") : 0;

// Utilisateurs — LEFT JOINs dérivés (évite les sous-requêtes corrélées)
$users_rows = [];
if ($has_users) {
    $msg_join = $has_msg ? "
        LEFT JOIN (SELECT outgoing_msg_id, COUNT(*) nb FROM ".PREFIXE."messages GROUP BY outgoing_msg_id) sent ON sent.outgoing_msg_id = u.unique_id
        LEFT JOIN (SELECT incoming_msg_id, COUNT(*) nb FROM ".PREFIXE."messages GROUP BY incoming_msg_id) recv ON recv.incoming_msg_id = u.unique_id
    " : "";
    $msg_cols = $has_msg ? "COALESCE(sent.nb,0) as nb_envoyes, COALESCE(recv.nb,0) as nb_recus," : "0 as nb_envoyes, 0 as nb_recus,";
    $q_users = mysqli_query($conn, "
        SELECT u.unique_id, u.fname, u.lname, u.email, u.status, u.update_sync, u.img,
               $msg_cols
               CASE WHEN p.pers_id IS NOT NULL THEN 'Personnel' ELSE 'Élève' END as profil
        FROM ".PREFIXE."users u
        LEFT JOIN ".PREFIXE."personnel p ON LOWER(TRIM(p.email)) COLLATE utf8_general_ci = LOWER(TRIM(u.email)) COLLATE utf8_general_ci
        $msg_join
        ORDER BY u.update_sync DESC
    ");
    if ($q_users) { while ($r = mysqli_fetch_assoc($q_users)) { $users_rows[] = $r; } }
}

$q_top = mysqli_query($conn, "
    SELECT u.fname, u.lname, COUNT(m.msg_id) as nb
    FROM ".PREFIXE."messages m
    JOIN ".PREFIXE."users u ON u.unique_id = m.outgoing_msg_id
    GROUP BY m.outgoing_msg_id ORDER BY nb DESC LIMIT 5
");
$top_rows = []; $top_max = 1;
if ($q_top) { while ($r = mysqli_fetch_assoc($q_top)) { $top_rows[] = $r; if ($r['nb'] > $top_max) $top_max = $r['nb']; } }

$q_conv = mysqli_query($conn, "
    SELECT c.uid1, c.uid2, c.nb_msg,
           u1.fname as fname1, u1.lname as lname1,
           u2.fname as fname2, u2.lname as lname2
    FROM (
        SELECT LEAST(outgoing_msg_id, incoming_msg_id)    as uid1,
               GREATEST(outgoing_msg_id, incoming_msg_id) as uid2,
               COUNT(*) as nb_msg
        FROM ".PREFIXE."messages
        GROUP BY uid1, uid2 ORDER BY nb_msg DESC LIMIT 10
    ) c
    JOIN ".PREFIXE."users u1 ON u1.unique_id = c.uid1
    JOIN ".PREFIXE."users u2 ON u2.unique_id = c.uid2
    ORDER BY c.nb_msg DESC
");
$conv_rows = [];
if ($q_conv) { while ($r = mysqli_fetch_assoc($q_conv)) { $conv_rows[] = $r; } }
?>

<!-- ══ STATS CARDS ══════════════════════════════════════════════════════════ -->
<div style="display:flex;gap:10px;flex-wrap:wrap;margin:12px 8px 0;">
    <div class="msn-card">
        <div class="msn-card-val"><?php print $total_users ?></div>
        <div class="msn-card-lbl">Utilisateurs inscrits</div>
    </div>
    <div class="msn-card msn-card-online">
        <div class="msn-card-val"><?php print $online_users ?></div>
        <div class="msn-card-lbl">Connectés maintenant</div>
    </div>
    <div class="msn-card">
        <div class="msn-card-val"><?php print $total_msg ?></div>
        <div class="msn-card-lbl">Messages échangés</div>
    </div>
    <div class="msn-card">
        <div class="msn-card-val"><?php print $total_conv ?></div>
        <div class="msn-card-lbl">Conversations</div>
    </div>
</div>

<div class="na-row" style="justify-content:flex-end;margin:6px 8px 0;">
    <span class="msn-td-sub">Actualisation auto dans <span id="msn-countdown">60</span>s</span>
    <form method="post" style="margin:0;">
        <input type="hidden" name="action" value="reset_status">
        <button type="submit" class="btn-retour" style="font-size:10px;padding:2px 8px;">
            Forcer statuts hors-ligne
        </button>
    </form>
</div>

<!-- ══ UTILISATEURS ═════════════════════════════════════════════════════════ -->
<div class="msn-section">Utilisateurs inscrits (<?php print count($users_rows) ?>)</div>
<div class="msn-filter-bar">
    <input type="text" id="msn-search" class="cc-select" placeholder="Recherche nom / email…" oninput="msnFilter()" style="width:180px;">
    <label><input type="radio" name="msn-statut" value=""       checked onchange="msnFilter()"> Tous</label>
    <label><input type="radio" name="msn-statut" value="online"         onchange="msnFilter()"> En ligne</label>
    <label><input type="radio" name="msn-statut" value="offline"        onchange="msnFilter()"> Hors ligne</label>
    <label><input type="radio" name="msn-profil" value=""       checked onchange="msnFilter()"> Tous profils</label>
    <label><input type="radio" name="msn-profil" value="Personnel"      onchange="msnFilter()"> Personnel</label>
    <label><input type="radio" name="msn-profil" value="Élève"          onchange="msnFilter()"> Élèves</label>
</div>

<table class="cc-data-table" id="msn-users-table" style="border-radius:0 0 8px 8px;">
<thead>
<tr class="cc-thead-row">
    <th class="cc-th">Photo</th>
    <th class="cc-th">Nom</th>
    <th class="cc-th">Email</th>
    <th class="cc-th">Profil</th>
    <th class="cc-th">Statut</th>
    <th class="cc-th">Dernière activité</th>
    <th class="cc-th" style="text-align:center;">Env.</th>
    <th class="cc-th" style="text-align:center;">Reçus</th>
    <th class="cc-th"></th>
</tr>
</thead>
<tbody>
<?php foreach ($users_rows as $u):
    $isOnline = $u['update_sync'] > $threshold;
    $badgeSt  = $isOnline
        ? '<span class="msn-badge msn-badge-on">En ligne</span>'
        : '<span class="msn-badge msn-badge-off">Hors ligne</span>';
    $badgePr  = ($u['profil'] === 'Personnel')
        ? '<span class="msn-badge msn-badge-pers">Personnel</span>'
        : '<span class="msn-badge msn-badge-elev">Élève</span>';
    $last = $u['update_sync'] ? date('d/m/Y H:i', $u['update_sync']) : '-';
    $img  = $u['img'] ? '../tchat/php/images/'.$u['img'] : '../image/commun/photo_vide.jpg';
?>
<tr class="cc-tr-data"
    data-name="<?php print htmlspecialchars(strtolower($u['fname'].' '.$u['lname'].' '.$u['email'])) ?>"
    data-statut="<?php print $isOnline ? 'online' : 'offline' ?>"
    data-profil="<?php print htmlspecialchars($u['profil']) ?>">
    <td class="cc-td cc-td-center" style="width:46px;">
        <img src="<?php print $img ?>" width="36" height="36"
             style="border-radius:50%;border:2px solid <?php print $isOnline ? '#a5d6a7' : '#e0e0e0' ?>;">
    </td>
    <td class="cc-td"><b><?php print htmlspecialchars($u['fname'].' '.$u['lname']) ?></b></td>
    <td class="cc-td msn-td-sub"><?php print htmlspecialchars($u['email']) ?></td>
    <td class="cc-td"><?php print $badgePr ?></td>
    <td class="cc-td"><?php print $badgeSt ?></td>
    <td class="cc-td msn-td-sub"><?php print $last ?></td>
    <td class="cc-td cc-td-center"><?php print (int)$u['nb_envoyes'] ?></td>
    <td class="cc-td cc-td-center"><?php print (int)$u['nb_recus'] ?></td>
    <td class="cc-td cc-td-center">
        <form method="post" style="margin:0;"
              onsubmit="return confirm('Supprimer le compte MSN de <?php print htmlspecialchars($u['fname'].' '.$u['lname']) ?> et tous ses messages ?');">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="uid"    value="<?php print (int)$u['unique_id'] ?>">
            <button type="submit" class="btn-enr"
                    style="background:#c62828;padding:2px 8px;font-size:10px;">Suppr.</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
<?php if (empty($users_rows)): ?>
<tr><td colspan="9" class="cc-td" style="text-align:center;color:#999;padding:16px;">
    Aucun utilisateur inscrit au TRIADE-MSN.
</td></tr>
<?php endif; ?>
</tbody>
</table>

<!-- ══ TOP 5 ÉMETTEURS ══════════════════════════════════════════════════════ -->
<?php if (!empty($top_rows)): ?>
<div class="msn-section">Top 5 émetteurs</div>
<div class="na-card" style="border-radius:0 0 8px 8px;margin-top:0;padding:14px 16px;">
<?php foreach ($top_rows as $t):
    $pct = round(($t['nb'] / $top_max) * 100); ?>
    <div class="msn-bar-wrap">
        <span class="msn-bar-name"><?php print htmlspecialchars($t['fname'].' '.$t['lname']) ?></span>
        <div class="msn-bar" style="width:<?php print $pct ?>%;max-width:300px;"></div>
        <span class="msn-bar-val"><?php print $t['nb'] ?> msg</span>
    </div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ══ CONVERSATIONS ════════════════════════════════════════════════════════ -->
<?php if (!empty($conv_rows)): ?>
<div class="msn-section">Conversations les plus actives (top 10)</div>
<table class="cc-data-table" style="border-radius:0 0 8px 8px;">
<thead>
<tr class="cc-thead-row">
    <th class="cc-th">Participant 1</th>
    <th class="cc-th">Participant 2</th>
    <th class="cc-th" style="text-align:center;">Messages</th>
</tr>
</thead>
<tbody>
<?php foreach ($conv_rows as $c): ?>
<tr class="cc-tr-data">
    <td class="cc-td"><?php print htmlspecialchars($c['fname1'].' '.$c['lname1']) ?></td>
    <td class="cc-td"><?php print htmlspecialchars($c['fname2'].' '.$c['lname2']) ?></td>
    <td class="cc-td cc-td-center"><b><?php print (int)$c['nb_msg'] ?></b></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<div class="na-card msn-td-sub" style="border-radius:0 0 8px 8px;margin-top:0;">
    Aucune conversation enregistrée.
</div>
<?php endif; ?>

<!-- ══ EXPORT ══════════════════════════════════════════════════════════════ -->
<br>
<div class="na-foot">
    <a href="gestionintramsn.php?export=csv" class="btn-retour" style="text-decoration:none;">&#11123; Export CSV</a>
    <span class="msn-td-sub">Liste complète des utilisateurs MSN avec leurs statistiques.</span>
</div>
<br>

<script>
function msnFilter() {
    var search = document.getElementById('msn-search').value.toLowerCase();
    var statut = document.querySelector('input[name="msn-statut"]:checked').value;
    var profil = document.querySelector('input[name="msn-profil"]:checked').value;
    document.querySelectorAll('#msn-users-table tbody tr').forEach(function(row) {
        var ok = (search === '' || (row.dataset.name||'').indexOf(search) !== -1)
              && (statut === '' || row.dataset.statut === statut)
              && (profil === '' || row.dataset.profil === profil);
        row.style.display = ok ? '' : 'none';
    });
}
var msnSec = 60;
setInterval(function() {
    msnSec--;
    var el = document.getElementById('msn-countdown');
    if (el) el.textContent = msnSec;
    if (msnSec <= 0) location.reload();
}, 1000);
</script>

<!-- // fin de la saisie -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
