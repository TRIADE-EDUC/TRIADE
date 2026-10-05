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
<title>Triade — Documentation SSO OIDC</title>
<style>
/* ── Reset scope ────────────────────────────────── */
.doc-wrap * { box-sizing:border-box; }
.doc-wrap { font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; font-size:12.5px; color:#1f2937; }

/* ── Layout ─────────────────────────────────────── */
.doc-wrap    { display:block; }
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
table.comp .req  { display:inline-block; background:#d1fae5; color:#065f46; font-size:9.5px; font-weight:700; padding:1px 7px; border-radius:10px; }
table.comp .opt  { display:inline-block; background:#f3f4f6; color:#374151; font-size:9.5px; padding:1px 7px; border-radius:10px; }
table.comp .pkce { display:inline-block; background:#fde68a; color:#92400e; font-size:9.5px; font-weight:700; padding:1px 7px; border-radius:10px; }

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

/* ── Comparison table ───────────────────────────── */
table.diff { border-collapse:collapse; width:100%; font-size:11.5px; margin:8px 0 14px; }
table.diff th { background:#0f2340; color:#c9d8ee; padding:8px 11px; font-size:11px; font-weight:600; text-align:left; }
table.diff td { padding:7px 11px; border-bottom:1px solid #edf0f6; }
table.diff tr:nth-child(even) td { background:#f9fafb; }
table.diff td:first-child { color:#374151; font-weight:600; }
table.diff td:nth-child(2) { color:#6b7280; }
table.diff td:nth-child(3) { color:#065f46; font-weight:500; }
table.diff tr:hover td { background:#eff6ff; }

/* ── Misc ────────────────────────────────────────── */
.tag { display:inline-block; padding:2px 8px; border-radius:12px; font-size:10px; font-weight:700; margin:1px; }
.tag-blue  { background:#dbeafe; color:#1d4ed8; }
.tag-green { background:#d1fae5; color:#065f46; }
.tag-gray  { background:#f3f4f6; color:#4b5563; }
.tag-orange{ background:#fde68a; color:#92400e; }
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
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Documentation SSO OIDC</font></b></td></tr>
<tr id="cadreCentral0"><td><p align="left"><font color="#000000">
<?php

$meta = [
    'presentation'       => ['icon'=>'fa-solid fa-id-badge',      'color'=>'#dbeafe','ic'=>'#1d4ed8', 'title'=>'Présentation',              'desc'=>'JWT vs OIDC, architecture, endpoints'],
    'flux_web'           => ['icon'=>'fa-solid fa-globe',          'color'=>'#d1fae5','ic'=>'#065f46', 'title'=>'Flux Web',                   'desc'=>'Authorization Code pour apps serveur'],
    'flux_mobile'        => ['icon'=>'fa-solid fa-mobile-screen',  'color'=>'#fde68a','ic'=>'#92400e', 'title'=>'Flux Mobile (PKCE)',          'desc'=>'Authorization Code + PKCE sans secret'],
    'authorize'          => ['icon'=>'fa-solid fa-right-to-bracket','color'=>'#dcfce7','ic'=>'#15803d', 'title'=>'authorize.php',              'desc'=>'Endpoint d\'autorisation OIDC'],
    'token'              => ['icon'=>'fa-solid fa-key',            'color'=>'#ede9fe','ic'=>'#6d28d9', 'title'=>'token.php',                  'desc'=>'Échange de code contre access_token'],
    'userinfo'           => ['icon'=>'fa-solid fa-user-circle',    'color'=>'#ffedd5','ic'=>'#c2410c', 'title'=>'userinfo.php',               'desc'=>'Claims utilisateur depuis l\'access token'],
    'discovery'          => ['icon'=>'fa-solid fa-magnifying-glass','color'=>'#cffafe','ic'=>'#0e7490', 'title'=>'Discovery & JWKS',           'desc'=>'Auto-configuration et clé publique RSA'],
    'securite'           => ['icon'=>'fa-solid fa-shield-halved',  'color'=>'#fee2e2','ic'=>'#b91c1c', 'title'=>'Sécurité',                   'desc'=>'Protections cryptographiques et anti-attaque'],
    'integration_php'    => ['icon'=>'fa-solid fa-code',           'color'=>'#f3f4f6','ic'=>'#374151', 'title'=>'Intégration PHP',            'desc'=>'App web serveur, callback, session'],
    'integration_mobile' => ['icon'=>'fa-brands fa-react',         'color'=>'#e0f2fe','ic'=>'#0369a1', 'title'=>'Intégration React Native',   'desc'=>'expo-auth-session, PKCE automatique'],
];

$chapitre = $_GET['chapitre'] ?? '';

/* ═══════════════════════════════════════════════════════════ INDEX */
if (!$chapitre || !isset($meta[$chapitre])): ?>

<div class="doc-wrap" style="display:block;">
  <div style="text-align:left;padding:4px 0 18px;">
    <div style="font-size:20px;font-weight:800;color:#0f2340;display:flex;align-items:center;gap:10px;">
      <i class="fa-solid fa-shield-halved" style="color:#4a9eff;"></i>
      Documentation SSO OIDC — Triade
    </div>
    <div style="font-size:12.5px;color:#6b7280;margin-top:4px;">
      OpenID Connect 1.0 sur OAuth 2.0 &mdash; Authorization Code Flow + PKCE
    </div>
  </div>

  <div class="doc-cards">
    <?php foreach ($meta as $key => $m): ?>
    <a href="oidc_doc.php?chapitre=<?php echo urlencode($key); ?>" class="doc-card">
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
    <a href="config_oidc.php" style="font-size:12px;color:#4a9eff;text-decoration:none;">
      <i class="fa-solid fa-arrow-left" style="margin-right:4px;"></i>Configuration OIDC
    </a>
    <span style="color:#d1d5db;">|</span>
    <a href="config_sso.php" style="font-size:12px;color:#6b7280;text-decoration:none;">
      <i class="fa-solid fa-link" style="margin-right:4px;"></i>Config SSO JWT
    </a>
    <span style="color:#d1d5db;">|</span>
    <a href="sso_doc.php" style="font-size:12px;color:#6b7280;text-decoration:none;">
      <i class="fa-solid fa-book" style="margin-right:4px;"></i>Doc SSO JWT
    </a>
  </div>
</div>

<?php else:
  $cur = $meta[$chapitre]; ?>

<div class="doc-wrap">
  <div class="doc-content">

    <!-- Breadcrumb -->
    <div class="doc-bc">
      <a href="oidc_doc.php"><i class="fa-solid fa-house" style="font-size:10px;"></i> Documentation OIDC</a>
      <span class="sep">/</span>
      <span><?php echo htmlspecialchars($cur['title']); ?></span>
    </div>

    <!-- Title -->
    <div class="doc-title">
      <i class="<?php echo $cur['icon']; ?>"></i>
      <?php echo htmlspecialchars($cur['title']); ?>
    </div>
    <div class="doc-subtitle"><?php echo htmlspecialchars($cur['desc']); ?></div>

    <?php switch ($chapitre):

    /* ═══════════════════════════════════════════════════════ PRÉSENTATION */
    case 'presentation': ?>

    <div class="doc-section"><i class="fa-solid fa-scale-balanced"></i>Comparaison SSO JWT vs OIDC</div>
    <table class="diff">
      <thead><tr><th>Critère</th><th>SSO JWT Triade</th><th>SSO OIDC Triade</th></tr></thead>
      <tbody>
        <tr><td>Protocole</td><td>Propriétaire</td><td>Standard OIDC 1.0</td></tr>
        <tr><td>Algorithme</td><td>HS256 — secret partagé</td><td>RS256 — clé privée / publique</td></tr>
        <tr><td>Token dans l'URL</td><td>Oui (<code>token=</code>)</td><td>Non — code court-vécu échangé en back-channel</td></tr>
        <tr><td>Clients mobiles</td><td>Intégration manuelle</td><td>Natif PKCE (expo-auth-session, AppAuth…)</td></tr>
        <tr><td>Interopérabilité</td><td>Triade uniquement</td><td>Tout client OIDC (Moodle, Nextcloud, apps…)</td></tr>
        <tr><td>Discovery auto</td><td>Non</td><td>Oui — <code>/.well-known/openid-configuration</code></td></tr>
        <tr><td>Rotation des clés</td><td>Changer SSO_SECRET</td><td>Rotation RSA sans toucher aux clients</td></tr>
        <tr><td>Rétrocompatibilité</td><td colspan="2">Les deux coexistent — login.php / validate.php inchangés</td></tr>
      </tbody>
    </table>

    <div class="doc-section"><i class="fa-solid fa-folder-tree"></i>Structure des fichiers</div>
    <div class="code-block"><span class="code-lang">arborescence</span>
<pre>sso/
&#9500;&#9472;&#9472; lib/
&#9474;   &#9500;&#9472;&#9472; jwt.php          &#8212; moteur JWT HS256 (SSO historique)
&#9474;   &#9492;&#9472;&#9472; jwt_rsa.php      &#8212; moteur JWT RS256 (OIDC id_token)
&#9500;&#9472;&#9472; keys/               &#8212; acc&#232;s HTTP bloqu&#233; par .htaccess
&#9474;   &#9500;&#9472;&#9472; private.pem      &#8212; cl&#233; priv&#233;e RSA 2048 bits
&#9474;   &#9500;&#9472;&#9472; public.pem       &#8212; cl&#233; publique RSA
&#9474;   &#9492;&#9472;&#9472; kid.txt          &#8212; empreinte cl&#233; (Key ID)
&#9500;&#9472;&#9472; .well-known/
&#9474;   &#9500;&#9472;&#9472; openid-configuration.php
&#9474;   &#9492;&#9472;&#9472; jwks.json.php
&#9500;&#9472;&#9472; config.php          &#8212; SSO JWT + OIDC
&#9500;&#9472;&#9472; authorize.php        &#8212; authorization endpoint
&#9500;&#9472;&#9472; token.php            &#8212; token endpoint
&#9492;&#9472;&#9472; userinfo.php         &#8212; userinfo endpoint</pre></div>

    <div class="doc-section"><i class="fa-solid fa-plug"></i>Endpoints OIDC</div>
    <table class="comp">
      <thead><tr><th>Endpoint</th><th>URL</th></tr></thead>
      <tbody>
        <tr><td><span class="tag tag-blue">Discovery</span></td><td><code>{issuer}/.well-known/openid-configuration</code></td></tr>
        <tr><td><span class="tag tag-blue">JWKS</span></td><td><code>{issuer}/.well-known/jwks.json</code></td></tr>
        <tr><td><span class="tag tag-green">Authorization</span></td><td><code>{issuer}/authorize.php</code></td></tr>
        <tr><td><span class="tag tag-green">Token</span></td><td><code>{issuer}/token.php</code></td></tr>
        <tr><td><span class="tag tag-green">UserInfo</span></td><td><code>{issuer}/userinfo.php</code></td></tr>
      </tbody>
    </table>

    <?php break;

    /* ════════════════════════════════════════════════════════ FLUX WEB */
    case 'flux_web': ?>

    <div class="doc-info"><i class="fa-solid fa-circle-info"></i><div>Flux pour les <strong>applications web serveur</strong> capables de stocker un <code>client_secret</code> de façon sécurisée (Moodle, Nextcloud, portail PHP…).</div></div>

    <div class="doc-section"><i class="fa-solid fa-diagram-project"></i>Séquence Authorization Code</div>
    <ul class="flow">
      <li class="flow-item">
        <div class="flow-left"><div class="flow-num">1</div></div>
        <div class="flow-body">
          <div class="flow-actor"><i class="fa-solid fa-globe"></i> Navigateur</div>
          <div class="flow-detail">Redirige vers Triade avec les paramètres OIDC</div>
          <code class="flow-code">GET {issuer}/authorize.php
  ?response_type=code&amp;client_id=moodle
  &amp;redirect_uri=https://moodle.ecole.fr/auth/oauth2/callback.php
  &amp;scope=openid+profile&amp;state=RAND&amp;nonce=RAND</code>
        </div>
      </li>
      <li class="flow-item">
        <div class="flow-left"><div class="flow-num">2</div></div>
        <div class="flow-body">
          <div class="flow-actor"><i class="fa-solid fa-shield-halved"></i> Triade</div>
          <div class="flow-detail">Session active → code immédiat. Sinon → formulaire login.</div>
        </div>
      </li>
      <li class="flow-item">
        <div class="flow-left"><div class="flow-num">3</div></div>
        <div class="flow-body">
          <div class="flow-actor"><i class="fa-solid fa-user"></i> Utilisateur</div>
          <div class="flow-detail">Saisit identifiants &mdash; vérification via <code>acces()</code>, blacklist, ip_timeout.</div>
        </div>
      </li>
      <li class="flow-item">
        <div class="flow-left"><div class="flow-num">4</div></div>
        <div class="flow-body">
          <div class="flow-actor"><i class="fa-solid fa-shield-halved"></i> Triade</div>
          <div class="flow-detail">Génère authorization_code (64 hex, 10 min, usage unique), redirige.</div>
          <code class="flow-code">302 → https://moodle.ecole.fr/auth/oauth2/callback.php
        ?code=AUTH_CODE&amp;state=RAND</code>
        </div>
      </li>
      <li class="flow-item">
        <div class="flow-left"><div class="flow-num">5</div></div>
        <div class="flow-body">
          <div class="flow-actor"><i class="fa-solid fa-server"></i> Moodle (back-channel)</div>
          <div class="flow-detail">Échange le code contre les tokens — jamais visible dans le navigateur.</div>
          <code class="flow-code">POST {issuer}/token.php
  grant_type=authorization_code&amp;code=AUTH_CODE
  &amp;client_id=moodle&amp;client_secret=SECRET
  &amp;redirect_uri=https://moodle.ecole.fr/auth/oauth2/callback.php</code>
        </div>
      </li>
      <li class="flow-item">
        <div class="flow-left"><div class="flow-num">6</div></div>
        <div class="flow-body">
          <div class="flow-actor"><i class="fa-solid fa-shield-halved"></i> Triade</div>
          <div class="flow-detail">Vérifie code + secret + redirect_uri, émet les tokens.</div>
          <code class="flow-code">{ "access_token":"…", "token_type":"Bearer",
  "expires_in":3600, "id_token":"eyJ…RS256…" }</code>
        </div>
      </li>
      <li class="flow-item">
        <div class="flow-left"><div class="flow-num">7</div></div>
        <div class="flow-body">
          <div class="flow-actor"><i class="fa-solid fa-server"></i> Moodle</div>
          <div class="flow-detail">Appelle UserInfo et crée la session Moodle.</div>
          <code class="flow-code">GET {issuer}/userinfo.php
  Authorization: Bearer ACCESS_TOKEN</code>
        </div>
      </li>
    </ul>

    <div class="doc-note"><i class="fa-solid fa-triangle-exclamation"></i><div>Toujours vérifier le <code>state</code> côté client (anti-CSRF) et le <code>nonce</code> dans l'<code>id_token</code> (anti-replay).</div></div>

    <?php break;

    /* ════════════════════════════════════════════════════ FLUX MOBILE */
    case 'flux_mobile': ?>

    <div class="doc-info"><i class="fa-solid fa-circle-info"></i><div>Les apps mobiles <strong>ne peuvent pas stocker de client_secret</strong>. PKCE (Proof Key for Code Exchange) joue ce rôle en prouvant que l'échangeur du code est bien celui qui l'a demandé.</div></div>

    <div class="doc-section"><i class="fa-solid fa-lock"></i>Préparation PKCE (côté app)</div>
    <div class="code-block"><span class="code-lang">javascript</span>
<pre>// 1. Générer un code_verifier aléatoire (43-128 caractères)
const codeVerifier = generateRandomString(64);

// 2. Calculer le code_challenge (S256)
const codeChallenge = base64url(sha256(codeVerifier));</pre></div>

    <div class="doc-section"><i class="fa-solid fa-diagram-project"></i>Séquence PKCE</div>
    <ul class="flow">
      <li class="flow-item">
        <div class="flow-left"><div class="flow-num">1</div></div>
        <div class="flow-body">
          <div class="flow-actor"><i class="fa-solid fa-mobile-screen"></i> App mobile</div>
          <div class="flow-detail">Ouvre le navigateur système avec code_challenge.</div>
          <code class="flow-code">GET {issuer}/authorize.php
  ?response_type=code&amp;client_id=pronote
  &amp;redirect_uri=pronote://oidc/callback
  &amp;scope=openid+profile&amp;state=RAND&amp;nonce=RAND
  &amp;code_challenge=BASE64URL(SHA256(verifier))
  &amp;code_challenge_method=S256</code>
        </div>
      </li>
      <li class="flow-item">
        <div class="flow-left"><div class="flow-num">2</div></div>
        <div class="flow-body">
          <div class="flow-actor"><i class="fa-solid fa-shield-halved"></i> Triade</div>
          <div class="flow-detail">Affiche le formulaire login (ou utilise la session active).</div>
        </div>
      </li>
      <li class="flow-item">
        <div class="flow-left"><div class="flow-num">3</div></div>
        <div class="flow-body">
          <div class="flow-actor"><i class="fa-solid fa-user"></i> Utilisateur</div>
          <div class="flow-detail">S'authentifie — Triade redirige vers le deep link de l'app.</div>
          <code class="flow-code">pronote://oidc/callback?code=AUTH_CODE&amp;state=RAND</code>
        </div>
      </li>
      <li class="flow-item">
        <div class="flow-left"><div class="flow-num">4</div></div>
        <div class="flow-body">
          <div class="flow-actor"><i class="fa-solid fa-mobile-screen"></i> App mobile</div>
          <div class="flow-detail">Échange le code en envoyant le <code>code_verifier</code> — pas de secret.</div>
          <code class="flow-code">POST {issuer}/token.php
  grant_type=authorization_code&amp;code=AUTH_CODE
  &amp;client_id=pronote&amp;redirect_uri=pronote://oidc/callback
  &amp;code_verifier=VERIFIER</code>
        </div>
      </li>
      <li class="flow-item">
        <div class="flow-left"><div class="flow-num">5</div></div>
        <div class="flow-body">
          <div class="flow-actor"><i class="fa-solid fa-shield-halved"></i> Triade</div>
          <div class="flow-detail">Vérifie <code>sha256(verifier) == code_challenge</code>, émet les tokens.</div>
        </div>
      </li>
    </ul>

    <div class="doc-info"><i class="fa-brands fa-react"></i><div>Avec <code>expo-auth-session</code>, PKCE est entièrement géré automatiquement. Voir <a href="oidc_doc.php?chapitre=integration_mobile">Intégration React Native</a>.</div></div>

    <?php break;

    /* ═══════════════════════════════════════════════════ AUTHORIZE */
    case 'authorize': ?>

    <div class="ep-box">
      <span class="ep-method get">GET</span>
      <span class="ep-url">{issuer}/authorize.php</span>
      <span class="ep-note">Lance le flux d'authentification</span>
    </div>

    <div class="doc-section"><i class="fa-solid fa-list"></i>Paramètres de la requête</div>
    <table class="comp">
      <thead><tr><th>Paramètre</th><th>Statut</th><th>Description</th></tr></thead>
      <tbody>
        <tr><td><code>response_type</code></td><td><span class="req">Obligatoire</span></td><td>Doit être <code>code</code></td></tr>
        <tr><td><code>client_id</code></td><td><span class="req">Obligatoire</span></td><td>Identifiant dans <code>tria_oidc_clients</code></td></tr>
        <tr><td><code>redirect_uri</code></td><td><span class="req">Obligatoire</span></td><td>URL enregistrée — correspondance exacte</td></tr>
        <tr><td><code>scope</code></td><td><span class="req">Obligatoire</span></td><td>Doit contenir <code>openid</code>. Ajouter <code>profile</code> pour le nom</td></tr>
        <tr><td><code>state</code></td><td><span class="opt">Recommandé</span></td><td>Valeur opaque renvoyée dans la redirection — anti-CSRF</td></tr>
        <tr><td><code>nonce</code></td><td><span class="opt">Recommandé</span></td><td>Inclus dans l'<code>id_token</code> — anti-replay</td></tr>
        <tr><td><code>code_challenge</code></td><td><span class="pkce">PKCE</span></td><td><code>base64url(sha256(code_verifier))</code></td></tr>
        <tr><td><code>code_challenge_method</code></td><td><span class="pkce">PKCE</span></td><td><code>S256</code> (recommandé) ou <code>plain</code></td></tr>
      </tbody>
    </table>

    <div class="doc-section"><i class="fa-solid fa-circle-check"></i>Réponse succès</div>
    <div class="code-block"><span class="code-lang">redirect</span>
<pre>302 → {redirect_uri}?code=AUTH_CODE&amp;state=STATE</pre></div>

    <div class="doc-section"><i class="fa-solid fa-circle-xmark"></i>Erreurs</div>
    <table class="comp">
      <thead><tr><th>Code</th><th>Cause</th><th>Mode</th></tr></thead>
      <tbody>
        <tr><td><code>invalid_scope</code></td><td>scope <code>openid</code> absent</td><td>Redirection</td></tr>
        <tr><td><code>invalid_request</code></td><td>PKCE requis mais absent</td><td>Redirection</td></tr>
        <tr><td><code>400</code></td><td><code>response_type</code> invalide ou <code>client_id</code> manquant</td><td>Page HTML</td></tr>
        <tr><td><code>403</code></td><td>Client inconnu ou redirect_uri non enregistrée</td><td>Page HTML</td></tr>
      </tbody>
    </table>

    <div class="doc-info"><i class="fa-solid fa-bolt"></i><div>Session Triade active → code émis immédiatement sans ré-afficher le formulaire. SSO transparent pour l'utilisateur.</div></div>

    <?php break;

    /* ════════════════════════════════════════════════════════ TOKEN */
    case 'token': ?>

    <div class="ep-box">
      <span class="ep-method post">POST</span>
      <span class="ep-url">{issuer}/token.php</span>
      <span class="ep-note">Content-Type: application/x-www-form-urlencoded</span>
    </div>

    <div class="doc-section"><i class="fa-solid fa-list"></i>Paramètres</div>
    <table class="comp">
      <thead><tr><th>Paramètre</th><th>Statut</th><th>Description</th></tr></thead>
      <tbody>
        <tr><td><code>grant_type</code></td><td><span class="req">Obligatoire</span></td><td>Doit être <code>authorization_code</code></td></tr>
        <tr><td><code>code</code></td><td><span class="req">Obligatoire</span></td><td>Code reçu depuis authorize.php (usage unique)</td></tr>
        <tr><td><code>client_id</code></td><td><span class="req">Obligatoire</span></td><td>Identifiant du client</td></tr>
        <tr><td><code>redirect_uri</code></td><td><span class="req">Obligatoire</span></td><td>Identique à la valeur passée à authorize (vérification stricte)</td></tr>
        <tr><td><code>client_secret</code></td><td><span class="opt">Confidentiel</span></td><td>Clients serveur — aussi accepté en HTTP Basic Auth</td></tr>
        <tr><td><code>code_verifier</code></td><td><span class="pkce">PKCE</span></td><td>Verifier original — remplace le client_secret</td></tr>
      </tbody>
    </table>

    <div class="doc-section"><i class="fa-solid fa-circle-check"></i>Réponse succès (200)</div>
    <div class="code-block"><span class="code-lang">json</span>
<pre>{
  "access_token": "a1b2c3d4e5f6…",
  "token_type":   "Bearer",
  "expires_in":   3600,
  "id_token":     "eyJhbGciOiJSUzI1NiIsImtpZCI6Ii4uLiJ9…",
  "scope":        "openid profile"
}</pre></div>

    <div class="doc-section"><i class="fa-solid fa-fingerprint"></i>Claims de l'id_token (RS256)</div>
    <div class="code-block"><span class="code-lang">jwt payload</span>
<pre>{
  "iss":         "https://triade.monecole.fr/sso",
  "sub":         "menuprof_42",
  "aud":         "moodle",
  "iat":         1746000000,
  "exp":         1746003600,
  "auth_time":   1746000000,
  "nonce":       "abc123",
  "name":        "DUPONT Marie",
  "given_name":  "Marie",
  "family_name": "DUPONT",
  "triade_role": "menuprof",
  "triade_id":   42
}</pre></div>

    <div class="doc-section"><i class="fa-solid fa-circle-xmark"></i>Codes d'erreur</div>
    <table class="comp">
      <thead><tr><th>Erreur</th><th>HTTP</th><th>Cause</th></tr></thead>
      <tbody>
        <tr><td><code>invalid_request</code></td><td>400</td><td>Paramètre manquant ou grant_type incorrect</td></tr>
        <tr><td><code>invalid_client</code></td><td>401</td><td>Client inconnu ou client_secret invalide</td></tr>
        <tr><td><code>invalid_grant</code></td><td>400</td><td>Code expiré, déjà utilisé, redirect_uri incorrecte, PKCE invalide</td></tr>
        <tr><td><code>server_error</code></td><td>500</td><td>Clé RSA non disponible</td></tr>
      </tbody>
    </table>

    <div class="doc-note"><i class="fa-solid fa-triangle-exclamation"></i><div>Le code est <strong>à usage unique</strong> — marqué <code>used=1</code> dès le premier échange réussi. Un second appel retourne <code>invalid_grant</code>.</div></div>

    <?php break;

    /* ════════════════════════════════════════════════════ USERINFO */
    case 'userinfo': ?>

    <div class="ep-box">
      <span class="ep-method get">GET</span>
      <span class="ep-url">{issuer}/userinfo.php</span>
      <span class="ep-note">Authorization: Bearer ACCESS_TOKEN</span>
    </div>

    <div class="doc-section"><i class="fa-solid fa-key"></i>Authentification</div>
    <div class="code-block"><span class="code-lang">http</span>
<pre>GET /sso/userinfo.php HTTP/1.1
Authorization: Bearer ACCESS_TOKEN</pre></div>
    <p class="text-muted" style="margin:0 0 8px;font-size:11.5px;">Fallback POST :</p>
    <div class="code-block"><span class="code-lang">http</span>
<pre>POST /sso/userinfo.php
Content-Type: application/x-www-form-urlencoded

access_token=ACCESS_TOKEN</pre></div>

    <div class="doc-section"><i class="fa-solid fa-circle-check"></i>Réponse (scope openid + profile)</div>
    <div class="code-block"><span class="code-lang">json</span>
<pre>{
  "sub":          "menuprof_42",
  "name":         "DUPONT Marie",
  "given_name":   "Marie",
  "family_name":  "DUPONT",
  "triade_role":  "menuprof",
  "triade_id":    42
}</pre></div>

    <div class="doc-section"><i class="fa-solid fa-tags"></i>Claims par scope</div>
    <table class="comp">
      <thead><tr><th>Scope</th><th>Claims retournés</th></tr></thead>
      <tbody>
        <tr><td><span class="tag tag-blue">openid</span></td><td><code>sub</code></td></tr>
        <tr><td><span class="tag tag-green">profile</span></td><td><code>name</code>, <code>given_name</code>, <code>family_name</code>, <code>triade_role</code>, <code>triade_id</code></td></tr>
      </tbody>
    </table>

    <div class="doc-section"><i class="fa-solid fa-users"></i>Valeurs de triade_role</div>
    <table class="comp">
      <thead><tr><th>Valeur</th><th>Profil Triade</th></tr></thead>
      <tbody>
        <tr><td><code>menuadmin</code></td><td>Administrateur</td></tr>
        <tr><td><code>menuprof</code></td><td>Enseignant</td></tr>
        <tr><td><code>menueleve</code></td><td>Élève</td></tr>
        <tr><td><code>menuparent</code></td><td>Parent</td></tr>
        <tr><td><code>menuscolaire</code></td><td>Vie scolaire</td></tr>
        <tr><td><code>menututeur</code></td><td>Tuteur de stage</td></tr>
        <tr><td><code>menupersonnel</code></td><td>Personnel</td></tr>
      </tbody>
    </table>

    <div class="doc-warn"><i class="fa-solid fa-circle-xmark"></i><div><code>invalid_token</code> (401) : token manquant, inconnu, expiré ou révoqué.</div></div>

    <?php break;

    /* ═══════════════════════════════════════════════════ DISCOVERY */
    case 'discovery': ?>

    <div class="doc-info"><i class="fa-solid fa-circle-info"></i><div>Le Discovery Document permet aux clients OIDC de <strong>se configurer automatiquement</strong> — aucune URL à saisir à la main si le client supporte auto-discovery.</div></div>

    <div class="doc-section"><i class="fa-solid fa-file-code"></i>Discovery Document</div>
    <div class="ep-box" style="margin-bottom:8px;">
      <span class="ep-method get">GET</span>
      <span class="ep-url">{issuer}/.well-known/openid-configuration</span>
    </div>
    <div class="code-block"><span class="code-lang">json</span>
<pre>{
  "issuer":                 "https://triade.monecole.fr/sso",
  "authorization_endpoint": "https://triade.monecole.fr/sso/authorize.php",
  "token_endpoint":         "https://triade.monecole.fr/sso/token.php",
  "userinfo_endpoint":      "https://triade.monecole.fr/sso/userinfo.php",
  "jwks_uri":               "https://triade.monecole.fr/sso/.well-known/jwks.json",
  "response_types_supported":              ["code"],
  "id_token_signing_alg_values_supported": ["RS256"],
  "scopes_supported":                      ["openid", "profile", "email"],
  "code_challenge_methods_supported":      ["S256", "plain"]
}</pre></div>

    <div class="doc-section"><i class="fa-solid fa-key"></i>JWKS — JSON Web Key Set</div>
    <div class="ep-box" style="margin-bottom:8px;">
      <span class="ep-method get">GET</span>
      <span class="ep-url">{issuer}/.well-known/jwks.json</span>
    </div>
    <p style="font-size:12px;color:#374151;margin:0 0 8px;">Clé publique RSA permettant aux clients de <strong>vérifier la signature</strong> de l'<code>id_token</code> sans appeler Triade.</p>
    <div class="code-block"><span class="code-lang">json</span>
<pre>{
  "keys": [{
    "kty": "RSA",  "alg": "RS256",  "use": "sig",
    "kid": "a1b2c3d4e5f6a7b8",
    "n":   "0vx7agoebGcQSuuPiLJXZptN…",
    "e":   "AQAB"
  }]
}</pre></div>

    <div class="doc-note"><i class="fa-solid fa-rotate"></i><div>À chaque régénération des clés RSA dans l'admin, le <code>kid</code> change dans <code>config.php</code>. Les clients détectent la rotation via le nouveau <code>kid</code> et rafraîchissent leur cache JWKS.</div></div>

    <div class="doc-section"><i class="fa-solid fa-server"></i>Configuration Apache (.htaccess)</div>
    <div class="code-block"><span class="code-lang">apache</span>
<pre># sso/.well-known/.htaccess
RewriteEngine On
RewriteRule ^openid-configuration$  openid-configuration.php  [L]
RewriteRule ^jwks\.json$            jwks.json.php             [L]</pre></div>

    <?php break;

    /* ═══════════════════════════════════════════════════ SÉCURITÉ */
    case 'securite': ?>

    <div class="doc-section"><i class="fa-solid fa-shield-halved"></i>Protections cryptographiques</div>
    <table class="comp">
      <thead><tr><th>Protection</th><th>Détail</th></tr></thead>
      <tbody>
        <tr><td><strong>RS256 / RSA 2048 bits</strong></td><td>Seul Triade signe l'id_token — n'importe qui vérifie via le JWKS sans secret partagé</td></tr>
        <tr><td><strong>Clé privée protégée</strong></td><td><code>.htaccess Require all denied</code> + <code>chmod 640</code> sur <code>private.pem</code></td></tr>
        <tr><td><strong>Authorization Code</strong></td><td>Le code transite dans l'URL (pas le token), valide 10 min, usage unique</td></tr>
        <tr><td><strong>PKCE S256</strong></td><td>Remplace le client_secret pour les apps publiques — protège contre l'interception du code</td></tr>
      </tbody>
    </table>

    <div class="doc-section"><i class="fa-solid fa-bugs"></i>Protections anti-attaque</div>
    <table class="comp">
      <thead><tr><th>Vecteur</th><th>Protection</th></tr></thead>
      <tbody>
        <tr><td>CSRF</td><td>Paramètre <code>state</code> — valeur aléatoire vérifiée par le client</td></tr>
        <tr><td>Replay</td><td>Paramètre <code>nonce</code> — inclus dans l'id_token, vérifié par le client</td></tr>
        <tr><td>Réutilisation de code</td><td>Code marqué <code>used=1</code> dès le premier échange</td></tr>
        <tr><td>CSRF formulaire login</td><td>Jeton CSRF dans le formulaire Triade OIDC</td></tr>
        <tr><td>Brute force</td><td><code>ip_timeout()</code> — délai exponentiel par IP</td></tr>
        <tr><td>Comptes bannis</td><td><code>verifblacklist()</code> — réutilise la blacklist Triade</td></tr>
      </tbody>
    </table>

    <div class="doc-section"><i class="fa-solid fa-list-check"></i>Checklist production</div>
    <ul class="doc-list">
      <li>Activer <code>SSO_FORCE_HTTPS = true</code> dans <code>sso/config.php</code></li>
      <li>Restreindre <code>redirect_uris</code> à la liste exacte — pas de wildcards</li>
      <li>Imposer PKCE (<code>pkce_required = 1</code>) pour tous les clients mobiles</li>
      <li>Vérifier <code>state</code> et <code>nonce</code> dans chaque client</li>
      <li>Purger les codes expirés : <code>DELETE FROM tria_oidc_auth_codes WHERE expires_at &lt; NOW()</code></li>
      <li>Vérifier que <code>sso/keys/.htaccess</code> est actif avant la mise en production</li>
    </ul>

    <?php break;

    /* ═══════════════════════════════════════════════ INTÉGRATION PHP */
    case 'integration_php': ?>

    <div class="doc-info"><i class="fa-solid fa-server"></i><div>Client confidentiel — le <code>client_secret</code> est stocké côté serveur, jamais exposé au navigateur.</div></div>

    <div class="doc-section"><i class="fa-solid fa-arrow-right-to-bracket"></i>1. Rediriger vers Triade</div>
    <div class="code-block"><span class="code-lang">php</span>
<pre>&lt;?php
$issuer      = 'https://triade.monecole.fr/sso';
$clientId    = 'monapp';
$redirectUri = 'https://monapp.fr/oidc/callback.php';

$state = bin2hex(random_bytes(16));
$nonce = bin2hex(random_bytes(16));
$_SESSION['oidc_state'] = $state;
$_SESSION['oidc_nonce'] = $nonce;

$params = http_build_query([
    'response_type' => 'code',
    'client_id'     => $clientId,
    'redirect_uri'  => $redirectUri,
    'scope'         => 'openid profile',
    'state'         => $state,
    'nonce'         => $nonce,
]);

header('Location: ' . $issuer . '/authorize.php?' . $params);
exit;</pre></div>

    <div class="doc-section"><i class="fa-solid fa-arrows-left-right"></i>2. Callback — échanger le code</div>
    <div class="code-block"><span class="code-lang">php</span>
<pre>&lt;?php
// callback.php
session_start();

if (($_GET['state'] ?? '') !== $_SESSION['oidc_state']) {
    die('State invalide');
}

$code   = $_GET['code'] ?? '';
$issuer = 'https://triade.monecole.fr/sso';

$ch = curl_init($issuer . '/token.php');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query([
        'grant_type'    => 'authorization_code',
        'code'          => $code,
        'client_id'     => 'monapp',
        'client_secret' => 'SECRET_MONAPP',
        'redirect_uri'  => 'https://monapp.fr/oidc/callback.php',
    ]),
]);
$resp = json_decode(curl_exec($ch), true);
curl_close($ch);</pre></div>

    <div class="doc-section"><i class="fa-solid fa-user"></i>3. Appeler UserInfo et créer la session</div>
    <div class="code-block"><span class="code-lang">php</span>
<pre>&lt;?php
$ch = curl_init($issuer . '/userinfo.php');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $resp['access_token']],
]);
$user = json_decode(curl_exec($ch), true);
curl_close($ch);

// Vérifier le nonce dans l'id_token
$parts   = explode('.', $resp['id_token']);
$payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
if (($payload['nonce'] ?? '') !== $_SESSION['oidc_nonce']) {
    die('Nonce invalide');
}

$_SESSION['user'] = [
    'nom'    => $user['family_name'],
    'prenom' => $user['given_name'],
    'role'   => $user['triade_role'],
    'id'     => $user['triade_id'],
];

unset($_SESSION['oidc_state'], $_SESSION['oidc_nonce']);
header('Location: /dashboard.php');
exit;</pre></div>

    <?php break;

    /* ══════════════════════════════════════════ INTÉGRATION MOBILE */
    case 'integration_mobile': ?>

    <div class="doc-info"><i class="fa-brands fa-react"></i><div>Client public — aucun <code>client_secret</code> dans le code. <code>expo-auth-session</code> gère PKCE automatiquement.</div></div>

    <div class="doc-section"><i class="fa-solid fa-database"></i>Client à enregistrer dans Triade</div>
    <div class="code-block"><span class="code-lang">config</span>
<pre>client_id     : pronote
client_secret : (vide — client public)
redirect_uri  : pronote://oidc/callback
pkce_required : oui</pre></div>

    <div class="doc-section"><i class="fa-solid fa-terminal"></i>Installation</div>
    <div class="code-block"><span class="code-lang">shell</span>
<pre>npx expo install expo-auth-session expo-crypto expo-web-browser</pre></div>

    <div class="doc-section"><i class="fa-solid fa-code"></i>Hook d'authentification</div>
    <div class="code-block"><span class="code-lang">typescript</span>
<pre>import * as AuthSession from 'expo-auth-session';
import * as WebBrowser  from 'expo-web-browser';

WebBrowser.maybeCompleteAuthSession();

const ISSUER    = 'https://triade.monecole.fr/sso';
const CLIENT_ID = 'pronote';
const REDIRECT  = AuthSession.makeRedirectUri({ scheme: 'pronote', path: 'oidc/callback' });

export function useTriadeAuth() {

  const discovery = AuthSession.useAutoDiscovery(ISSUER);

  const [request, response, promptAsync] = AuthSession.useAuthRequest(
    {
      clientId:     CLIENT_ID,
      redirectUri:  REDIRECT,
      scopes:       ['openid', 'profile'],
      usePKCE:      true,   // PKCE géré automatiquement
      responseType: AuthSession.ResponseType.Code,
    },
    discovery
  );

  React.useEffect(() => {
    if (response?.type !== 'success') return;
    AuthSession.exchangeCodeAsync(
      {
        clientId:    CLIENT_ID,
        code:        response.params.code,
        redirectUri: REDIRECT,
        extraParams: { code_verifier: request.codeVerifier },
      },
      discovery
    ).then(tokens => {
      // tokens.accessToken — appeler /sso/userinfo.php
      // tokens.idToken     — JWT RS256 avec claims utilisateur
      console.log(tokens.accessToken);
    });
  }, [response]);

  return { request, promptAsync };
}</pre></div>

    <div class="doc-section"><i class="fa-solid fa-user-circle"></i>Appel UserInfo</div>
    <div class="code-block"><span class="code-lang">typescript</span>
<pre>const user = await fetch(ISSUER + '/userinfo.php', {
  headers: { Authorization: 'Bearer ' + accessToken }
}).then(r => r.json());

console.log(user.given_name, user.triade_role);</pre></div>

    <div class="doc-section"><i class="fa-solid fa-file-code"></i>app.json — deep link scheme</div>
    <div class="code-block"><span class="code-lang">json</span>
<pre>{
  "expo": { "scheme": "pronote" }
}</pre></div>
    <div class="doc-note"><i class="fa-solid fa-android"></i><div>Sur Android natif (hors Expo Go), configurer aussi le deep link dans <code>AndroidManifest.xml</code>.</div></div>

    <?php break;
    endswitch; ?>

    <!-- Footer nav -->
    <div style="margin-top:22px;padding-top:14px;border-top:1px solid #e5e9f0;display:flex;align-items:center;justify-content:space-between;">
      <a href="oidc_doc.php" style="font-size:12px;color:#4a9eff;text-decoration:none;">
        <i class="fa-solid fa-arrow-left" style="margin-right:4px;"></i>Index documentation
      </a>
      <a href="config_oidc.php" style="font-size:12px;color:#6b7280;text-decoration:none;">
        <i class="fa-solid fa-gear" style="margin-right:4px;"></i>Config OIDC
      </a>
    </div>

  </div><!-- /doc-content -->
</div><!-- /doc-wrap -->

<?php endif; ?>
</font></p></td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
