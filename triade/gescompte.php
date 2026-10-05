<?php
session_start();
error_reporting(0);
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -
 *   Site                 : http://www.triade-educ.com
 *
 ***************************************************************************/
?>
<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include_once("./common/config5.inc.php") ?>
    <meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
    <meta http-equiv="CacheControl" content="no-cache" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="-1" />
    <meta name="Copyright" content="Triade©, 2001" />
    <link rel="SHORTCUT ICON" href="./favicon.ico" />
    <link title="style" type="text/css" rel="stylesheet" href="./librairie_css/css.css" />
    <link title="style" type="text/css" rel="stylesheet" href="./librairie_css/video.css" />
    <link rel="stylesheet" href="./librairie_css/css-v4.css">
    <link rel="stylesheet" href="./librairie_css/css-v4-2.css">
    <link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
    <title>Triade - Compte de <?php print stripslashes("$_SESSION[nom] $_SESSION[prenom] ") ?></title>
    <script language="JavaScript" src="./librairie_js/function.js"></script>
    <script type="text/javascript" src="./librairie_js/prototype.js"></script>
    <script type="text/javascript" src="./librairie_js/scriptaculous.js"></script>
    <script>
    function changeConfGoogle(val, ideleve) {
        var divid = document.getElementById('retour');
        var myAjax = new Ajax.Request("ajaxGoogleAuthen.php", {
            method: "post",
            asynchronous: true,
            parameters: "ideleve="+ideleve+"&val="+val,
            timeout: 5000,
            onComplete: function(request) {
                divid.innerHTML = "<i style='color:#2e7d32'>&#10003; Option enregistrée.</i>";
            }
        });
        var myGlobalHandlers = {
            onLoading: function() { divid.innerHTML = '<img src="./image/temps1.gif" align="center" style="height:14px"> Sauvegarde…'; }
        };
        Ajax.Responders.register(myGlobalHandlers);
    }

    function togglePwdVisibility(inputId, btn) {
        var input = document.getElementById(inputId);
        if (!input) return;
        var icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) {
                icon.className = 'bi bi-eye-slash';
            }
        } else {
            input.type = 'password';
            if (icon) {
                icon.className = 'bi bi-eye';
            }
        }
    }

    function evaluerSoliditeMotDePasse(pwd) {
        var meter = document.getElementById('pwdMeter');
        var text = document.getElementById('pwdMeterText');
        if (!meter || !text) return;

        if (!pwd || pwd.length === 0) {
            meter.className = 'gc-pwd-meter';
            text.textContent = '<?php echo defined("LANG_CHG_PASS_STRENGTH_LABEL") ? LANG_CHG_PASS_STRENGTH_LABEL : "Solidité du mot de passe :"; ?>';
            return;
        }

        var score = 0;
        if (pwd.length >= 4) score += 10;
        if (pwd.length >= 8) score += 25;
        if (pwd.length >= 12) score += 25;
        if (pwd.length >= 16) score += 10;

        var hasLower = /[a-z]/.test(pwd);
        var hasUpper = /[A-Z]/.test(pwd);
        var hasDigit = /[0-9]/.test(pwd);
        var hasSpecial = /[^a-zA-Z0-9]/.test(pwd);

        var variete = (hasLower ? 1 : 0) + (hasUpper ? 1 : 0) + (hasDigit ? 1 : 0) + (hasSpecial ? 1 : 0);
        score += variete * 10;

        if (variete >= 3 && pwd.length >= 8) score += 10;
        if (variete === 4 && pwd.length >= 10) score += 15;

        var level = 1;
        var label = '<?php echo defined("LANG_CHG_PASS_STRENGTH_1") ? LANG_CHG_PASS_STRENGTH_1 : "Très faible"; ?>';

        if (score >= 80) {
            level = 4;
            label = '<?php echo defined("LANG_CHG_PASS_STRENGTH_4") ? LANG_CHG_PASS_STRENGTH_4 : "Robuste"; ?>';
        } else if (score >= 55) {
            level = 3;
            label = '<?php echo defined("LANG_CHG_PASS_STRENGTH_3") ? LANG_CHG_PASS_STRENGTH_3 : "Moyen"; ?>';
        } else if (score >= 30) {
            level = 2;
            label = '<?php echo defined("LANG_CHG_PASS_STRENGTH_2") ? LANG_CHG_PASS_STRENGTH_2 : "Faible"; ?>';
        }

        meter.className = 'gc-pwd-meter lvl-' + level;
        text.textContent = label;
    }

    function verifierConcordanceMdp() {
        var newPwd = document.getElementById('new_pwd');
        var confirmPwd = document.getElementById('confirm_pwd');
        var note = document.getElementById('pwdMatchNote');
        if (!newPwd || !confirmPwd || !note) return;

        if (!confirmPwd.value) {
            note.className = 'gc-note';
            note.textContent = '';
            return;
        }

        if (newPwd.value === confirmPwd.value) {
            note.className = 'gc-match-ok';
            note.textContent = '✓ Les mots de passe correspondent.';
        } else {
            note.className = 'gc-match-err';
            note.textContent = '⚠ Les mots de passe ne correspondent pas.';
        }
    }
    </script>
</head>

<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<script type="text/javascript" src="./librairie_js/lib_defil.js"></script>
<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
<script type="text/javascript" src="./librairie_js/lib_css.js"></script>
<?php
include_once("./librairie_php/lib_rss.php");
include_once("./librairie_php/lib_licence.php");
include_once("./common/productId.php");
include_once("./librairie_php/db_triade.php");
include_once("./common/config-module.php");
if (defined("PRODUCTID")) $productId = PRODUCTID;
$url = $_SERVER["SERVER_NAME"];
$cnx = cnx();
$message = "";

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

$autoriseChangementPwd = false;
if ($_SESSION['membre'] === 'menuprof' && defined('PWDPROF') && PWDPROF === 'oui') {
    $autoriseChangementPwd = true;
} elseif ($_SESSION['membre'] === 'menuparent' && defined('PWDPARENT') && PWDPARENT === 'oui') {
    $autoriseChangementPwd = true;
} elseif ($_SESSION['membre'] === 'menueleve' && defined('PWDELEVE') && PWDELEVE === 'oui') {
    $autoriseChangementPwd = true;
} elseif (in_array($_SESSION['membre'], array('menuadmin', 'menuscolaire', 'menupersonnel', 'menututeur')) && defined('VALIDPWD') && VALIDPWD === 'oui') {
    $autoriseChangementPwd = true;
}

if (isset($_POST["modif"])) {
    if (ValideMail($_POST["email"])) {
        modifEmail($_SESSION["membre"],$_SESSION["id_pers"],$_SESSION["idparent"],$_POST["email"]);
        $message = LANGPARAM16;
        $messageOk = true;
    } else {
        $message = LANGTMESS408;
        $messageOk = false;
    }
}

if (isset($_POST["modif2"])) {
    modifKeyTriade($_SESSION["membre"],$_SESSION["id_pers"],$_SESSION["idparent"],$_POST["keytriade"]);
    $message = LANGPARAM16;
    $messageOk = true;
    updateFichierIa();
}

if (isset($_POST["chg_pwd"])) {
    $postCsrf = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $postCsrf)) {
        $message = "Erreur de sécurité : session ou jeton CSRF invalide. Veuillez recharger la page.";
        $messageOk = false;
    } elseif (!$autoriseChangementPwd) {
        $message = defined('LANG_CHG_PASS_DISABLED') ? LANG_CHG_PASS_DISABLED : "La modification du mot de passe est désactivée par votre établissement.";
        $messageOk = false;
    } else {
        $oldPwd = isset($_POST['old_pwd']) ? (string)$_POST['old_pwd'] : '';
        $newPwd = isset($_POST['new_pwd']) ? (string)$_POST['new_pwd'] : '';
        $confirmPwd = isset($_POST['confirm_pwd']) ? (string)$_POST['confirm_pwd'] : '';
        $idparent = isset($_SESSION['idparent']) ? $_SESSION['idparent'] : 1;

        if ($oldPwd === '' || $newPwd === '' || $confirmPwd === '') {
            $message = defined('LANG_CHG_PASS_ERR_EMPTY') ? LANG_CHG_PASS_ERR_EMPTY : "Veuillez remplir tous les champs du mot de passe.";
            $messageOk = false;
        } elseif (!verifier_mot_de_passe_actuel($_SESSION['id_pers'], $_SESSION['membre'], $oldPwd, $idparent)) {
            $message = defined('LANG_CHG_PASS_ERR_ACTUEL') ? LANG_CHG_PASS_ERR_ACTUEL : "Le mot de passe actuel est incorrect.";
            $messageOk = false;
            $ipLog = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'inconnue';
            acceslog("ECHEC MODIFICATION MOT DE PASSE (actuel incorrect)#$ipLog#{$_SESSION['nom']}#{$_SESSION['prenom']}#{$_SESSION['membre']} ({$_SESSION['id_pers']})");
        } elseif ($newPwd !== $confirmPwd) {
            $message = defined('LANG_CHG_PASS_ERR_CONFIRM') ? LANG_CHG_PASS_ERR_CONFIRM : "Le nouveau mot de passe et sa confirmation ne correspondent pas.";
            $messageOk = false;
        } else {
            $securityOk = true;
            if (defined('SECURITE')) {
                if (SECURITE == 3) {
                    if (strlen($newPwd) < 8 || !preg_match('/[a-z]/', $newPwd) || !preg_match('/[A-Z]/', $newPwd) || !preg_match('/[0-9]/', $newPwd)) {
                        $securityOk = false;
                    }
                } elseif (SECURITE == 2) {
                    if (strlen($newPwd) < 8 || !preg_match('/[a-z]/', $newPwd) || !preg_match('/[0-9]/', $newPwd)) {
                        $securityOk = false;
                    }
                } elseif (SECURITE == 1) {
                    if (strlen($newPwd) < 4) {
                        $securityOk = false;
                    }
                }
            }

            if (!$securityOk) {
                $message = defined('LANG_CHG_PASS_ERR_SECURITY') ? LANG_CHG_PASS_ERR_SECURITY : "Le nouveau mot de passe ne respecte pas les critères de sécurité de l'établissement.";
                $messageOk = false;
            } else {
                if (isset($_SESSION['idparent']) && (string)$_SESSION['idparent'] === '2' && $_SESSION['membre'] === 'menuparent') {
                    $resUpdate = update_passwd_parent2($newPwd, $_SESSION['membre'], $_SESSION['id_pers']);
                } else {
                    $resUpdate = update_passwd($newPwd, $_SESSION['membre'], $_SESSION['id_pers']);
                }

                if ($resUpdate) {
                    $message = defined('LANG_CHG_PASS_OK') ? LANG_CHG_PASS_OK : "Votre mot de passe a été modifié avec succès.";
                    $messageOk = true;
                    $ipLog = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'inconnue';
                    acceslog("MODIFICATION MOT DE PASSE#$ipLog#{$_SESSION['nom']}#{$_SESSION['prenom']}#{$_SESSION['membre']} ({$_SESSION['id_pers']})");

                    $userEmail = recupEmail($_SESSION['membre'], $_SESSION['id_pers'], isset($_SESSION['idparent']) ? $_SESSION['idparent'] : 1);
                    if (ValideMail($userEmail)) {
                        envoiMailConfirmationModifMotDePasse($userEmail, $_SESSION['nom'], $_SESSION['prenom']);
                    }
                } else {
                    $message = defined('LANG_CHG_PASS_ERR_SECURITY') ? LANG_CHG_PASS_ERR_SECURITY : "Erreur lors de la modification du mot de passe.";
                    $messageOk = false;
                }
            }
        }
    }
}

$keytriade = recupkeytriade($_SESSION["membre"],$_SESSION["id_pers"],$_SESSION["idparent"]);

if ((defined("VATEL")) && (VATEL == 1)) {
    $email = recupEmailVatel($_SESSION["membre"],$_SESSION["id_pers"],$_SESSION["idparent"]);
} else {
    $email = recupEmail($_SESSION["membre"],$_SESSION["id_pers"],$_SESSION["idparent"]);
}

if ((isset($_GET["alerte"])) ) {
    $message = LANGTMESS409;
    $messageOk = false;
}

// Carte de visite
$nomEcole = recupSite(1) ?: '';
$profileMap = [
    'menuadmin'     => ['Administrateur',  '#7b1fa2', '#f3e5f5'],
    'menueleve'     => ['Élève',           '#1565c0', '#e3f2fd'],
    'menuprof'      => ['Professeur',      '#2e7d32', '#e8f5e9'],
    'menuscolaire'  => ['Vie scolaire',    '#e65100', '#fff3e0'],
    'menuparent'    => ['Parent',          '#00695c', '#e0f2f1'],
    'menututeur'    => ['Tuteur de stage', '#4527a0', '#ede7f6'],
    'menupersonnel' => ['Personnel',       '#37474f', '#eceff1'],
];
$profileInfo  = $profileMap[$_SESSION['membre']] ?? ['Utilisateur', '#080A66', '#e8eaf6'];
$profileLabel = $profileInfo[0];
$profileColor = $profileInfo[1];
$profileBg    = $profileInfo[2];
$dataParam     = visu_param();
$anneeScolaire = (is_array($dataParam) && !empty($dataParam)) ? trim($dataParam[0][11]) : '';
if (!$anneeScolaire) $anneeScolaire = $_COOKIE['anneeScolaire'] ?? '';
$classeInfo    = '';
$classesProf   = [];
$matieresProf  = [];
if ($_SESSION['membre'] === 'menueleve') {
    $idClasse = chercheIdClasseDunEleve($_SESSION['id_pers']);
    if ($idClasse) $classeInfo = chercheClasse_nom($idClasse);
} elseif ($_SESSION['membre'] === 'menuprof' && $anneeScolaire) {
    $dataClasses = recupClasseProf($_SESSION['id_pers'], $anneeScolaire);
    if (is_array($dataClasses)) {
        foreach ($dataClasses as $row) {
            $nom = chercheClasse_nom($row[0]);
            if ($nom) $classesProf[] = htmlspecialchars($nom);
        }
    }
    $sqlMat = "SELECT DISTINCT m.libelle FROM ".PREFIXE."matieres m
               JOIN ".PREFIXE."affectations a ON m.code_mat=a.code_matiere
               WHERE a.code_prof='".intval($_SESSION['id_pers'])."' AND a.annee_scolaire='".addslashes($anneeScolaire)."'
               ORDER BY m.libelle";
    $resMat = execSql($sqlMat);
    $dataMat = chargeMat($resMat);
    if (is_array($dataMat)) {
        foreach ($dataMat as $row) {
            if ($row[0]) $matieresProf[] = htmlspecialchars($row[0]);
        }
    }
}
$initiales = strtoupper(mb_substr($_SESSION['prenom'], 0, 1)).strtoupper(mb_substr($_SESSION['nom'], 0, 1));
$photoUrl  = '';
if ($_SESSION['membre'] === 'menueleve') {
    $pf = recherche_photo_eleve($_SESSION['id_pers']);
    if ($pf && file_exists('./data/image_eleve/'.$pf)) $photoUrl = 'image_trombi.php?idE='.(int)$_SESSION['id_pers'];
} elseif (in_array($_SESSION['membre'], ['menuprof','menuadmin','menuscolaire','menututeur','menupersonnel'])) {
    $pf = recherche_photo_pers($_SESSION['id_pers']);
    if ($pf && file_exists('./data/image_pers/'.$pf)) $photoUrl = 'image_trombi.php?idP='.(int)$_SESSION['id_pers'];
}

// Google Auth value
$val = 0;
if ($_SESSION['membre'] == "menueleve")    $val = recupValEleveGoogle($_SESSION['id_pers']);
if ($_SESSION['membre'] == "menuadmin")    $val = recupValPersGoogle($_SESSION['id_pers']);
if ($_SESSION['membre'] == "menuscolaire") $val = recupValPersGoogle($_SESSION['id_pers']);
if ($_SESSION['membre'] == "menuprof")     $val = recupValPersGoogle($_SESSION['id_pers']);
if ($_SESSION['membre'] == "menupersonnel")$val = recupValPersGoogle($_SESSION['id_pers']);
if ($_SESSION['membre'] == "menututeur")   $val = recupValPersGoogle($_SESSION['id_pers']);
if ($_SESSION['membre'] == "menuparent")   $val = recupValParentGoogle($_SESSION['id_pers'],$_SESSION["idparent"]);

$checkedA = ($val == 1) ? "checked" : "";
$checkedB = ($val != 1) ? "checked" : "";
if (defined("VERIFMAIL") && VERIFEMAIL != "non") { $onblur = " onblur='verifEmail(this)' "; } else { $onblur=""; }
if (preg_match('/demo.triade-educ.net/', WEBROOT)) $disableddemo = 'disabled';
?>

<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center' style='overflow:hidden;height:<?php echo defined("BANNIEREHAUTEUR") ? (int)BANNIEREHAUTEUR : 150; ?>px'><?php top_h(); ?></div>
<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMESS161 ?></font></b></td></tr>
<tr id='cadreCentral0'><td>
<!-- // fin  -->

<div class="gc-stack">

    <!-- Carte de visite -->
    <div class="gc-vcard" style="border-top-color:<?php echo $profileColor ?>">
        <div class="gc-vcard-avatar" style="background:<?php echo $profileColor ?>">
            <?php if ($photoUrl): ?>
            <img src="<?php echo htmlspecialchars($photoUrl) ?>" alt=""
                 onerror="this.style.display='none';this.parentNode.textContent='<?php echo $initiales ?>'">
            <?php else: ?>
            <?php echo $initiales ?>
            <?php endif; ?>
        </div>
        <div class="gc-vcard-body">
            <div class="gc-vcard-name"><?php echo htmlspecialchars(strtoupper($_SESSION['nom']).' '.ucwords(strtolower($_SESSION['prenom']))) ?></div>
            <span class="gc-vcard-badge" style="background:<?php echo $profileBg ?>;color:<?php echo $profileColor ?>"><?php echo $profileLabel ?></span>
            <?php if ($email): ?>
            <div class="gc-vcard-detail"><i class="bi bi-at gc-card-detail-icon"></i><?php echo htmlspecialchars($email) ?></div>
            <?php endif; ?>
            <?php if ($nomEcole): ?>
            <div class="gc-vcard-detail"><i class="bi bi-buildings-fill gc-card-detail-icon"></i><?php echo htmlspecialchars($nomEcole) ?></div>
            <?php endif; ?>
            <?php if ($classeInfo): ?>
            <div class="gc-vcard-detail"><i class="bi bi-person-video3 gc-card-detail-icon"></i><?php echo htmlspecialchars($classeInfo) ?></div>
            <?php endif; ?>
            <?php if ($classesProf): ?>
            <div class="gc-vcard-detail"><i class="bi bi-person-video3 gc-card-detail-icon"></i><?php echo implode(' &bull; ', $classesProf) ?></div>
            <?php endif; ?>
            <?php if ($matieresProf): ?>
            <div class="gc-vcard-detail"><i class="bi bi-book-fill gc-card-detail-icon"></i><?php echo implode(' &bull; ', $matieresProf) ?></div>
            <?php endif; ?>
            <?php if ($anneeScolaire && ($_SESSION['membre'] === 'menueleve' || $_SESSION['membre'] === 'menuprof')): ?>
            <div class="gc-vcard-detail"><i class="bi bi-calendar-range-fill gc-card-detail-icon"></i>Année <?php echo htmlspecialchars($anneeScolaire).'-'.((int)$anneeScolaire+1) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($message): ?>
    <div class="gc-msg-box <?php print (isset($messageOk) && !$messageOk) ? 'error' : '' ?>">
        <?php print $message ?>
    </div>
    <?php endif; ?>

    <!-- Email -->
    <div class="gc-card">
        <div class="gc-card-title"><i class="bi bi-at gc-card-icon"></i> Adresse e-mail</div>
        <form method="post" action="gescompte.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="gc-field-row">
                <input type="text" name="email" class="gc-input" value="<?php print htmlspecialchars($email) ?>" maxlength="250" <?php print $onblur ?> placeholder="votre@email.com">
                <button type="submit" name="modif" class="gc-btn">Enregistrer</button>
            </div>
        </form>
    </div>

    <!-- Mot de passe -->
    <div class="gc-card">
        <div class="gc-card-title"><i class="bi bi-lock gc-card-icon"></i> <?php echo defined('LANG_CHG_PASS_TITLE') ? LANG_CHG_PASS_TITLE : 'Mot de passe'; ?></div>
        <?php if ($autoriseChangementPwd): ?>
        <form method="post" action="gescompte.php" class="gc-pwd-form" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="gc-pwd-field">
                <label class="gc-label" for="old_pwd"><?php echo defined('LANG_CHG_PASS_ACTUEL') ? LANG_CHG_PASS_ACTUEL : 'Mot de passe actuel'; ?></label>
                <div class="gc-pwd-wrapper">
                    <input type="password" id="old_pwd" name="old_pwd" class="gc-input" required autocomplete="current-password" placeholder="••••••••">
                    <button type="button" class="gc-pwd-toggle" onclick="togglePwdVisibility('old_pwd', this)" title="Afficher / Masquer" aria-label="Afficher / Masquer">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="gc-pwd-field">
                <label class="gc-label" for="new_pwd"><?php echo defined('LANG_CHG_PASS_NOUVEAU') ? LANG_CHG_PASS_NOUVEAU : 'Nouveau mot de passe'; ?></label>
                <div class="gc-pwd-wrapper">
                    <input type="password" id="new_pwd" name="new_pwd" class="gc-input" required autocomplete="new-password" placeholder="••••••••" oninput="evaluerSoliditeMotDePasse(this.value)">
                    <button type="button" class="gc-pwd-toggle" onclick="togglePwdVisibility('new_pwd', this)" title="Afficher / Masquer" aria-label="Afficher / Masquer">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <!-- Jauge de solidité -->
                <div class="gc-pwd-meter" id="pwdMeter">
                    <div class="gc-pwd-bars">
                        <span class="gc-pwd-bar"></span>
                        <span class="gc-pwd-bar"></span>
                        <span class="gc-pwd-bar"></span>
                        <span class="gc-pwd-bar"></span>
                    </div>
                    <span class="gc-pwd-text" id="pwdMeterText"><?php echo defined('LANG_CHG_PASS_STRENGTH_LABEL') ? LANG_CHG_PASS_STRENGTH_LABEL : 'Solidité du mot de passe :'; ?></span>
                </div>
            </div>

            <div class="gc-pwd-field">
                <label class="gc-label" for="confirm_pwd"><?php echo defined('LANG_CHG_PASS_CONFIRM') ? LANG_CHG_PASS_CONFIRM : 'Confirmer le nouveau mot de passe'; ?></label>
                <div class="gc-pwd-wrapper">
                    <input type="password" id="confirm_pwd" name="confirm_pwd" class="gc-input" required autocomplete="new-password" placeholder="••••••••" oninput="verifierConcordanceMdp()">
                    <button type="button" class="gc-pwd-toggle" onclick="togglePwdVisibility('confirm_pwd', this)" title="Afficher / Masquer" aria-label="Afficher / Masquer">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div id="pwdMatchNote" class="gc-note"></div>
            </div>

            <div class="gc-pwd-actions">
                <button type="submit" name="chg_pwd" class="gc-btn"><?php echo defined('LANG_CHG_PASS_BTN') ? LANG_CHG_PASS_BTN : 'Modifier le mot de passe'; ?></button>
            </div>
        </form>
        <?php else: ?>
        <div class="gc-note">
            <i class="bi bi-info-circle"></i> <?php echo defined('LANG_CHG_PASS_DISABLED') ? LANG_CHG_PASS_DISABLED : 'La modification du mot de passe est désactivée par votre établissement pour votre profil.'; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Clé Triade -->
    <div class="gc-card">
        <div class="gc-card-title"><i class="bi bi-key gc-card-icon"></i> Clé-Triade</div>
        <form method="post" action="gescompte.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="gc-field-row">
                <?php if ($keytriade == ""): ?>
                    <span class="gc-key-create">Vous devez créer votre clé pour bénéficier des nouvelles actions.</span>
                    <button type="submit" name="modif2" class="gc-btn">Créer la clé</button>
                <?php else: ?>
                    <input type="text" name="keytriade" class="gc-input" value="<?php print htmlspecialchars($keytriade) ?>" maxlength="150">
                    <button type="submit" name="modif2" class="gc-btn">Enregistrer</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if ($keytriade != "" && ((defined("MODULETRIADECOACH") && MODULETRIADECOACH == "oui") || !defined('MODULETRIADECOACH'))): ?>
    <!-- Compte IA -->
    <div class="gc-card">
        <div class="gc-card-title"><i class="bi bi-robot gc-card-icon"></i> Compte IA Triade</div>
        <div style="font-size:12px;color:#555;margin-bottom:10px">Créditez votre compte IA pour pouvoir l'utiliser via Triade.</div>
        <a href="./crediter-pers.php?idkeypers=<?php print urlencode($keytriade) ?>&productid=<?php print urlencode($productId) ?>&url=<?php print urlencode($url) ?>"
            <button type="button" class="gc-btn-ia">Créditer mon compte IA</button>
        </a>
    </div>
    <?php endif; ?>

    <!-- Google Authenticator -->
    <div class="gc-card">
        <div class="gc-card-title"><i class="bi bi-shield gc-card-icon"></i> Google Authenticator
            &nbsp;<a href="#" onclick="openVideo('https://www.youtube.com/embed/J099Jh7FHgE')" style="font-size:10px;font-weight:400;color:#080A66">[Aide vidéo]</a>
        </div>
        <div class="gc-radio-group">
            <label class="gc-radio-opt on">
                <input type="radio" name="gooauthen" value="1" <?php print $checkedA ?> <?php print $disableddemo ?>
                    onclick="changeConfGoogle(this.value,<?php print $_SESSION['id_pers'] ?>)">
                Activé
            </label>
            <label class="gc-radio-opt off">
                <input type="radio" name="gooauthen" value="0" <?php print $checkedB ?>
                    onclick="changeConfGoogle(this.value,<?php print $_SESSION['id_pers'] ?>)">
                Désactivé
            </label>
        </div>
        <div id="retour" class="gc-retour"></div>
        <?php if (preg_match('/demo.triade-educ.net/', WEBROOT)): ?>
        <div class="gc-note">Cette option n'est pas activée en version démonstration.</div>
        <?php endif; ?>
    </div>

    <?php if ((defined("MODIFTROMBIELEVE")) && (MODIFTROMBIELEVE == "oui")): ?>
    <!-- Photo -->
    <div class="gc-card">
        <div class="gc-card-title"><i class="bi bi-person-circle gc-card-icon"></i> Trombinoscope</div>
        <button type="button" class="gc-btn-outline" onclick="open('photoajouteleve.php','photo','width=450,height=280')">
            Modifier ma photo
        </button>
    </div>
    <?php endif; ?>

    <?php if ($keytriade != ""):
        $index  = recupIndexViaMembre($_SESSION['membre']);
        $http   = protohttps();
        $urlical = "$http".URLSITE."ical-triade.php?ref=$keytriade&index=$index";
    ?>
    <!-- iCal -->
    <div class="gc-card">
        <div class="gc-card-title"><i class="bi bi-calendar gc-card-icon"></i> Calendrier iCal</div>
        <div style="font-size:12px;color:#555;margin-bottom:8px">Importez votre emploi du temps dans votre application calendrier.</div>
        <div class="gc-ical-row">
            <input type="text" class="gc-ical-input gc-input-readonly" value="<?php print htmlspecialchars($urlical) ?>" readonly onclick="this.select()">
            <button type="button" class="gc-btn-outline" onclick="navigator.clipboard&&navigator.clipboard.writeText('<?php print addslashes($urlical) ?>');this.textContent='✓ Copié'">Copier</button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Historique des connexions -->
    <?php
    $histoConn = getHistoriqueConnexionsMobile($_SESSION['id_pers'], $_SESSION['membre'], $_SESSION['idparent']);
    $sourceLabel = ['mobile' => '<i class="bi bi-phone"></i> Mobile', 'web' => '<i class="bi bi-globe"></i> Web'];
    ?>
    <div class="gc-card">
        <div class="gc-card-title"><i class="bi bi-shield-lock gc-card-icon"></i> Historique des connexions
            <span style="font-size:10px;font-weight:400;color:#888;margin-left:6px">(60 derniers jours)</span>
        </div>
        <?php if (empty($histoConn)): ?>
        <div style="font-size:12px;color:#888;font-style:italic">Aucune connexion enregistrée.</div>
        <?php else: ?>
        <table style="width:100%;border-collapse:collapse;font-size:11px">
            <thead>
                <tr>
                    <th style="text-align:left;padding:4px 8px;color:#080A66;border-bottom:1px solid #eef0f8">Source</th>
                    <th style="text-align:left;padding:4px 8px;color:#080A66;border-bottom:1px solid #eef0f8">Adresse IP</th>
                    <th style="text-align:left;padding:4px 8px;color:#080A66;border-bottom:1px solid #eef0f8">Dernière connexion</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($histoConn as $row): ?>
                <tr style="border-bottom:1px solid #f5f7ff">
                    <td style="padding:5px 8px;color:#555"><?php echo $sourceLabel[$row[1]] ?? htmlspecialchars($row[1]) ?></td>
                    <td style="padding:5px 8px;font-family:monospace;color:#333"><?php
                        $pays = ipToCountryCode($row[0]);
                        if ($pays) echo '<img src="https://flagcdn.com/16x12/'.strtolower($pays).'.png" alt="'.htmlspecialchars($pays).'" style="vertical-align:middle;margin-right:5px">';
                        echo htmlspecialchars($row[0]);
                    ?></td>
                    <td style="padding:5px 8px;color:#555"><?php
                        $dt = new DateTime($row[2]);
                        echo $dt->format('d/m/Y H:i');
                    ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div style="font-size:10px;color:#aaa;margin-top:8px;font-style:italic">
            <i class="bi bi-shield-check" style="color:#2e7d32"></i>
            Les IPs listées ici peuvent se reconnecter sans re-validation A2F pendant 60 jours.
        </div>
        <?php endif; ?>
    </div>

</div>

<!-- // fin  -->
</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
} else {
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
}
Pgclose();
?>
<?php video(); ?>
</BODY></HTML>
