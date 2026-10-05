<?php
session_start();
include_once("../common/config.inc.php");
include_once("../librairie_php/db_triade.php");
$cnx = cnx();

if (empty($_SESSION['membre']) && empty($_SESSION['admin1'])) {
    header('Location: ../index1.php'); exit;
}
include_once("check_abo.php");
$membre    = $_SESSION['membre'] ?? '';
$peutCreer = in_array($membre, ['menuprof', 'menuadmin', 'menuscolaire', 'menupersonnel']);
$idClasse  = $_SESSION['idClasse'] ?? '';
$isProf    = $peutCreer || !empty($_SESSION['admin1']);
Pgclose();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="../librairie_css/css.css">
<link rel="stylesheet" href="../librairie_css/css-v4.css">
<style>
body { margin:0; padding:0; background:transparent; }
.salle-card {
    background:#fff; border:1px solid #dde; border-radius:8px;
    padding:12px 16px; margin-bottom:10px;
    display:flex; align-items:center; justify-content:space-between;
    gap:12px; flex-wrap:wrap;
}
.salle-code  { font-size:17px; font-weight:bold; letter-spacing:3px; color:#080A66; }
.salle-nb    { font-size:12px; color:#555; }
.salle-peers { font-size:11px; color:#777; }
.badge-live  { display:inline-block; background:#2ecc71; color:#fff; font-size:10px; padding:2px 6px; border-radius:10px; vertical-align:middle; }
.empty       { color:#999; font-style:italic; margin:20px 0; text-align:center; font-size:12px; }
.toolbar     { display:flex; gap:8px; margin-bottom:14px; flex-wrap:wrap; align-items:center; }
.join-box    { background:#f0f2fa; border:1px solid #c5caee; border-radius:8px; padding:12px 14px; margin-bottom:16px; }
.join-box h3 { font-size:12px; color:#080A66; margin:0 0 8px; font-weight:bold; }
.join-row    { display:flex; gap:8px; }
.join-row input {
    flex:1; padding:7px 10px; border:1px solid #c5caee; border-radius:5px;
    font-size:14px; letter-spacing:3px; text-transform:uppercase; font-weight:bold; color:#080A66;
}
.refresh-info { font-size:11px; color:#999; }
.badge-classe { display:inline-block; background:#e8eaf6; color:#080A66; font-size:10px; padding:2px 7px; border-radius:10px; font-weight:bold; margin:1px; }
</style>
</head>
<body>
<div style="padding:10px 14px;">

    <!-- Rejoindre par code -->
    <div class="join-box">
        <h3>Rejoindre une salle par code</h3>
        <div class="join-row">
            <input type="text" id="code-input" maxlength="6" placeholder="ABC123"
                   oninput="this.value=this.value.toUpperCase()" autocomplete="off">
            <button class="btn-primary" onclick="rejoindreParCode()">Rejoindre &rarr;</button>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
        <?php if ($peutCreer): ?>
        <button class="btn-primary" onclick="navParent('create')">+ Créer une salle</button>
        <?php endif; ?>
        <span class="refresh-info" id="refresh-info">Mise à jour automatique</span>
    </div>

    <!-- Liste des salles -->
    <div id="liste-salles">
        <div class="empty">Chargement…</div>
    </div>

</div>

<script>
var PEUT_CREER = <?php echo $peutCreer ? 'true' : 'false'; ?>;
var IS_PROF    = <?php echo $isProf    ? 'true' : 'false'; ?>;
var ID_CLASSE  = <?php echo json_encode($idClasse); ?>;

function navParent(page) {
    var local = page === 'create' ? 'index.php' : 'rooms.php';
    if (window.parent !== window) {
        window.parent.postMessage({ type: 'visio-nav', url: './visio/' + local }, '*');
    } else {
        window.location.href = local;
    }
}

function rejoindreParCode() {
    var code = document.getElementById('code-input').value.trim().toUpperCase();
    if (code.length < 4) { alert('Entrez un code valide.'); return; }
    fetch('signal.php', {
        method: 'POST',
        body: new URLSearchParams({ action: 'list', room: '_', my_id: '_' })
    })
    .then(function(r) { return r.json(); })
    .then(function(salles) {
        var existe = Array.isArray(salles) && salles.some(function(s) { return s.code === code; });
        if (existe) {
            window.open('room.php?code=' + code, '_blank');
        } else {
            alert('Aucune salle active avec ce code.');
        }
    })
    .catch(function() { alert('Erreur de vérification. Réessayez.'); });
}

document.getElementById('code-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') rejoindreParCode();
});

function chargerSalles() {
    fetch('signal.php', {
        method: 'POST',
        body: new URLSearchParams({ action: 'list', room: '_', my_id: '_' })
    })
    .then(function(r) { return r.json(); })
    .then(function(salles) {
        var div = document.getElementById('liste-salles');
        if (!Array.isArray(salles) || salles.length === 0) {
            div.innerHTML = '<div class="empty">Aucune salle active pour le moment.</div>';
        } else {
            var html = '';
            salles.forEach(function(s) {
                // Filtrage pour élèves/parents : n'afficher que les salles accessibles
                if (!IS_PROF && s.classes_autorisees && s.classes_autorisees.length > 0) {
                    if (s.classes_autorisees.indexOf(ID_CLASSE) === -1) return;
                }
                var noms  = s.participants.join(', ') || '—';
                var live  = s.nb > 0 ? '<span class="badge-live">&#9679; LIVE</span>' : '';
                var nom   = s.nom ? '<div style="font-size:11px;color:#555;margin-top:1px;">' + s.nom + '</div>' : '';
                var acces = '';
                if (s.classes_autorisees && s.classes_autorisees.length > 0) {
                    var libelles = s.classes_libelles || {};
                    acces = '<div style="margin-top:3px;">'
                          + s.classes_autorisees.map(function(c) {
                              var label = libelles[c] || c;
                              return '<span class="badge-classe">&#128275; ' + label + '</span>';
                            }).join(' ')
                          + '</div>';
                } else {
                    acces = '<span style="font-size:10px;color:#2ecc71;">&#10003; Accès libre</span>';
                }
                html += '<div class="salle-card">'
                      + '<div>'
                      + '<div class="salle-code">' + s.code + ' ' + live + '</div>'
                      + nom
                      + '<div class="salle-nb">' + s.nb + ' participant' + (s.nb > 1 ? 's' : '') + '</div>'
                      + '<div class="salle-peers">' + noms + '</div>'
                      + acces
                      + '</div>'
                      + '<a href="room.php?code=' + s.code + '" target="_blank" class="btn-primary">Rejoindre &rarr;</a>'
                      + '</div>';
            });
            div.innerHTML = html;
        }
        document.getElementById('refresh-info').textContent =
            'Mis à jour à ' + new Date().toLocaleTimeString('fr-FR');
        window.parent.postMessage({ type: 'visio-resize', h: document.body.scrollHeight + 20 }, '*');
    })
    .catch(function() {
        document.getElementById('refresh-info').textContent = 'Erreur de chargement';
    });
}

chargerSalles();
setInterval(chargerSalles, 10000);
</script>
</body>
</html>
