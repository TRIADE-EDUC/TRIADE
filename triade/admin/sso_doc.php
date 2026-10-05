<?php
session_start();
error_reporting(0);
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");
include_once("../librairie_php/timezone.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade — Documentation SSO JWT</title>
<style>
/* ── Reset scope ────────────────────────────────── */
.doc-wrap * { box-sizing:border-box; }
.doc-wrap { font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; font-size:12.5px; color:#1f2937; display:block; }
.doc-content { padding:4px 0 24px; }

/* ── Breadcrumb ─────────────────────────────────── */
.doc-bc { display:flex; align-items:center; gap:6px; font-size:11px; color:#6b7280; margin-bottom:16px; flex-wrap:wrap; }
.doc-bc a { color:#4a9eff; text-decoration:none; }
.doc-bc a:hover { text-decoration:underline; }
.doc-bc .sep { color:#d1d5db; }

/* ── Page title ─────────────────────────────────── */
.doc-title { font-size:17px; font-weight:700; color:#0f2340; margin:0 0 4px; display:flex; align-items:center; gap:10px; }
.doc-title i { color:#4a9eff; font-size:16px; }
.doc-subtitle { font-size:12px; color:#6b7280; margin:0 0 18px; }

/* ── Section headings ───────────────────────────── */
.doc-section { display:flex; align-items:center; gap:8px; font-size:12.5px; font-weight:700; color:#0f2340; margin:20px 0 8px; padding-bottom:5px; border-bottom:2px solid #e5e9f0; }
.doc-section i { color:#4a9eff; font-size:12px; }
.doc-section:first-child { margin-top:0; }

/* ── Endpoint box ───────────────────────────────── */
.ep-box { background:#0f1e33; border-radius:6px; padding:11px 14px; margin-bottom:16px; display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.ep-method { font-size:10px; font-weight:800; letter-spacing:.8px; padding:3px 9px; border-radius:4px; flex-shrink:0; }
.ep-method.get  { background:#16a34a; color:#fff; }
.ep-method.post { background:#2563eb; color:#fff; }
.ep-url { font-family:'Courier New',Courier,monospace; font-size:12px; color:#7ec8ff; letter-spacing:.3px; }
.ep-note { font-size:10.5px; color:#6b8aaa; margin-left:auto; }

/* ── Code blocks ────────────────────────────────── */
.code-block { position:relative; margin:6px 0 14px; border-radius:6px; overflow:hidden; }
.code-lang { position:absolute; top:0; right:0; background:#4a9eff; color:#fff; font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; padding:3px 9px; border-radius:0 6px 0 5px; z-index:1; }
pre { background:#0f1e33; color:#c9d8ee; padding:13px 14px; font-size:11px; line-height:1.75; overflow-x:auto; white-space:pre-wrap; margin:0; font-family:'Courier New',Courier,monospace; }
code { background:#e8eef7; color:#1a3358; padding:1px 5px; border-radius:3px; font-size:11px; font-family:'Courier New',Courier,monospace; }
pre code { background:none; color:inherit; padding:0; }

/* ── Alert boxes ────────────────────────────────── */
.doc-info { background:#eff6ff; border-left:4px solid #3b82f6; padding:10px 13px; margin:10px 0; font-size:12px; border-radius:0 5px 5px 0; display:flex; gap:9px; }
.doc-note { background:#fffbeb; border-left:4px solid #f59e0b; padding:10px 13px; margin:10px 0; font-size:12px; border-radius:0 5px 5px 0; display:flex; gap:9px; }
.doc-warn { background:#fef2f2; border-left:4px solid #ef4444; padding:10px 13px; margin:10px 0; font-size:12px; border-radius:0 5px 5px 0; display:flex; gap:9px; }
.doc-info i { color:#3b82f6; flex-shrink:0; margin-top:1px; }
.doc-note i { color:#f59e0b; flex-shrink:0; margin-top:1px; }
.doc-warn i { color:#ef4444; flex-shrink:0; margin-top:1px; }

/* ── Tables ─────────────────────────────────────── */
table.comp { border-collapse:collapse; width:100%; font-size:11.5px; margin:6px 0 14px; border-radius:6px; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.08); }
table.comp thead th { background:#0f2340; color:#c9d8ee; padding:8px 11px; text-align:left; font-weight:600; font-size:11px; letter-spacing:.3px; }
table.comp tbody td { padding:7px 11px; border-bottom:1px solid #edf0f6; }
table.comp tbody tr:last-child td { border-bottom:none; }
table.comp tbody tr:hover td { background:#f7faff; }
.tag { display:inline-block; padding:2px 8px; border-radius:12px; font-size:10px; font-weight:700; margin:1px; }
.tag-blue  { background:#dbeafe; color:#1d4ed8; }
.tag-green { background:#d1fae5; color:#065f46; }
.tag-red   { background:#fee2e2; color:#b91c1c; }
.tag-gray  { background:#f3f4f6; color:#4b5563; }

/* ── Flow steps ─────────────────────────────────── */
.flow { margin:10px 0 14px; padding:0; list-style:none; display:flex; flex-direction:column; gap:0; }
.flow-item { display:flex; gap:10px; position:relative; }
.flow-item:not(:last-child)::before { content:''; position:absolute; left:13px; top:26px; bottom:0; width:2px; background:#dde5f0; }
.flow-left { display:flex; flex-direction:column; align-items:center; flex-shrink:0; }
.flow-num { width:26px; height:26px; background:#4a9eff; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; flex-shrink:0; z-index:1; }
.flow-body { flex:1; padding:4px 0 14px; }
.flow-actor { font-size:10px; font-weight:700; color:#4a9eff; text-transform:uppercase; letter-spacing:.5px; margin-bottom:3px; }
.flow-detail { font-size:11.5px; color:#374151; line-height:1.5; }
.flow-code { display:block; background:#f1f5f9; border:1px solid #e2e8f0; border-radius:4px; padding:5px 9px; margin-top:5px; font-family:'Courier New',monospace; font-size:10.5px; color:#1e3a5f; line-height:1.5; }

/* ── Index cards ─────────────────────────────────── */
.doc-cards { display:grid; grid-template-columns:repeat(2,1fr); gap:10px; margin:14px 0; }
.doc-card { display:flex; gap:12px; align-items:flex-start; background:#fff; border:1px solid #e5e9f0; border-radius:8px; padding:13px; text-decoration:none; color:inherit; transition:all .18s; }
.doc-card:hover { border-color:#4a9eff; box-shadow:0 3px 12px rgba(74,158,255,.15); transform:translateY(-1px); }
.doc-card-icon { width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:15px; flex-shrink:0; }
.doc-card-title { font-size:12px; font-weight:700; color:#0f2340; margin-bottom:3px; }
.doc-card-desc  { font-size:11px; color:#6b7280; line-height:1.4; }

ul.doc-list { padding-left:18px; margin:6px 0 12px; }
ul.doc-list li { margin-bottom:5px; line-height:1.5; }
.text-muted { color:#6b7280; }
</style>
</head>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" height="85" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Documentation SSO JWT</font></b></td></tr>
<tr id="cadreCentral0"><td><p align="left"><font color="#000000">
<?php

$meta = [
    'presentation' => ['icon'=>'fa-solid fa-id-badge',        'color'=>'#dbeafe','ic'=>'#1d4ed8', 'title'=>'Présentation',          'desc'=>'Principe, avantages, structure des fichiers'],
    'installation' => ['icon'=>'fa-solid fa-screwdriver-wrench','color'=>'#d1fae5','ic'=>'#065f46', 'title'=>'Installation',          'desc'=>'Table DB, config.php, vérification'],
    'flux'         => ['icon'=>'fa-solid fa-diagram-project',  'color'=>'#ede9fe','ic'=>'#6d28d9', 'title'=>'Flux d\'authentification','desc'=>'Séquence complète du SSO JWT'],
    'login'        => ['icon'=>'fa-solid fa-right-to-bracket', 'color'=>'#dcfce7','ic'=>'#15803d', 'title'=>'login.php',              'desc'=>'Endpoint d\'authentification et émission du token'],
    'validate'     => ['icon'=>'fa-solid fa-circle-check',     'color'=>'#cffafe','ic'=>'#0e7490', 'title'=>'validate.php',           'desc'=>'Validation du token JWT par l\'app externe'],
    'logout'       => ['icon'=>'fa-solid fa-right-from-bracket','color'=>'#fee2e2','ic'=>'#b91c1c', 'title'=>'logout.php',             'desc'=>'Révocation du token et déconnexion'],
    'securite'     => ['icon'=>'fa-solid fa-shield-halved',    'color'=>'#fde68a','ic'=>'#92400e', 'title'=>'Sécurité',               'desc'=>'Protections et recommandations production'],
    'integration'  => ['icon'=>'fa-solid fa-code',             'color'=>'#f3f4f6','ic'=>'#374151', 'title'=>'Intégration PHP',        'desc'=>'Exemple complet côté application externe'],
];

$chapitre = $_GET['chapitre'] ?? '';

/* ═══════════════════════════════════════════════════════════ INDEX */
if (!$chapitre || !isset($meta[$chapitre])): ?>

<div class="doc-wrap">
  <div style="text-align:left;padding:4px 0 18px;">
    <div style="font-size:20px;font-weight:800;color:#0f2340;display:flex;align-items:center;gap:10px;">
      <i class="fa-solid fa-key" style="color:#4a9eff;"></i>
      Documentation SSO JWT — Triade
    </div>
    <div style="font-size:12.5px;color:#6b7280;margin-top:4px;">
      Single Sign-On par JSON Web Token HS256 &mdash; authentification déléguée
    </div>
  </div>

  <div class="doc-cards">
    <?php foreach ($meta as $key => $m): ?>
    <a href="sso_doc.php?chapitre=<?php echo urlencode($key); ?>" class="doc-card">
      <div class="doc-card-icon" style="background:<?php echo $m['color']; ?>;">
        <i class="<?php echo $m['icon']; ?>" style="color:<?php echo $m['ic']; ?>;"></i>
      </div>
      <div>
        <div class="doc-card-title"><?php echo htmlspecialchars($m['title']); ?></div>
        <div class="doc-card-desc"><?php echo htmlspecialchars($m['desc']); ?></div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>

  <div style="margin-top:16px;padding-top:12px;border-top:1px solid #e5e9f0;display:flex;gap:12px;align-items:center;">
    <a href="config_sso.php" style="font-size:12px;color:#4a9eff;text-decoration:none;">
      <i class="fa-solid fa-arrow-left" style="margin-right:4px;"></i>Configuration SSO JWT
    </a>
    <span style="color:#d1d5db;">|</span>
    <a href="oidc_doc.php" style="font-size:12px;color:#6b7280;text-decoration:none;">
      <i class="fa-solid fa-book" style="margin-right:4px;"></i>Doc SSO OIDC
    </a>
  </div>
</div>

<?php else:
  $cur = $meta[$chapitre]; ?>

<div class="doc-wrap">
  <div class="doc-content">

    <div class="doc-bc">
      <a href="sso_doc.php"><i class="fa-solid fa-house" style="font-size:10px;"></i> Documentation SSO JWT</a>
      <span class="sep">/</span>
      <span><?php echo htmlspecialchars($cur['title']); ?></span>
    </div>

    <div class="doc-title">
      <i class="<?php echo $cur['icon']; ?>"></i>
      <?php echo htmlspecialchars($cur['title']); ?>
    </div>
    <div class="doc-subtitle"><?php echo htmlspecialchars($cur['desc']); ?></div>

    <?php switch ($chapitre):

    /* ═══════════════════════════════════════════════ PRÉSENTATION */
    case 'presentation': ?>

    <p>Le SSO JWT de Triade permet à des applications externes de déléguer l'authentification à Triade. L'utilisateur se connecte <strong>une seule fois</strong> sur Triade — les apps partenaires reçoivent un token JWT signé vérifiable sans accès à la base Triade.</p>

    <div class="doc-section"><i class="fa-solid fa-circle-check"></i>Avantages</div>
    <table class="comp">
      <thead><tr><th>Point</th><th>Détail</th></tr></thead>
      <tbody>
        <tr><td>Pas de sync de mots de passe</td><td>Chaque app garde ses sessions, Triade reste le seul référentiel d'identité</td></tr>
        <tr><td>Token auto-porteur</td><td>Vérifiable de façon stateless — signature HS256 + <code>SSO_SECRET</code></td></tr>
        <tr><td>Révocation</td><td>Table <code>tria_sso_tokens</code> avec flags <code>used</code> et <code>revoked</code></td></tr>
        <tr><td>Audit complet</td><td>Logins, échecs, validations et logouts tracés dans <code>tria_history_cmd</code></td></tr>
        <tr><td>Aucune dépendance</td><td>JWT implémenté en PHP pur dans <code>sso/lib/jwt.php</code></td></tr>
      </tbody>
    </table>

    <div class="doc-section"><i class="fa-solid fa-folder-tree"></i>Structure des fichiers</div>
    <div class="code-block"><span class="code-lang">arborescence</span>
<pre>sso/
&#9500;&#9472;&#9472; lib/
&#9474;   &#9492;&#9472;&#9472; jwt.php        &#8212; moteur JWT HS256
&#9500;&#9472;&#9472; config.php         &#8212; cl&#233; secr&#232;te, dur&#233;e, services autoris&#233;s
&#9500;&#9472;&#9472; login.php          &#8212; formulaire login + &#233;mission token
&#9500;&#9472;&#9472; validate.php       &#8212; validation token (appel&#233; par app externe)
&#9492;&#9472;&#9472; logout.php         &#8212; r&#233;vocation token + d&#233;connexion</pre></div>

    <?php break;

    /* ═══════════════════════════════════════════════ INSTALLATION */
    case 'installation': ?>

    <div class="doc-section"><i class="fa-solid fa-database"></i>1. Créer la table de révocation</div>
    <div class="code-block"><span class="code-lang">sql</span>
<pre>CREATE TABLE `tria_sso_tokens` (
  `id`         int(11)      NOT NULL AUTO_INCREMENT,
  `jti`        varchar(64)  NOT NULL,
  `id_pers`    int(11)      NOT NULL DEFAULT 0,
  `service`    varchar(255) NOT NULL DEFAULT '',
  `created_at` datetime     NOT NULL,
  `expires_at` datetime     NOT NULL,
  `revoked`    tinyint(1)   NOT NULL DEFAULT 0,
  `used`       tinyint(1)   NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jti` (`jti`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;</pre></div>

    <div class="doc-note"><i class="fa-solid fa-triangle-exclamation"></i><div>Si la table existe déjà sans la colonne <code>used</code> :<br>
    <code>ALTER TABLE `tria_sso_tokens` ADD COLUMN `used` tinyint(1) NOT NULL DEFAULT 0;</code></div></div>

    <div class="doc-section"><i class="fa-solid fa-gear"></i>2. Modifier sso/config.php</div>
    <ul class="doc-list">
      <li>Remplacer <code>SSO_SECRET</code> par une clé aléatoire d'<strong>au moins 32 caractères</strong></li>
      <li>Passer <code>SSO_FORCE_HTTPS</code> à <code>true</code> en production</li>
      <li>Renseigner <code>$SSO_ALLOWED_SERVICES</code> avec les URLs des applications autorisées</li>
    </ul>

    <div class="doc-section"><i class="fa-solid fa-circle-check"></i>3. Vérification</div>
    <div class="doc-info"><i class="fa-solid fa-circle-info"></i><div>La page <a href="config_sso.php">Config. SSO JWT</a> affiche l'état de la configuration, la clé active et le nombre de tokens en base.</div></div>

    <?php break;

    /* ════════════════════════════════════════════════════ FLUX */
    case 'flux': ?>

    <div class="doc-section"><i class="fa-solid fa-diagram-project"></i>Séquence complète</div>
    <ul class="flow">
      <li class="flow-item">
        <div class="flow-left"><div class="flow-num">1</div></div>
        <div class="flow-body">
          <div class="flow-actor"><i class="fa-solid fa-globe"></i> App externe</div>
          <div class="flow-detail">Redirige le navigateur vers Triade avec l'URL de callback.</div>
          <code class="flow-code">GET /sso/login.php?service=https://app.externe.fr/callback</code>
        </div>
      </li>
      <li class="flow-item">
        <div class="flow-left"><div class="flow-num">2</div></div>
        <div class="flow-body">
          <div class="flow-actor"><i class="fa-solid fa-shield-halved"></i> Triade</div>
          <div class="flow-detail">Session active → token émis immédiatement. Sinon → formulaire de connexion.</div>
        </div>
      </li>
      <li class="flow-item">
        <div class="flow-left"><div class="flow-num">3</div></div>
        <div class="flow-body">
          <div class="flow-actor"><i class="fa-solid fa-user"></i> Utilisateur</div>
          <div class="flow-detail">Saisit ses identifiants Triade.</div>
        </div>
      </li>
      <li class="flow-item">
        <div class="flow-left"><div class="flow-num">4</div></div>
        <div class="flow-body">
          <div class="flow-actor"><i class="fa-solid fa-shield-halved"></i> Triade</div>
          <div class="flow-detail">Vérifie via <code>acces()</code> + blacklist + ip_timeout, génère le JWT signé HS256, l'enregistre dans <code>tria_sso_tokens</code>, puis redirige.</div>
          <code class="flow-code">302 → https://app.externe.fr/callback?token=eyJhbGciOiJIUzI1NiJ9…</code>
        </div>
      </li>
      <li class="flow-item">
        <div class="flow-left"><div class="flow-num">5</div></div>
        <div class="flow-body">
          <div class="flow-actor"><i class="fa-solid fa-server"></i> App externe</div>
          <div class="flow-detail">Reçoit le token dans l'URL, appelle validate.php en back-channel.</div>
          <code class="flow-code">GET /sso/validate.php?token=eyJ…</code>
        </div>
      </li>
      <li class="flow-item">
        <div class="flow-left"><div class="flow-num">6</div></div>
        <div class="flow-body">
          <div class="flow-actor"><i class="fa-solid fa-shield-halved"></i> Triade</div>
          <div class="flow-detail">Vérifie signature + expiration + révocation + usage unique, retourne les infos utilisateur.</div>
        </div>
      </li>
      <li class="flow-item">
        <div class="flow-left"><div class="flow-num">7</div></div>
        <div class="flow-body">
          <div class="flow-actor"><i class="fa-solid fa-server"></i> App externe</div>
          <div class="flow-detail">Crée sa propre session utilisateur avec les données reçues.</div>
        </div>
      </li>
    </ul>

    <?php break;

    /* ════════════════════════════════════════════════ LOGIN */
    case 'login': ?>

    <div class="ep-box">
      <span class="ep-method get">GET</span>
      <span class="ep-url">/sso/login.php?service=[url_callback]</span>
    </div>

    <p style="font-size:12px;color:#374151;margin:0 0 14px;">Affiche le formulaire de connexion Triade ou redirige directement si une session est déjà active. Après authentification, redirige vers <code>service?token=JWT</code>.</p>

    <div class="doc-section"><i class="fa-solid fa-list"></i>Paramètre</div>
    <table class="comp">
      <thead><tr><th>Paramètre</th><th>Statut</th><th>Description</th></tr></thead>
      <tbody>
        <tr><td><code>service</code></td><td><span class="tag tag-green" style="font-size:10px;">Obligatoire</span></td><td>URL de callback de l'app externe — doit figurer dans <code>$SSO_ALLOWED_SERVICES</code></td></tr>
      </tbody>
    </table>

    <div class="doc-section"><i class="fa-solid fa-shield-halved"></i>Protections intégrées</div>
    <ul class="doc-list">
      <li>Jeton CSRF dans le formulaire de login</li>
      <li>Vérification blacklist Triade via <code>verifblacklist()</code></li>
      <li>Délai exponentiel par IP sur chaque échec via <code>ip_timeout()</code></li>
      <li>Redirection HTTPS si <code>SSO_FORCE_HTTPS = true</code></li>
    </ul>

    <div class="doc-section"><i class="fa-solid fa-circle-check"></i>Exemple</div>
    <div class="code-block"><span class="code-lang">url</span>
<pre>https://triade.monecole.fr/sso/login.php?service=https://moodle.monecole.fr/sso_callback.php</pre></div>
    <p class="text-muted" style="font-size:11.5px;margin:0 0 6px;">Résultat après connexion :</p>
    <div class="code-block"><span class="code-lang">redirect</span>
<pre>302 → https://moodle.monecole.fr/sso_callback.php?token=eyJhbGciOiJIUzI1NiJ9…</pre></div>

    <?php break;

    /* ════════════════════════════════════════════ VALIDATE */
    case 'validate': ?>

    <div class="ep-box">
      <span class="ep-method post">POST</span>
      <span class="ep-url">/sso/validate.php</span>
      <span class="ep-note">GET accepté pour rétrocompatibilité</span>
    </div>

    <p style="font-size:12px;color:#374151;margin:0 0 14px;">Valide un token JWT. Appelé par l'application externe après réception du token. La sécurité repose uniquement sur la signature JWT (<code>SSO_SECRET</code>) — aucune clé API requise.</p>

    <div class="doc-section"><i class="fa-solid fa-arrow-up"></i>Méthode recommandée — POST</div>
    <div class="doc-info"><i class="fa-solid fa-circle-info"></i><div>Envoyer le token dans le corps de la requête (hors logs serveur).</div></div>
    <div class="code-block"><span class="code-lang">http</span>
<pre>POST /sso/validate.php
Content-Type: application/x-www-form-urlencoded

token=eyJ…</pre></div>

    <div class="doc-section"><i class="fa-solid fa-circle-check"></i>Réponse succès (200)</div>
    <div class="code-block"><span class="code-lang">json</span>
<pre>{
  "valid":    true,
  "id_pers":  42,
  "nom":      "DUPONT",
  "prenom":   "Marie",
  "type":     "menuprof",
  "sub":      "dupont.marie",
  "exp":      1746007200,
  "service":  "https://app.externe.fr/callback"
}</pre></div>

    <div class="doc-section"><i class="fa-solid fa-circle-xmark"></i>Codes d'erreur</div>
    <table class="comp">
      <thead><tr><th>Code</th><th>Cause</th></tr></thead>
      <tbody>
        <tr><td><code>400</code></td><td>Token manquant dans la requête</td></tr>
        <tr><td><code>401</code></td><td>Token invalide, expiré, inconnu, révoqué ou déjà utilisé</td></tr>
        <tr><td><code>405</code></td><td>Méthode HTTP non autorisée</td></tr>
      </tbody>
    </table>

    <div class="doc-note"><i class="fa-solid fa-triangle-exclamation"></i><div><strong>Token à usage unique</strong> — après une validation réussie, le token est marqué <code>used=1</code>. Tout appel ultérieur avec ce même token retourne <code>401 Token déjà utilisé</code>.</div></div>

    <?php break;

    /* ═════════════════════════════════════════════ LOGOUT */
    case 'logout': ?>

    <div class="ep-box">
      <span class="ep-method get">GET</span>
      <span class="ep-url">/sso/logout.php</span>
    </div>

    <p style="font-size:12px;color:#374151;margin:0 0 14px;">Révoque le token dans <code>tria_sso_tokens</code>, détruit la session Triade et redirige l'utilisateur.</p>

    <div class="doc-section"><i class="fa-solid fa-list"></i>Paramètres</div>
    <table class="comp">
      <thead><tr><th>Paramètre</th><th>Statut</th><th>Description</th></tr></thead>
      <tbody>
        <tr><td><code>token</code></td><td><span class="tag tag-gray" style="font-size:10px;">Optionnel</span></td><td>JWT à révoquer (recommandé)</td></tr>
        <tr><td><code>redirect</code></td><td><span class="tag tag-gray" style="font-size:10px;">Optionnel</span></td><td>URL de redirection après déconnexion</td></tr>
      </tbody>
    </table>

    <div class="doc-section"><i class="fa-solid fa-circle-check"></i>Exemple</div>
    <div class="code-block"><span class="code-lang">url</span>
<pre>GET /sso/logout.php?token=eyJ…&amp;redirect=https://app.externe.fr/bye</pre></div>

    <?php break;

    /* ══════════════════════════════════════════ SÉCURITÉ */
    case 'securite': ?>

    <div class="doc-section"><i class="fa-solid fa-shield-halved"></i>Protections en place</div>
    <table class="comp">
      <thead><tr><th>Protection</th><th>Détail</th></tr></thead>
      <tbody>
        <tr><td><strong>Signature HS256</strong></td><td>Token signé avec <code>SSO_SECRET</code> — toute altération est détectée immédiatement</td></tr>
        <tr><td><strong>Expiration</strong></td><td>Champ <code>exp</code> dans le JWT, durée configurable via <code>SSO_TOKEN_LIFETIME</code></td></tr>
        <tr><td><strong>CSRF</strong></td><td>Jeton aléatoire dans le formulaire de login</td></tr>
        <tr><td><strong>Brute force</strong></td><td><code>ip_timeout()</code> — délai exponentiel par IP sur chaque échec</td></tr>
        <tr><td><strong>Blacklist</strong></td><td><code>verifblacklist()</code> — réutilise la blacklist Triade</td></tr>
        <tr><td><strong>Whitelist services</strong></td><td><code>$SSO_ALLOWED_SERVICES</code> dans <code>sso/config.php</code></td></tr>
        <tr><td><strong>Usage unique</strong></td><td>Flag <code>used</code> dans <code>tria_sso_tokens</code> — token intercepté non rejouable</td></tr>
        <tr><td><strong>validate POST</strong></td><td>Token dans le corps de la requête, absent des logs serveur</td></tr>
        <tr><td><strong>Révocation</strong></td><td>Table <code>tria_sso_tokens</code>, flag <code>revoked</code></td></tr>
        <tr><td><strong>lib/ protégé</strong></td><td><code>.htaccess</code> bloque l'accès HTTP direct à <code>jwt.php</code></td></tr>
        <tr><td><strong>Audit</strong></td><td>Logins, échecs, validations, logouts tracés dans <code>tria_history_cmd</code></td></tr>
      </tbody>
    </table>

    <div class="doc-section"><i class="fa-solid fa-list-check"></i>Checklist production</div>
    <ul class="doc-list">
      <li>Changer <code>SSO_SECRET</code> — ne jamais laisser la valeur par défaut</li>
      <li>Passer <code>SSO_FORCE_HTTPS</code> à <code>true</code></li>
      <li>Restreindre <code>$SSO_ALLOWED_SERVICES</code> à la liste exacte des applications</li>
      <li>Purger régulièrement <code>tria_sso_tokens</code> des tokens expirés</li>
    </ul>

    <?php break;

    /* ══════════════════════════════════════════ INTÉGRATION */
    case 'integration': ?>

    <div class="doc-info"><i class="fa-solid fa-circle-info"></i><div>Exemple complet d'intégration côté application externe PHP.</div></div>

    <div class="doc-section"><i class="fa-solid fa-arrow-right-to-bracket"></i>1. Rediriger vers Triade SSO</div>
    <div class="code-block"><span class="code-lang">php</span>
<pre>&lt;?php
$triade_sso = 'https://triade.monecole.fr/sso/login.php';
$callback   = 'https://monapp.fr/sso_callback.php';
header('Location: ' . $triade_sso . '?service=' . urlencode($callback));
exit;</pre></div>

    <div class="doc-section"><i class="fa-solid fa-arrows-left-right"></i>2. Valider le token (callback)</div>
    <div class="code-block"><span class="code-lang">php</span>
<pre>&lt;?php
// sso_callback.php
$token = $_GET['token'] ?? '';
$url   = 'https://triade.monecole.fr/sso/validate.php';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['token' => $token]));
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if ($data['valid'] === true) {
    session_start();
    $_SESSION['user_nom']    = $data['nom'];
    $_SESSION['user_prenom'] = $data['prenom'];
    $_SESSION['user_type']   = $data['type'];
    $_SESSION['user_id']     = $data['id_pers'];
    header('Location: /dashboard.php');
} else {
    header('Location: /erreur.php?msg=' . urlencode($data['error'] ?? 'inconnu'));
}
exit;</pre></div>

    <div class="doc-section"><i class="fa-solid fa-right-from-bracket"></i>3. Déconnecter l'utilisateur</div>
    <div class="code-block"><span class="code-lang">php</span>
<pre>&lt;?php
$token    = $_SESSION['sso_token'] ?? '';
$redirect = urlencode('https://monapp.fr/bye.php');
header('Location: https://triade.monecole.fr/sso/logout.php?token=' . $token . '&redirect=' . $redirect);
exit;</pre></div>

    <?php break;
    endswitch; ?>

    <div style="margin-top:22px;padding-top:14px;border-top:1px solid #e5e9f0;display:flex;align-items:center;justify-content:space-between;">
      <a href="sso_doc.php" style="font-size:12px;color:#4a9eff;text-decoration:none;">
        <i class="fa-solid fa-arrow-left" style="margin-right:4px;"></i>Index documentation
      </a>
      <a href="config_sso.php" style="font-size:12px;color:#6b7280;text-decoration:none;">
        <i class="fa-solid fa-gear" style="margin-right:4px;"></i>Config SSO JWT
      </a>
    </div>

  </div>
</div>

<?php endif; ?>
</font></p></td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
