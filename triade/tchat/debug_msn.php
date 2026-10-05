<?php
session_start();
if (file_exists("../../common/config.inc.php")) include_once "../../common/config.inc.php";
if (file_exists("../common/config.inc.php"))  include_once "../common/config.inc.php";
if (file_exists("./common/config.inc.php"))   include_once "./common/config.inc.php";
include_once "php/config.php";

// Lancer l'auto-init pour voir ce qui se passe réellement
include_once "php/msn_auto_init.php";

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Debug MSN</title>
<style>
  body { font-family: monospace; background: #1a1a2e; color: #e0e0e0; padding: 20px; }
  h2   { color: #4fc3f7; border-bottom: 1px solid #4fc3f7; padding-bottom: 6px; }
  .ok  { color: #81c784; } .ko { color: #e57373; } .warn { color: #ffb74d; }
  table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
  th, td { text-align: left; padding: 6px 12px; border: 1px solid #444; font-size: 13px; }
  th { background: #2a2a4a; color: #90caf9; }
  tr:nth-child(even) td { background: #1e1e3a; }
  pre { background: #111; padding: 10px; border-radius: 4px; overflow-x: auto; font-size: 12px; }
  .box { background: #12122a; border: 1px solid #333; border-radius: 6px; padding: 14px; margin-bottom: 20px; }
</style>
</head>
<body>
<h2>Debug MSN — Session &amp; DB</h2>

<?php
/* ── 1. Variables de session TRIADE ─────────────────────────── */
echo '<div class="box"><h2>1. Session TRIADE</h2>';
echo '<table><tr><th>Variable</th><th>Valeur</th><th>Statut</th></tr>';
$vars = ['id_pers','membre','nom','prenom','unique_id','connecte','msn_debug'];
foreach ($vars as $v) {
    $val = isset($_SESSION[$v]) ? $_SESSION[$v] : null;
    $ok  = $val !== null && $val !== '' && $val !== 0;
    printf('<tr><td>$_SESSION["%s"]</td><td>%s</td><td class="%s">%s</td></tr>',
        htmlspecialchars($v),
        htmlspecialchars((string)$val),
        $ok ? 'ok' : 'ko',
        $ok ? 'OK' : 'MANQUANT'
    );
}
echo '</table>';

if (isset($_SESSION['msn_debug'])) {
    echo '<p><b>Chemin auto-init :</b> <span class="warn">' . htmlspecialchars($_SESSION['msn_debug']) . '</span></p>';
}
echo '</div>';

/* ── 2. Email synthétique attendu ───────────────────────────── */
echo '<div class="box"><h2>2. Email MSN attendu</h2>';
$idPers = intval($_SESSION['id_pers'] ?? 0);
$membre = $_SESSION['membre'] ?? '';
$prenom = trim($_SESSION['prenom'] ?? '');
$nom    = trim($_SESSION['nom'] ?? '');
$expectedEmail = $idPers . '_' . $membre . '@msn.triade';
$crc32uid = abs(crc32($expectedEmail)) % 9000000 + 1000000;
echo '<table><tr><th>Champ</th><th>Valeur</th></tr>';
echo '<tr><td>Email attendu</td><td>' . htmlspecialchars($expectedEmail) . '</td></tr>';
echo '<tr><td>CRC32 unique_id généré</td><td>' . $crc32uid . '</td></tr>';
echo '<tr><td>unique_id session actuel</td><td>' . intval($_SESSION['unique_id'] ?? 0) . '</td></tr>';
$match = isset($_SESSION['unique_id']) ? ($crc32uid == intval($_SESSION['unique_id']) ? 'OUI (CRC32)' : 'NON (migration ou creation aléatoire)') : 'N/A';
echo '<tr><td>Match CRC32 == session ?</td><td class="' . (strpos($match,'OUI')!==false?'ok':'warn') . '">' . $match . '</td></tr>';
echo '</table></div>';

/* ── 3. Compte dans tria_users ──────────────────────────────── */
echo '<div class="box"><h2>3. Compte(s) dans tria_users</h2>';
if ($idPers) {
    $emailDb = mysqli_real_escape_string($conn, $expectedEmail);
    $prenomDb = mysqli_real_escape_string($conn, $prenom);
    $nomDb    = mysqli_real_escape_string($conn, $nom);

    $q = mysqli_query($conn, "SELECT unique_id, fname, lname, email, status, update_sync FROM " . PREFIXE . "users WHERE email='$emailDb' OR (fname='$prenomDb' AND lname='$nomDb') ORDER BY unique_id");
    if ($q && mysqli_num_rows($q) > 0) {
        echo '<table><tr><th>unique_id</th><th>fname</th><th>lname</th><th>email</th><th>status</th><th>update_sync</th></tr>';
        while ($row = mysqli_fetch_assoc($q)) {
            $isTarget = ($row['email'] === $expectedEmail);
            echo '<tr' . ($isTarget ? ' style="background:#1a3a1a"' : '') . '>';
            echo '<td class="' . ($isTarget?'ok':'warn') . '">' . htmlspecialchars($row['unique_id']) . '</td>';
            echo '<td>' . htmlspecialchars($row['fname']) . '</td>';
            echo '<td>' . htmlspecialchars($row['lname']) . '</td>';
            echo '<td>' . htmlspecialchars($row['email']) . '</td>';
            echo '<td>' . htmlspecialchars($row['status']) . '</td>';
            echo '<td>' . date('d/m H:i', intval($row['update_sync'])) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p class="ko">Aucun compte trouvé pour cet utilisateur.</p>';
    }
} else {
    echo '<p class="ko">id_pers manquant — impossible de chercher le compte.</p>';
}
echo '</div>';

/* ── 4. Derniers messages envoyés/reçus ─────────────────────── */
echo '<div class="box"><h2>4. Derniers messages (tria_messages)</h2>';
$myUid = intval($_SESSION['unique_id'] ?? 0);
if ($myUid) {
    $q = mysqli_query($conn, "SELECT msg_id, outgoing_msg_id, incoming_msg_id, LEFT(msg,60) as msg
                               FROM " . PREFIXE . "messages
                               WHERE outgoing_msg_id='$myUid' OR incoming_msg_id='$myUid'
                               ORDER BY msg_id DESC LIMIT 20");
    if ($q && mysqli_num_rows($q) > 0) {
        echo '<table><tr><th>msg_id</th><th>from (outgoing)</th><th>to (incoming)</th><th>Message</th><th>Dir</th></tr>';
        while ($row = mysqli_fetch_assoc($q)) {
            $dir = ($row['outgoing_msg_id'] == $myUid) ? '<span class="ok">→ Envoyé</span>' : '<span class="warn">← Reçu</span>';
            echo '<tr>';
            echo '<td>' . $row['msg_id'] . '</td>';
            echo '<td>' . $row['outgoing_msg_id'] . '</td>';
            echo '<td>' . $row['incoming_msg_id'] . '</td>';
            echo '<td>' . htmlspecialchars($row['msg']) . '</td>';
            echo '<td>' . $dir . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p class="ko">Aucun message trouvé pour unique_id=' . $myUid . '</p>';
    }
} else {
    echo '<p class="ko">unique_id non défini en session.</p>';
}
echo '</div>';

/* ── 5. Tous les comptes récents (debug liste contacts) ──────── */
echo '<div class="box"><h2>5. Tous les comptes tria_users récents</h2>';
$q = mysqli_query($conn, "SELECT unique_id, fname, lname, email, status FROM " . PREFIXE . "users ORDER BY unique_id DESC LIMIT 30");
if ($q && mysqli_num_rows($q) > 0) {
    echo '<table><tr><th>unique_id</th><th>fname</th><th>lname</th><th>email</th><th>status</th></tr>';
    while ($row = mysqli_fetch_assoc($q)) {
        $isMsn = strpos($row['email'], '@msn.triade') !== false;
        echo '<tr>';
        echo '<td class="' . ($isMsn?'ok':'warn') . '">' . htmlspecialchars($row['unique_id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['fname']) . '</td>';
        echo '<td>' . htmlspecialchars($row['lname']) . '</td>';
        echo '<td>' . htmlspecialchars($row['email']) . '</td>';
        echo '<td>' . htmlspecialchars($row['status']) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
}
echo '</div>';
?>

</body>
</html>
