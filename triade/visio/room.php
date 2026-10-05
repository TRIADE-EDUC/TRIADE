<?php
session_start();
include_once("../common/config.inc.php");
include_once("../librairie_php/db_triade.php");
if (empty($_SESSION['membre']) && empty($_SESSION['admin1'])) { header('Location: ../index1.php'); exit; }
$cnx = cnx();
include_once("check_abo.php");
Pgclose();

$code = preg_replace('/[^a-zA-Z0-9]/', '', isset($_GET['code']) ? $_GET['code'] : '');
$_allowedHost = ['menuprof', 'menuadmin', 'menuscolaire', 'menupersonnel'];
$_peutEtreHost = !empty($_SESSION['admin1']) || in_array($_SESSION['membre'] ?? '', $_allowedHost);
$role = $_peutEtreHost ? 'host' : 'guest';
if ($code === '') { header('Location: rooms.php'); exit; }

$nom_user = !empty($_SESSION['nom']) ? $_SESSION['nom'] : (!empty($_SESSION['admin1']) ? 'Admin' : 'Participant');

// Clé école pour le token Triade central
if (!defined('KEY_CODE_ECOLE')) {
    $_visioCfgKey = __DIR__ . '/../common/config_key.php';
    if (!file_exists($_visioCfgKey)) {
        $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0,0xffff), mt_rand(0,0xffff), mt_rand(0,0xffff),
            mt_rand(0,0x0fff)|0x4000, mt_rand(0,0x3fff)|0x8000,
            mt_rand(0,0xffff), mt_rand(0,0xffff), mt_rand(0,0xffff));
        file_put_contents($_visioCfgKey, "<?php\ndefine('KEY_CODE_ECOLE', '" . $uuid . "');\n");
    }
    include_once($_visioCfgKey);
}
$_visio_ecole_code = defined('KEY_CODE_ECOLE') ? KEY_CODE_ECOLE : (defined('REPECOLE') ? REPECOLE : 'triade');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Visio — <?php echo htmlspecialchars($code); ?></title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { background: #eef0f8; color: #333; font-family: Arial, sans-serif; height: 100vh; display: flex; flex-direction: column; overflow: hidden; }

/* ── Header ── */
#header {
    display: flex; align-items: center; justify-content: space-between;
    background: #080A66; padding: 8px 16px; flex-shrink: 0; gap: 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
}
#header h1 { font-size: 15px; color: #ccd; white-space: nowrap; }
#code-badge { background: #080A66; color: #fff; padding: 3px 10px; border-radius: 20px; font-size: 12px; letter-spacing: 2px; white-space: nowrap; }
#status { font-size: 12px; color: #f90; white-space: nowrap; }

/* ── Grille vidéos ── */
#video-grid {
    flex: 1; display: grid; gap: 12px; padding: 12px;
    background: #eef0f8; overflow: hidden;
    grid-template-columns: 1fr;
}
#video-grid.p2  { grid-template-columns: 1fr 1fr; }
#video-grid.p3  { grid-template-columns: 1fr 1fr; grid-template-rows: 1fr 1fr; }
#video-grid.p4  { grid-template-columns: 1fr 1fr; grid-template-rows: 1fr 1fr; }
#video-grid.p5,
#video-grid.p6  { grid-template-columns: 1fr 1fr 1fr; grid-template-rows: 1fr 1fr; }

.video-tile {
    position: relative; background: #CACCEF; border-radius: 10px; overflow: hidden;
    display: flex; align-items: center; justify-content: center;
    border: 1px solid #b0b5e0; transition: border-color .2s, box-shadow .2s;
    box-shadow: 0 2px 8px rgba(8,10,102,.08);
}
.video-tile:hover { border-color: #9099d8; box-shadow: 0 4px 14px rgba(8,10,102,.13); }
.video-tile.speaking { border-color: #080A66; box-shadow: 0 0 0 2px #080A66, 0 4px 18px rgba(8,10,102,.2); }
.video-tile video { width: 100%; height: 100%; object-fit: cover; }
.video-tile .tile-nom {
    position: absolute; bottom: 0; left: 0; right: 0;
    padding: 22px 10px 8px;
    background: linear-gradient(to top, rgba(8,10,102,.55) 0%, transparent 100%);
    color: #fff; font-size: 12px; font-weight: 600;
    text-shadow: 0 1px 4px rgba(8,10,102,.8);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

/* ── Avatar initiales (caméra coupée — style Teams) ── */
.tile-avatar {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    width: 72px; height: 72px; border-radius: 50%;
    display: none; /* géré par JS */
    align-items: center; justify-content: center;
    font-size: 26px; font-weight: 700; color: #fff;
    letter-spacing: 1px; text-shadow: 0 1px 3px rgba(0,0,0,.4);
    user-select: none;
    box-shadow: 0 2px 8px rgba(0,0,0,.4);
}
/* Main levée — badge temporaire en haut à gauche */
.tile-hand {
    position: absolute; top: 6px; left: 8px; z-index: 4;
    font-size: 22px; line-height: 1;
    filter: drop-shadow(0 1px 3px rgba(0,0,0,.4));
    animation: hand-pulse 0.5s ease-in-out 3;
}
@keyframes hand-pulse {
    0%,100% { transform: scale(1); }
    50% { transform: scale(1.3); }
}
/* Icône caméra coupée en bas à droite de l'avatar */
.tile-cam-off {
    position: absolute; bottom: 6px; right: 8px;
    background: rgba(255,255,255,.8); color: #c62828;
    border-radius: 50%; width: 24px; height: 24px;
    display: none; align-items: center; justify-content: center;
    font-size: 13px; border: 1px solid rgba(198,40,40,.2);
    box-shadow: 0 1px 4px rgba(0,0,0,.1);
}

/* ── Panneau droit : participants + chat ── */
#panel {
    position: fixed; top: 48px; right: 0; width: 260px; height: calc(100% - 48px - 70px);
    background: #f5f6fb; display: flex; flex-direction: column;
    transform: translateX(100%); transition: transform .2s; z-index: 10;
    border-left: 1px solid #c5cae9;
}
#panel.open { transform: translateX(0); }

/* Onglets du panel droit */
#panel-tabs {
    display: flex; border-bottom: 1px solid rgba(255,255,255,.12); flex-shrink: 0;
}
.panel-tab {
    flex: 1; background: none; border: none; color: #7986cb; font-size: 12px;
    padding: 8px 4px; cursor: pointer; text-align: center;
}
.panel-tab.active { color: #080A66; border-bottom: 2px solid #080A66; font-weight: 600; }
.panel-tab-content { display: none; flex: 1; flex-direction: column; overflow: hidden; }
.panel-tab-content.active { display: flex; }

/* Participants */
#peers-content { padding: 10px; overflow-y: auto; }
#peer-list { list-style: none; }
#peer-list li { font-size: 13px; padding: 5px 4px; border-bottom: 1px solid #e8eaf6; color: #333; display: flex; align-items: center; justify-content: space-between; gap: 4px; }
#peer-list li .peer-nom { flex: 1; }
.mute-btn {
    background: #eef0f8; border: 1px solid #c5cae9; color: #555;
    border-radius: 4px; font-size: 13px; padding: 1px 6px; cursor: pointer; flex-shrink: 0;
}
.mute-btn:hover { background: #c62828; color: #fff; border-color: #c62828; }
.mute-btn.muted { background: #c62828; color: #fff; border-color: #c62828; }
.ban-btn {
    background: #fff; border: 1px solid #c62828; color: #c62828;
    border-radius: 4px; font-size: 11px; padding: 1px 5px; cursor: pointer; flex-shrink: 0;
}
.ban-btn:hover { background: #c62828; color: #fff; }
.mute-all-btn {
    background: #fff; color: #c62828; border: 1px solid #c62828; border-radius: 4px;
    font-size: 11px; padding: 5px 10px; cursor: pointer; width: 100%; margin-bottom: 8px;
}
.mute-all-btn:hover { background: #c62828; color: #fff; }
.host-muted-bar {
    background: #ffebee; color: #c62828; font-size: 11px; text-align: center;
    padding: 5px 10px; display: none; flex-shrink: 0;
}

/* Chat */
#chat-messages {
    flex: 1; overflow-y: auto; padding: 8px;
    display: flex; flex-direction: column; gap: 6px;
}
.chat-msg {
    background: #fff; border: 1px solid #e8eaf6; border-radius: 8px; padding: 7px 10px;
    max-width: 95%; box-shadow: 0 1px 3px rgba(8,10,102,.05);
}
.chat-msg.mine { background: #080A66; border-color: #080A66; align-self: flex-end; }
.chat-msg .msg-nom { font-size: 10px; color: #9099d8; margin-bottom: 3px; }
.chat-msg .msg-txt { font-size: 13px; color: #333; word-break: break-word; }
.chat-msg.mine .msg-nom { color: rgba(202,204,239,.7); }
.chat-msg.mine .msg-txt { color: #fff; }
#chat-input-area {
    display: flex; gap: 6px; padding: 8px; border-top: 1px solid #c5cae9; flex-shrink: 0;
}
#chat-input {
    flex: 1; background: #fff; border: 1px solid #c5cae9; color: #333;
    border-radius: 16px; padding: 6px 12px; font-size: 13px; outline: none;
}
#chat-input:focus { border-color: #3949ab; }
#chat-input::placeholder { color: #b0b8e0; }
#chat-send {
    background: #080A66; color: #fff; border: none; border-radius: 50%;
    width: 34px; height: 34px; cursor: pointer; font-size: 16px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
#chat-send:hover { background: #3949ab; }

/* ── Panneau gauche : documents ── */
#doc-panel {
    position: fixed; top: 48px; left: 0; width: 240px; height: calc(100% - 48px - 70px);
    background: #f5f6fb; padding: 10px; display: flex; flex-direction: column;
    transform: translateX(-100%); transition: transform .2s; z-index: 10;
    border-right: 1px solid #c5cae9;
}
#doc-panel.open { transform: translateX(0); }
#doc-panel-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 8px; flex-shrink: 0;
}
#doc-panel-header h3 { font-size: 13px; color: #080A66; font-weight: 700; }
.doc-upload-btn {
    background: #080A66; color: #fff; border: none; font-size: 11px; padding: 4px 10px;
    border-radius: 12px; cursor: pointer; white-space: nowrap;
}
.doc-upload-btn:hover { background: #3949ab; }
#doc-list { overflow-y: auto; flex: 1; }
.doc-empty { color: #b0b8e0; font-size: 12px; font-style: italic; padding: 8px 0; }
.doc-item {
    background: #fff; border: 1px solid #e8eaf6; border-radius: 6px; padding: 8px 10px; margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(8,10,102,.05);
}
.doc-item .doc-nom { font-size: 12px; color: #080A66; margin-bottom: 3px; word-break: break-all; }
.doc-item .doc-par { font-size: 10px; color: #9099d8; margin-bottom: 6px; }
.doc-item a {
    color: #080A66; font-size: 11px; text-decoration: none;
    background: #eef0f8; padding: 2px 8px; border-radius: 4px; margin-right: 4px;
    display: inline-block; margin-bottom: 2px;
}
.doc-item a:hover { background: #c5cae9; }

/* ── Notification document (toast enrichi) ── */
#doc-notif {
    position: fixed; top: 58px; right: 14px;
    background: #fff; border: 1px solid #c5cae9; color: #333;
    padding: 10px 14px; border-radius: 8px; font-size: 12px;
    max-width: 260px; display: none; z-index: 110;
    box-shadow: 0 4px 16px rgba(8,10,102,.12);
}
#doc-notif .dn-title { font-weight: bold; color: #080A66; margin-bottom: 2px; word-break: break-all; }
#doc-notif .dn-par { font-size: 10px; color: #9099d8; margin-bottom: 8px; }
#doc-notif .dn-actions { display: flex; gap: 8px; }
.dn-open { background: #080A66; color: #fff; border: none; font-size: 11px; padding: 4px 12px; border-radius: 4px; cursor: pointer; text-decoration: none; }
.dn-close { background: #eef0f8; color: #555; border: none; font-size: 11px; padding: 4px 10px; border-radius: 4px; cursor: pointer; }

/* Badge chat non lus */
#chat-badge {
    position: absolute; top: -4px; right: -4px; background: #e74c3c;
    color: #fff; font-size: 10px; width: 16px; height: 16px;
    border-radius: 50%; display: none; align-items: center; justify-content: center;
}

/* ── Contrôles ── */
#controls {
    display: flex; justify-content: center; align-items: center; gap: 10px;
    background: #080A66; padding: 10px 16px; flex-shrink: 0; flex-wrap: wrap;
    border-top: 1px solid rgba(255,255,255,.12);
}
.btn-ctrl {
    width: 46px; height: 46px; border-radius: 50%; border: none;
    cursor: pointer; font-size: 18px; display: flex; align-items: center;
    justify-content: center; transition: background .15s; position: relative;
}
.btn-on  { background: rgba(202,204,239,.15); color: #CACCEF; }
.btn-on:hover  { background: rgba(202,204,239,.28); color: #fff; }
.btn-off { background: rgba(198,40,40,.25); color: #ef9a9a; }
.btn-red { background: #c62828; color: #fff; }
.btn-red:hover { background: #e53935; }
.btn-blue { background: rgba(202,204,239,.15); color: #CACCEF; }
.btn-blue:hover { background: rgba(202,204,239,.28); color: #fff; }
.btn-amber { background: rgba(249,198,0,.2); color: #f9c600; }
.btn-amber:hover { background: rgba(249,198,0,.35); }

#nb-participants { font-size: 12px; color: rgba(202,204,239,.7); }

/* ── Toast ── */
#toast {
    position: fixed; top: 55px; left: 50%; transform: translateX(-50%);
    background: #080A66; color: #fff; padding: 7px 18px;
    border-radius: 20px; font-size: 13px; display: none; z-index: 99;
    white-space: nowrap; box-shadow: 0 4px 12px rgba(8,10,102,.3);
}

/* ── Mode Présentation (partage écran ou document) ── */
#video-grid.mode-presentation {
    display: flex !important;
    flex-direction: column;
    gap: 8px; padding: 8px;
}
/* Bande mini-tuiles en haut */
#participants-strip {
    display: flex; gap: 8px; height: 92px; flex-shrink: 0;
    overflow-x: auto; overflow-y: hidden;
    scrollbar-width: thin; scrollbar-color: #c5cae9 transparent;
}
#participants-strip::-webkit-scrollbar { height: 4px; }
#participants-strip::-webkit-scrollbar-thumb { background: #c5cae9; border-radius: 2px; }
#participants-strip .video-tile {
    min-width: 140px; width: 140px; height: 92px;
    flex-shrink: 0; border-radius: 6px !important;
    box-shadow: 0 1px 4px rgba(8,10,102,.1) !important;
}
#participants-strip .video-tile .tile-nom {
    font-size: 10px; padding: 12px 5px 4px;
}
#participants-strip .video-tile .tile-avatar {
    width: 40px; height: 40px; font-size: 14px;
}
#participants-strip .video-tile .tile-cam-off {
    width: 16px; height: 16px; font-size: 9px; bottom: 3px; right: 3px;
}
/* Tuile du présentateur (zone principale) */
.video-tile.presenter {
    flex: 1; min-height: 0;
    border-color: #080A66 !important;
    box-shadow: 0 0 0 2px #080A66, 0 4px 18px rgba(8,10,102,.15) !important;
}
.video-tile.presenter video { object-fit: contain; background: #000; }
.presenter-badge {
    position: absolute; top: 8px; right: 8px; z-index: 3;
    background: #080A66; color: #fff;
    font-size: 9px; font-weight: 700; letter-spacing: .05em;
    text-transform: uppercase; padding: 2px 10px; border-radius: 12px;
    pointer-events: none;
}
/* Zone document en présentation */
#doc-presenter-wrap {
    flex: 1; min-height: 0;
    background: #fff; border-radius: 10px; overflow: hidden;
    border: 2px solid #080A66;
    box-shadow: 0 0 0 2px #080A66, 0 4px 18px rgba(8,10,102,.15);
    display: none; flex-direction: column;
}
#doc-presenter-wrap.active { display: flex; }
#doc-presenter-bar {
    display: flex; align-items: center; gap: 10px;
    padding: 6px 14px; background: #080A66; color: #fff;
    flex-shrink: 0; font-size: 12px;
}
.doc-pres-name { flex: 1; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.doc-pres-by { font-size: 10px; color: rgba(255,255,255,.65); white-space: nowrap; flex-shrink: 0; }
.btn-close-pres {
    background: rgba(255,255,255,.15); border: none; color: #fff;
    border-radius: 12px; padding: 2px 10px; cursor: pointer; font-size: 11px; flex-shrink: 0;
}
.btn-close-pres:hover { background: rgba(255,255,255,.3); }
#doc-presenter-iframe { flex: 1; width: 100%; border: none; }
</style>
</head>
<body>

<div id="header">
    <h1>📹 TRIADE Visio <span style="font-size:11px;color:#778"><?php echo $role==='host'?'(Animateur)':'(Participant)'; ?></span></h1>
    <span id="code-badge"><?php echo htmlspecialchars($code); ?></span>
    <span id="status">⏳ Connexion…</span>
</div>

<div id="video-grid"></div>

<!-- Panneau droit : participants + chat -->
<div id="panel">
    <div id="panel-tabs">
        <button class="panel-tab active" onclick="switchPanelTab('peers')" id="tab-peers">👥 Participants</button>
        <button class="panel-tab" onclick="switchPanelTab('chat')" id="tab-chat">💬 Chat</button>
    </div>
    <div class="panel-tab-content active" id="pane-peers">
        <div id="host-muted-bar" class="host-muted-bar">🔇 Votre micro a été coupé par l'animateur</div>
        <div id="peers-content">
            <div id="host-controls" style="display:none;">
                <button class="mute-all-btn" onclick="muterTous()">🔇 Couper tous les micros</button>
            </div>
            <ul id="peer-list"></ul>
        </div>
    </div>
    <div class="panel-tab-content" id="pane-chat">
        <div id="chat-messages"></div>
        <div id="chat-input-area">
            <input type="text" id="chat-input" placeholder="Écrire un message…" maxlength="500" autocomplete="off">
            <button id="chat-send" onclick="sendChat()" title="Envoyer">➤</button>
        </div>
    </div>
</div>

<!-- Panneau gauche : documents -->
<div id="doc-panel">
    <div id="doc-panel-header">
        <h3>📁 Documents</h3>
        <button class="doc-upload-btn" onclick="document.getElementById('doc-input').click()">📤 Partager</button>
    </div>
    <div id="doc-list"><div class="doc-empty">Aucun document partagé</div></div>
</div>
<input type="file" id="doc-input"
    accept=".pdf,.jpg,.jpeg,.png,.gif,.docx,.pptx,.xlsx,.doc,.ppt,.xls"
    style="display:none" onchange="partagerDocument(this)">

<!-- Notification document -->
<div id="doc-notif">
    <div class="dn-title" id="doc-notif-title"></div>
    <div class="dn-par" id="doc-notif-par"></div>
    <div class="dn-actions">
        <a id="doc-notif-link" class="dn-open" target="_blank">Ouvrir ↗</a>
        <button class="dn-close" onclick="document.getElementById('doc-notif').style.display='none'">✕ Fermer</button>
    </div>
</div>

<div id="controls">
    <span id="nb-participants">1 participant</span>
    <button class="btn-ctrl btn-on"  id="btnMic"    onclick="toggleMic()"     title="Micro">🎤</button>
    <button class="btn-ctrl btn-on"  id="btnCam"    onclick="toggleCam()"     title="Caméra">📷</button>
    <button class="btn-ctrl btn-on"  id="btnScreen" onclick="toggleScreen()"  title="Partager l'écran">🖥️</button>
    <button class="btn-ctrl btn-blue" id="btnChat"  onclick="togglePanel()"   title="Chat / Participants" style="font-size:16px;">
        💬<span id="chat-badge">0</span>
    </button>
    <button class="btn-ctrl btn-on"  id="btnDoc"    onclick="toggleDocPanel()" title="Documents partagés">📁</button>
    <button class="btn-ctrl btn-amber" id="btnHand" onclick="leverMain()"     title="Lever la main">✋</button>
    <button class="btn-ctrl btn-blue"              onclick="location.reload()" title="Rafraîchir">🔄</button>
    <button class="btn-ctrl btn-red" onclick="raccrocher()"                   title="Raccrocher">📵</button>
</div>

<div id="toast"></div>

<script>
const ROOM         = <?php echo json_encode($code); ?>;
const MY_ROLE      = <?php echo json_encode($role); ?>;
const MY_NOM       = <?php echo json_encode($nom_user); ?>;
const ECOLE_CODE   = <?php echo json_encode($_visio_ecole_code); ?>;
const TOKEN_URL    = 'https://ws-visio.triade-educ.net/token.php';
const CENTRAL_SIGNAL = 'https://ws-visio.triade-educ.net/signal.php';
const LOCAL_SIGNAL = 'signal.php';
const ICE_CFG = { iceServers: [
    { urls: 'stun:stun.l.google.com:19302' },
    { urls: 'stun:stun1.l.google.com:19302' }
]};

const MY_ID   = MY_ROLE + '_' + Math.random().toString(36).substr(2, 8);
const IS_HOST = (MY_ROLE === 'host');

// Signal URL et token (définis après fetchToken)
let SIGNAL = LOCAL_SIGNAL;
let _jwt   = '';

let localStream  = null;
let screenStream = null;
let screenSharing = false;
let peers        = {};
let polling      = null;
let lastTs       = 0;
let micOn        = true;
let camOn        = true;
let panelOpen    = false;
let docPanelOpen = false;
let chatUnread   = 0;
let sharedDocs   = [];
let mutedPeers   = {};
let mutedByHost  = false;
let knownPeerNames    = {};
let pendingCandidates = {};
let presentationMode  = false;
let presenterPeerId   = null;

// ── Signaling ──────────────────────────────────────────────────────────
function sig(params) {
    const extra = _jwt ? { token: _jwt } : {};
    const body  = new URLSearchParams(Object.assign({ room: ROOM, my_id: MY_ID, nom: MY_NOM }, extra, params));
    return fetch(SIGNAL, { method: 'POST', body }).then(r => r.json());
}

// ── Récupération du JWT Triade (avant init) ───────────────────────────
async function fetchToken() {
    try {
        const body = new URLSearchParams({
            api_key:   ECOLE_CODE,
            room_code: ROOM,
            peer_id:   MY_ID,
            nom:       MY_NOM
        });
        const resp = await fetch(TOKEN_URL, { method: 'POST', body });
        if (resp.status === 403) {
            // Abonnement expiré ou clé invalide → bloquer
            document.body.innerHTML = `
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100vh;gap:18px;font-family:Arial,sans-serif;background:#eef0f8;">
                  <div style="font-size:48px">🔒</div>
                  <h2 style="color:#080A66">Accès Triade Visio suspendu</h2>
                  <p style="color:#555;max-width:420px;text-align:center">L'abonnement Visio de cet établissement est inactif ou expiré.<br>Contactez votre administrateur ou <a href="https://triade-educ.org" style="color:#080A66">triade-educ.org</a>.</p>
                </div>`;
            return false;
        }
        if (resp.ok) {
            const data = await resp.json();
            if (data.token) {
                _jwt   = data.token;
                SIGNAL = CENTRAL_SIGNAL;
                return true;
            }
        }
        // Erreur inattendue → fallback local
        console.warn('[Triade Visio] Token fetch error, fallback local signal');
        SIGNAL = LOCAL_SIGNAL;
        return true;
    } catch (e) {
        // Serveur Triade inaccessible (réseau) → fallback local
        console.warn('[Triade Visio] Token server unreachable, fallback local signal');
        SIGNAL = LOCAL_SIGNAL;
        return true;
    }
}

async function push(to, type, data) {
    return sig({ action: 'push', from: MY_ID, to, type, data: JSON.stringify(data) });
}

async function pull() {
    const r = await sig({ action: 'pull', since: lastTs });
    if (r.msgs && r.msgs.length > 0) lastTs = Math.max(...r.msgs.map(m => m.ts));
    if (r.peers) {
        for (const [id, nom] of Object.entries(r.peers)) {
            if (id === MY_ID) continue;
            knownPeerNames[id] = nom;
            if (peers[id]) peers[id].nom = nom;
        }
        mettreAJourPeerList(r.peers);
    }
    return r.msgs || [];
}

// ── Gestion des peers (RTCPeerConnection) ─────────────────────────────
function creerPC(peerId, peerNom) {
    if (peers[peerId] && peers[peerId].pc) return peers[peerId].pc;

    const pc = new RTCPeerConnection(ICE_CFG);

    if (localStream && localStream.getTracks().length > 0) {
        localStream.getTracks().forEach(t => pc.addTrack(t, localStream));
    } else {
        // Mode observateur : recevonly — partage d'écran déclenchera une renégociation
        pc.addTransceiver('audio', { direction: 'recvonly' });
        pc.addTransceiver('video', { direction: 'recvonly' });
    }

    pc.ontrack = (e) => {
        if (!peers[peerId]) return;
        // Toujours un stream géré par peer — évite les conflits audio/vidéo dans streams séparés
        if (!peers[peerId].stream) peers[peerId].stream = new MediaStream();
        const stream = peers[peerId].stream;
        if (!stream.getTracks().some(t => t.id === e.track.id)) {
            stream.addTrack(e.track);
        }
        toast('📹 ' + e.track.kind + ' reçu de ' + (peers[peerId].nom || peerId));
        mettreAJourVideoTile(peerId, stream);
    };

    pc.onicecandidate = (e) => {
        if (e.candidate) push(peerId, 'ice', e.candidate);
    };

    pc.onconnectionstatechange = () => {
        const s = pc.connectionState;
        if (s === 'connected') {
            setStatus('✅ Connecté', '#2ecc71');
            // Annuler le timer de déconnexion si la connexion est rétablie
            if (peers[peerId] && peers[peerId]._disconnectTimer) {
                clearTimeout(peers[peerId]._disconnectTimer);
                peers[peerId]._disconnectTimer = null;
            }
        } else if (s === 'connecting') {
            setStatus('⏳ Connexion P2P…', '#f39c12');
        } else if (s === 'failed') {
            setStatus('❌ Connexion P2P échouée', '#e74c3c');
            supprimerPeer(peerId);
        } else if (s === 'disconnected') {
            // 'disconnected' est transitoire (renégociation, réseau instable)
            // Attendre 12s avant de supprimer — la reconnexion ICE est automatique
            setStatus('⚠️ Connexion instable…', '#e67e22');
            if (peers[peerId] && !peers[peerId]._disconnectTimer) {
                peers[peerId]._disconnectTimer = setTimeout(() => {
                    if (peers[peerId] && peers[peerId].pc.connectionState === 'disconnected') {
                        supprimerPeer(peerId);
                    }
                }, 12000);
            }
        }
    };

    pc.oniceconnectionstatechange = () => {
        if (pc.iceConnectionState === 'failed') {
            setStatus('❌ ICE échoué — HTTPS requis pour la vidéo', '#e74c3c');
        }
    };

    peers[peerId] = { pc, nom: peerNom || peerId, stream: null };
    return pc;
}

async function initierConnexionVers(peerId, peerNom) {
    const pc = creerPC(peerId, peerNom);
    const offer = await pc.createOffer();
    await pc.setLocalDescription(offer);
    await push(peerId, 'offer', offer);
}

async function renegocier(peerId, pc) {
    try {
        if (pc.signalingState !== 'stable') return; // ne pas renegocier si déjà en cours
        const offer = await pc.createOffer();
        await pc.setLocalDescription(offer);
        await push(peerId, 'offer', offer);
    } catch(e) { console.warn('Renegotiation:', e.message); }
}

function supprimerPeer(peerId) {
    if (!peers[peerId]) return;
    if (peers[peerId].pc) peers[peerId].pc.close();
    delete peers[peerId];
    const tile = document.getElementById('tile_' + peerId);
    if (tile) tile.remove();
    if (presenterPeerId === peerId) quitterModePresentateur();
    mettreAJourGrille();
    toast('Un participant a quitté la salle.');
}

// ── Traitement des messages de signaling ──────────────────────────────
let _traitementEnCours = false;
async function traiterMessages() {
    if (_traitementEnCours) return;
    _traitementEnCours = true;
    try {
    let msgs;
    try { msgs = await pull(); } catch(e) { return; }

    for (const msg of msgs) {
        if (msg.from === MY_ID) continue;
        const peerId  = msg.from;
        const peerNom = (peers[peerId] && peers[peerId].nom) ? peers[peerId].nom : (knownPeerNames[peerId] || peerId);

        if (msg.type === 'offer') {
            const offer = JSON.parse(msg.data);
            const pc    = creerPC(peerId, peerNom);
            // Rollback si on a notre propre offre en cours (glare / renégociation croisée)
            if (pc.signalingState === 'have-local-offer') {
                try { await pc.setLocalDescription({ type: 'rollback' }); } catch(e) {}
            }
            await pc.setRemoteDescription(offer);
            const answer = await pc.createAnswer();
            await pc.setLocalDescription(answer);
            await push(peerId, 'answer', answer);
            // Appliquer les ICE candidates mis en attente
            if (pendingCandidates[peerId]) {
                for (const c of pendingCandidates[peerId]) {
                    try { await pc.addIceCandidate(c); } catch(e) {}
                }
                delete pendingCandidates[peerId];
            }
        }

        if (msg.type === 'answer') {
            const pc = peers[peerId] ? peers[peerId].pc : null;
            if (pc && pc.signalingState === 'have-local-offer') {
                await pc.setRemoteDescription(JSON.parse(msg.data));
                // Appliquer les ICE candidates mis en attente
                if (pendingCandidates[peerId]) {
                    for (const c of pendingCandidates[peerId]) {
                        try { await pc.addIceCandidate(c); } catch(e) {}
                    }
                    delete pendingCandidates[peerId];
                }
            }
        }

        if (msg.type === 'ice') {
            const pc = peers[peerId] ? peers[peerId].pc : null;
            if (pc && pc.remoteDescription) {
                try { await pc.addIceCandidate(JSON.parse(msg.data)); } catch(e) {}
            } else {
                // Mettre en attente jusqu'à ce que remoteDescription soit prêt
                if (!pendingCandidates[peerId]) pendingCandidates[peerId] = [];
                pendingCandidates[peerId].push(JSON.parse(msg.data));
            }
        }

        if (msg.type === 'bye') {
            supprimerPeer(peerId);
        }

        if (msg.type === 'chat') {
            const d = JSON.parse(msg.data);
            ajouterMessageChat(d.nom || peerNom, d.msg, false);
        }

        if (msg.type === 'mute_request') {
            if (IS_HOST) continue; // l'animateur ne peut pas être muté
            mutedByHost = true;
            if (localStream) {
                micOn = false;
                localStream.getAudioTracks().forEach(t => t.enabled = false);
            }
            const btn = document.getElementById('btnMic');
            btn.textContent = '🔇';
            btn.className   = 'btn-ctrl btn-off';
            document.getElementById('host-muted-bar').style.display = 'block';
            toast('🔇 L\'animateur a coupé votre micro.');
        }

        if (msg.type === 'unmute_request') {
            mutedByHost = false;
            document.getElementById('host-muted-bar').style.display = 'none';
            toast('🎤 L\'animateur a réactivé votre micro.');
        }

        if (msg.type === 'ban') {
            clearInterval(polling);
            if (localStream) localStream.getTracks().forEach(t => t.stop());
            document.body.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100vh;flex-direction:column;font-family:sans-serif;background:#1a0000;">'
                + '<div style="font-size:48px;margin-bottom:16px;">🚫</div>'
                + '<div style="font-size:18px;color:#e74c3c;font-weight:bold;margin-bottom:8px;">Accès révoqué</div>'
                + '<div style="font-size:13px;color:#aaa;margin-bottom:20px;">L\'animateur vous a exclu de cette salle.</div>'
                + '</div>';
        }

        if (msg.type === 'screen_share_start') {
            if (screenSharing) await stopScreenShare();
            // Ré-attacher le stream si srcObject a été nullifié par un screen_share_stop précédent
            if (peers[peerId] && peers[peerId].stream) {
                const tile = document.getElementById('tile_' + peerId);
                if (tile) {
                    const v = tile.querySelector('video');
                    if (v) {
                        if (!v.srcObject) v.srcObject = peers[peerId].stream;
                        if (v.paused) v.play().catch(() => {});
                    }
                }
            }
            entrerModePresentateur(peerId);
        }

        if (msg.type === 'screen_share_stop') {
            // Ne pas nullifier srcObject : syncAvatarTile détecte videoWidth=0 et affiche l'avatar
            if (presenterPeerId === peerId) quitterModePresentateur();
        }

        if (msg.type === 'raise_hand') {
            const tile = document.getElementById('tile_' + peerId);
            if (tile) {
                let hand = tile.querySelector('.tile-hand');
                if (!hand) {
                    hand = document.createElement('span');
                    hand.className = 'tile-hand';
                    hand.textContent = '✋';
                    tile.appendChild(hand);
                }
                clearTimeout(hand._timer);
                hand._timer = setTimeout(() => hand.remove(), 8000);
            }
            toast('✋ ' + peerNom + ' souhaite poser une question.');
        }

        if (msg.type === 'doc_share') {
            const d = JSON.parse(msg.data);
            const docNom = d.nom || 'Document';
            ajouterDoc(docNom, d.url, d.par || peerNom);
            afficherDocPresentateur(d.url, docNom, d.par || peerNom);
        }
    }
    } finally {
        _traitementEnCours = false;
    }
}

// ── Mode Présentation ────────────────────────────────────────────────
function entrerModePresentateur(peerId) {
    if (presentationMode) quitterModePresentateur(true);
    presentationMode = true;
    presenterPeerId  = peerId;

    const grid = document.getElementById('video-grid');
    grid.classList.add('mode-presentation');

    let strip = document.getElementById('participants-strip');
    if (!strip) {
        strip = document.createElement('div');
        strip.id = 'participants-strip';
        grid.prepend(strip);
    }

    const tileId = (peerId && peerId !== 'doc') ? 'tile_' + peerId : 'tile_local';

    Array.from(grid.querySelectorAll('.video-tile')).forEach(tile => {
        if (peerId === 'doc' || tile.id !== tileId) {
            strip.appendChild(tile);
        } else {
            tile.classList.add('presenter');
            if (!tile.querySelector('.presenter-badge')) {
                const b = document.createElement('span');
                b.className = 'presenter-badge';
                b.textContent = peerId ? '🖥 Partage en cours' : '🖥 Vous partagez';
                tile.appendChild(b);
            }
        }
    });
}

function quitterModePresentateur(silent) {
    if (!presentationMode) return;
    presentationMode = false;
    presenterPeerId  = null;

    const grid  = document.getElementById('video-grid');
    const strip = document.getElementById('participants-strip');
    grid.classList.remove('mode-presentation');

    grid.querySelectorAll('.video-tile.presenter').forEach(tile => {
        tile.classList.remove('presenter');
        const b = tile.querySelector('.presenter-badge');
        if (b) b.remove();
    });

    if (strip) {
        Array.from(strip.querySelectorAll('.video-tile')).forEach(t => grid.appendChild(t));
        strip.remove();
    }

    const wrap = document.getElementById('doc-presenter-wrap');
    if (wrap) wrap.remove();

    if (!silent) mettreAJourGrille();
}

function afficherDocPresentateur(url, nom, par) {
    entrerModePresentateur('doc');

    let wrap = document.getElementById('doc-presenter-wrap');
    if (!wrap) {
        wrap = document.createElement('div');
        wrap.id = 'doc-presenter-wrap';
        document.getElementById('video-grid').appendChild(wrap);
    }
    wrap.innerHTML =
        '<div id="doc-presenter-bar">' +
        '<span>📄</span>' +
        '<span class="doc-pres-name">' + nom.replace(/</g, '&lt;') + '</span>' +
        '<span class="doc-pres-by">par ' + par.replace(/</g, '&lt;') + '</span>' +
        '<button class="btn-close-pres" onclick="quitterModePresentateur()">✕ Fermer</button>' +
        '</div>' +
        '<iframe id="doc-presenter-iframe" src="' + url.replace(/"/g, '&quot;') + '"></iframe>';
    wrap.classList.add('active');
}

// ── Helpers avatar ────────────────────────────────────────────────────
const _AVATAR_COLORS = ['#1565C0','#00695C','#6A1B9A','#E65100','#2E7D32','#AD1457','#0277BD'];
function getInitiales(nom) {
    const p = nom.trim().split(/\s+/).filter(s => s.length > 0);
    if (p.length >= 2) return (p[0][0] + p[p.length - 1][0]).toUpperCase();
    return nom.trim().substring(0, 2).toUpperCase();
}
function getAvatarColor(nom) {
    let h = 0;
    for (let i = 0; i < nom.length; i++) h = (h * 31 + nom.charCodeAt(i)) >>> 0;
    return _AVATAR_COLORS[h % _AVATAR_COLORS.length];
}

// ── Vidéos ────────────────────────────────────────────────────────────
function creerTile(id, nom, stream, local) {
    const tile = document.createElement('div');
    tile.className = 'video-tile';
    tile.id = 'tile_' + id;

    const video = document.createElement('video');
    video.autoplay = true;
    video.playsInline = true;
    if (local) video.muted = true;
    if (stream) video.srcObject = stream;

    // Avatar initiales (caméra coupée)
    const avatar = document.createElement('div');
    avatar.className = 'tile-avatar';
    avatar.id = 'avatar_' + id;
    avatar.style.background = getAvatarColor(nom);
    avatar.textContent = getInitiales(nom);

    // Indicateur caméra coupée
    const camOff = document.createElement('div');
    camOff.className = 'tile-cam-off';
    camOff.id = 'camoff_' + id;
    camOff.textContent = '🚫';
    camOff.title = 'Caméra désactivée';

    const label = document.createElement('div');
    label.className = 'tile-nom';
    label.textContent = (local ? '📍 ' : '') + nom;

    tile.appendChild(video);
    tile.appendChild(avatar);
    tile.appendChild(camOff);
    tile.appendChild(label);
    document.getElementById('video-grid').appendChild(tile);
    if (stream) video.play().catch(() => {});
    return tile;
}

function syncAvatarTile(id, hasVideo) {
    const video  = document.querySelector('#tile_' + id + ' video');
    const avatar = document.getElementById('avatar_' + id);
    const camOff = document.getElementById('camoff_' + id);
    if (!video || !avatar) return;
    if (hasVideo) {
        video.style.display  = '';
        avatar.style.display = 'none';
        if (camOff) camOff.style.display = 'none';
    } else {
        video.style.display  = 'none';
        avatar.style.display = 'flex';
        if (camOff) camOff.style.display = 'flex';
    }
}

// Rafraîchissement global des avatars (1s)
setInterval(() => {
    // Tuile locale
    const localHasVideo = screenSharing ||
        (camOn && localStream && localStream.getVideoTracks().some(t => t.enabled && t.readyState === 'live'));
    syncAvatarTile('local', localHasVideo);

    // Tuiles distantes
    Object.keys(peers).forEach(id => {
        const v = document.querySelector('#tile_' + id + ' video');
        if (!v) return;
        const hasVideo = v.videoWidth > 0 && v.readyState >= 2;
        syncAvatarTile(id, hasVideo);
        if (hasVideo && v.paused) v.play().catch(() => {});
    });
}, 1000);

function mettreAJourVideoTile(peerId, stream) {
    let tile = document.getElementById('tile_' + peerId);
    if (!tile) {
        tile = creerTile(peerId, peers[peerId].nom, stream, false);
    } else {
        const v = tile.querySelector('video');
        if (v) {
            // Réassigner srcObject uniquement si le stream change (évite reset inutile)
            if (v.srcObject !== stream) { v.srcObject = stream; }
            if (v.paused) v.play().catch(() => {});
        }
    }
    mettreAJourGrille();
    setStatus('✅ Connecté', '#2ecc71');
}

function mettreAJourGrille() {
    const grid = document.getElementById('video-grid');
    const n = grid.querySelectorAll('.video-tile').length;
    document.getElementById('nb-participants').textContent = n + ' participant' + (n > 1 ? 's' : '');
    if (presentationMode) return; // ne pas écraser les classes en mode présentation
    grid.className = n <= 1 ? '' : (n === 2 ? 'p2' : (n <= 4 ? 'p' + n : (n <= 6 ? 'p' + n : 'p6')));
}

function mettreAJourPeerList(peersObj) {
    const ul = document.getElementById('peer-list');
    ul.innerHTML = '';

    // Soi-même
    const liMe = document.createElement('li');
    const nomMe = document.createElement('span');
    nomMe.className = 'peer-nom';
    nomMe.textContent = '📍 ' + MY_NOM + ' (vous)';
    liMe.appendChild(nomMe);
    ul.appendChild(liMe);

    // Autres participants
    let hasOthers = false;
    Object.entries(peersObj).forEach(([id, nom]) => {
        if (id === MY_ID) return;
        hasOthers = true;
        const li = document.createElement('li');
        const nomEl = document.createElement('span');
        nomEl.className = 'peer-nom';
        nomEl.textContent = '👤 ' + nom;
        li.appendChild(nomEl);

        if (IS_HOST && !id.startsWith('host_')) {
            const btnMute = document.createElement('button');
            btnMute.className   = 'mute-btn' + (mutedPeers[id] ? ' muted' : '');
            btnMute.title       = mutedPeers[id] ? 'Réactiver le micro' : 'Couper le micro';
            btnMute.textContent = mutedPeers[id] ? '🎤' : '🔇';
            btnMute.onclick = () => muterPeer(id, nom);
            li.appendChild(btnMute);

            const btnBan = document.createElement('button');
            btnBan.className   = 'ban-btn';
            btnBan.title       = 'Exclure de la salle';
            btnBan.textContent = '✕';
            btnBan.onclick = () => bannerPeer(id, nom);
            li.appendChild(btnBan);
        }
        ul.appendChild(li);
    });

    // Afficher le bouton "Muter tous" uniquement si host et il y a d'autres participants
    if (IS_HOST) {
        document.getElementById('host-controls').style.display = hasOthers ? 'block' : 'none';
    }
}

// ── Contrôles caméra / micro ─────────────────────────────────────────
function toggleMic() {
    if (!localStream) return;
    if (mutedByHost) {
        toast('🔇 Votre micro est désactivé par l\'animateur.');
        return;
    }
    micOn = !micOn;
    localStream.getAudioTracks().forEach(t => t.enabled = micOn);
    const btn = document.getElementById('btnMic');
    btn.textContent = micOn ? '🎤' : '🔇';
    btn.className   = 'btn-ctrl ' + (micOn ? 'btn-on' : 'btn-off');
}

function toggleCam() {
    if (!localStream) return;
    camOn = !camOn;
    localStream.getVideoTracks().forEach(t => t.enabled = camOn);
    const btn = document.getElementById('btnCam');
    btn.className = 'btn-ctrl ' + (camOn ? 'btn-on' : 'btn-off');
    syncAvatarTile('local', camOn && localStream.getVideoTracks().some(t => t.enabled));
}

// ── Partage d'écran ──────────────────────────────────────────────────
async function toggleScreen() {
    if (screenSharing) { await stopScreenShare(); return; }

    if (!navigator.mediaDevices || !navigator.mediaDevices.getDisplayMedia) {
        toast('⚠️ Partage d\'écran indisponible (HTTPS requis).');
        return;
    }
    try {
        screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true, audio: false });
        screenSharing = true;

        const screenTrack = screenStream.getVideoTracks()[0];
        screenTrack.addEventListener('ended', stopScreenShare);

        for (const [id, p] of Object.entries(peers)) {
            if (!p.pc) continue;
            // Trouver le transceiver vidéo (sender actif ou receiver existant)
            const trans = p.pc.getTransceivers().find(t =>
                (t.sender && t.sender.track && t.sender.track.kind === 'video') ||
                (t.receiver && t.receiver.track && t.receiver.track.kind === 'video')
            );
            if (trans) {
                // Forcer la direction sendrecv (Chrome downgrade à recvonly si sender.track=null)
                if (trans.direction !== 'sendrecv' && trans.direction !== 'sendonly') {
                    trans.direction = 'sendrecv';
                }
                try { await trans.sender.replaceTrack(screenTrack); } catch(e) {}
            } else {
                // Aucun transceiver vidéo : en ajouter un
                p.pc.addTrack(screenTrack);
            }
            // Renégocier TOUJOURS : garantit que le SDP reflète sendrecv avec la vraie track
            await renegocier(id, p.pc);
        }
        // Afficher localement
        const localVideo = document.querySelector('#tile_local video');
        if (localVideo) localVideo.srcObject = screenStream;

        document.getElementById('btnScreen').className = 'btn-ctrl btn-amber';
        await push('all', 'screen_share_start', { nom: MY_NOM });
        entrerModePresentateur(null);
        toast('🖥️ Partage d\'écran activé.');
    } catch(e) {
        toast('⚠️ Partage d\'écran annulé.');
    }
}

async function stopScreenShare() {
    if (!screenSharing) return;
    screenSharing = false;
    if (screenStream) { screenStream.getTracks().forEach(t => t.stop()); screenStream = null; }

    document.getElementById('btnScreen').className = 'btn-ctrl btn-on';

    const camTrack = localStream ? (localStream.getVideoTracks()[0] || null) : null;

    for (const [id, p] of Object.entries(peers)) {
        if (!p.pc) continue;
        const trans = p.pc.getTransceivers().find(t =>
            (t.sender && t.sender.track && t.sender.track.kind === 'video') ||
            (t.receiver && t.receiver.track && t.receiver.track.kind === 'video')
        );
        if (trans) {
            try { await trans.sender.replaceTrack(camTrack); } catch(e) {}
            if (!camTrack) {
                // Pas de caméra : signaler l'arrêt pour éviter image figée côté receveur
                await push(id, 'screen_share_stop', {});
            }
        }
    }

    const localVideo = document.querySelector('#tile_local video');
    if (localVideo) localVideo.srcObject = localStream || null;
    quitterModePresentateur();
    await push('all', 'screen_share_stop', {});
    toast('📷 Partage d\'écran arrêté.');
}

// ── Panels ────────────────────────────────────────────────────────────
function togglePanel() {
    panelOpen = !panelOpen;
    document.getElementById('panel').classList.toggle('open', panelOpen);
    if (panelOpen) {
        chatUnread = 0;
        document.getElementById('chat-badge').style.display = 'none';
    }
}

function switchPanelTab(tab) {
    ['peers','chat'].forEach(t => {
        document.getElementById('pane-' + t).classList.toggle('active', t === tab);
        document.getElementById('tab-' + t).classList.toggle('active', t === tab);
    });
    if (tab === 'chat') {
        chatUnread = 0;
        document.getElementById('chat-badge').style.display = 'none';
        setTimeout(() => {
            const msgs = document.getElementById('chat-messages');
            msgs.scrollTop = msgs.scrollHeight;
        }, 50);
    }
}

function toggleDocPanel() {
    docPanelOpen = !docPanelOpen;
    document.getElementById('doc-panel').classList.toggle('open', docPanelOpen);
}

// ── Contrôle micros (animateur) ───────────────────────────────────────
async function muterPeer(peerId, peerNom) {
    if (peerId.startsWith('host_')) { toast('⚠️ Impossible de muter un animateur.'); return; }
    if (mutedPeers[peerId]) {
        delete mutedPeers[peerId];
        await push(peerId, 'unmute_request', {});
        toast('🎤 Micro de ' + peerNom + ' réactivé.');
    } else {
        mutedPeers[peerId] = true;
        await push(peerId, 'mute_request', { mic: true });
        toast('🔇 Micro de ' + peerNom + ' coupé.');
    }
    mettreAJourPeerList(Object.fromEntries(Object.entries(peers).map(([id, p]) => [id, p.nom])));
}

async function bannerPeer(peerId, peerNom) {
    if (!confirm('Exclure ' + peerNom + ' de la salle ? Il ne pourra plus revenir.')) return;
    await sig({ action: 'ban', ban_id: peerId, ban_nom: peerNom });
    supprimerPeer(peerId);
    toast('🚫 ' + peerNom + ' a été exclu de la salle.');
}

async function muterTous() {
    // N'envoyer qu'aux participants (pas aux animateurs)
    for (const [id] of Object.entries(peers)) {
        if (!id.startsWith('host_')) {
            mutedPeers[id] = true;
            await push(id, 'mute_request', { mic: true });
        }
    }
    toast('🔇 Tous les micros participants ont été coupés.');
    mettreAJourPeerList(Object.fromEntries(Object.entries(peers).map(([id, p]) => [id, p.nom])));
}

// ── Chat ──────────────────────────────────────────────────────────────
function ajouterMessageChat(nom, texte, mine) {
    const div = document.createElement('div');
    div.className = 'chat-msg' + (mine ? ' mine' : '');
    const nomEl = document.createElement('div');
    nomEl.className = 'msg-nom';
    nomEl.textContent = mine ? 'Vous' : nom;
    const txtEl = document.createElement('div');
    txtEl.className = 'msg-txt';
    txtEl.textContent = texte;
    div.appendChild(nomEl);
    div.appendChild(txtEl);
    const container = document.getElementById('chat-messages');
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;

    if (!mine && (!panelOpen || document.getElementById('pane-chat').style.display === 'none'
               || !document.getElementById('pane-chat').classList.contains('active'))) {
        chatUnread++;
        const badge = document.getElementById('chat-badge');
        badge.style.display = 'flex';
        badge.textContent = chatUnread > 9 ? '9+' : chatUnread;
        toast('💬 ' + nom + ' : ' + texte.substring(0, 40) + (texte.length > 40 ? '…' : ''));
    }
}

async function sendChat() {
    const input = document.getElementById('chat-input');
    const texte = input.value.trim();
    if (!texte) return;
    input.value = '';
    ajouterMessageChat(MY_NOM, texte, true);
    await push('all', 'chat', { msg: texte, nom: MY_NOM });
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('chat-input').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendChat(); }
    });
});

// ── Documents partagés ────────────────────────────────────────────────
function ajouterDoc(nom, url, par) {
    sharedDocs.push({ nom, url, par });

    const list = document.getElementById('doc-list');
    const empty = list.querySelector('.doc-empty');
    if (empty) empty.remove();

    const item = document.createElement('div');
    item.className = 'doc-item';
    const safenom = nom.replace(/</g,'&lt;').replace(/>/g,'&gt;');
    const safeurl = url.replace(/"/g, '&quot;');
    item.innerHTML = '<div class="doc-nom">📄 ' + safenom + '</div>'
        + '<div class="doc-par">Partagé par ' + par.replace(/</g,'&lt;') + '</div>'
        + '<a href="' + safeurl + '" target="_blank">Ouvrir ↗</a>'
        + '<a href="' + safeurl + '" download>Télécharger ⬇</a>';
    list.appendChild(item);

    document.getElementById('doc-notif-title').textContent = '📄 ' + nom;
    document.getElementById('doc-notif-par').textContent   = 'Partagé par ' + par;
    document.getElementById('doc-notif-link').href         = url;
    const notif = document.getElementById('doc-notif');
    notif.style.display = 'block';
    setTimeout(() => { notif.style.display = 'none'; }, 8000);
}

async function partagerDocument(input) {
    const file = input.files[0];
    if (!file) return;
    if (file.size > 10 * 1024 * 1024) { toast('⚠️ Fichier trop grand (max 10 Mo).'); input.value = ''; return; }

    toast('📤 Envoi en cours…');
    const fd = new FormData();
    fd.append('doc', file);
    fd.append('room', ROOM);

    try {
        const resp = await fetch('upload.php', { method: 'POST', body: fd });
        const json = await resp.json();
        if (json.ok) {
            await push('all', 'doc_share', { url: json.url, nom: json.nom, par: MY_NOM });
            ajouterDoc(json.nom, json.url, 'moi');
            afficherDocPresentateur(json.url, json.nom, 'moi');
            toast('📄 Document partagé.');
        } else {
            toast('⚠️ ' + (json.error || 'Erreur lors du partage.'));
        }
    } catch(e) {
        toast('⚠️ Erreur réseau lors du partage.');
    }
    input.value = '';
}

// ── Lever la main ────────────────────────────────────────────────────
async function leverMain() {
    const localTile = document.getElementById('tile_local');
    if (localTile) {
        let hand = localTile.querySelector('.tile-hand');
        if (!hand) {
            hand = document.createElement('span');
            hand.className = 'tile-hand';
            hand.textContent = '✋';
            localTile.appendChild(hand);
        }
        clearTimeout(hand._timer);
        hand._timer = setTimeout(() => hand.remove(), 8000);
    }
    await push('all', 'raise_hand', {});
    toast('✋ Main levée — les participants ont été notifiés.');
}

// ── Raccrocher ────────────────────────────────────────────────────────
async function raccrocher() {
    clearInterval(polling);
    await sig({ action: 'leave' }).catch(() => {});
    fetch('cleanup.php?room=' + encodeURIComponent(ROOM)).catch(() => {});
    Object.values(peers).forEach(p => { try { p.pc.close(); } catch(e) {} });
    if (localStream) localStream.getTracks().forEach(t => t.stop());
    if (screenStream) screenStream.getTracks().forEach(t => t.stop());
    window.close();
    setTimeout(function() {
        document.body.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100vh;flex-direction:column;font-family:sans-serif;background:#f0f2fa;">'
            + '<div style="font-size:48px;margin-bottom:16px;">📵</div>'
            + '<div style="font-size:18px;color:#080A66;font-weight:bold;margin-bottom:8px;">Session terminée</div>'
            + '<div style="font-size:13px;color:#666;margin-bottom:20px;">Vous avez quitté la salle.</div>'
            + '<button onclick="window.close()" style="background:#080A66;color:#fff;border:none;padding:10px 24px;border-radius:6px;cursor:pointer;font-size:14px;">Fermer cet onglet</button>'
            + '</div>';
    }, 300);
}

// ── Init ──────────────────────────────────────────────────────────────
async function init() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        toast('⚠️ Caméra/Micro indisponibles — HTTPS requis. Mode observateur.');
        setStatus('👁️ Mode observateur (pas de caméra)', '#e67e22');
    } else {
        try {
            localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        } catch(e) {
            try {
                localStream = await navigator.mediaDevices.getUserMedia({ video: false, audio: true });
                toast('Caméra non disponible, audio seul.');
            } catch(e2) {
                toast('⚠️ ' + e2.message + ' — mode observateur.');
                setStatus('👁️ Mode observateur (pas de caméra)', '#e67e22');
            }
        }
    }

    creerTile('local', MY_NOM, localStream, true);
    mettreAJourGrille();
    setStatus('📡 Connexion…', '#f39c12');

    const joinResp = await sig({ action: 'join' });
    if (joinResp.error === 'acces_refuse' || joinResp.error === 'salle_pleine' || joinResp.error === 'banni') {
        if (localStream) localStream.getTracks().forEach(t => t.stop());
        const icons   = { acces_refuse: '🔒', salle_pleine: '🚫', banni: '⛔' };
        const titres  = { acces_refuse: 'Accès non autorisé', salle_pleine: 'Salle complète', banni: 'Accès refusé' };
        const defMsgs = {
            acces_refuse: "Votre classe n'est pas autorisée dans cette salle.",
            salle_pleine: "La salle a atteint son nombre maximum de participants.",
            banni:        "Vous avez été exclu de cette salle par l'animateur."
        };
        document.body.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100vh;flex-direction:column;font-family:sans-serif;background:#fff3cd;">'
            + '<div style="font-size:48px;margin-bottom:16px;">' + (icons[joinResp.error] || '🔒') + '</div>'
            + '<div style="font-size:18px;color:#856404;font-weight:bold;margin-bottom:8px;">' + (titres[joinResp.error] || 'Accès refusé') + '</div>'
            + '<div style="font-size:13px;color:#666;margin-bottom:20px;">' + (joinResp.msg || defMsgs[joinResp.error] || '') + '</div>'
            + '<button onclick="window.close()" style="background:#856404;color:#fff;border:none;padding:10px 24px;border-radius:6px;cursor:pointer;font-size:14px;">Fermer</button>'
            + '</div>';
        return;
    }
    if (joinResp.peers) {
        for (const [peerId, peerNom] of Object.entries(joinResp.peers)) {
            knownPeerNames[peerId] = peerNom;
        }
        for (const [peerId, peerNom] of Object.entries(joinResp.peers)) {
            try { await initierConnexionVers(peerId, peerNom); } catch(e) { console.error('Connexion vers', peerId, e); }
        }
        if (Object.keys(joinResp.peers).length > 0) {
            setStatus('⏳ En attente de réponse…', '#f39c12');
        } else {
            setStatus('⏳ En attente de participants…', '#aab');
        }
        mettreAJourPeerList(joinResp.peers);
    }

    polling = setInterval(traiterMessages, 600);
    setInterval(() => sig({ action: 'ping' }), 10000);

    // Keepalive session PHP (même mécanisme que CnxAjax() des menus — toutes les 4 min)
    setInterval(() => {
        fetch('../verifConnex2.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'nb=0'
        }).catch(() => {});
    }, 240000);

}

// ── UI ────────────────────────────────────────────────────────────────
function setStatus(txt, color) {
    const el = document.getElementById('status');
    el.textContent = txt;
    el.style.color = color || '#fff';
}

function toast(msg) {
    const el = document.getElementById('toast');
    el.textContent = msg;
    el.style.display = 'block';
    setTimeout(() => el.style.display = 'none', 4000);
}

window.addEventListener('load', async () => {
    const ok = await fetchToken();
    if (!ok) return; // abonnement bloqué, page remplacée
    init().catch(e => { console.error('Init visio:', e); setStatus('⚠️ Erreur init', '#e74c3c'); });
});
window.addEventListener('beforeunload', () => {
    const extra = _jwt ? { token: _jwt } : {};
    navigator.sendBeacon(SIGNAL, new URLSearchParams(Object.assign({ action:'leave', room:ROOM, my_id:MY_ID, nom:MY_NOM }, extra)));
});
</script>
</body>
</html>
