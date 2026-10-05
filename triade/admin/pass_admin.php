<?php
session_start();
if (empty($_SESSION["admin1"])) {
    print "<script>location.href='./acces_refuse.php'</script>"; exit;
}
error_reporting(0);
include_once("../common/lib_admin.php");
include_once("../common/lib_ecole.php");
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");
include_once("./librairie_php/lib_auth_admin.php");
include("../common/lib_acces_inc.php");

// Initialisation du jeton CSRF
if (empty($_SESSION['csrf_admin_pass'])) {
    $_SESSION['csrf_admin_pass'] = bin2hex(random_bytes(32));
}

$erreur = "";
$succes = "";
$activeTab = "pass";

// Traitement POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_admin_pass'], $token)) {
        $erreur = "Session ou requête invalide. Veuillez réessayer.";
    } else {
        // --- 1. MODIFICATION DU MOT DE PASSE ---
        if (isset($_POST["action_pass"])) {
            $activeTab = "pass";
            $ancien  = (string)($_POST["saisie_ancien"] ?? '');
            $nouveau = (string)($_POST["saisie_new"] ?? '');
            $conf    = (string)($_POST["saisie_renew"] ?? '');

            if (!admin_verify_password($ancien, $PASSWORD)) {
                $erreur = "L'ancien mot de passe est incorrect.";
            } elseif (empty($nouveau)) {
                $erreur = "Le nouveau mot de passe ne peut pas être vide.";
            } elseif ($nouveau !== $conf) {
                $erreur = "La confirmation du nouveau mot de passe ne correspond pas.";
            } else {
                $newHash = password_hash($nouveau, PASSWORD_DEFAULT);
                $pinHash = $ADMIN_PIN ?? null;
                admin_save_credentials($LOGIN, $newHash, $pinHash);
                $PASSWORD = $newHash;

                $cnx = cnx();
                if (function_exists('modifpassedokeos')) modifpassedokeos($nouveau);
                history_cmd("Admin Triade", "MODIF", "Mot de passe Administrateur Triade");
                if ($cnx) Pgclose($cnx);

                admin_log_security_event('PASSWORD_CHANGE', 'SUCCESS', 'Modification du mot de passe administrateur');
                $succes = "Mot de passe administrateur modifié avec succès.";
            }
        }

        // --- 2. MODIFICATION DU CODE PIN ---
        elseif (isset($_POST["action_pin"])) {
            $activeTab = "pin";
            $pwdCheck = (string)($_POST["pin_admin_pwd"] ?? '');
            $newPin   = (string)($_POST["saisie_new_pin"] ?? '');
            $confPin  = (string)($_POST["saisie_renew_pin"] ?? '');

            if (!admin_verify_password($pwdCheck, $PASSWORD)) {
                $erreur = "Mot de passe administrateur incorrect pour valider la modification du PIN.";
            } elseif (!preg_match('/^[0-9]{6}$/', $newPin)) {
                $erreur = "Le code PIN doit comporter exactement 6 chiffres.";
            } elseif ($newPin !== $confPin) {
                $erreur = "La confirmation du code PIN ne correspond pas.";
            } else {
                $newPinHash = password_hash($newPin, PASSWORD_DEFAULT);
                admin_save_credentials($LOGIN, $PASSWORD, $newPinHash);
                $ADMIN_PIN = $newPinHash;

                history_cmd("Admin Triade", "MODIF", "Code PIN Administrateur Triade");
                admin_log_security_event('PIN_CHANGE', 'SUCCESS', 'Modification du code PIN administrateur');
                $succes = "Code PIN administrateur enregistré avec succès.";
            }
        }

        // --- 3. RÉVOCATION DES APPAREILS DE CONFIANCE ---
        elseif (isset($_POST["action_revoke_devices"])) {
            $activeTab = "devices";
            admin_revoke_all_trusted_devices();
            history_cmd("Admin Triade", "REVOKE", "Révocation des appareils de confiance");
            $succes = "Tous les appareils de confiance ont été révoqués. Une authentification par code PIN sera requise lors de votre prochaine connexion.";
        }
    }
}

$trustedDevices = admin_load_trusted_devices();
$activeDevicesCount = 0;
$now = time();
foreach ($trustedDevices as $d) {
    if (isset($d['expires']) && $d['expires'] > $now) {
        $activeDevicesCount++;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="CacheControl" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="-1">
<link rel="stylesheet" type="text/css" href="../librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4-2.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<title>Triade — Sécurité et accès administrateur</title>
<style>
* { box-sizing: border-box; }
body { margin: 0; padding: 14px; background: #f0f2fa; font-family: Electrolize, Arial, sans-serif; }
.pw-wrap { max-width: 440px; margin: 0 auto; }
.tab-header { display: flex; gap: 4px; margin-bottom: 12px; }
.tab-btn { flex: 1; padding: 8px 10px; border: 1px solid #c5cae9; background: #e8eaf6; color: #1a3a8f; border-radius: 6px; font-weight: 700; font-size: 12px; cursor: pointer; font-family: Electrolize, Arial, sans-serif; transition: all .15s; }
.tab-btn.active { background: #1a3a8f; color: #fff; border-color: #1a3a8f; }
.tab-pane { display: none; }
.tab-pane.active { display: block; }
</style>
</head>
<body>
<div class="pw-wrap">

  <div class="tab-header">
    <button type="button" class="tab-btn <?php echo ($activeTab === 'pass') ? 'active' : ''; ?>" onclick="switchTab('pass', this)">
      <i class="bi bi-key-fill"></i> Mot de passe
    </button>
    <button type="button" class="tab-btn <?php echo ($activeTab === 'pin') ? 'active' : ''; ?>" onclick="switchTab('pin', this)">
      <i class="bi bi-shield-lock-fill"></i> Code PIN
    </button>
    <button type="button" class="tab-btn <?php echo ($activeTab === 'devices') ? 'active' : ''; ?>" onclick="switchTab('devices', this)">
      <i class="bi bi-laptop"></i> Appareils (<?php echo $activeDevicesCount; ?>)
    </button>
  </div>

  <?php if (!empty($erreur)): ?>
  <div class="alert alert-danger" style="margin-bottom:12px;font-size:12px;">
    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($erreur); ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($succes)): ?>
  <div class="alert alert-success" style="margin-bottom:12px;font-size:12px;color:#155724;background:#d4edda;border:1px solid #c3e6cb;border-radius:6px;padding:10px;">
    <i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($succes); ?>
  </div>
  <?php endif; ?>

  <!-- ── ONGLET 1 : MOT DE PASSE ── -->
  <div id="pane-pass" class="tab-pane <?php echo ($activeTab === 'pass') ? 'active' : ''; ?>">
    <div class="card">
      <div class="card-header card-header-primary">
        <span><i class="bi bi-person-lock" style="margin-right:6px;"></i>Modifier le mot de passe administrateur</span>
      </div>
      <div class="card-body">
        <form method="post" name="form_pass">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_admin_pass']); ?>">
          <input type="hidden" name="action_pass" value="1">

          <div class="form-row">
            <label class="form-lbl">Ancien mot de passe :</label>
            <input type="password" name="saisie_ancien" class="bouton2" required autofocus>
          </div>
          <div class="form-row" style="margin-top:8px;">
            <label class="form-lbl">Nouveau mot de passe :</label>
            <input type="password" name="saisie_new" class="bouton2" required>
          </div>
          <div class="form-row" style="margin-top:8px;">
            <label class="form-lbl">Confirmer mot de passe :</label>
            <input type="password" name="saisie_renew" class="bouton2" required>
          </div>
          <div style="margin-top:14px;display:flex;gap:8px;">
            <script language="JavaScript">buttonMagicSubmit("Enregistrer","btn_save_pass");</script>
            <script language="JavaScript">buttonMagicFermeture();</script>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ── ONGLET 2 : CODE PIN ── -->
  <div id="pane-pin" class="tab-pane <?php echo ($activeTab === 'pin') ? 'active' : ''; ?>">
    <div class="card">
      <div class="card-header card-header-primary">
        <span><i class="bi bi-shield-lock-fill" style="margin-right:6px;"></i>Modifier le Code PIN (6 chiffres)</span>
      </div>
      <div class="card-body">
        <form method="post" name="form_pin">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_admin_pass']); ?>">
          <input type="hidden" name="action_pin" value="1">

          <div class="form-row">
            <label class="form-lbl">Mot de passe admin (sécurité) :</label>
            <input type="password" name="pin_admin_pwd" class="bouton2" required>
          </div>
          <div class="form-row" style="margin-top:8px;">
            <label class="form-lbl">Nouveau Code PIN (6 chiffres) :</label>
            <input type="password" name="saisie_new_pin" class="bouton2" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" placeholder="123456" required>
          </div>
          <div class="form-row" style="margin-top:8px;">
            <label class="form-lbl">Confirmer le Code PIN :</label>
            <input type="password" name="saisie_renew_pin" class="bouton2" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" placeholder="123456" required>
          </div>
          <div style="margin-top:14px;display:flex;gap:8px;">
            <script language="JavaScript">buttonMagicSubmit("Enregistrer le PIN","btn_save_pin");</script>
            <script language="JavaScript">buttonMagicFermeture();</script>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ── ONGLET 3 : APPAREILS DE CONFIANCE ── -->
  <div id="pane-devices" class="tab-pane <?php echo ($activeTab === 'devices') ? 'active' : ''; ?>">
    <div class="card">
      <div class="card-header card-header-primary">
        <span><i class="bi bi-laptop" style="margin-right:6px;"></i>Appareils de confiance enregistrés</span>
      </div>
      <div class="card-body">
        <p style="font-size:12px;color:#555;margin-bottom:12px;">
          Actuellement <b><?php echo $activeDevicesCount; ?> appareil(s) de confiance</b> peuvent se connecter sans saisir le code PIN.
        </p>

        <form method="post" onsubmit="return confirm('Voulez-vous vraiment révoquer tous les appareils de confiance ? Le code PIN sera à nouveau demandé sur tous les navigateurs.');">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_admin_pass']); ?>">
          <input type="hidden" name="action_revoke_devices" value="1">
          <button type="submit" class="btn-retour" style="color:#c62828;font-weight:700;padding:8px 14px;">
            <i class="bi bi-trash3-fill"></i> Révoquer tous les appareils
          </button>
        </form>
      </div>
    </div>
  </div>

</div>

<script>
function switchTab(tabName, btn) {
    document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
    document.querySelectorAll('.tab-pane').forEach(function(p) { p.classList.remove('active'); });
    btn.classList.add('active');
    var pane = document.getElementById('pane-' + tabName);
    if (pane) pane.classList.add('active');
}
</script>
</body>
</html>
