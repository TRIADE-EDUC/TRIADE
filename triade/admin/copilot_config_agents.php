<?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>
<HTML>
<HEAD>
<?php
include_once("./librairie_php/lib_licence.php");

$configFile = '../common/config-ia-agents.php';

// Valeurs par défaut
$cfg = array(
    'IA_AGENT_EDUXPERT'  => 'oui',
    'IA_AGENT_DIRECTION' => 'oui',
    'IA_AGENT_VEILLE'    => 'oui',
    'IA_AGENT_CREATION'  => 'oui',
);

// Charger config existante
if (file_exists($configFile)) {
    include_once($configFile);
    foreach (array_keys($cfg) as $k) {
        if (defined($k)) $cfg[$k] = constant($k);
    }
}

$msg = '';
$msgType = '';

if (isset($_POST['save'])) {
    $cfg['IA_AGENT_EDUXPERT']  = isset($_POST['IA_AGENT_EDUXPERT'])  ? 'oui' : 'non';
    $cfg['IA_AGENT_DIRECTION'] = isset($_POST['IA_AGENT_DIRECTION']) ? 'oui' : 'non';
    $cfg['IA_AGENT_VEILLE']    = isset($_POST['IA_AGENT_VEILLE'])    ? 'oui' : 'non';
    $cfg['IA_AGENT_CREATION']  = isset($_POST['IA_AGENT_CREATION'])  ? 'oui' : 'non';

    $php = "<?php\n";
    foreach ($cfg as $k => $v) {
        $php .= "define('$k', '$v');\n";
    }
    $php .= "?>\n";

    if (file_put_contents($configFile, $php) !== false) {
        $msg = 'Configuration sauvegardée.';
        $msgType = 'ok';
    } else {
        $msg = 'Erreur : impossible d\'écrire le fichier de configuration.';
        $msgType = 'err';
    }
}
?>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<title>Triade — Configuration Agents IA</title>
<style>
.ag-wrap { padding: 14px 16px; }
.ag-section { background:#fff; border:1px solid #dde; border-radius:8px; padding:14px 16px; margin-bottom:14px; }
.ag-section h3 { color:#080A66; font-size:13px; margin:0 0 12px; border-bottom:1px solid #eef; padding-bottom:6px; }
.ag-toggle-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 0; border-bottom: 1px solid #f0f2fa;
}
.ag-toggle-row:last-child { border-bottom: none; }
.ag-toggle-label { font-size: 12px; }
.ag-toggle-label strong { display: block; color: #222; margin-bottom: 2px; }
.ag-toggle-label span { color: #888; font-size: 11px; }
/* Toggle switch */
.toggle-sw { position: relative; display: inline-block; width: 40px; height: 22px; }
.toggle-sw input { opacity: 0; width: 0; height: 0; }
.toggle-sl {
    position: absolute; cursor: pointer; inset: 0;
    background: #ccc; border-radius: 22px; transition: .2s;
}
.toggle-sl:before {
    content: ''; position: absolute;
    width: 16px; height: 16px; left: 3px; bottom: 3px;
    background: #fff; border-radius: 50%; transition: .2s;
}
.toggle-sw input:checked + .toggle-sl { background: #080A66; }
.toggle-sw input:checked + .toggle-sl:before { transform: translateX(18px); }

.btn-save {
    background: #080A66; color: #fff; border: none;
    padding: 8px 24px; border-radius: 6px; font-size: 12px;
    font-weight: bold; cursor: pointer; margin-top: 6px;
}
.btn-save:hover { background: #0d10a0; }
.alert-ok  { background:#d4edda;border:1px solid #c3e6cb;border-radius:6px;padding:8px 12px;font-size:12px;color:#155724;margin-bottom:12px; }
.alert-err { background:#f8d7da;border:1px solid #f5c6cb;border-radius:6px;padding:8px 12px;font-size:12px;color:#721c24;margin-bottom:12px; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Configuration — Agents IA</font></b></td></tr>
<tr id='cadreCentral0'><td>

<div class="ag-wrap">

<?php if ($msg): ?>
<div class="<?php echo $msgType === 'ok' ? 'alert-ok' : 'alert-err'; ?>">
    <?php echo $msgType === 'ok' ? '&#10003;' : '&#9888;'; ?> <?php echo htmlspecialchars($msg); ?>
</div>
<?php endif; ?>

<form method="POST" action="">

    <!-- Agents du service -->
    <div class="ag-section">
        <h3>&#129302; Agents disponibles TRIADE-COPILOT</h3>

        <div class="ag-toggle-row">
            <div class="ag-toggle-label">
                <strong>Eduxpert</strong>
                <span>Agent pédagogique — questions sur l'éducation nationale</span>
            </div>
            <label class="toggle-sw">
                <input type="checkbox" name="IA_AGENT_EDUXPERT" <?php echo $cfg['IA_AGENT_EDUXPERT'] === 'oui' ? 'checked' : ''; ?>>
                <span class="toggle-sl"></span>
            </label>
        </div>

        <div class="ag-toggle-row">
            <div class="ag-toggle-label">
                <strong>Stratégique — Direction</strong>
                <span>Analyse de documents pour la direction d'établissement</span>
            </div>
            <label class="toggle-sw">
                <input type="checkbox" name="IA_AGENT_DIRECTION" <?php echo $cfg['IA_AGENT_DIRECTION'] === 'oui' ? 'checked' : ''; ?>>
                <span class="toggle-sl"></span>
            </label>
        </div>

        <div class="ag-toggle-row">
            <div class="ag-toggle-label">
                <strong>Veille documentaire</strong>
                <span>Veille automatique sur les actualités de l'éducation</span>
            </div>
            <label class="toggle-sw">
                <input type="checkbox" name="IA_AGENT_VEILLE" <?php echo $cfg['IA_AGENT_VEILLE'] === 'oui' ? 'checked' : ''; ?>>
                <span class="toggle-sl"></span>
            </label>
        </div>
    </div>

    <!-- Agents personnalisés -->
    <div class="ag-section">
        <h3>&#9998; Agents personnalisés</h3>

        <div class="ag-toggle-row">
            <div class="ag-toggle-label">
                <strong>Création d'agents personnalisés</strong>
                <span>Permettre aux utilisateurs de créer leurs propres agents IA</span>
            </div>
            <label class="toggle-sw">
                <input type="checkbox" name="IA_AGENT_CREATION" <?php echo $cfg['IA_AGENT_CREATION'] === 'oui' ? 'checked' : ''; ?>>
                <span class="toggle-sl"></span>
            </label>
        </div>
    </div>

    <button type="submit" name="save" value="1" class="btn-save">Enregistrer</button>
    <a href="triade-copilot.php" style="margin-left:14px;font-size:11px;color:#666;">&#8592; Retour</a>

</form>

</div>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</HTML>
