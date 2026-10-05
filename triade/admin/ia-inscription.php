<?php
session_start();
include("./librairie_php/lib_licence.php");
include_once("../librairie_php/db_triade.php");
valideProductId();
include_once("../common/config2.inc.php");
if (file_exists("../common/productId.php")) include_once("../common/productId.php");

$urlServeur  = $_SERVER['SERVER_NAME'] ?? '';
$graphCourant = defined('GRAPH') ? GRAPH : '';
$productid   = defined('PRODUCTID') ? PRODUCTID : '';

// Déjà inscrit ?
$dejaInscrit = file_exists('../common/config-ia.php');

// Étape courante (0=CGV, 1=formulaire, 2=résultat)
$etape  = 0;
$erreur = '';
$succes = false;

if ($dejaInscrit) {
    $etape = 99; // déjà inscrit
} elseif (isset($_POST['valide_cgv'])) {
    $etape = 1;
} elseif (isset($_POST['create'])) {
    // Validation locale
    $etablissement = trim($_POST['etablissement']          ?? '');
    $adresse       = trim($_POST['addresse_etablissement'] ?? '');
    $ville         = trim($_POST['ville_etablissement']    ?? '');
    $cp            = trim($_POST['ccp_etablissement']      ?? '');
    $pays          = trim($_POST['saisie_pays']            ?? '');
    $nom           = trim($_POST['nom']                    ?? '');
    $prenom        = trim($_POST['prenom']                 ?? '');
    $tel           = trim($_POST['tel']                    ?? '');
    $email         = trim($_POST['email']                  ?? '');

    if (!$etablissement || !$nom || !$prenom || !$email) {
        $erreur = 'Tous les champs obligatoires (*) doivent être renseignés.';
        $etape  = 1;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = 'Adresse email invalide.';
        $etape  = 1;
    } elseif (preg_match('/localhost|127\.0\.0\.1|192\.168\./', $urlServeur)) {
        $erreur = 'Inscription impossible : votre serveur n\'est pas accessible via Internet. Un nom de domaine est nécessaire.';
        $etape  = 1;
    } else {
        // Envoi vers le serveur central via curl
        $supportUrl = 'https://support.triade-educ.org/support/ia-inscription1.php'
                    . '?inc=' . urlencode($graphCourant)
                    . '&url=' . urlencode($urlServeur)
                    . '&productid=' . urlencode($productid);

        $postFields = http_build_query([
            'create'                  => 1,
            'etablissement'           => $etablissement,
            'addresse_etablissement'  => $adresse,
            'ville_etablissement'     => $ville,
            'ccp_etablissement'       => $cp,
            'saisie_pays'             => $pays,
            'nom'                     => $nom,
            'prenom'                  => $prenom,
            'tel'                     => $tel,
            'email'                   => $email,
            'url'                     => $urlServeur,
            'productid'               => $productid,
        ]);

        $ch = curl_init($supportUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST,           true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $postFields);
        curl_setopt($ch, CURLOPT_TIMEOUT,        30);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && strpos($response, 'Enregistrement') !== false) {
            $succes = true;
            $etape  = 2;
        } elseif (strpos((string)$response, 'Erreur de transfert') !== false) {
            $erreur = 'Erreur lors de la création du patch. Contactez support@triade-educ.org.';
            $etape  = 1;
        } elseif ($httpCode !== 200) {
            $erreur = 'Serveur d\'inscription inaccessible (HTTP ' . $httpCode . '). Réessayez dans quelques instants.';
            $etape  = 1;
        } else {
            $succes = true;
            $etape  = 2;
        }
    }
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
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<title>Triade — Inscription TRIADE-COPILOT</title>
<style>
.ins-wrap { padding: 14px 16px; }
.ins-step-bar {
    display: flex; align-items: center; gap: 6px;
    margin-bottom: 18px; font-size: 11px;
}
.ins-snum {
    width: 20px; height: 20px; border-radius: 50%;
    background: #c5caee; color: #080A66; font-size: 10px; font-weight: bold;
    display: inline-flex; align-items: center; justify-content: center;
}
.ins-snum.active { background: #080A66; color: #fff; }
.ins-snum.done   { background: #28a745; color: #fff; }
.ins-sep { flex: 1; height: 1px; background: #c5caee; max-width: 30px; }
.ins-slabel { font-size: 11px; color: #555; }
.ins-slabel.active { color: #080A66; font-weight: bold; }

.cgv-box {
    height: 200px; overflow-y: auto;
    border: 1px solid #c5caee; border-radius: 5px;
    background: #fafafa; padding: 10px 12px;
    font-size: 11px; line-height: 1.6; color: #444;
    margin-bottom: 12px;
}
.cgv-accept { display: flex; align-items: center; gap: 8px; font-size: 12px; margin-bottom: 14px; }
.cgv-accept input[type=checkbox] { width: 15px; height: 15px; cursor: pointer; }

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 14px; margin-bottom: 14px; }
.form-grid .full { grid-column: 1 / -1; }
.fg label { display: block; font-size: 11px; color: #555; margin-bottom: 3px; font-weight: bold; }
.fg label span { color: #c00; }
.fg input, .fg select {
    width: 100%; padding: 6px 8px; border: 1px solid #c5caee;
    border-radius: 5px; font-size: 12px; box-sizing: border-box;
}
.fg input:focus, .fg select:focus { outline: 2px solid #080A66; border-color: transparent; }

.ins-alert-err { background:#f8d7da;border:1px solid #f5c6cb;border-radius:6px;padding:8px 12px;font-size:12px;color:#721c24;margin-bottom:12px; }
.ins-alert-ok  { background:#d4edda;border:1px solid #c3e6cb;border-radius:6px;padding:14px 16px;font-size:12px;color:#155724;margin-bottom:12px; }

.btn-ins {
    background: #080A66; color: #fff; border: none;
    padding: 8px 22px; border-radius: 6px; font-size: 12px;
    font-weight: bold; cursor: pointer; margin-top: 6px;
}
.btn-ins:disabled { background: #aaa; cursor: not-allowed; }
.btn-ins:hover:not(:disabled) { background: #0d10a0; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Inscription TRIADE-COPILOT</font></b></td></tr>
<tr id='cadreCentral0'><td>

<div class="ins-wrap">

<?php if ($etape === 99): ?>
<!-- Déjà inscrit -->
<div class="ins-alert-ok">
    &#10003; Votre établissement est déjà inscrit au service TRIADE-COPILOT.<br>
    <a href="triade-copilot.php" style="color:#155724;font-weight:700;">&#8592; Retour au tableau de bord COPILOT</a>
</div>

<?php elseif ($etape === 2): ?>
<!-- Étape 2 : Confirmation -->
<div class="ins-step-bar">
    <span class="ins-snum done">&#10003;</span><span class="ins-slabel">Conditions</span>
    <span class="ins-sep"></span>
    <span class="ins-snum done">&#10003;</span><span class="ins-slabel">Informations</span>
    <span class="ins-sep"></span>
    <span class="ins-snum active">3</span><span class="ins-slabel active">Confirmation</span>
</div>

<div class="ins-alert-ok">
    <strong>&#10003; Enregistrement terminé !</strong><br><br>
    Un email contenant le <strong>patch d'activation</strong> vient d'être envoyé à l'adresse fournie.<br><br>
    &#8250; Installez le patch via le module <strong>Admin → Patch/Update</strong> pour activer TRIADE-COPILOT.<br>
    &#8250; Si l'email n'arrive pas, vérifiez vos <strong>spams</strong>.
</div>
<div style="margin-top:10px;">
    <a href="triade-copilot.php" style="font-size:12px;color:#080A66;font-weight:700;">&#8592; Retour au tableau de bord COPILOT</a>
</div>

<?php elseif ($etape === 1): ?>
<!-- Étape 1 : Formulaire -->
<div class="ins-step-bar">
    <span class="ins-snum done">&#10003;</span><span class="ins-slabel">Conditions</span>
    <span class="ins-sep"></span>
    <span class="ins-snum active">2</span><span class="ins-slabel active">Informations</span>
    <span class="ins-sep"></span>
    <span class="ins-snum">3</span><span class="ins-slabel">Confirmation</span>
</div>

<?php if ($erreur): ?>
<div class="ins-alert-err">&#9888; <?php echo htmlspecialchars($erreur); ?></div>
<?php endif; ?>

<form method="POST" action="">
<div class="form-grid">
    <div class="fg full">
        <label>Nom de l'établissement <span>*</span></label>
        <input type="text" name="etablissement" maxlength="60" value="<?php echo htmlspecialchars($_POST['etablissement'] ?? ''); ?>" required>
    </div>
    <div class="fg">
        <label>Adresse</label>
        <input type="text" name="addresse_etablissement" maxlength="80" value="<?php echo htmlspecialchars($_POST['addresse_etablissement'] ?? ''); ?>">
    </div>
    <div class="fg">
        <label>Ville</label>
        <input type="text" name="ville_etablissement" maxlength="30" value="<?php echo htmlspecialchars($_POST['ville_etablissement'] ?? ''); ?>">
    </div>
    <div class="fg">
        <label>Code postal</label>
        <input type="text" name="ccp_etablissement" maxlength="20" value="<?php echo htmlspecialchars($_POST['ccp_etablissement'] ?? ''); ?>">
    </div>
    <div class="fg">
        <label>Pays</label>
        <select name="saisie_pays">
            <?php
            $pays_liste = ['France','Belgique','Suisse','Luxembourg','Canada','Maroc','Tunisie','Algérie','Sénégal','Côte d\'Ivoire','Cameroun','Madagascar','Mali','Guinée','Congo','Gabon','Togo','Bénin','Burkina Faso','Niger','Tchad','Mauritanie','Autre'];
            $selPays = $_POST['saisie_pays'] ?? 'France';
            foreach ($pays_liste as $p) {
                $sel = ($p === $selPays) ? ' selected' : '';
                echo "<option value=\"" . htmlspecialchars($p) . "\"$sel>" . htmlspecialchars($p) . "</option>";
            }
            ?>
        </select>
    </div>
    <div class="fg">
        <label>URL du serveur Triade</label>
        <input type="text" value="<?php echo htmlspecialchars($urlServeur); ?>" readonly style="background:#f5f5f5;color:#888;">
    </div>
    <div class="fg full" style="margin-top:8px;padding-top:8px;border-top:1px solid #eee;">
        <label>Nom du responsable <span>*</span></label>
        <input type="text" name="nom" maxlength="30" value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>" required>
    </div>
    <div class="fg">
        <label>Prénom <span>*</span></label>
        <input type="text" name="prenom" maxlength="30" value="<?php echo htmlspecialchars($_POST['prenom'] ?? ''); ?>" required>
    </div>
    <div class="fg">
        <label>Téléphone</label>
        <input type="tel" name="tel" maxlength="15" value="<?php echo htmlspecialchars($_POST['tel'] ?? ''); ?>">
    </div>
    <div class="fg full">
        <label>Email du responsable <span>*</span></label>
        <input type="email" name="email" maxlength="60" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
    </div>
</div>
<button type="submit" name="create" value="1" class="btn-ins">Enregistrer &#8594;</button>
<a href="triade-copilot.php" style="margin-left:14px;font-size:11px;color:#666;">Annuler</a>
</form>

<?php else: ?>
<!-- Étape 0 : CGV -->
<div class="ins-step-bar">
    <span class="ins-snum active">1</span><span class="ins-slabel active">Conditions</span>
    <span class="ins-sep"></span>
    <span class="ins-snum">2</span><span class="ins-slabel">Informations</span>
    <span class="ins-sep"></span>
    <span class="ins-snum">3</span><span class="ins-slabel">Confirmation</span>
</div>

<p style="font-size:12px;color:#333;margin-bottom:8px;">
    Lisez et acceptez les conditions générales pour accéder au service TRIADE-COPILOT.
</p>

<div class="cgv-box">
<strong>CONDITIONS GÉNÉRALES — TRIADE-COPILOT</strong><br><br>

En utilisant les services proposés sur le Site, vous acceptez pleinement et sans réserve les présentes Conditions Générales de Vente. Si vous n'êtes pas d'accord avec ces Conditions Générales de Vente, vous ne devez pas utiliser les services proposés sur le Site.<br><br>

<strong>1. DÉFINITIONS</strong><br><br>
Afin de garantir un fonctionnement optimal du service offert par TRIADE et pour éviter la surcharge de nos serveurs, un nombre maximum de questions/réponses journalier sera alloué à chaque membre en fonction de son niveau d'abonnement.<br><br>
Les Questions/Réponses effectuées auprès de TRIADE-COPILOT sont comptabilisées en unités appelées « tokens ». Un token représente un certain nombre de caractères qui varie en fonction de la taille et de la complexité de la requête. TRIADE se réserve le droit d'adapter le nombre de tokens alloués sans obligation de prévenir les abonnés.<br><br>

<strong>2. DURÉE ET RÉSILIATION</strong><br><br>
La durée de l'abonnement est déterminée par la formule choisie. Une annulation par mail ne peut donner lieu à un remboursement, toute période entamée est due à compter de son paiement.<br><br>

<strong>3. CONDITIONS DU SERVICE</strong><br><br>
T.R.I.A.D.E. s'engage à mettre en œuvre les moyens nécessaires pour fournir le Service avec un taux de disponibilité d'au moins 98,5 % sur un mois donné. T.R.I.A.D.E. se réserve le droit de suspendre le Service pour des raisons de maintenance avec un préavis de 2 jours ouvrés.<br><br>

<strong>4. CONDITIONS FINANCIÈRES</strong><br><br>
Pour utiliser le service, le client doit créditer son compte. Les tarifs sont définis sur https://www.triade-educ.org<br><br>

<strong>5. USAGE DU SERVICE</strong><br><br>
Le Client s'engage à faire un usage conforme aux dispositions légales. Tout abus pourra entraîner la suspension du compte sans remboursement.<br><br>

<strong>6. DONNÉES PERSONNELLES</strong><br><br>
Les données personnelles collectées sont nécessaires à la gestion des abonnements. Conformément à la réglementation, l'utilisateur dispose d'un droit d'accès, rectification et suppression en écrivant à contact@triade-educ.org.<br><br>

<strong>7. RESPONSABILITÉ</strong><br><br>
Le Site et les services sont fournis « en l'état » sans garantie d'aucune sorte. T.R.I.A.D.E. ne peut être tenu responsable des dommages directs ou indirects découlant de l'utilisation du service.
</div>

<form method="POST" action="">
<div class="cgv-accept">
    <input type="checkbox" id="cgv_ok" onchange="document.getElementById('btn_cgv').disabled = !this.checked;">
    <label for="cgv_ok">J'accepte les conditions générales d'utilisation de TRIADE-COPILOT</label>
</div>
<button type="submit" name="valide_cgv" value="1" id="btn_cgv" class="btn-ins" disabled>
    Continuer &#8594;
</button>
<a href="triade-copilot.php" style="margin-left:14px;font-size:11px;color:#666;">Annuler</a>
</form>

<?php endif; ?>

</div>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</HTML>
