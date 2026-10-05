<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
/*

Ce logiciel est un programme informatique servant à gérer une bibliothèque
ou un centre de documentation et notamment le catalogue des ouvrages et le
fichier des lecteurs. PMB est conforme à la déclaration simplifiée de la CNIL
en ce qui concerne le respect de la Loi Informatique et Libertés applicable
en France.

Ce logiciel est régi par la licence CeCILL soumise au droit français et
respectant les principes de diffusion des logiciels libres. Vous pouvez
utiliser, modifier et/ou redistribuer ce programme sous les conditions
de la licence CeCILL telle que diffusée par le CEA, le CNRS et l'INRIA
sur le site "http://www.cecill.info".

En contrepartie de l'accessibilité au code source et des droits de copie,
de modification et de redistribution accordés par cette licence, il n'est
offert aux utilisateurs qu'une garantie limitée.  Pour les mêmes raisons,
seule une responsabilité restreinte pèse sur l'auteur du programme,  le
titulaire des droits patrimoniaux et les concédants successifs.

A cet égard  l'attention de l'utilisateur est attirée sur les risques
associés au chargement,  à l'utilisation,  à la modification et/ou au
développement et à la reproduction du logiciel par l'utilisateur étant
donné sa spécificité de logiciel libre, qui peut le rendre complexe à
manipuler et qui le réserve donc à des développeurs et des professionnels
avertis possédant  des  connaissances  informatiques approfondies.  Les
utilisateurs sont donc invités à charger  et  tester  l'adéquation  du
logiciel à leurs besoins dans des conditions permettant d'assurer la
sécurité de leurs systèmes et ou de leurs données et, plus généralement,
à l'utiliser et l'exploiter dans les mêmes conditions de sécurité.

Le fait que vous puissiez accéder à cet en-tête signifie que vous avez
pris connaissance de la licence CeCILL, et que vous en avez accepté les
termes.

 */
// +-------------------------------------------------+
// $Id: index.php,v 1.25.4.1 2025/05/20 14:00:08 qvarin Exp $

// définition du minimum nécéssaire
$base_path=".";

use Pmb\Security\Library\Auth;

include_once $base_path."/includes/error_report.inc.php";
require_once $base_path."/includes/pmb_cookie.inc.php";
include_once $base_path."/includes/config.inc.php";

if (!file_exists("$include_path/db_param.inc.php")) {
    // Pas de fichier présent, on s'assure quand même qu'il n'y a pas déjà eu une installation
    if(file_exists($base_path."/tables/install.php")){
        // Fichier d'installation présent, on renvoie dessus !
        header("Location: $base_path/tables/install.php");
    }
    // Si on est encore la, on n'a pas été redirigé ;
    die("Fichier db_param.inc.php absent / Missing file db_param.inc.php");
}
require_once "$include_path/db_param.inc.php";
require_once "$include_path/mysql_connect.inc.php";
$dbh = connection_mysql();

require_once "$include_path/sessions.inc.php";

require_once "$include_path/misc.inc.php";
include_once "$javascript_path/misc.inc.php";


// récupération des messages avec localisation
// localisation (fichier XML)
include_once "$class_path/XMLlist.class.php";

$messages = new XMLlist("$include_path/messages/$lang.xml", 0);
$messages->analyser();
$msg = $messages->table;

// temporaire :
$inst_language = "";
$current_module = "";

// Chargement de l'autoload des librairies externes
require_once $base_path.'/vendor/autoload.php';
// Chargement de l'autoload back-office
require_once __DIR__."/classes/autoloader/classLoader.class.php";
$al = classLoader::getInstance();
$al->register();

// Definition et chargement des parametres necessaires
// puis verification blocage acces / liste noire / liste blanche
if(!defined('GESTION')) {
    define('GESTION', 1);
}

global $user;
if (empty($user) || !is_string($user)) {
	$user = '';
}

$login_attempt_config = Auth::DEFAULT_CONFIG;
foreach($login_attempt_config as $k=>$v) {
    global ${'pmb_'.$k};
    ${'pmb_'.$k} = $v;
}

$q = "select concat(type_param, '_', sstype_param) as param, valeur_param from parametres where type_param='pmb' and sstype_param in ('" . implode("','", array_keys($login_attempt_config)) . "') ";
$r = pmb_mysql_query($q);

$remainingAttemptsMessage = '';
if(pmb_mysql_num_rows($r)) {
    while ($row = pmb_mysql_fetch_assoc($r)) {
        ${$row['param']} = $row['valeur_param'];
    }

    if (isset($pmb_active_log_login_attempts) && (1 == $pmb_active_log_login_attempts)) {
        $auth_instance = Auth::getInstance($user);
		if ($auth_instance->isRejected()) {
			$remainingAttemptsMessage = '<h4 class="login_rejected erreur">' . $auth_instance->getRejectCause() . '</h4>';
		} elseif ($auth_instance->getRemainingAttempts() != $pmb_block_after_failures) {
			$remainingAttemptsMessage = '<h4 class="login_remaining_attempts">' . $auth_instance->getRemainingAttemptsMessage() . '</h4>';
		}
    }
}

require_once "$include_path/templates/index.tpl.php";
if (!$dbh) {
	header ("Content-Type: text/html; charset=".$charset);
	print $index_header;
	print $extra_version;
	print "<br /><br /><div class='erreur'> $__erreur_cnx_base__ </div><br /><br />" ;
	print $msg["cnx_base_err1"]." <a href='./tables".$inst_language."/install.php'>./tables/install.php</a> ? <br /><br />.".$msg["cnx_base_err2"];
	print $index_footer;
	exit ;
}

require_once "$include_path/templates/common.tpl.php";

// affichage du form de login
if (!isset($demo) || $demo=="") $demo = 0;
header ("Content-Type: text/html; charset=$charset");


if (!isset($login_error) || !$login_error) {
	//Est-on déjà authentifié ?
	if (checkUser('PhpMyBibli')) {
		header("Location: ./main.php");
		exit();
	}
}

print $index_layout;

if ($demo) {
	if (!isset($login_error) || !$login_error) {
		$login_form_demo = str_replace("!!erreur!!", "&nbsp;", $login_form_demo);
		print $login_form_demo;
	} else {
		$login_form_demo = str_replace("!!erreur!!", $login_form_error, $login_form_demo);
		print $login_form_demo;
	}
} else {

	$error = '';
	if (!isset($login_error) || !$login_error) {
		$error = "&nbsp;";
	} else {
		$error = $login_form_error;
	}

	if (!empty($remainingAttemptsMessage)) {
		$error .= $remainingAttemptsMessage;
	}

	$login_form = str_replace("!!erreur!!", $error, $login_form);
	if (isset($login_message) && $login_message) {
		$login_form = str_replace("!!login_message!!", $login_message, $login_form);
	} else {
		$login_form = str_replace("!!login_message!!", "", $login_form);
	}
	print $login_form;
}

print form_focus('login', 'user');
print $index_footer;
