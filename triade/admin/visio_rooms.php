<?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>
<HTML>
<HEAD>
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");
include_once("../common/config6.inc.php");
$cnx = cnx();
?>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<title>Triade — Salles Visioconférence</title>
<style>
.salle-card {
    background:#fff; border:1px solid #c5caee; border-radius:8px;
    padding:12px 16px; margin-bottom:10px;
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
}
.salle-code  { font-size:16px; font-weight:bold; letter-spacing:3px; color:#080A66; }
.salle-nb    { font-size:12px; color:#555; margin-top:2px; }
.salle-peers { font-size:11px; color:#888; margin-top:2px; }
.badge-live  { background:#d4edda; color:#155724; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:bold; }
.empty       { color:#999; font-style:italic; text-align:center; padding:20px; font-size:12px; }
.join-box {
    background:#f0f2fa; border:1px solid #c5caee; border-radius:8px;
    padding:12px 14px; margin-bottom:16px;
}
.join-box h3 { font-size:12px; color:#080A66; margin:0 0 8px; font-weight:bold; }
.join-row    { display:flex; gap:8px; }
.join-row input {
    flex:1; padding:7px 10px; border:1px solid #c5caee; border-radius:5px;
    font-size:14px; letter-spacing:3px; text-transform:uppercase; font-weight:bold; color:#080A66;
}
.refresh-info { font-size:11px; color:#999; margin-bottom:12px; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Salles Visioconférence</font></b></td></tr>
<tr id='cadreCentral0'><td>

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
    <div class="toolbar" style="margin-bottom:12px;">
        <a href="visio_create.php" class="btn-primary">+ Créer une salle</a>
        <a href="visio_dashboard.php" class="btn-secondary">&#128202; Tableau de bord</a>
        <span class="refresh-info" id="refresh-info" style="margin-left:auto;">Mise à jour automatique</span>
    </div>

    <!-- Liste des salles -->
    <div id="liste-salles">
        <div class="empty">Chargement…</div>
    </div>

</div>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>

<script>
function rejoindreParCode() {
    var code = document.getElementById('code-input').value.trim().toUpperCase();
    if (code.length < 4) { alert('Entrez un code valide.'); return; }
    window.open('../visio/room.php?code=' + code, '_blank');
}

document.getElementById('code-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') rejoindreParCode();
});

function chargerSalles() {
    fetch('../visio/signal.php', {
        method: 'POST',
        body: new URLSearchParams({ action: 'list', room: '_', my_id: '_' })
    })
    .then(function(r) { return r.json(); })
    .then(function(salles) {
        var div = document.getElementById('liste-salles');
        if (!Array.isArray(salles) || salles.length === 0) {
            div.innerHTML = '<div class="empty">Aucune salle active pour le moment.</div>';
            return;
        }
        var html = '';
        salles.forEach(function(s) {
            var noms = s.participants.join(', ') || '—';
            var live = s.nb > 0 ? '<span class="badge-live">&#9679; LIVE</span>' : '';
            html += '<div class="salle-card">'
                  + '<div>'
                  + '<div class="salle-code">' + s.code + ' ' + live + '</div>'
                  + '<div class="salle-nb">' + s.nb + ' participant' + (s.nb > 1 ? 's' : '') + '</div>'
                  + '<div class="salle-peers">' + noms + '</div>'
                  + '</div>'
                  + '<a href="../visio/room.php?code=' + s.code + '" target="_blank" class="btn-primary">Rejoindre &rarr;</a>'
                  + '</div>';
        });
        div.innerHTML = html;
        document.getElementById('refresh-info').textContent =
            'Mis à jour à ' + new Date().toLocaleTimeString('fr-FR');
    })
    .catch(function() {
        document.getElementById('refresh-info').textContent = 'Erreur de chargement';
    });
}

chargerSalles();
setInterval(chargerSalles, 10000);
</script>
</body>
</HTML>
