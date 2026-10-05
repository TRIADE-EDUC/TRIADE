<?php
error_reporting(0);
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.php,v 1.53.2.2.2.2 2025/05/20 14:00:08 qvarin Exp $

// définition du minimum nécéssaire
$base_path=".";
$base_auth = "";
$base_title = "\$msg[308]";
$base_noheader=1;
$base_nocheck=1;

global $include_path, $msg, $charset;
global $password, $user, $action, $otp;
global $security_mfa_active, $pmb_indexation_must_be_initialized;
global $no_check_db_version;

use Pmb\MFA\Controller\MFAMailController;
use Pmb\Common\Helper\MySQL;
use Pmb\Security\Library\Auth;

require_once "$base_path/includes/init.inc.php";

$external_admin_auth_file_exists = false;
//Est-on déjà authentifié ?
if (!checkUser('PhpMyBibli')) {

    $valid_user = 0;

    /************** Authentification externe  *******************/
    $ext_auth_hook = 1;
    $external_admin_auth_file_exists = file_exists( "$include_path/external_admin_auth.inc.php") ;
    if ($external_admin_auth_file_exists ) {
        require "$include_path/external_admin_auth.inc.php";
    }

    if (empty($user)) {
       $user = '';
    }

    // Instanciation classe blocage acces
    $auth_instance = Auth::getInstance($user);

    /************** Authentification classique *******************/
    if (!$auth_instance->isAuthorized()) {
        // On n'est pas autorise a s'authentifier
        $valid_user = 0;
    } elseif ($valid_user != 1) {
        // Vérification que l'utilisateur existe dans PMB
        $query = "SELECT userid, username FROM users WHERE username='$user'";
        $result = pmb_mysql_query($query);

        if (pmb_mysql_num_rows($result)) {
            //Récupération du mot de passe
            $dbuser=pmb_mysql_fetch_object($result);

            /************** Authentification externe  (Vérification mot de passe hors admin uniquement) *******************/
            if ( $external_admin_auth_file_exists && ($ext_auth_hook !=0) && ($dbuser->userid !=1 ) ) {
                require "$include_path/external_admin_auth.inc.php";
            } else {
                // on checke si l'utilisateur existe et si le mot de passe est OK
                //$query = "SELECT count(1) FROM users WHERE username='$user' AND pwd=password('$password') ";
                $query = "SELECT count(1) FROM users WHERE username='$user' AND pwd='" . MySQL::password($password) . "'";
                $result = pmb_mysql_query($query);
                $valid_user = pmb_mysql_result($result, 0, 0);
            }
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
} else {
    $valid_user=2;
}

if(!$valid_user) {
    header("Location: index.php?login_error=1&user=$user");
} else {
    if ($valid_user == 1) {

        /************** Double authentification *******************/
        if($security_mfa_active && !$external_admin_auth_file_exists) {
            $mfa_service = (new Pmb\MFA\Controller\MFAServicesController())->getData('GESTION');
            if($mfa_service->application) {
                // On regarde si l'utilisateur a initialisé sa double authentification
                $query = "SELECT mfa_secret_code, mfa_favorite, user_email FROM users WHERE username='$user'";
                $result = pmb_mysql_query($query);
                if (pmb_mysql_num_rows($result)) {
                    $row = pmb_mysql_fetch_object($result);
                    if(!empty($row->mfa_secret_code)) {
                        // Si l'utilisateur a initialiser sa double authentification
                        // Il ne doit pas passer par ici, mais par main_mfa.php
                        header("Location: index.php?login_error=1");
                        return;
                    }
                }
            }
        }

        startSession('PhpMyBibli', $user, $database);
    }
}

if (defined('SESSlang') && SESSlang) {
    $lang=SESSlang;
    $helpdir = $lang;
}

// localisation (fichier XML)
$messages = new XMLlist("$include_path/messages/$lang.xml", 0);
$messages->analyser();
$msg = $messages->table;
require("$include_path/templates/common.tpl.php");
header ("Content-Type: text/html; charset=$charset");

$sphinx_message = check_sphinx_service();
if (!empty($sphinx_message)) {
    print "<script>alert('$sphinx_message')</script>";
}
if (empty($no_check_db_version)) {

    if ( (!$param_licence) || ($pmb_bdd_version != $pmb_version_database_as_it_should_be) || ($pmb_subversion_database_as_it_shouldbe != $pmb_bdd_subversion) || $pmb_bdd_subversion_error ) {

        require_once "$include_path/templates/main.tpl.php";
        print $std_header;
        print "<body class='$current_module claro' id='body_current_module' page_name='$current_module'>";
        print $menu_bar;

        print $extra;
        if($use_shortcuts) {
            include "$include_path/shortcuts/circ.sht";
        }
        print $main_layout;

        if ( $pmb_bdd_version != $pmb_version_database_as_it_should_be ) {

            echo "<h1>".$msg["pmb_v_db_pas_a_jour"]."</h1>";
            echo "<h1>".$msg[1803]."<span style='color:red'>".$pmb_bdd_version."</span></h1>";
            echo "<h1>".$msg['pmb_v_db_as_it_should_be']."<span style='color:red'>".$pmb_version_database_as_it_should_be."</span></h1>";
            echo "<a href='./admin.php?categ=alter&sub='>".$msg["pmb_v_db_mettre_a_jour"]."</a>";
            echo "<script>alert(\"".$msg["pmb_v_db_pas_a_jour"]."\\n".$pmb_version_database_as_it_should_be." <> ".$pmb_bdd_version."\");</script>";

        } elseif ( $pmb_subversion_database_as_it_shouldbe != $pmb_bdd_subversion ) {

            echo "<h1>Minor changes in database in progress...</h1>";
            include("./admin/misc/addon.inc.php");
            echo "<h1>Changes applied in database.</h1>";

        } elseif ( $pmb_bdd_subversion_error ) {
            echo "<div class='erreur' >".htmlentities( sprintf($msg['bdd_subversion_error'], $pmb_bdd_subversion_error), ENT_QUOTES, $charset)."</div>";
        }

        //On est probablement sur une première connexion à PMB
        $pmb_indexation_must_be_initialized = empty($pmb_indexation_must_be_initialized) ? 0 : intval($pmb_indexation_must_be_initialized);

        if($pmb_indexation_must_be_initialized) {
            echo "<h1>Indexation in progress...</h1>";
            flush();
            ob_flush();
            include("./admin/misc/setup_initialization.inc.php");
            echo "<h1>Indexation applied in database.</h1>";
        }

        if (!$param_licence) {
            include("$base_path/resume_licence.inc.php");
        }

        print $main_layout_end;
        print $footer;

        pmb_mysql_close($dbh);
        exit ;
    }
}

if (!isset($ret_url)) $ret_url=false;

if ($ret_url) {
    if(strpos($ret_url, 'ajax.php') !== false) {
        print "<script>document.location=\"".$_SERVER['HTTP_REFERER']."\";</script>";
        exit;
    }
    //AR - on évite une redirection vers une url absolue...
    if((strpos($ret_url, 'http://') === false) && (strpos($ret_url, 'https://') === false)) {
        print "<script>document.location=\"$ret_url\";</script>";
        exit ;
    }
}

//chargement de la première page
require_once($include_path."/misc.inc.php");

go_first_tab();

pmb_mysql_close();
