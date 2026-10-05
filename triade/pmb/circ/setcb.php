<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: setcb.php,v 1.21.2.1.2.1 2025/05/20 14:00:07 qvarin Exp $

// popup de saisie d'un code barre pour emprunteur

use Pmb\Security\Library\Auth;

$base_path = "..";
$current_module = "circ";

require_once "../includes/error_report.inc.php";
require_once "../includes/global_vars.inc.php";
require_once "../includes/config.inc.php";

$include_path      = "../".$include_path;
$class_path        = "../".$class_path;
$styles_path       = "../".$styles_path;

require "$include_path/db_param.inc.php";
require "$include_path/mysql_connect.inc.php";
// connection MySQL
$dbh = connection_mysql();

// Chargement de l'autoload des librairies externes
require_once $base_path.'/vendor/autoload.php';
// Chargement de l'autoload back-office
require_once "$class_path/autoloader/classLoader.class.php";
$al = classLoader::getInstance();
$al->register();

// Definition et chargement des parametres necessaires
// puis verification blocage acces / liste noire / liste blanche
if(!defined('GESTION')) {
    define('GESTION', 1);
}

include "$include_path/error_handler.inc.php";
include "$include_path/sessions.inc.php";
include "$include_path/misc.inc.php";
include "$class_path/XMLlist.class.php";

// Chargement de l'autoload back-office
require_once "$class_path/autoloader/classLoader.class.php";
$al = classLoader::getInstance();
$al->register();

if( !checkUser('PhpMyBibli') ) {
	// localisation (fichier XML) (valeur par défaut)
	$messages = new XMLlist("$include_path/messages/$lang.xml", 0);
	$messages->analyser();
	$msg = $messages->table;
	print '<html><head><link rel=\"stylesheet\" type=\"text/css\" href=\"../../styles/$stylesheet; ?>\"></head><body>';
	require_once "$include_path/user_error.inc.php";
	error_message($msg[11], $msg[12], 1);
	print '</body></html>';
	exit;
} else {
	$auth_instance = Auth::getInstance();
	if ($auth_instance->isInBlackList()) {
		header('Location: ./logout.php', true, 302);
		exit();
	}
}

if( defined('SESSlang') && SESSlang ) {
	$lang=SESSlang;
	$helpdir = $lang;
}

// localisation (fichier XML)
$messages = new XMLlist("$include_path/messages/$lang.xml", 0);
$messages->analyser();
$msg = $messages->table;

require_once $class_path."/html_helper.class.php";

header ("Content-Type: text/html; charset=".$charset);

echo
"<!DOCTYPE html>
	<html lang='".get_iso_lang_code()."'>
		<head>
			<meta charset=\"".$charset."\" />
			<meta http-equiv='Pragma' content='no-cache'>
			<meta http-equiv='Cache-Control' content='no-cache'>";
echo HtmlHelper::getInstance()->getStyle($stylesheet);
echo
			"<title>setcb</title>
		</head>
		<body>";
?>

<script>
	function updateParent() {
		window.opener.document.forms['empr_form'].elements['f_cb'].value = document.forms['setcb'].elements['cb'].value;
		self.close();
	}
</script>
<div class='center'>
	<form class='form-circ' name='setcb' onSubmit='updateParent();'>
		<small><?php echo $msg[4056]; ?></small><br />
		<input type='text' name='cb' value=''>
		<input type='hidden' name='verif' value='1'>
		<p>
			<input type='button' class='bouton' name='bouton' value='<?php echo $msg[76]; ?>' onClick='window.close();'>
			<input type='submit' class='bouton' name='save' value='<?php echo $msg[77]; ?>' />
		</p>
	</form>
	<script>
	self.focus();
			document.forms['setcb'].elements['cb'].focus();
	</script>
</div>
</body>
</html>
