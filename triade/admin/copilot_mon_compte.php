<?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>
<HTML>
<HEAD>
<?php
include_once("./librairie_php/lib_licence.php");

$iakey      = '';
$iaActif    = false;
$iaApiDispo = false;
if (file_exists('../common/config-ia.php')) {
    include_once('../common/config-ia.php');
    if (defined('IAKEY')) { $iakey = IAKEY; $iaActif = true; }
}
if (file_exists('../librairie_php/db_ia.php')) {
    include_once('../librairie_php/db_ia.php');
    $iaApiDispo = true;
}

$msg = $msgType = '';
$iaInfo = null;

if ($iaActif && $iaApiDispo) {
    $iaInfo = getIAInfo($iakey);
}

if (isset($_POST['save']) && $iaActif && $iaApiDispo) {
    $fields = array();
    foreach (array('nom','prenom','email','etablissement','adresse','ville','ccp','pays') as $f) {
        if (isset($_POST[$f])) $fields[$f] = trim($_POST[$f]);
    }
    if (!empty($fields) && updateIAInfo($iakey, $fields)) {
        $msg     = 'Informations mises à jour.';
        $msgType = 'ok';
        $iaInfo  = getIAInfo($iakey);
    } else {
        $msg     = 'Erreur lors de la mise à jour. Veuillez réessayer.';
        $msgType = 'err';
    }
}

$f_nom   = $iaInfo ? htmlspecialchars($iaInfo['nom']          ?? '') : '';
$f_pren  = $iaInfo ? htmlspecialchars($iaInfo['prenom']       ?? '') : '';
$f_mail  = $iaInfo ? htmlspecialchars($iaInfo['email']        ?? '') : '';
$f_etab  = $iaInfo ? htmlspecialchars($iaInfo['etablissement']?? '') : '';
?>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<title>Triade — Mon compte COPILOT</title>
<style>
.mc-wrap  { padding: 14px 16px; }
.mc-card  { background: #fff; border: 1px solid #dde; border-radius: 8px; padding: 14px 16px; margin-bottom: 14px; }
.mc-card h3 { color: #080A66; font-size: 13px; margin: 0 0 12px; border-bottom: 1px solid #eef; padding-bottom: 6px; }
.mc-row   { margin-bottom: 10px; }
.mc-row label { display: block; font-size: 11px; font-weight: bold; color: #555; margin-bottom: 3px; }
.mc-row input { width: 100%; padding: 6px 8px; border: 1px solid #c5caee; border-radius: 5px; font-size: 12px; box-sizing: border-box; }
.mc-row input:focus { border-color: #080A66; outline: none; }
.mc-2col  { display: flex; gap: 10px; }
.mc-2col .mc-row { flex: 1; }
.mc-key   { background: #f0f2fa; border: 1px solid #c5caee; border-radius: 6px;
            padding: 8px 12px; font-size: 11px; color: #555; margin-bottom: 14px; }
.mc-key code { font-family: monospace; background: #e8eaf0; padding: 2px 6px; border-radius: 3px; }
.btn-save {
    background: #080A66; color: #fff; border: none;
    padding: 8px 24px; border-radius: 6px; font-size: 12px;
    font-weight: bold; cursor: pointer;
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
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Mon compte — TRIADE-COPILOT</font></b></td></tr>
<tr id='cadreCentral0'><td>

<?php if (!$iaActif): ?>
<div style="margin:14px 8px;padding:10px 14px;background:#fff8e1;border:1px solid #f5c842;border-radius:6px;font-size:12px;color:#856404;">
    &#9888; Compte COPILOT non configuré. <a href="ia-inscription.php" style="color:#c0392b;font-weight:700;">Inscription gratuite</a>
</div>
<?php elseif (!$iaInfo): ?>
<div style="margin:14px 8px;padding:10px 14px;background:#fff8e1;border:1px solid #f5c842;border-radius:6px;font-size:12px;color:#856404;">
    &#9888; Impossible de récupérer les informations du compte (serveur central inaccessible).
</div>
<?php else: ?>

<div class="mc-wrap">

<?php if ($msg): ?>
<div class="<?php echo $msgType === 'ok' ? 'alert-ok' : 'alert-err'; ?>">
    <?php echo $msgType === 'ok' ? '&#10003;' : '&#9888;'; ?> <?php echo htmlspecialchars($msg); ?>
</div>
<?php endif; ?>

<!-- Clé IA (lecture seule) -->
<div class="mc-key">
    Clé IA (IAKEY) : <code><?php echo htmlspecialchars(substr($iakey, 0, 8) . '••••••••'); ?></code>
    &nbsp;—&nbsp; non modifiable
</div>

<form method="POST" action="">

    <div class="mc-card">
        <h3>&#128100; Identité</h3>
        <div class="mc-2col">
            <div class="mc-row">
                <label>Prénom</label>
                <input type="text" name="prenom" maxlength="80" value="<?php echo $f_pren; ?>">
            </div>
            <div class="mc-row">
                <label>Nom</label>
                <input type="text" name="nom" maxlength="80" value="<?php echo $f_nom; ?>">
            </div>
        </div>
        <div class="mc-row">
            <label>Email du compte</label>
            <input type="email" name="email" maxlength="120" value="<?php echo $f_mail; ?>">
        </div>
        <div class="mc-row">
            <label>Établissement</label>
            <input type="text" name="etablissement" maxlength="150" value="<?php echo $f_etab; ?>">
        </div>
    </div>

    <button type="submit" name="save" value="1" class="btn-save">Enregistrer</button>
    <a href="triade-copilot.php" style="margin-left:14px;font-size:11px;color:#666;">&#8592; Retour</a>

</form>

</div>

<?php endif; ?>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>

<?php if ($msg): ?>
<script>
window.addEventListener('DOMContentLoaded', function() {
    alertify.<?php echo $msgType === 'ok' ? 'success' : 'error'; ?>('<?php echo addslashes($msg); ?>');
});
</script>
<?php endif; ?>
</body>
</HTML>
