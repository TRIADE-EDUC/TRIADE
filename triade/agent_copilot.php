<?php
session_start();
if (empty($_SESSION["nom"]) && empty($_SESSION["membre"])) {
    header("Location: ./index.php");
    exit;
}
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2024
 *   copyright            : (C) 2000 E. TAESCH -
 *   Site                 : http://www.triade-educ.org
 *
 ***************************************************************************/
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<?php
$productID = '';
$iakey     = '';
if (file_exists("./common/config-ia.php")) {
    include_once("common/productId.php");
    include_once("common/config-ia.php");
    $productID = PRODUCTID;
    $iakey     = IAKEY;
}
$iaActif = ($productID !== '' && $iakey !== '');
$attrDis = $iaActif ? '' : 'disabled title="Compte IA non activé — contactez votre administrateur"';

// Config agents (admin peut activer/désactiver chaque agent)
$agentEduxpert  = true;
$agentDirection = true;
$agentVeille    = true;
$agentCreation  = true;
if (file_exists("./common/config-ia-agents.php")) {
    include_once("./common/config-ia-agents.php");
    if (defined('IA_AGENT_EDUXPERT'))  $agentEduxpert  = (IA_AGENT_EDUXPERT  === 'oui');
    if (defined('IA_AGENT_DIRECTION')) $agentDirection = (IA_AGENT_DIRECTION === 'oui');
    if (defined('IA_AGENT_VEILLE'))    $agentVeille    = (IA_AGENT_VEILLE    === 'oui');
    if (defined('IA_AGENT_CREATION'))  $agentCreation  = (IA_AGENT_CREATION  === 'oui');
}

$lienVeille = $iaActif
    ? "lancerVeille('$productID','$iakey','veille_result')"
    : "alert('Votre Triade n\\'est pas configuré pour utiliser l\\'IA.')";
?>
<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="CacheControl" content="no-cache" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="expires" content=-1 />
<meta name="Copyright" content="Triade©, 2001" />
<link rel="shortcut icon" href="./favicon.ico" type="image/icon" />
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script type="text/javascript" src="./librairie_js/lib_defil.js"></script>
<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
<script type="text/javascript" src="./librairie_js/function.js"></script>
<script type="text/javascript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/ajaxIA.js"></script>
<title>Agent - TRIADE-COPILOT</title>
<style>
.cop-tabs       { display:flex; gap:4px; margin-bottom:14px; }
.cop-tab        { padding:6px 18px; border:1px solid #c8cfe8; border-radius:6px 6px 0 0;
                  background:#f5f7ff; font-size:12px; font-weight:600; color:#555; cursor:pointer; }
.cop-tab.active { background:#080A66; color:#fff; border-color:#080A66; }
.cop-panel      { display:none; }
.cop-panel.active { display:block; }

.ag-card        { border:1px solid #dde3f5; border-radius:8px; padding:14px 16px;
                  margin-bottom:12px; background:#fff; }
.ag-card-head   { display:flex; align-items:center; gap:8px; margin-bottom:8px; }
.ag-card-icon   { font-size:20px; }
.ag-card-title  { font-weight:700; font-size:13px; color:#080A66; }
.ag-card-desc   { font-size:11px; color:#666; margin-bottom:10px; }
.ag-setup-row   { display:flex; align-items:center; gap:8px; margin-bottom:8px; font-size:11px; color:#555; }
.ag-card-disabled { opacity:.55; pointer-events:none; }
.ag-disabled-msg  { font-size:11px; color:#c0392b; margin-top:4px; }
</style>
<script>
window.alert = function(msg) { if (msg) alertify.error(msg); };

var _iaActif = <?php echo $iaActif ? 'true' : 'false'; ?>;

/* ── Onglets ── */
function copTab(name) {
    if (!document.getElementById('tab_' + name)) name = 'disponibles';
    document.querySelectorAll('.cop-tab').forEach(function(t) { t.classList.remove('active'); });
    document.querySelectorAll('.cop-panel').forEach(function(p) { p.classList.remove('active'); });
    document.getElementById('tab_' + name).classList.add('active');
    document.getElementById('panel_' + name).classList.add('active');
    localStorage.setItem('copilot_tab', name);
}

/* ── Agent Journal Officiel ── */
function lancerVeille(productID, ia, retour) {
    var sujet = document.getElementById('veille_sujet').value.trim();
    if (sujet === '') { alertify.error('Veuillez saisir un sujet de recherche.'); return; }
    ajaxVeille(sujet, productID, ia, retour);
}

/* ── Agent Stratégie Direction (ID fixe) ── */
function lancerAnalyseDirection(productID, ia) {
    var texte = document.getElementById('direction_texte').value.trim();
    var retour = 'direction_result';
    var btn   = document.getElementById('btn_direction');
    if (texte.length < 10) { alertify.error('Veuillez coller un texte à analyser.'); return; }
    document.getElementById(retour).innerHTML = "<img src='image/commun/3pp.gif' width='40' />";
    if (btn) btn.disabled = true;
    var requete = new XMLHttpRequest();
    var corps = 'message='     + encodeURIComponent(texte)
              + '&product_id=' + encodeURIComponent(productID)
              + '&key='        + encodeURIComponent(ia);
    requete.open('POST', 'proxy-ia.php?e=agent-direction.php', true);
    requete.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    requete.onload = function() {
        if (btn) btn.disabled = false;
        if (requete.status == 200) {
            document.getElementById(retour).innerHTML = requete.responseText;
        } else if (requete.status == 402) {
            try { var e = JSON.parse(requete.responseText); alertify.error(e.erreur || 'Plus de tokens.'); }
            catch(x) { alertify.error('Plus de tokens disponibles.'); }
            document.getElementById(retour).innerHTML = '';
        } else {
            alertify.error('Erreur serveur : ' + requete.status);
            document.getElementById(retour).innerHTML = '';
        }
    };
    requete.send(corps);
}

/* ── Création d'agent personnalisé ── */
function creerAgent() {
    var nom          = document.getElementById('agent_nom').value.trim();
    var description  = document.getElementById('agent_description').value.trim();
    var instructions = document.getElementById('agent_instructions').value.trim();

    if (nom === '')          { alertify.error('Veuillez saisir un nom pour l\'agent.'); return; }
    if (instructions === '') { alertify.error('Veuillez saisir des instructions pour l\'agent.'); return; }

    document.getElementById('agent_result').innerHTML = '<em>Création en cours...</em>';
    document.getElementById('btn_creer').disabled = true;

    var requete = getRequete();
    var params  = 'nom='          + encodeURIComponent(nom)
                + '&description=' + encodeURIComponent(description)
                + '&instructions='+ encodeURIComponent(instructions)
                + '&product_id='  + encodeURIComponent('<?php print $productID; ?>')
                + '&key='         + encodeURIComponent('<?php print $iakey; ?>')
                + '&membre='      + encodeURIComponent('<?php print addslashes($_SESSION["membre"]); ?>');

    if (requete != null) {
        requete.onreadystatechange = function() {
            if (requete.readyState == 4) {
                document.getElementById('btn_creer').disabled = false;
                if (requete.status == 200) {
                    document.getElementById('agent_result').innerHTML = requete.responseText;
                    document.getElementById('agent_nom').value = '';
                    document.getElementById('agent_description').value = '';
                    document.getElementById('agent_instructions').value = '';
                    ajaxAgentList('<?php print $productID; ?>', '<?php print $iakey; ?>', 'agent_list', true);
                } else if (requete.status == 402) {
                    try { var err = JSON.parse(requete.responseText); alertify.error(err.erreur || 'Plus de tokens disponibles.'); }
                    catch(e) { alertify.error('Plus de tokens disponibles.'); }
                    document.getElementById('agent_result').innerHTML = '';
                } else {
                    alertify.error('Erreur serveur : ' + requete.status);
                    document.getElementById('agent_result').innerHTML = '';
                }
            }
        };
        requete.open('POST', 'proxy-ia.php?e=agent-create.php', true);
        requete.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        requete.send(params);
    }
}

/* ── Chat agent ── */
var _chatAgentId  = '';
var _chatAgentNom = '';

function ouvrirChat(agentId, agentNom) {
    _chatAgentId  = agentId;
    _chatAgentNom = agentNom;
    document.getElementById('agent_chat_nom').textContent = agentNom;
    document.getElementById('agent_chat_history').innerHTML = '';
    document.getElementById('agent_chat_result').innerHTML  = '';
    document.getElementById('agent_chat_input').value       = '';
    document.getElementById('agent_chat_zone').style.display = 'block';
    document.getElementById('agent_chat_input').focus();
    document.getElementById('agent_chat_zone').scrollIntoView({behavior:'smooth', block:'start'});
}

function fermerChat() {
    document.getElementById('agent_chat_zone').style.display = 'none';
    _chatAgentId = '';
}

function supprimerAgent(agentId, agentNom) {
    if (!confirm('Supprimer l\'agent "' + agentNom + '" ? Cette action est irréversible.')) return;
    var req = new XMLHttpRequest();
    req.open('POST', 'proxy-ia.php?e=agent-delete.php', true);
    req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    req.onload = function() {
        if (req.status === 200) {
            alertify.success('Agent "' + agentNom + '" supprimé.');
            ajaxAgentList('<?php print $productID; ?>', '<?php print $iakey; ?>', 'agent_list', true);
        } else {
            try { var e = JSON.parse(req.responseText); alertify.error(e.erreur || 'Erreur suppression.'); }
            catch(x) { alertify.error('Erreur lors de la suppression.'); }
        }
    };
    req.send('agent_id=' + encodeURIComponent(agentId)
           + '&product_id=' + encodeURIComponent('<?php print $productID; ?>')
           + '&key=' + encodeURIComponent('<?php print $iakey; ?>'));
}

function _agentChatSend() {
    if (!_chatAgentId) return;
    ajaxRunAgent(_chatAgentId, _chatAgentNom, '<?php print $productID; ?>', '<?php print $iakey; ?>');
}

window.addEventListener('DOMContentLoaded', function() {
    /* Restaurer l'onglet actif */
    var lastTab = localStorage.getItem('copilot_tab') || 'disponibles';
    copTab(lastTab);

    /* Afficher les agents pré-configurés si présents */
    var sid = localStorage.getItem('triade_veille_agent_id');
    if (sid) {
        var el = document.getElementById('veille_agent_status');
        if (el) el.innerHTML = "&#10003; Agent initialisé : <code style='background:#e8e8e8;padding:1px 5px;border-radius:3px;font-size:10px;'>" + sid + "</code>";
    }

    <?php if ($iaActif): ?>
    ajaxAgentList('<?php print $productID; ?>', '<?php print $iakey; ?>', 'agent_list', true);
    <?php endif; ?>

    /* Si arrivée depuis le dashboard avec un agent pré-sélectionné */
    var dashId  = localStorage.getItem('dash_agent_id');
    var dashNom = localStorage.getItem('dash_agent_nom');
    if (dashId && dashNom) {
        localStorage.removeItem('dash_agent_id');
        localStorage.removeItem('dash_agent_nom');
        copTab('mes_agents');
        setTimeout(function() { ouvrirChat(dashId, dashNom); }, 300);
    }
});
</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include_once("./librairie_php/lib_licence.php"); ?>
<SCRIPT type="text/javascript" <?php print "src='librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include_once("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT type="text/javascript" <?php print "src='librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="750">
<tr id='coulBar0'>
<td height="2"><b><font id='menumodule1'>Agent - TRIADE-COPILOT &nbsp;—&nbsp; <i>Gestion des agents IA</i></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign='top'>

<!-- ── Onglets ── -->
<div class="cop-tabs">
  <div class="cop-tab" id="tab_disponibles" onclick="copTab('disponibles')">&#129302; Agents disponibles</div>
  <?php if ($agentCreation): ?>
  <div class="cop-tab" id="tab_mes_agents"  onclick="copTab('mes_agents')">&#9998; Mes agents</div>
  <?php endif; ?>
</div>

<!-- ══════════════════════════════════════════════
     PANEL 1 — Agents disponibles (pré-configurés)
     ══════════════════════════════════════════════ -->
<div class="cop-panel" id="panel_disponibles">

  <?php if ($agentVeille): ?>
  <!-- Agent : Journal Officiel Éducation Nationale -->
  <div class="ag-card">
    <div class="ag-card-head">
      <span class="ag-card-icon">&#128269;</span>
      <span class="ag-card-title">Journal Officiel — Éducation Nationale</span>
    </div>
    <div class="ag-card-desc">
      Interroge les sites officiels : eduscol.education.fr, education.gouv.fr,
      légifrance.gouv.fr, bulletin-officiel.education.fr, onisep.fr…
    </div>

    <div class="ag-setup-row">
      <button type="button" class="gc-btn-outline" style="font-size:11px;padding:2px 10px;" <?php echo $attrDis; ?>
              onclick="ajaxSetupVeilleAgent('<?php print $productID; ?>','<?php print $iakey; ?>','veille_setup_result')">
        &#9881; Initialiser l'agent
      </button>
      <span id="veille_agent_status" style="color:#1a7f4b;"></span>
      <span id="veille_setup_result"></span>
    </div>

    <div class="gc-field-col" style="gap:8px;">
      <div class="gc-field-row">
        <input type="text" id="veille_sujet" maxlength="200" class="gc-input"
               placeholder="Ex : nouveautés programme lycée <?php echo date('Y'); ?>, réforme baccalauréat…">
        <button type="button" id="btn_veille" class="gc-btn" <?php echo $attrDis; ?>
                onclick="<?php print $lienVeille; ?>">
          Lancer
        </button>
      </div>
    </div>
    <div id="veille_result" style="margin-top:10px;"></div>
  </div>
  <?php else: ?>
  <div class="ag-card ag-card-disabled">
    <div class="ag-card-head">
      <span class="ag-card-icon">&#128269;</span>
      <span class="ag-card-title">Journal Officiel — Éducation Nationale</span>
    </div>
    <div class="ag-disabled-msg">&#128274; Option non activée par votre administrateur Triade.</div>
  </div>
  <?php endif; ?>

  <?php if ($agentEduxpert): ?>
  <!-- Agent : Eduxpert -->
  <div class="ag-card">
    <div class="ag-card-head">
      <span class="ag-card-icon">&#127891;</span>
      <span class="ag-card-title">Eduxpert</span>
    </div>
    <div class="ag-card-desc">
      Veille documentaire et informationnelle sur les actualités de l'éducation nationale.
    </div>
    <div class="gc-field-col" style="gap:8px;">
      <div class="gc-field-row">
        <input type="text" id="eduxpert_question" maxlength="300" class="gc-input"
               placeholder="Ex : réforme du bac, nouvelles instructions pédagogiques…">
        <button type="button" id="btn_eduxpert" class="gc-btn" <?php echo $attrDis; ?>
                onclick="ajaxEduxpert(document.getElementById('eduxpert_question').value,'<?php print $productID; ?>','<?php print $iakey; ?>','eduxpert_result')">
          Lancer
        </button>
      </div>
    </div>
    <div id="eduxpert_result" style="margin-top:10px;font-size:12px;line-height:1.6;"></div>
  </div>
  <?php else: ?>
  <div class="ag-card ag-card-disabled">
    <div class="ag-card-head">
      <span class="ag-card-icon">&#127891;</span>
      <span class="ag-card-title">Eduxpert</span>
    </div>
    <div class="ag-disabled-msg">&#128274; Option non activée par votre administrateur Triade.</div>
  </div>
  <?php endif; ?>

  <?php if ($agentDirection): ?>
  <!-- Agent : Stratégique — Direction -->
  <div class="ag-card">
    <div class="ag-card-head">
      <span class="ag-card-icon">&#128203;</span>
      <span class="ag-card-title">Stratégique — Direction</span>
    </div>
    <div class="ag-card-desc">
      Analyse un texte brut (BO, Eduscol, Légifrance…) et produit une note de synthèse
      structurée : résumé exécutif, impacts concrets, plan d'action et points de vigilance.
    </div>

    <div class="gc-field-col" style="gap:8px;">
      <textarea id="direction_texte" class="gc-input" rows="5"
                placeholder="Collez ici un texte officiel (article BO, page Eduscol, extrait Légifrance…)"></textarea>
      <button type="button" id="btn_direction" class="gc-btn" <?php echo $attrDis; ?>
              onclick="lancerAnalyseDirection('<?php print $productID; ?>','<?php print $iakey; ?>')">
        &#128202; Analyser
      </button>
    </div>
    <div id="direction_result" style="margin-top:10px;font-size:12px;line-height:1.6;"></div>
  </div>
  <?php else: ?>
  <div class="ag-card ag-card-disabled">
    <div class="ag-card-head">
      <span class="ag-card-icon">&#128203;</span>
      <span class="ag-card-title">Stratégique — Direction</span>
    </div>
    <div class="ag-disabled-msg">&#128274; Option non activée par votre administrateur Triade.</div>
  </div>
  <?php endif; ?>

</div>

<!-- ══════════════════════════════════════════════
     PANEL 2 — Mes agents (création + liste)
     ══════════════════════════════════════════════ -->
<?php if ($agentCreation): ?>
<div class="cop-panel" id="panel_mes_agents">

  <div class="gc-card">
    <div class="gc-card-title"><span class="gc-card-icon">&#9998;</span> Créer un agent personnalisé</div>

    <div class="gc-field-col" style="gap:10px;">

      <div class="gc-field-col">
        <label class="gc-label">Nom de l'agent</label>
        <input type="text" id="agent_nom" maxlength="80" class="gc-input"
               placeholder="Ex : Agent Admissions 2025">
      </div>

      <div class="gc-field-col">
        <label class="gc-label">Description <span style="font-weight:400;color:#888">(optionnel)</span></label>
        <input type="text" id="agent_description" maxlength="200" class="gc-input"
               placeholder="Rôle résumé de l'agent">
      </div>

      <div class="gc-field-col">
        <label class="gc-label">Instructions</label>
        <textarea id="agent_instructions" rows="6" class="gc-textarea"
                  placeholder="Décrivez précisément le comportement et les règles de l'agent."></textarea>
      </div>

      <div class="gc-field-row" style="margin-top:4px;">
        <button type="button" id="btn_creer" class="gc-btn" onclick="creerAgent()" <?php echo $attrDis; ?>>Enregistrer</button>
        <button type="button" class="gc-btn-outline" onclick="open('besoin_daide.php','_self','')">Retour</button>
      </div>

    </div>

    <div id="agent_result"></div>

    <div style="margin-top:16px;">
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
        <span style="font-weight:600;font-size:13px;color:#333;">Mes agents créés</span>
        <button type="button" class="gc-btn-outline" style="padding:2px 10px;font-size:11px;"
                onclick="ajaxAgentList('<?php print $productID; ?>','<?php print $iakey; ?>','agent_list')">
          &#8635; Actualiser
        </button>
      </div>
      <div id="agent_list"><em style="font-size:12px;color:#aaa;">Chargement...</em></div>
    </div>
  </div>

  <!-- Zone de chat agent -->
  <div id="agent_chat_zone" style="display:none;" class="gc-card">
    <div class="gc-card-title">
      <span class="gc-card-icon">&#128172;</span>
      <span id="agent_chat_nom">Agent</span>
      <button onclick="fermerChat()" style="margin-left:auto;background:none;border:none;font-size:16px;cursor:pointer;color:#888;" title="Fermer">&#10005;</button>
    </div>
    <div id="agent_chat_history"
         style="min-height:120px;max-height:300px;overflow-y:auto;padding:6px;background:#fafbff;border:1px solid #e8eaf5;border-radius:6px;margin-bottom:8px;"></div>
    <div id="agent_chat_result"></div>
    <div class="gc-field-row" style="gap:6px;">
      <input type="text" id="agent_chat_input" class="gc-input"
             placeholder="Posez votre question à cet agent…"
             onkeydown="if(event.key==='Enter') document.getElementById('btn_agent_send').click()">
      <button type="button" id="btn_agent_send" class="gc-btn" style="white-space:nowrap;"
              onclick="_agentChatSend()">Envoyer</button>
    </div>
  </div>

</div>
<?php endif; ?>

</td></tr></table>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    print "<SCRIPT type='text/javascript' src='librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
} else {
    print "<SCRIPT type='text/javascript' src='librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT type='text/javascript' src='librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
}
?>
</BODY></HTML>
