<?php
session_start();
include_once("../common/config.inc.php");
include_once("../librairie_php/db_triade.php");
$cnx = cnx();

if (empty($_SESSION['membre']) && empty($_SESSION['admin1'])) {
    header('Location: ../index1.php'); exit;
}
$membre = $_SESSION['membre'] ?? '';
if (!empty($membre) && !in_array($membre, ['menuprof', 'menuadmin', 'menuscolaire', 'menupersonnel'])) {
    header('Location: rooms.php'); exit;
}
include_once("check_abo.php");
include_once("lk_config.php");

function genererCode() {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code  = '';
    for ($i = 0; $i < 6; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $code;
}

// Charger la liste des classes + map libellé (tout avant Pgclose)
global $prefixe;
$classes    = array();
$classesMap = array();
$sql  = "SELECT code_class, trim(libelle) FROM {$prefixe}classes WHERE offline='0' ORDER BY libelle";
$rows = chargeMat(execSql($sql));
foreach ($rows as $row) {
    $classes[]               = array('code_class' => $row[0], 'libelle' => $row[1]);
    $classesMap[$row[0]]     = $row[1];
}

$code_cree   = '';
$lien_host   = '';
$lien_guest  = '';
$classes_sel = array();

// Limites du plan (récupérées une seule fois)
$limites     = getAbonnementVisioLimites();
$max_salles  = $limites['nb_max_salles'];
$max_part    = $limites['nb_max_participants'];
$erreur_crea = '';

if (isset($_POST['creer'])) {
    // Vérifier le nombre de salles actives (avec au moins 1 participant connecté)
    $dir_rooms = __DIR__ . '/rooms/';
    $nb_salles_actives = 0;
    if (is_dir($dir_rooms)) {
        $now_ts = time();
        foreach (glob($dir_rooms . '*.json') as $f) {
            $d = json_decode(file_get_contents($f), true);
            if (!is_array($d)) continue;
            foreach (($d['peers'] ?? array()) as $p) {
                if (($now_ts - intval($p['ping'] ?? 0)) <= 30) {
                    $nb_salles_actives++;
                    break; // 1 peer actif suffit pour compter la salle
                }
            }
        }
    }
    if ($nb_salles_actives >= $max_salles) {
        $erreur_crea = "Limite atteinte : votre plan autorise {$max_salles} salle(s) simultanée(s). "
                     . "Fermez une salle existante avant d'en créer une nouvelle.";
    } else {
        $code_cree  = genererCode();
        $engine     = visioEngine($max_part);
        $room_file  = ($engine === 'livekit') ? 'room_lk.php' : 'room.php';
        $base_url   = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']
                      . rtrim(dirname(dirname($_SERVER['PHP_SELF'])), '/') . '/visio';
        $lien_host  = $base_url . '/' . $room_file . '?code=' . $code_cree . '&role=host';
        $lien_guest = $base_url . '/' . $room_file . '?code=' . $code_cree;

        $classes_sel = isset($_POST['classes_auto']) && is_array($_POST['classes_auto'])
                       ? array_map('trim', $_POST['classes_auto'])
                       : array();

        // Map code → libellé pour l'affichage dans rooms.php
        $classes_libelles = array();
        foreach ($classes_sel as $cc) {
            if (isset($classesMap[$cc])) $classes_libelles[$cc] = $classesMap[$cc];
        }

        if (!is_dir($dir_rooms)) mkdir($dir_rooms, 0755, true);
        $room_data = array(
            'peers'              => array(),
            'msgs'               => array(),
            'nom'                => trim($_POST['nom_salle'] ?? ''),
            'classes_autorisees' => $classes_sel,
            'classes_libelles'   => $classes_libelles,
            'max_participants'   => $max_part,
            'engine'             => $engine,
            'cree_par'           => $_SESSION['nom'] ?? 'Inconnu',
            'cree_le'            => date('Y-m-d H:i:s'),
        );
        file_put_contents($dir_rooms . $code_cree . '.json', json_encode($room_data), LOCK_EX);
    }
}

Pgclose();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="../librairie_css/css.css">
<link rel="stylesheet" href="../librairie_css/css-v4.css">
<style>
body { margin:0; padding:0; background:transparent; }
.code-badge {
    display:inline-block; background:#080A66; color:#fff;
    font-size:22px; letter-spacing:6px; padding:10px 20px;
    border-radius:8px; font-weight:bold; margin:12px 0;
}
.lien-box {
    background:#f0f2fa; border:1px solid #c5caee; border-radius:6px;
    padding:10px 12px; font-family:monospace; font-size:12px;
    word-break:break-all; margin-bottom:6px;
}
.lien-label { font-size:11px; color:#555; margin-bottom:3px; font-weight:bold; }
.sep { border:none; border-top:1px solid #e0e3f0; margin:16px 0; }
.classes-grid {
    display:flex; flex-wrap:wrap; gap:6px; margin-top:6px; max-height:180px;
    overflow-y:auto; padding:8px; border:1px solid #c5caee;
    border-radius:5px; background:#fafbff;
}
.classes-grid label {
    display:flex; align-items:center; gap:4px;
    font-size:12px; color:#333; white-space:nowrap;
    background:#fff; border:1px solid #dde; border-radius:4px;
    padding:3px 8px; cursor:pointer;
}
.classes-grid label:hover { border-color:#080A66; background:#f0f2fa; }
.classes-grid input[type=checkbox]:checked + span { color:#080A66; font-weight:bold; }
.badge-classe {
    display:inline-block; background:#e8eaf6; color:#080A66;
    font-size:10px; padding:2px 7px; border-radius:10px; font-weight:bold; margin:1px;
}
.acces-libre { color:#2ecc71; font-weight:bold; font-size:12px; }
</style>
</head>
<body>
<div style="padding:14px 16px;">

<?php if ($code_cree): ?>

    <div style="margin-bottom:8px;font-size:12px;color:#555;">Salle créée — Code :</div>
    <div class="code-badge"><?php echo htmlspecialchars($code_cree); ?></div>

    <?php if (empty($classes_sel)): ?>
        <div class="acces-libre">&#10003; Accès libre — toutes les classes</div>
    <?php else: ?>
        <div style="margin-top:6px;font-size:11px;color:#555;">Classes autorisées :</div>
        <div style="margin-top:4px;">
        <?php foreach ($classes_sel as $cc): ?>
            <span class="badge-classe"><?php echo htmlspecialchars($classesMap[$cc] ?? $cc); ?></span>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <hr class="sep">

    <div class="lien-label">Lien Animateur (vous) :</div>
    <div class="lien-box" id="lien_host"><?php echo htmlspecialchars($lien_host); ?></div>
    <button class="btn-secondary" onclick="copier('lien_host', this)" style="margin-bottom:12px;">Copier</button>

    <div class="lien-label">Lien à partager (élèves / parents) :</div>
    <div class="lien-box" id="lien_guest"><?php echo htmlspecialchars($lien_guest); ?></div>
    <button class="btn-secondary" onclick="copier('lien_guest', this)" style="margin-bottom:12px;">Copier</button>

    <hr class="sep">
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="<?php echo htmlspecialchars($lien_host); ?>" target="_blank" class="btn-primary">&#9654; Rejoindre comme animateur</a>
        <button class="btn-secondary" onclick="navParent('create')">+ Créer une autre salle</button>
        <button class="btn-secondary" onclick="navParent('rooms')">&#128249; Voir les salles</button>
    </div>

<?php else: ?>

    <?php if ($erreur_crea): ?>
    <div style="background:#f8d7da;border:1px solid #f5c6cb;border-radius:8px;padding:14px 16px;color:#721c24;margin-bottom:14px;">
        <strong>&#128683; Limite atteinte</strong><br>
        <span style="font-size:12px;"><?php echo htmlspecialchars($erreur_crea); ?></span>
    </div>
    <?php endif; ?>

    <form method="POST" action="index.php">
        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:11px;color:#555;margin-bottom:4px;font-weight:bold;">Nom de la salle</label>
            <input type="text" name="nom_salle" placeholder="Ex : Cours de maths - Lundi 14h"
                   style="width:100%;padding:8px 10px;border:1px solid #c5caee;border-radius:5px;font-size:13px;box-sizing:border-box;">
        </div>

        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:11px;color:#555;margin-bottom:4px;font-weight:bold;">
                Accès — Classes autorisées
                <span style="font-weight:normal;color:#888;">(laisser vide = accès libre à tous)</span>
            </label>
            <?php if (empty($classes)): ?>
                <div style="color:#999;font-size:12px;">Aucune classe trouvée.</div>
            <?php else: ?>
            <div class="classes-grid">
                <?php foreach ($classes as $cl): ?>
                <label>
                    <input type="checkbox" name="classes_auto[]" value="<?php echo htmlspecialchars($cl['code_class']); ?>">
                    <span><?php echo htmlspecialchars($cl['libelle']); ?></span>
                </label>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <button type="submit" name="creer" class="btn-primary">Créer la salle</button>
        <button type="button" class="btn-secondary" onclick="navParent('rooms')" style="margin-left:8px;">&#8592; Retour</button>
    </form>

<?php endif; ?>

</div>

<script>
function navParent(page) {
    var url = (page === 'create') ? './visio/index.php' : './visio/rooms.php';
    window.parent.postMessage({ type: 'visio-nav', url: url }, '*');
}

function copier(id, btn) {
    var texte = document.getElementById(id).innerText;
    if (navigator.clipboard) {
        navigator.clipboard.writeText(texte).then(function() {
            btn.innerText = 'Copié !';
            setTimeout(function() { btn.innerText = 'Copier'; }, 2000);
        });
    } else {
        var ta = document.createElement('textarea');
        ta.value = texte; document.body.appendChild(ta); ta.select();
        document.execCommand('copy'); document.body.removeChild(ta);
        btn.innerText = 'Copié !';
        setTimeout(function() { btn.innerText = 'Copier'; }, 2000);
    }
}

window.addEventListener('load', function() {
    window.parent.postMessage({ type: 'visio-resize', h: document.body.scrollHeight + 20 }, '*');
});
</script>
</body>
</html>
