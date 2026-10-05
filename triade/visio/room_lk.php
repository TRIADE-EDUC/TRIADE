<?php
session_start();
include_once("../common/config.inc.php");
include_once("../librairie_php/db_triade.php");
if (empty($_SESSION['membre']) && empty($_SESSION['admin1'])) { header('Location: ../index1.php'); exit; }
$cnx = cnx();
include_once("check_abo.php");
include_once("lk_config.php");
Pgclose();

$code = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['code'] ?? '');
if ($code === '') { header('Location: rooms.php'); exit; }

// Vérifier LiveKit activé + configuré
if (!lkEnabled() || !lkHost() || !lkApiKey() || !lkSecret()) {
    header('Location: room.php?code=' . urlencode($code) . '&role=' . urlencode($_GET['role'] ?? 'guest'));
    exit;
}

$_allowedHost = ['menuprof', 'menuadmin', 'menuscolaire', 'menupersonnel'];
$_peutEtreHost = !empty($_SESSION['admin1']) || in_array($_SESSION['membre'] ?? '', $_allowedHost);
$role = $_peutEtreHost ? 'host' : 'guest';
$nom_user = !empty($_SESSION['nom']) ? $_SESSION['nom'] : (!empty($_SESSION['admin1']) ? 'Admin' : 'Participant');

// Lire le JSON de salle pour le nom
$dir_rooms = __DIR__ . '/rooms/';
$room_file = $dir_rooms . $code . '.json';
$room_nom  = $code;
if (file_exists($room_file)) {
    $rd = json_decode(file_get_contents($room_file), true);
    if (!empty($rd['nom'])) $room_nom = $rd['nom'];
}

$lkWsUrl = preg_replace('/^wss?:\/\//', '', lkHost());
$lkWsUrl = 'wss://' . $lkWsUrl;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Visio LiveKit — <?php echo htmlspecialchars($code); ?></title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { background: #eef0f8; color: #333; font-family: Arial, sans-serif; height: 100vh; display: flex; flex-direction: column; overflow: hidden; }

#header {
    display: flex; align-items: center; justify-content: space-between;
    background: #080A66; padding: 8px 16px; flex-shrink: 0; gap: 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
}
#header h1 { font-size: 15px; color: #ccd; white-space: nowrap; }
#code-badge { background: #080A66; color: #fff; padding: 3px 10px; border-radius: 20px; font-size: 12px; letter-spacing: 2px; white-space: nowrap; }
#status { font-size: 12px; color: #f90; white-space: nowrap; }

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
#video-grid.many { grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); }

.video-tile {
    position: relative; background: #CACCEF; border-radius: 10px; overflow: hidden;
    display: flex; align-items: center; justify-content: center;
    border: 1px solid #b0b5e0; transition: border-color .2s, box-shadow .2s;
    box-shadow: 0 2px 8px rgba(8,10,102,.08);
}
.video-tile.speaking { border-color: #080A66; box-shadow: 0 0 0 2px #080A66, 0 4px 18px rgba(8,10,102,.2); }
.video-tile video { width: 100%; height: 100%; object-fit: cover; }
.video-tile .tile-nom {
    position: absolute; bottom: 0; left: 0; right: 0;
    padding: 22px 10px 8px;
    background: linear-gradient(to top, rgba(8,10,102,.55) 0%, transparent 100%);
    color: #fff; font-size: 12px; font-weight: 600;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.tile-avatar {
    position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
    width: 72px; height: 72px; border-radius: 50%;
    display: none; align-items: center; justify-content: center;
    font-size: 26px; font-weight: 700; color: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,.4);
}
.tile-lk-badge {
    position: absolute; top: 6px; right: 8px;
    background: rgba(8,10,102,.7); color: #9099d8;
    font-size: 9px; padding: 2px 7px; border-radius: 8px; font-weight: bold;
}

/* Panel droit */
#panel {
    position: fixed; top: 48px; right: 0; width: 260px; height: calc(100% - 48px - 70px);
    background: #f5f6fb; display: flex; flex-direction: column;
    transform: translateX(100%); transition: transform .2s; z-index: 10;
    border-left: 1px solid #c5cae9;
}
#panel.open { transform: translateX(0); }
#panel-tabs { display: flex; border-bottom: 1px solid #e8eaf6; flex-shrink: 0; }
.panel-tab {
    flex: 1; background: none; border: none; color: #7986cb; font-size: 12px;
    padding: 8px 4px; cursor: pointer; text-align: center;
}
.panel-tab.active { color: #080A66; border-bottom: 2px solid #080A66; font-weight: 600; }
.panel-tab-content { display: none; flex: 1; flex-direction: column; overflow: hidden; }
.panel-tab-content.active { display: flex; }
#peers-content { padding: 10px; overflow-y: auto; }
#peer-list { list-style: none; }
#peer-list li { font-size: 13px; padding: 5px 4px; border-bottom: 1px solid #e8eaf6; color: #333; }

/* Chat */
#chat-messages { flex: 1; overflow-y: auto; padding: 8px; display: flex; flex-direction: column; gap: 6px; }
.chat-msg { background: #fff; border: 1px solid #e8eaf6; border-radius: 8px; padding: 7px 10px; max-width: 95%; }
.chat-msg.mine { background: #080A66; border-color: #080A66; align-self: flex-end; }
.chat-msg .msg-nom { font-size: 10px; color: #9099d8; margin-bottom: 3px; }
.chat-msg .msg-txt { font-size: 13px; color: #333; word-break: break-word; }
.chat-msg.mine .msg-nom { color: rgba(202,204,239,.7); }
.chat-msg.mine .msg-txt { color: #fff; }
#chat-input-area { display: flex; gap: 6px; padding: 8px; border-top: 1px solid #c5cae9; flex-shrink: 0; }
#chat-input {
    flex: 1; background: #fff; border: 1px solid #c5cae9; color: #333;
    border-radius: 16px; padding: 6px 12px; font-size: 13px; outline: none;
}
#chat-input:focus { border-color: #3949ab; }
#chat-send {
    background: #080A66; color: #fff; border: none; border-radius: 50%;
    width: 34px; height: 34px; cursor: pointer; font-size: 16px;
    display: flex; align-items: center; justify-content: center;
}
#chat-badge {
    position: absolute; top: -4px; right: -4px; background: #e74c3c;
    color: #fff; font-size: 10px; width: 16px; height: 16px;
    border-radius: 50%; display: none; align-items: center; justify-content: center;
}

/* Contrôles */
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
#nb-participants { font-size: 12px; color: rgba(202,204,239,.7); }

/* Toast */
#toast {
    position: fixed; top: 55px; left: 50%; transform: translateX(-50%);
    background: #080A66; color: #fff; padding: 7px 18px;
    border-radius: 20px; font-size: 13px; display: none; z-index: 99;
    white-space: nowrap; box-shadow: 0 4px 12px rgba(8,10,102,.3);
}
</style>
</head>
<body>

<div id="header">
    <h1>📹 TRIADE Visio <span style="font-size:10px;color:#9099d8">LiveKit</span>
        <span style="font-size:11px;color:#778"><?php echo $role==='host'?'(Animateur)':'(Participant)'; ?></span>
    </h1>
    <span id="code-badge"><?php echo htmlspecialchars($code); ?></span>
    <span id="status">⏳ Connexion…</span>
</div>

<div id="video-grid"></div>

<!-- Panel droit -->
<div id="panel">
    <div id="panel-tabs">
        <button class="panel-tab active" onclick="switchTab('peers')" id="tab-peers">👥 Participants</button>
        <button class="panel-tab" onclick="switchTab('chat')" id="tab-chat">💬 Chat</button>
    </div>
    <div class="panel-tab-content active" id="pane-peers">
        <div id="peers-content"><ul id="peer-list"></ul></div>
    </div>
    <div class="panel-tab-content" id="pane-chat">
        <div id="chat-messages"></div>
        <div id="chat-input-area">
            <input type="text" id="chat-input" placeholder="Écrire un message…" maxlength="500" autocomplete="off">
            <button id="chat-send" onclick="sendChat()">➤</button>
        </div>
    </div>
</div>

<div id="controls">
    <span id="nb-participants">1 participant</span>
    <button class="btn-ctrl btn-on"  id="btnMic"    onclick="toggleMic()"    title="Micro">🎤</button>
    <button class="btn-ctrl btn-on"  id="btnCam"    onclick="toggleCam()"    title="Caméra">📷</button>
    <button class="btn-ctrl btn-on"  id="btnScreen" onclick="toggleScreen()" title="Partager l'écran">🖥️</button>
    <button class="btn-ctrl btn-blue" id="btnChat"  onclick="togglePanel()"  title="Chat / Participants" style="position:relative;">
        💬<span id="chat-badge">0</span>
    </button>
    <button class="btn-ctrl btn-amber" onclick="leverMain()"                 title="Lever la main">✋</button>
    <button class="btn-ctrl btn-red"   onclick="raccrocher()"                title="Raccrocher">📵</button>
</div>

<div id="toast"></div>

<script src="https://cdn.jsdelivr.net/npm/livekit-client@2/dist/livekit-client.umd.min.js"></script>
<script>
const ROOM    = <?php echo json_encode($code); ?>;
const MY_ROLE = <?php echo json_encode($role); ?>;
const MY_NOM  = <?php echo json_encode($nom_user); ?>;
const LK_TOKEN_ENDPOINT = 'lk_token.php';
const IS_HOST = (MY_ROLE === 'host');

let _lkRoom     = null;
let micEnabled  = true;
let camEnabled  = true;
let screenSharing = false;
let panelOpen   = false;
let chatUnread  = 0;

// ── Couleurs avatar ───────────────────────────────────────────────────
const _COLORS = ['#1565C0','#00695C','#6A1B9A','#E65100','#2E7D32','#AD1457','#0277BD'];
function initiales(nom) {
    const p = nom.trim().split(/\s+/).filter(s => s.length);
    return p.length >= 2 ? (p[0][0]+p[p.length-1][0]).toUpperCase() : nom.trim().substring(0,2).toUpperCase();
}
function avatarColor(nom) {
    let h = 0;
    for (let i = 0; i < nom.length; i++) h = (h*31 + nom.charCodeAt(i)) >>> 0;
    return _COLORS[h % _COLORS.length];
}

// ── Tiles vidéo ───────────────────────────────────────────────────────
function creerTile(id, nom, local) {
    if (document.getElementById('tile_'+id)) return;
    const tile  = document.createElement('div');
    tile.className = 'video-tile';
    tile.id = 'tile_'+id;

    const video = document.createElement('video');
    video.autoplay = true; video.playsInline = true;
    if (local) video.muted = true;

    const avatar = document.createElement('div');
    avatar.className = 'tile-avatar';
    avatar.id = 'avatar_'+id;
    avatar.style.background = avatarColor(nom);
    avatar.textContent = initiales(nom);
    avatar.style.display = 'flex';

    const badge = document.createElement('div');
    badge.className = 'tile-lk-badge';
    badge.textContent = 'LiveKit';

    const label = document.createElement('div');
    label.className = 'tile-nom';
    label.textContent = (local ? '📍 ' : '') + nom;

    tile.appendChild(video);
    tile.appendChild(avatar);
    tile.appendChild(badge);
    tile.appendChild(label);
    document.getElementById('video-grid').appendChild(tile);
    mettreAJourGrille();
}

function attacherTrack(identity, track) {
    let tile = document.getElementById('tile_'+identity);
    if (!tile) return;
    const video = tile.querySelector('video');
    if (!video) return;
    track.attach(video);
    video.play().catch(()=>{});
    // Masquer avatar quand la vidéo a des données
    video.addEventListener('loadeddata', () => {
        const av = document.getElementById('avatar_'+identity);
        if (av) av.style.display = 'none';
        video.style.display = '';
    });
}

function detacherVideo(identity) {
    const tile = document.getElementById('tile_'+identity);
    if (!tile) return;
    const video = tile.querySelector('video');
    if (video) { video.style.display = 'none'; video.srcObject = null; }
    const av = document.getElementById('avatar_'+identity);
    if (av) av.style.display = 'flex';
}

function supprimerTile(identity) {
    const tile = document.getElementById('tile_'+identity);
    if (tile) tile.remove();
    mettreAJourGrille();
}

function mettreAJourGrille() {
    const grid = document.getElementById('video-grid');
    const n = grid.querySelectorAll('.video-tile').length;
    document.getElementById('nb-participants').textContent = n + ' participant' + (n > 1 ? 's' : '');
    if (n > 6) { grid.className = 'many'; return; }
    grid.className = n <= 1 ? '' : (n <= 6 ? 'p'+n : '');
}

// ── Liste participants ────────────────────────────────────────────────
function rafraichirPeerList() {
    if (!_lkRoom) return;
    const ul = document.getElementById('peer-list');
    ul.innerHTML = '';
    const liMe = document.createElement('li');
    liMe.textContent = '📍 ' + MY_NOM + ' (vous)';
    ul.appendChild(liMe);
    for (const [, p] of _lkRoom.remoteParticipants) {
        const li = document.createElement('li');
        li.textContent = '👤 ' + (p.name || p.identity);
        ul.appendChild(li);
    }
}

// ── Contrôles ─────────────────────────────────────────────────────────
async function toggleMic() {
    if (!_lkRoom) return;
    micEnabled = !micEnabled;
    await _lkRoom.localParticipant.setMicrophoneEnabled(micEnabled);
    const btn = document.getElementById('btnMic');
    btn.textContent = micEnabled ? '🎤' : '🔇';
    btn.className   = 'btn-ctrl ' + (micEnabled ? 'btn-on' : 'btn-off');
}

async function toggleCam() {
    if (!_lkRoom) return;
    camEnabled = !camEnabled;
    await _lkRoom.localParticipant.setCameraEnabled(camEnabled);
    const btn = document.getElementById('btnCam');
    btn.className = 'btn-ctrl ' + (camEnabled ? 'btn-on' : 'btn-off');
    // Afficher/masquer avatar local
    const localId = _lkRoom.localParticipant.identity;
    if (!camEnabled) { detacherVideo(localId); }
}

async function toggleScreen() {
    if (!_lkRoom) return;
    screenSharing = !screenSharing;
    try {
        await _lkRoom.localParticipant.setScreenShareEnabled(screenSharing);
        document.getElementById('btnScreen').className = 'btn-ctrl ' + (screenSharing ? 'btn-amber' : 'btn-on');
        toast(screenSharing ? '🖥️ Partage d\'écran activé.' : '📷 Partage d\'écran arrêté.');
    } catch(e) {
        screenSharing = !screenSharing;
        toast('⚠️ Partage d\'écran annulé.');
    }
}

async function leverMain() {
    if (!_lkRoom) return;
    await _lkRoom.localParticipant.publishData(
        new TextEncoder().encode(JSON.stringify({ type: 'raise_hand', nom: MY_NOM })),
        { reliable: true }
    );
    toast('✋ Main levée.');
}

async function raccrocher() {
    if (_lkRoom) await _lkRoom.disconnect();
    document.body.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100vh;flex-direction:column;font-family:sans-serif;background:#f0f2fa;">'
        + '<div style="font-size:48px;margin-bottom:16px;">📵</div>'
        + '<div style="font-size:18px;color:#080A66;font-weight:bold;margin-bottom:8px;">Session terminée</div>'
        + '<button onclick="window.close()" style="background:#080A66;color:#fff;border:none;padding:10px 24px;border-radius:6px;cursor:pointer;font-size:14px;">Fermer</button>'
        + '</div>';
}

// ── Chat ──────────────────────────────────────────────────────────────
function ajouterMessage(nom, texte, mine) {
    const div = document.createElement('div');
    div.className = 'chat-msg' + (mine ? ' mine' : '');
    div.innerHTML = '<div class="msg-nom">' + (mine ? 'Vous' : htmlEsc(nom)) + '</div>'
                  + '<div class="msg-txt">' + htmlEsc(texte) + '</div>';
    const c = document.getElementById('chat-messages');
    c.appendChild(div);
    c.scrollTop = c.scrollHeight;
    if (!mine) {
        chatUnread++;
        const badge = document.getElementById('chat-badge');
        badge.style.display = 'flex'; badge.textContent = chatUnread > 9 ? '9+' : chatUnread;
        toast('💬 ' + nom + ' : ' + texte.substring(0,40) + (texte.length>40?'…':''));
    }
}

async function sendChat() {
    const input = document.getElementById('chat-input');
    const texte = input.value.trim();
    if (!texte || !_lkRoom) return;
    input.value = '';
    ajouterMessage(MY_NOM, texte, true);
    await _lkRoom.localParticipant.publishData(
        new TextEncoder().encode(JSON.stringify({ type: 'chat', nom: MY_NOM, msg: texte })),
        { reliable: true }
    );
}

// ── Panel ─────────────────────────────────────────────────────────────
function togglePanel() {
    panelOpen = !panelOpen;
    document.getElementById('panel').classList.toggle('open', panelOpen);
    if (panelOpen) {
        chatUnread = 0;
        document.getElementById('chat-badge').style.display = 'none';
    }
}
function switchTab(tab) {
    ['peers','chat'].forEach(t => {
        document.getElementById('pane-'+t).classList.toggle('active', t===tab);
        document.getElementById('tab-'+t).classList.toggle('active', t===tab);
    });
    if (tab === 'chat') {
        chatUnread = 0;
        document.getElementById('chat-badge').style.display = 'none';
    }
}

// ── Toast ─────────────────────────────────────────────────────────────
function toast(msg) {
    const el = document.getElementById('toast');
    el.textContent = msg; el.style.display = 'block';
    setTimeout(() => el.style.display = 'none', 4000);
}
function setStatus(txt, color) {
    const el = document.getElementById('status');
    el.textContent = txt; el.style.color = color || '#fff';
}
function htmlEsc(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// ── Init LiveKit ──────────────────────────────────────────────────────
async function init() {
    setStatus('⏳ Récupération du token…', '#f90');

    // 1. Obtenir le token LiveKit
    let tokenData;
    try {
        const resp = await fetch(LK_TOKEN_ENDPOINT, {
            method: 'POST',
            body: new URLSearchParams({ room: ROOM, identity: MY_ROLE+'_'+Math.random().toString(36).substr(2,8), nom: MY_NOM })
        });
        tokenData = await resp.json();
    } catch(e) {
        setStatus('⚠️ Impossible de joindre le serveur LiveKit', '#e74c3c');
        return;
    }

    if (!tokenData.token) {
        setStatus('⚠️ Erreur token : ' + (tokenData.error || 'inconnu'), '#e74c3c');
        return;
    }

    // 2. Créer et connecter la Room LiveKit
    _lkRoom = new LivekitClient.Room({
        adaptiveStream: true,
        dynacast: true,
        videoCaptureDefaults: { resolution: LivekitClient.VideoPresets.h360.resolution },
    });

    const { RoomEvent, TrackSource } = LivekitClient;

    _lkRoom.on(RoomEvent.ParticipantConnected, (participant) => {
        const nom = participant.name || participant.identity;
        creerTile(participant.identity, nom, false);
        rafraichirPeerList();
        toast('👤 ' + nom + ' a rejoint la salle.');
    });

    _lkRoom.on(RoomEvent.ParticipantDisconnected, (participant) => {
        supprimerTile(participant.identity);
        rafraichirPeerList();
        toast('Un participant a quitté.');
    });

    _lkRoom.on(RoomEvent.TrackSubscribed, (track, pub, participant) => {
        creerTile(participant.identity, participant.name || participant.identity, false);
        if (track.kind === 'video') {
            attacherTrack(participant.identity, track);
        }
        if (track.kind === 'audio') {
            // L'audio se joue automatiquement via attach()
            const audioEl = track.attach();
            document.body.appendChild(audioEl);
        }
    });

    _lkRoom.on(RoomEvent.TrackUnsubscribed, (track, pub, participant) => {
        if (track.kind === 'video') detacherVideo(participant.identity);
        track.detach();
    });

    _lkRoom.on(RoomEvent.ActiveSpeakersChanged, (speakers) => {
        document.querySelectorAll('.video-tile').forEach(t => t.classList.remove('speaking'));
        speakers.forEach(s => {
            const t = document.getElementById('tile_' + s.identity);
            if (t) t.classList.add('speaking');
        });
    });

    _lkRoom.on(RoomEvent.DataReceived, (data, participant) => {
        try {
            const msg = JSON.parse(new TextDecoder().decode(data));
            const nom = participant ? (participant.name || participant.identity) : '?';
            if (msg.type === 'chat') {
                ajouterMessage(msg.nom || nom, msg.msg, false);
            }
            if (msg.type === 'raise_hand') {
                toast('✋ ' + (msg.nom || nom) + ' souhaite poser une question.');
            }
        } catch(e) {}
    });

    _lkRoom.on(RoomEvent.Disconnected, () => {
        setStatus('🔌 Déconnecté', '#e74c3c');
    });

    try {
        await _lkRoom.connect(tokenData.lk_url, tokenData.token);
    } catch(e) {
        setStatus('⚠️ Connexion LiveKit échouée : ' + e.message, '#e74c3c');
        return;
    }

    setStatus('📡 Initialisation caméra…', '#f39c12');

    // 3. Tile locale
    const localIdentity = _lkRoom.localParticipant.identity;
    creerTile(localIdentity, MY_NOM, true);

    // 4. Publier micro + caméra
    try {
        await _lkRoom.localParticipant.enableMicrophone();
        await _lkRoom.localParticipant.enableCamera();
        // Attacher la vidéo locale
        const pubCam = Array.from(_lkRoom.localParticipant.videoTrackPublications.values())
            .find(p => p.source === TrackSource.Camera);
        if (pubCam && pubCam.videoTrack) {
            attacherTrack(localIdentity, pubCam.videoTrack);
        }
    } catch(e) {
        toast('⚠️ Caméra/micro non disponibles : ' + e.message);
    }

    // 5. Participants déjà présents
    for (const [, participant] of _lkRoom.remoteParticipants) {
        const nom = participant.name || participant.identity;
        creerTile(participant.identity, nom, false);
        for (const pub of participant.videoTrackPublications.values()) {
            if (pub.track) attacherTrack(participant.identity, pub.track);
        }
        for (const pub of participant.audioTrackPublications.values()) {
            if (pub.track) {
                const audioEl = pub.track.attach();
                document.body.appendChild(audioEl);
            }
        }
    }

    rafraichirPeerList();
    mettreAJourGrille();
    setStatus('✅ Connecté (LiveKit)', '#2ecc71');

    // Keepalive session PHP
    setInterval(() => {
        fetch('../verifConnex2.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'nb=0' }).catch(()=>{});
    }, 240000);
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('chat-input').addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendChat(); }
    });
});

window.addEventListener('load', () => {
    init().catch(e => { console.error('LiveKit init:', e); setStatus('⚠️ Erreur : '+e.message, '#e74c3c'); });
});

window.addEventListener('beforeunload', () => {
    if (_lkRoom) _lkRoom.disconnect();
});
</script>
</body>
</html>
