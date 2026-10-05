<?php
session_start();
include_once("./librairie_php/lib_licence.php");

define('_SIGN_API_BASE', 'https://support.triade-educ.org/support/api-sign.php');
define('_SIGN_API_KEY',  'sign-2026-xT5nVqP9');

$url = $_SERVER['SERVER_NAME'];
$productid = '';
if (file_exists('../common/productid.php')) {
    include_once('../common/productid.php');
    if (defined('PRODUCTID')) $productid = PRODUCTID;
}
if (empty($productid) && isset($_POST['productid'])) $productid = trim($_POST['productid']);

$erreur = '';
$champs = array(
    'etablissement' => '', 'adresse' => '', 'ville' => '',
    'ccp' => '', 'pays' => 'France', 'nom' => '', 'prenom' => '', 'email' => '',
);

if (isset($_POST['inscribe'])) {
    foreach ($champs as $k => $v) {
        $champs[$k] = isset($_POST[$k]) ? trim($_POST[$k]) : '';
    }
    if (empty($_POST['cgu'])) {
        $erreur = 'Vous devez accepter les conditions générales.';
    } elseif (!$champs['etablissement'] || !$champs['email'] || !$champs['nom']) {
        $erreur = 'Veuillez remplir tous les champs obligatoires.';
    } else {
        $p = array_merge($champs, array(
            'action'    => 'inscribe',
            'key'       => _SIGN_API_KEY,
            'url'       => $url,
            'productid' => $productid,
        ));
        $ch = curl_init(_SIGN_API_BASE);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15,
            CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query($p),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $rsp = curl_exec($ch);
        curl_close($ch);
        $r = $rsp ? json_decode($rsp, true) : null;
        if ($r && !empty($r['ok']) && !empty($r['signkey'])) {
            $texte  = "<?php\n";
            $texte .= 'define("SIGNKEY","'   . addslashes($r['signkey'])                     . '");' . "\n";
            $texte .= 'define("SIGNURL","https://ia.triade-educ.net/triade-sign/");'                 . "\n";
            $texte .= 'define("SIGNPAYS","'  . addslashes(isset($r['pays']) ? $r['pays'] : '') . '");' . "\n";
            $texte .= "?>\n";
            file_put_contents('../common/config-sign.php', $texte);
            echo "<script>location.href='triade-sign.php?sign_msg=inscription_success';</script>";
            exit;
        } else {
            $erreur = ($r && isset($r['error'])) ? $r['error'] : 'Erreur de connexion au serveur TRIADE-SIGN.';
        }
    }
}

$localServer = (preg_match('/localhost|127\.0\.0\.1|192\.168\./', $url));

// DEBUG TEMPORAIRE — à supprimer après diagnostic
$_DBG = null;
if (isset($_POST['inscribe'])) {
    $_DBG = array(
        'url'         => $url,
        'localServer' => $localServer,
        'cgu'         => !empty($_POST['cgu']),
        'etab'        => isset($_POST['etablissement']) ? $_POST['etablissement'] : '',
        'email'       => isset($_POST['email']) ? $_POST['email'] : '',
        'nom'         => isset($_POST['nom']) ? $_POST['nom'] : '',
        'erreur'      => $erreur,
        'rsp'         => isset($rsp) ? $rsp : '(non défini — conditions non remplies ou CGU manquante)',
    );
}
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4-2.css">
<link rel="stylesheet" href="../librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<title>Triade — Inscription TRIADE-SIGN</title>
<style>
.si-card { background:#fff; border:1px solid #c5cae9; border-radius:8px; margin-bottom:10px; }
.si-card-header { background:#080A66; color:#fff; padding:8px 14px; border-radius:8px 8px 0 0; font-family:Electrolize,Arial; font-size:12px; font-weight:bold; }
.si-card-body { padding:12px 14px; }
.si-cgu { height:160px; overflow-y:auto; border:1px solid #ddd; border-radius:4px; padding:8px 10px; font-size:11px; color:#555; font-family:Arial; line-height:1.5; background:#fafafa; margin-bottom:10px; }
.si-grid { display:flex; flex-wrap:wrap; gap:8px; }
.si-field { flex:1 1 180px; }
.si-field.wide { flex:2 1 280px; }
.si-field label { display:block; font-size:11px; color:#555; margin-bottom:3px; font-family:Electrolize,Arial; }
.si-field input[type=text], .si-field input[type=email] {
    width:100%; padding:5px 8px; border:1px solid #c8cfe8; border-radius:4px;
    font-size:12px; font-family:Electrolize,Arial; box-sizing:border-box;
}
.si-field input:focus { outline:none; border-color:#080A66; }
.si-error { background:#ffebee; border:1px solid #ef9a9a; border-radius:6px; padding:8px 12px; font-size:12px; color:#c62828; margin-bottom:10px; }
.si-warn  { background:#fff3e0; border:1px solid #ffcc80; border-radius:6px; padding:8px 12px; font-size:12px; color:#e65100; margin-bottom:10px; }
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>TRIADE-SIGN — Inscription</font></b></td></tr>
<tr id='cadreCentral0'><td>

<div style="padding:10px 8px;">

<?php if ($_DBG): ?>
<div style="background:#fff3e0;border:1px solid #ff9800;border-radius:6px;padding:10px;font-size:11px;font-family:monospace;margin-bottom:10px;">
<b>DEBUG (temporaire)</b><br>
url=<b><?php echo htmlspecialchars($_DBG['url']); ?></b> |
localServer=<b><?php echo $_DBG['localServer'] ? 'OUI' : 'non'; ?></b> |
cgu=<b><?php echo $_DBG['cgu'] ? 'oui' : 'NON'; ?></b> |
etab=<b><?php echo htmlspecialchars($_DBG['etab']); ?></b> |
nom=<b><?php echo htmlspecialchars($_DBG['nom']); ?></b> |
email=<b><?php echo htmlspecialchars($_DBG['email']); ?></b><br>
erreur=<b><?php echo htmlspecialchars($_DBG['erreur'] ?: '(vide)'); ?></b><br>
rsp=<code><?php echo htmlspecialchars(substr($_DBG['rsp'], 0, 300)); ?></code>
</div>
<?php endif; ?>

<?php if ($localServer): ?>
<div class="si-warn">
    <i class="bi bi-exclamation-triangle"></i>
    <b>Inscription impossible.</b> Votre serveur TRIADE n'est pas accessible depuis Internet
    (<code><?php echo htmlspecialchars($url); ?></code>). Un nom de domaine est nécessaire pour utiliser TRIADE-SIGN.
</div>

<?php else: ?>

<?php if ($erreur): ?>
<div class="si-error"><i class="bi bi-x-circle"></i> <?php echo htmlspecialchars($erreur); ?></div>
<?php endif; ?>

<form method="post" action="sign-inscription.php">
<input type="hidden" name="productid" value="<?php echo htmlspecialchars($productid); ?>">

<div class="si-card">
    <div class="si-card-header"><i class="bi bi-file-text" style="margin-right:6px;"></i>Conditions Générales d'Utilisation</div>
    <div class="si-card-body">
        <div class="si-cgu">CONDITIONS GÉNÉRALES

En utilisant les services proposés sur le Site, vous acceptez pleinement et sans réserve les présentes Conditions Générales de Vente. Si vous n'êtes pas d'accord avec ces Conditions Générales de Vente, vous ne devez pas utiliser les services proposés sur le Site.

1. DÉFINITIONS
Afin de garantir un fonctionnement optimal du service offert par TRIADE, un nombre maximum de signatures journalier sera alloué à chaque membre. Les signatures effectuées auprès de TRIADE-SIGN sont comptabilisées en unités appelées "Signature". TRIADE se réserve le droit d'adapter le nombre de signatures alloués sans préavis.

2. DURÉE ET RÉSILIATION
La durée est déterminée par la formule choisie. Une annulation par mail ne peut donner lieu à un remboursement, toute période entamée est due.

3. CONDITIONS DU SERVICE
T.R.I.A.D.E. s'engage à mettre en oeuvre les moyens nécessaires pour fournir le Service avec un taux de disponibilité d'au moins 98,5 %, 7j/7 sur un mois donné.

4. CONDITIONS FINANCIÈRES
Pour utiliser le service, le client doit créditer son compte. Les tarifs sont définis sur https://www.triade-educ.org

5. DONNÉES PERSONNELLES
Les données personnelles collectées sont nécessaires à la gestion des abonnements. Conformément à la législation en vigueur, l'utilisateur dispose d'un droit d'accès, de rectification et de suppression.

6. RESPONSABILITÉ
Le Client s'engage à faire un usage du Service conforme aux dispositions légales et réglementaires en vigueur.

L'Équipe TRIADE — https://www.triade-educ.org</div>
        <label style="font-size:12px;display:flex;align-items:center;gap:8px;cursor:pointer;color:#333;">
            <input type="checkbox" name="cgu" value="1"<?php echo (!empty($_POST['cgu'])) ? ' checked' : ''; ?>>
            J'accepte les conditions générales d'utilisation
        </label>
    </div>
</div>

<div class="si-card">
    <div class="si-card-header"><i class="bi bi-building" style="margin-right:6px;"></i>Établissement *</div>
    <div class="si-card-body">
        <div class="si-grid">
            <div class="si-field wide">
                <label>Nom de l'établissement *</label>
                <input type="text" name="etablissement" value="<?php echo htmlspecialchars($champs['etablissement']); ?>" required>
            </div>
            <div class="si-field wide">
                <label>Adresse</label>
                <input type="text" name="adresse" value="<?php echo htmlspecialchars($champs['adresse']); ?>">
            </div>
            <div class="si-field">
                <label>Code postal</label>
                <input type="text" name="ccp" value="<?php echo htmlspecialchars($champs['ccp']); ?>">
            </div>
            <div class="si-field">
                <label>Ville</label>
                <input type="text" name="ville" value="<?php echo htmlspecialchars($champs['ville']); ?>">
            </div>
            <div class="si-field">
                <label>Pays</label>
                <input type="text" name="pays" value="<?php echo htmlspecialchars($champs['pays']); ?>">
            </div>
        </div>
    </div>
</div>

<div class="si-card">
    <div class="si-card-header"><i class="bi bi-person-fill" style="margin-right:6px;"></i>Responsable *</div>
    <div class="si-card-body">
        <div class="si-grid">
            <div class="si-field">
                <label>Nom *</label>
                <input type="text" name="nom" value="<?php echo htmlspecialchars($champs['nom']); ?>" required>
            </div>
            <div class="si-field">
                <label>Prénom</label>
                <input type="text" name="prenom" value="<?php echo htmlspecialchars($champs['prenom']); ?>">
            </div>
            <div class="si-field wide">
                <label>Email de contact *</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($champs['email']); ?>" required>
            </div>
        </div>
        <div style="font-size:11px;color:#999;margin-top:6px;">
            Serveur : <code><?php echo htmlspecialchars($url); ?></code>
            <?php if ($productid): ?> — Product-ID : <code><?php echo htmlspecialchars($productid); ?></code><?php endif; ?>
        </div>
    </div>
</div>

<div style="margin-top:4px;display:flex;gap:10px;align-items:center;">
    <script language=JavaScript>buttonMagicSubmit4("Inscription Gratuite","inscribe",""); </script>
    <button type="button" class="btn-retour" onclick="history.go(-1)">Retour</button>
</div>

</form>
<?php endif; ?>

</div>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</HTML>
