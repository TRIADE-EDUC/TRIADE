<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main_mfa.php,v 1.2.4.2 2025/05/20 14:00:08 qvarin Exp $

// définition du minimum nécéssaire

use Pmb\Common\Helper\MySQL;
use Pmb\MFA\Controller\MFAMailController;
use Pmb\MFA\Controller\MFAOtpController;
use Pmb\Security\Library\Auth;

$base_path = ".";
$base_auth = "";
$base_title = "\$msg[308]";
$base_noheader = 1;
$base_nocheck = 1;

global $include_path, $msg, $charset;
global $database, $password, $user, $action, $otp;
global $security_mfa_active, $pmb_indexation_must_be_initialized;
global $no_check_db_version;

require_once "$base_path/includes/init.inc.php";

if (file_exists("$include_path/external_admin_auth.inc.php")) {
    // MFA incompatible avec une authentification externe
    http_response_code(403);
    echo json_encode(['success' => false, 'reason' => 'external auth']);
    pmb_mysql_close();
    exit();
}

$auth_instance = Auth::getInstance($user);
if ($auth_instance->isInBlackList()) {
    http_response_code(403);
    echo json_encode([ 'success' => false, 'reason' => 'unauthorized' ]);
    pmb_mysql_close();
    exit();
}

$user = empty($user) ? '' : $user;
$password = empty($password) ? '' : $password;

function authenticateUser($user, $password) {
    $valid_user = 0;
    $dbuser = null;

    $auth_instance = Auth::getInstance($user);
    if ($auth_instance->isAuthorized()) {
        // Vérification que l'utilisateur existe dans PMB
        $query = "SELECT userid, username FROM users WHERE username='$user'";
        $result = pmb_mysql_query($query);

        if (pmb_mysql_num_rows($result)) {
            //Récupération du mot de passe
            $dbuser = pmb_mysql_fetch_object($result);

            $query = "SELECT count(1) FROM users WHERE username='$user' AND pwd='" . MySQL::password($password) . "'";
            $result = pmb_mysql_query($query);
            $valid_user = pmb_mysql_result($result, 0, 0);
        }
    }

    // Enregistrement acces
    if ($auth_instance->isAuthorized()) {
        if ($valid_user) {
            $auth_instance->logAttempt(true);
        } else {
            $auth_instance->logAttempt(false);
        }
    }

    return [$dbuser, $valid_user];
}

function fetchMFAFromUserid($userid) {
    global $security_mfa_active;

    if (!$security_mfa_active) {
        return false;
    }

    // On regarde si l'utilisateur a initialisé sa double authentification
    $query = "SELECT mfa_secret_code, mfa_favorite, user_email FROM users WHERE userid='$userid'";
    $result = pmb_mysql_query($query);
    if (pmb_mysql_num_rows($result)) {
        $row = pmb_mysql_fetch_object($result);
        if (!empty($row->mfa_secret_code)) {
            return [
                'id' => $userid,
                'email' => $row->user_email,
                'favorite' => $row->mfa_favorite,
                'secret_code' => $row->mfa_secret_code
            ];
        }
    }

    return false;
}

switch ($action) {
    case 'login':
        [$dbuser, $valid_user] = authenticateUser($user, $password);
        $resultMFA = [ 'active' => false ];

        if ($valid_user == 1) {
            $mfa_service = (new Pmb\MFA\Controller\MFAServicesController())->getData('GESTION');
            if ($mfa_service->application && isset($dbuser)) {
                $userData = fetchMFAFromUserid($dbuser->userid);
                if (false !== $userData) {
                    $resultMFA['active'] = true;
                    $resultMFA['user'] = $user;
                    $resultMFA['has_email'] = !empty($userData['email']);
                    $resultMFA['favorite'] = $userData['favorite'];
                }
            }
        }

        if (1 == $valid_user) {
            if (false === $resultMFA['active']) {
                // Cas utilisateur sans MFA, on lui définis donc les cookies
                // et on lance la session
                startSession('PhpMyBibli', $user, $database);
            }
            echo json_encode([ 'success' => true,  'mfa' => $resultMFA ]);
        } else {
            echo json_encode([ 'success' => false]);
        }
        break;

    case 'check_otp':
        [$dbuser, $valid_user] = authenticateUser($user, $password);
        if (1 != $valid_user) {
            http_response_code(401);
            echo json_encode(['success' => false, 'reason' => 'not logged in']);
            break;
        }

        if (!isset($otp) || empty($otp) || !is_string($otp)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'reason' => 'invalid otp']);
            break;
        }


        $userData = fetchMFAFromUserid($dbuser->userid);
        if (false === $userData) {
            http_response_code(400);
            echo json_encode(['success' => false, 'reason' => 'invalid user or mfa not initialized']);
            break;
        }

        $mfa_otp = (new MFAOtpController())->getData('GESTION');
        $otp_valid = false;

        $mfa_totp = new mfa_totp();
        $mfa_totp->set_hash_method("sha1");
        $mfa_totp->set_length_code(10);

        if ($mfa_totp->check_totp_reset_code(base32_upper_decode($userData['secret_code']), $otp)) {
            $request = "UPDATE users SET mfa_secret_code = NULL, mfa_favorite = NULL WHERE userid = " . $_SESSION['authentication']['userid'];
            pmb_mysql_query($request);
            $otp_valid = true;
        } else {
            // On regarde si le code de sécurité est valide
            $mfa_totp->set_hash_method($mfa_otp->hashMethod);
            $mfa_totp->set_life_time($mfa_otp->lifetime);
            $mfa_totp->set_length_code($mfa_otp->lengthCode);

            if ($mfa_totp->check_totp(base32_upper_decode($userData['secret_code']), $otp)) {
                $otp_valid = true;
            }
        }

        if ($otp_valid) {
            // Cas utilisateur avec MFA et le code otp est correct, on lui définis donc les cookies
            // et on lance la session
            startSession('PhpMyBibli', $user, $database);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'reason' => 'invalid otp']);
        }
        break;

    case 'send_otp':
        [$dbuser, $valid_user] = authenticateUser($user, $password);
        if (1 != $valid_user) {
            http_response_code(401);
            echo json_encode(['success' => false, 'reason' => 'not logged in']);
            break;
        }

        $userData = fetchMFAFromUserid($dbuser->userid);
        if (false === $userData) {
            http_response_code(403);
            echo json_encode(['success' => false, 'reason' => 'invalid user or mfa not initialized']);
            break;
        }

        $mfa_mail = (new MFAMailController())->getData('GESTION');

        $lang = user::get_param($dbuser->userid, "user_lang");
        $messages = new XMLlist("$include_path/messages/$lang.xml", 0);
        $messages->analyser();
        $msg = $messages->table;

        $mail_user = mail_user_mfa::get_instance();
        $mail_user->set_mfa_mail($mfa_mail);
        $mail_user->set_mail_to_id($dbuser->userid);

        if ($mail_user->send_mail()) {
            echo json_encode(['success' => true, 'message' => $msg['mfa_login_notify_mail']]);
        } else {
            echo json_encode(['success' => false, 'message' => $msg['mfa_error_mail']]);
        }
        break;

    default:
        http_response_code(404);
        break;
}

pmb_mysql_close();
