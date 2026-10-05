<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: test_ftp.php,v 1.12.2.1.2.2 2025/05/20 14:00:08 qvarin Exp $

use Pmb\Security\Library\Auth;

global $base_path, $include_path, $class_path, $javascript_path, $styles_path;
global $lang, $charset;
global $helpdir, $stylesheet;
global $url, $user, $password, $chemin;

$base_path = "../../..";

require_once "../../../includes/error_report.inc.php";
require_once "../../../includes/global_vars.inc.php";
require_once "../../../includes/config.inc.php";

$include_path = $base_path . "/" . $include_path;
$class_path = $base_path . "/" . $class_path;
$javascript_path = $base_path . "/" . $javascript_path;
$styles_path = $base_path . "/" . $styles_path;

require $include_path . "/db_param.inc.php";
require $include_path . "/mysql_connect.inc.php";
// connection MySQL
$dbh = connection_mysql();

// Chargement de l'autoload des librairies externes
require_once $base_path . '/vendor/autoload.php';
// Chargement de l'autoload back-office
require_once $class_path . "/autoloader/classLoader.class.php";
$al = classLoader::getInstance();
$al->register();

include $include_path . "/error_handler.inc.php";
include $include_path . "/sessions.inc.php";
include $include_path . "/misc.inc.php";

// Test d'une connexion ftp
require_once "api.inc.php";
if (! checkUser('PhpMyBibli', ADMINISTRATION_AUTH)) {

    // localisation (fichier XML) (valeur par défaut)
    $messages = new XMLlist($include_path . "/messages/" . $lang . ".xml", 0);
    $messages->analyser();
    $msg = $messages->table;

    $css_links  = HtmlHelper::getInstance()->getStyle($stylesheet);
    require_once $include_path . "/user_error.inc.php";
    $body_content = error_message(htmlentities($msg[11], ENT_QUOTES, $charset), htmlentities($msg[12], ENT_QUOTES, $charset), 1, '', true);

    header ("Content-Type: text/html; charset=".$charset);
    echo "<!DOCTYPE html>
            <html>
                <head>
                    <meta charset=\"" . $charset . "\" />
                    <meta http-equiv='Pragma' content='no-cache'>
                    <meta http-equiv='Cache-Control' content='no-cache'>" .
                    $css_links .
                "</head>
                <body>" .
                    $body_content .
                "</body>
    </html>";
    exit();
} else {
	$auth_instance = Auth::getInstance();
	if ($auth_instance->isInBlackList()) {
		header('Location: ./logout.php', true, 302);
		exit();
	}
}

if (defined('SESSlang') && SESSlang) {
    $lang = SESSlang;
    $helpdir = $lang;
}
// localisation (fichier XML)
$messages = new XMLlist($include_path . "/messages/" . $lang . ".xml", 0);
$messages->analyser();
$msg = $messages->table;

$css_links  = HtmlHelper::getInstance()->getStyle($stylesheet);

header ("Content-Type: text/html; charset=".$charset);
echo "<!DOCTYPE html>
    <html>
        <head>
            <meta charset=\"" . $charset . "\" />
            <meta http-equiv='Pragma' content='no-cache'>
            <meta http-equiv='Cache-Control' content='no-cache'>" .
            $css_links .
        "</head>
        <body>
            <span class='center'><small><b>".htmlentities($msg["sauv_ftp_test_running"], ENT_QUOTES, $charset)."</b></small></span>
            <span class='center'><img src='connect.gif'></span>";
flush();
$msg_ = "";
$chemin = (! empty($chemin)) ? $chemin : "/";
$conn_id = connectFtp($url, $user, $password, $chemin, $msg_);
if ($conn_id != "") {
    $msg_ = $msg["sauv_ftp_test_succeed"];
}
echo        "<script>alert(\"$msg_\"); self.close();</script>";
echo    "</body>
    </html>";

