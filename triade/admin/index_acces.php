<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -
 *   Site                 : http://www.triade-educ.org
 ***************************************************************************/
error_reporting(0);

include_once("../common/config.inc.php");
include_once("../common/lib_acces_inc.php");
if (file_exists("../../../common/lib_acces_inc.php")) {
    include_once("../../../common/lib_acces_inc.php");
}
include_once("./librairie_php/lib_auth_admin.php");
include_once("../common/lib_admin.php");
include_once("../common/lib_ecole.php");
include_once("./librairie_php/langue.php");
include_once("./librairie_php/lib_licence_text.php");
include_once("./librairie_php/lib_error.php");
include_once("./librairie_php/mactu.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/timezone.php");

// Initialisation du jeton CSRF
if (empty($_SESSION['csrf_admin_login'])) {
    $_SESSION['csrf_admin_login'] = bin2hex(random_bytes(32));
}

// Vérification de la licence / noaccess
$disabled = "";
$verif = "";
if (file_exists("../data/install_log/noaccess.inc")) {
    $verif = 2;
    $disabled = "disabled";
}

$ip = admin_get_ip();
$rateLimit = admin_check_rate_limit($ip);
$error_msg = "";
$success_msg = "";

// Action Annuler / Recommencer
if (isset($_GET['cancel'])) {
    unset($_SESSION['admin_auth_step']);
    unset($_SESSION['admin_temp_login']);
    unset($_SESSION['admin_temp_pass']);
    unset($_SESSION['admin_setup_pin_step1']);
    header("Location: index_acces.php");
    exit;
}

// Traitement POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_admin_login'], $token)) {
        $error_msg = "Session ou requête invalide. Veuillez réessayer.";
    } elseif ($rateLimit['blocked']) {
        $mins = ceil($rateLimit['remaining_seconds'] / 60);
        $error_msg = "Accès temporairement verrouillé suite à trop de tentatives. Réessayez dans $mins minute(s).";
    } else {
        // --- ÉTAPE 1 : Identifiant + Mot de passe ---
        if (isset($_POST['action_login'])) {
            $inputLogin = trim($_POST['login'] ?? '');
            $inputPass  = (string)($_POST['passwd'] ?? '');

            $isLoginOk = (isset($LOGIN) && $inputLogin === $LOGIN) || (isset($LOGINT) && $inputLogin === $LOGINT);
            $targetPass = (isset($LOGIN) && $inputLogin === $LOGIN) ? ($PASSWORD ?? '') : ($PASSWORDT ?? '');

            $passwordOk = admin_verify_password($inputPass, $targetPass);
            // Fallback : si la vérification principale échoue et que LOGINT/PASSWORDT existent, les essayer
            if (!$passwordOk && isset($LOGINT) && $inputLogin === $LOGINT && isset($PASSWORDT)) {
                if (admin_verify_password($inputPass, $PASSWORDT)) {
                    $passwordOk = true;
                    $targetPass = $PASSWORDT;
                }
            }

            if ($isLoginOk && $passwordOk) {
                // Re-hachage transparent si nécessaire
                if (admin_password_needs_rehash($targetPass)) {
                    $newPassHash = password_hash($inputPass, PASSWORD_DEFAULT);
                    $currentPin = $ADMIN_PIN ?? null;
                    admin_save_credentials($inputLogin, $newPassHash, $currentPin);
                    $PASSWORD = $newPassHash;
                    admin_log_security_event('PASSWORD_REHASH', 'SUCCESS', 'Mise à niveau automatique du hash mot de passe');
                }

                // Vérifier si le PIN est configuré
                if (!isset($ADMIN_PIN) || empty($ADMIN_PIN)) {
                    // PIN non configuré -> Redirection vers étape configuration PIN
                    $_SESSION['admin_auth_step'] = 'setup_pin';
                    $_SESSION['admin_temp_login'] = $inputLogin;
                } else {
                    // PIN configuré -> Vérification de l'appareil de confiance
                    if (admin_verify_trusted_device()) {
                        // Appareil reconnu -> Connexion directe
                        admin_reset_rate_limit($ip);
                        session_regenerate_id(true);
                        $_SESSION["admin1"] = "Administrateur";
                        $_SESSION["langue"] = "fr";
                        unset($_SESSION['admin_auth_step']);
                        unset($_SESSION['admin_temp_login']);
                        admin_log_security_event('LOGIN', 'SUCCESS', 'Connexion directe (Appareil de confiance reconnu)');
                        header("Location: index1.php");
                        exit;
                    } else {
                        // Appareil non reconnu -> Demande du PIN
                        $_SESSION['admin_auth_step'] = 'verify_pin';
                        $_SESSION['admin_temp_login'] = $inputLogin;
                    }
                }
            } else {
                $rec = admin_record_failed_attempt($ip, 'Mot de passe incorrect pour ' . htmlspecialchars($inputLogin));
                if ($rec['blocked']) {
                    $mins = ceil($rec['remaining_seconds'] / 60);
                    $error_msg = "Trop de tentatives échouées. Accès bloqué pendant $mins minute(s).";
                } else {
                    $restants = 5 - $rec['attempts'];
                    $error_msg = "Identifiant ou mot de passe incorrect. ($restants essai(s) restant(s))";
                }
            }
        }

        // --- ÉTAPE 2A : Configuration initiale du PIN ---
        elseif (isset($_POST['action_setup_pin'])) {
            if (($_SESSION['admin_auth_step'] ?? '') !== 'setup_pin') {
                header("Location: index_acces.php");
                exit;
            }

            $pin = (string)($_POST['pin_code'] ?? '');
            $pinConfirm = (string)($_POST['pin_confirm'] ?? '');

            if (!preg_match('/^[0-9]{6}$/', $pin)) {
                $error_msg = "Le code PIN doit comporter exactement 6 chiffres.";
            } elseif ($pin !== $pinConfirm) {
                $error_msg = "La confirmation du code PIN ne correspond pas.";
            } else {
                $newPinHash = password_hash($pin, PASSWORD_DEFAULT);
                $login = $_SESSION['admin_temp_login'] ?? $LOGIN;
                admin_save_credentials($login, $PASSWORD, $newPinHash);

                if (!empty($_POST['trust_device'])) {
                    admin_create_trusted_device();
                }

                admin_reset_rate_limit($ip);
                session_regenerate_id(true);
                $_SESSION["admin1"] = "Administrateur";
                $_SESSION["langue"] = "fr";
                unset($_SESSION['admin_auth_step']);
                unset($_SESSION['admin_temp_login']);
                admin_log_security_event('PIN_SETUP', 'SUCCESS', 'Code PIN initial configuré avec succès');
                header("Location: index1.php");
                exit;
            }
        }

        // --- ÉTAPE 2B : Vérification du Code PIN ---
        elseif (isset($_POST['action_verify_pin'])) {
            if (($_SESSION['admin_auth_step'] ?? '') !== 'verify_pin') {
                header("Location: index_acces.php");
                exit;
            }

            $pin = (string)($_POST['pin_code'] ?? '');
            if (admin_verify_pin($pin, $ADMIN_PIN ?? '')) {
                if (!empty($_POST['trust_device'])) {
                    admin_create_trusted_device();
                }

                admin_reset_rate_limit($ip);
                session_regenerate_id(true);
                $_SESSION["admin1"] = "Administrateur";
                $_SESSION["langue"] = "fr";
                unset($_SESSION['admin_auth_step']);
                unset($_SESSION['admin_temp_login']);
                admin_log_security_event('LOGIN_2FA', 'SUCCESS', 'Authentification forte PIN validée');
                header("Location: index1.php");
                exit;
            } else {
                $rec = admin_record_failed_attempt($ip, 'Code PIN incorrect');
                if ($rec['blocked']) {
                    $mins = ceil($rec['remaining_seconds'] / 60);
                    $error_msg = "Trop de tentatives échouées. Accès bloqué pendant $mins minute(s).";
                } else {
                    $restants = 5 - $rec['attempts'];
                    $error_msg = "Code PIN invalide. ($restants essai(s) restant(s))";
                }
            }
        }
    }
}

// Détermination de l'étape courante
$currentStep = $_SESSION['admin_auth_step'] ?? 'login';

// Génération de l'ordre aléatoire du digicode (0 à 9)
$digicodeDigits = range(0, 9);
shuffle($digicodeDigits);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="CacheControl" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="-1">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="Copyright" content="Triade©, 2001">
<link rel="stylesheet" type="text/css" href="../librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4-2.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<link rel="shortcut icon" href="./favicon.ico">
<title>TRIADE — Authentification Administrateur</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { min-height: 100vh; background: #eef0f5; display: flex; flex-direction: column; align-items: center; justify-content: center; font-family: Electrolize, Trebuchet MS, Arial, sans-serif; padding: 20px 10px; }
.lc { background: #fff; border-radius: 12px; width: 100%; max-width: 360px; box-shadow: 0 4px 24px rgba(0,0,0,0.10); overflow: hidden; }
.lc-top { height: 6px; background: linear-gradient(90deg, #1a3a8f, #2e5fca); }
.lc-inner { padding: 28px 28px 22px; }
.lc-logo { text-align: center; margin-bottom: 20px; }
.lc-logo img { width: 65%; max-width: 190px; }
.lc-field { margin-bottom: 14px; }
.lc-field label { display: block; font-size: 11px; font-weight: 700; color: #555; margin-bottom: 5px; text-transform: uppercase; letter-spacing: .5px; }
.lc-wrap { position: relative; }
.lc-wrap i { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #bcc; font-size: 14px; pointer-events: none; }
.lc-wrap input { width: 100%; padding: 9px 12px 9px 34px; border: 1.5px solid #dde; border-radius: 8px; font-size: 13px; font-family: Electrolize, Trebuchet MS, Arial, sans-serif; color: #333; outline: none; transition: border .2s, box-shadow .2s; background: #fafbff; }
.lc-wrap input:focus { border-color: #2e5fca; box-shadow: 0 0 0 3px rgba(46,95,202,.10); }
.lc-btn { margin-top: 18px; text-align: center; }
.lc-ver { text-align: center; margin-top: 14px; font-size: 11px; color: #bbb; }
.lc-ver b { color: #999; }
.lc-alert { background: #fff8e1; border: 1px solid #ffc107; border-radius: 8px; padding: 10px 13px; margin-bottom: 14px; font-size: 12px; color: #795548; display: flex; align-items: flex-start; gap: 8px; line-height: 1.4; }
.lc-alert i { color: #ffc107; margin-top: 1px; flex-shrink: 0; }
.lc-error { background: #ffebee; border: 1px solid #ffcdd2; border-radius: 8px; padding: 10px 13px; margin-bottom: 14px; font-size: 12px; color: #c62828; display: flex; align-items: flex-start; gap: 8px; line-height: 1.4; }
.lc-error i { color: #c62828; margin-top: 1px; flex-shrink: 0; }
.lc-info { background: #e8f0fe; border: 1px solid #c2e0ff; border-radius: 8px; padding: 10px 13px; margin-bottom: 14px; font-size: 12px; color: #1a3a8f; display: flex; align-items: flex-start; gap: 8px; line-height: 1.4; }
.lc-info i { color: #1a3a8f; margin-top: 1px; flex-shrink: 0; }
.lc-footer { margin-top: 22px; text-align: center; color: #aaa; font-size: 11px; }
.lc-footer img { opacity: .4; vertical-align: middle; margin: 0 3px; }
.lc-footer a img { opacity: .45; }
</style>
</head>
<body>

<div class="lc">
  <div class="lc-top"></div>
  <div class="lc-inner">
    <div class="lc-logo">
      <img src="../image/commun/logo_triade_licence.png" alt="TRIADE">
    </div>

    <?php if ($verif == "2"): ?>
    <div class="lc-alert">
      <i class="bi bi-exclamation-triangle-fill"></i>
      <span><b><?php print LANGDEPART3bis; ?></b> <?php print LANGDEPART4bis; ?></span>
    </div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
    <div class="lc-error">
      <i class="bi bi-shield-slash-fill"></i>
      <span><?php echo htmlspecialchars($error_msg); ?></span>
    </div>
    <?php endif; ?>

    <?php if ($rateLimit['blocked']): ?>
    <div class="lc-error">
      <i class="bi bi-lock-fill"></i>
      <span><b>Sécurité :</b> Compte temporairement verrouillé.<br>Réessayez dans <?php echo ceil($rateLimit['remaining_seconds']/60); ?> minute(s).</span>
    </div>
    <div class="lc-btn">
      <a href="index_acces.php" class="btn-retour" style="display:inline-block;padding:8px 16px;text-decoration:none;">Actualiser</a>
    </div>

    <?php elseif ($currentStep === 'login'): ?>
    <!-- ═══════════════════════════════════════════════════════
         ÉTAPE 1 : Identifiant + Mot de passe
         ═══════════════════════════════════════════════════════ -->
    <form method="post" action="index_acces.php">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_admin_login']); ?>">
      <input type="hidden" name="action_login" value="1">

      <div class="lc-field">
        <label for="login">Login</label>
        <div class="lc-wrap">
          <i class="bi bi-person"></i>
          <input type="text" id="login" name="login" autocomplete="username" required autofocus>
        </div>
      </div>

      <div class="lc-field">
        <label for="passwd">Mot de passe</label>
        <div class="lc-wrap">
          <i class="bi bi-lock"></i>
          <input type="password" id="passwd" name="passwd" autocomplete="current-password" required>
        </div>
      </div>

      <div class="lc-btn">
        <script language="JavaScript">buttonMagicSubmitAtt("Connexion","create","<?php print $disabled ?>"); </script>
      </div>
    </form>

    <?php elseif ($currentStep === 'setup_pin'): ?>
    <!-- ═══════════════════════════════════════════════════════
         ÉTAPE 2A : Configuration initiale du Code PIN
         ═══════════════════════════════════════════════════════ -->
    <div class="lc-info">
      <i class="bi bi-shield-check"></i>
      <span><b>Première configuration :</b> Veuillez définir votre code PIN à 6 chiffres pour sécuriser ce compte.</span>
    </div>

    <form method="post" action="index_acces.php" id="form-setup-pin">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_admin_login']); ?>">
      <input type="hidden" name="action_setup_pin" value="1">
      <input type="hidden" name="pin_code" id="pin_code" value="">
      <input type="hidden" name="pin_confirm" id="pin_confirm" value="">

      <div style="text-align:center;margin-bottom:8px;">
        <span id="pin_phase_title" style="font-size:12px;font-weight:700;color:#1a3a8f;">1. Saisissez votre code PIN (6 chiffres)</span>
      </div>

      <div class="triade-digicode">
        <div class="digicode-dots" id="digicode-dots">
          <div class="digicode-dot" id="dot-0"></div>
          <div class="digicode-dot" id="dot-1"></div>
          <div class="digicode-dot" id="dot-2"></div>
          <div class="digicode-dot" id="dot-3"></div>
          <div class="digicode-dot" id="dot-4"></div>
          <div class="digicode-dot" id="dot-5"></div>
        </div>

        <div class="digicode-grid">
          <?php for ($k = 0; $k < 10; $k++): ?>
          <button type="button" class="digicode-btn" onclick="digicodePress(<?php echo $digicodeDigits[$k]; ?>)">
            <?php echo $digicodeDigits[$k]; ?>
          </button>
          <?php endfor; ?>
          <button type="button" class="digicode-btn digicode-btn-action" onclick="digicodeBackspace()" title="Effacer le dernier chiffre">
            <i class="bi bi-backspace"></i>
          </button>
          <button type="button" class="digicode-btn digicode-btn-action" onclick="digicodeClear()" title="Réinitialiser la saisie">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <label class="digicode-checkbox-wrap" style="margin-top:12px;">
          <input type="checkbox" name="trust_device" value="1" checked>
          <span>Mémoriser cet appareil pendant 30 jours</span>
        </label>

        <p class="digicode-notice">
          <i class="bi bi-keyboard"></i> Saisie par pavé virtuel sécurisé
        </p>
      </div>

      <div style="display:flex;justify-content:space-between;margin-top:12px;">
        <a href="index_acces.php?cancel=1" class="btn-retour" style="text-decoration:none;padding:6px 12px;font-size:11px;">Annuler</a>
        <button type="button" id="btn-submit-setup" class="btn-primary" style="padding:6px 14px;font-size:12px;" disabled onclick="validateSetupStep()">Suivant</button>
      </div>
    </form>

    <script>
    var currentPin = "";
    var firstPin = "";
    var phase = 1; // 1 = saisie, 2 = confirmation

    function updateDots() {
        for (var i = 0; i < 6; i++) {
            var dot = document.getElementById('dot-' + i);
            if (dot) {
                if (i < currentPin.length) {
                    dot.classList.add('filled');
                } else {
                    dot.classList.remove('filled');
                }
            }
        }
        var btn = document.getElementById('btn-submit-setup');
        if (btn) {
            btn.disabled = (currentPin.length !== 6);
        }
    }

    function digicodePress(digit) {
        if (currentPin.length < 6) {
            currentPin += digit;
            updateDots();
            if (currentPin.length === 6 && phase === 1) {
                setTimeout(validateSetupStep, 250);
            } else if (currentPin.length === 6 && phase === 2) {
                setTimeout(validateSetupStep, 250);
            }
        }
    }

    function digicodeBackspace() {
        if (currentPin.length > 0) {
            currentPin = currentPin.slice(0, -1);
            updateDots();
        }
    }

    function digicodeClear() {
        currentPin = "";
        updateDots();
    }

    function validateSetupStep() {
        if (currentPin.length !== 6) return;
        if (phase === 1) {
            firstPin = currentPin;
            document.getElementById('pin_code').value = firstPin;
            currentPin = "";
            phase = 2;
            document.getElementById('pin_phase_title').innerHTML = "2. Confirmez votre code PIN (6 chiffres)";
            document.getElementById('btn-submit-setup').innerHTML = "Enregistrer";
            updateDots();
        } else if (phase === 2) {
            document.getElementById('pin_confirm').value = currentPin;
            if (currentPin !== firstPin) {
                alert("La confirmation ne correspond pas au premier code PIN saisi.");
                currentPin = "";
                phase = 1;
                firstPin = "";
                document.getElementById('pin_phase_title').innerHTML = "1. Saisissez votre code PIN (6 chiffres)";
                document.getElementById('btn-submit-setup').innerHTML = "Suivant";
                updateDots();
                return;
            }
            document.getElementById('form-setup-pin').submit();
        }
    }
    </script>

    <?php elseif ($currentStep === 'verify_pin'): ?>
    <!-- ═══════════════════════════════════════════════════════
         ÉTAPE 2B : Saisie du Code PIN (Appareil non reconnu)
         ═══════════════════════════════════════════════════════ -->
    <div class="lc-info">
      <i class="bi bi-shield-lock-fill"></i>
      <span><b>Authentification forte :</b> Saisissez votre code PIN à 6 chiffres.</span>
    </div>

    <form method="post" action="index_acces.php" id="form-verify-pin">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_admin_login']); ?>">
      <input type="hidden" name="action_verify_pin" value="1">
      <input type="hidden" name="pin_code" id="pin_code_verify" value="">

      <div class="triade-digicode">
        <div class="digicode-dots" id="digicode-dots-v">
          <div class="digicode-dot" id="vdot-0"></div>
          <div class="digicode-dot" id="vdot-1"></div>
          <div class="digicode-dot" id="vdot-2"></div>
          <div class="digicode-dot" id="vdot-3"></div>
          <div class="digicode-dot" id="vdot-4"></div>
          <div class="digicode-dot" id="vdot-5"></div>
        </div>

        <div class="digicode-grid">
          <?php for ($k = 0; $k < 10; $k++): ?>
          <button type="button" class="digicode-btn" onclick="digicodeVerifyPress(<?php echo $digicodeDigits[$k]; ?>)">
            <?php echo $digicodeDigits[$k]; ?>
          </button>
          <?php endfor; ?>
          <button type="button" class="digicode-btn digicode-btn-action" onclick="digicodeVerifyBackspace()" title="Effacer le dernier chiffre">
            <i class="bi bi-backspace"></i>
          </button>
          <button type="button" class="digicode-btn digicode-btn-action" onclick="digicodeVerifyClear()" title="Réinitialiser la saisie">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <label class="digicode-checkbox-wrap">
          <input type="checkbox" name="trust_device" value="1" checked>
          <span>Mémoriser cet appareil pendant 30 jours</span>
        </label>

        <p class="digicode-notice">
          <i class="bi bi-keyboard"></i> Saisie par pavé virtuel sécurisé
        </p>
      </div>

      <div style="display:flex;justify-content:space-between;margin-top:14px;">
        <a href="index_acces.php?cancel=1" class="btn-retour" style="text-decoration:none;padding:6px 12px;font-size:11px;">Changer de compte</a>
        <button type="button" id="btn-submit-verify" class="btn-primary" style="padding:6px 16px;font-size:12px;" disabled onclick="document.getElementById('form-verify-pin').submit()">Valider</button>
      </div>
    </form>

    <script>
    var currentVerifyPin = "";

    function updateVerifyDots() {
        for (var i = 0; i < 6; i++) {
            var dot = document.getElementById('vdot-' + i);
            if (dot) {
                if (i < currentVerifyPin.length) {
                    dot.classList.add('filled');
                } else {
                    dot.classList.remove('filled');
                }
            }
        }
        var btn = document.getElementById('btn-submit-verify');
        if (btn) {
            btn.disabled = (currentVerifyPin.length !== 6);
        }
        document.getElementById('pin_code_verify').value = currentVerifyPin;
    }

    function digicodeVerifyPress(digit) {
        if (currentVerifyPin.length < 6) {
            currentVerifyPin += digit;
            updateVerifyDots();
            if (currentVerifyPin.length === 6) {
                setTimeout(function() {
                    document.getElementById('form-verify-pin').submit();
                }, 200);
            }
        }
    }

    function digicodeVerifyBackspace() {
        if (currentVerifyPin.length > 0) {
            currentVerifyPin = currentVerifyPin.slice(0, -1);
            updateVerifyDots();
        }
    }

    function digicodeVerifyClear() {
        currentVerifyPin = "";
        updateVerifyDots();
    }
    </script>
    <?php endif; ?>

    <div class="lc-ver">Version : <b><?php print VERSION ?></b></div>
  </div>
</div>

<div class="lc-footer">
  <?php print PIEDPAGE ?>
  <br><br>
  <img src="../image/commun/triade-xhtml.jpg" alt="XHTML">
  <img src="../image/commun/triade-w3C.jpg" alt="w3C">
  <img src="../image/commun/triade-css.png" alt="css">
  <a href="https://www.triade-educ.org/fr/donation.php" target="_blank">
    <img src="../image/commun/triade_paypal.png" alt="Paypal">
  </a>
</div>

</body>
</html>
