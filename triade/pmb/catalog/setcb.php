<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: setcb.php,v 1.26.2.1.2.1 2025/05/20 14:00:09 qvarin Exp $

// popup de saisie d'un code barre

use Pmb\Security\Library\Auth;

$base_path = "..";

require_once "../includes/error_report.inc.php";
require_once "../includes/global_vars.inc.php";
require_once "../includes/config.inc.php";

$include_path      = $base_path."/".$include_path;
$class_path        = $base_path."/".$class_path;
$styles_path       = $base_path."/".$styles_path;


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
include "$include_path/isbn.inc.php";
include "$class_path/XMLlist.class.php";

$current = current_page();
$current_module = str_replace(".php","",$current);

if(!checkUser('PhpMyBibli')) {
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

print "<!DOCTYPE html>
<html>
<head>
	<meta charset=\"".$charset."\" />
	<meta http-equiv='Pragma' content='no-cache'>
	<meta http-equiv='Cache-Control' content='no-cache'>";
echo HtmlHelper::getInstance()->getStyle($stylesheet);
print "	<title>$msg[4014]</title></head><body>";

if (!isset($formulaire_appelant) || !$formulaire_appelant) $formulaire_appelant="notice" ;
if (!isset($objet_appelant) || !$objet_appelant) $objet_appelant="f_cb" ;
if(!isset($bulletin)) $bulletin = '';
if (!isset($notice_id)) $notice_id = 0;

$alerte_code_double = 0;
// traitement de la soumission
if (isset($suite) && $suite) { // un CB a été soumis
	if ($cb) {
		if(isEAN($cb)) {
			// la saisie est un EAN -> on tente de le formater en ISBN
			$code = EANtoISBN($cb);
			// si échec, on prend l'EAN comme il vient
			if(!$code) $code = $cb;
		} else {
			if(isISBN($cb)) {
				// si la saisie est un ISBN
				$code = formatISBN($cb,13);
				// si échec, ISBN erroné on le prend sous cette forme
				if(!$code) $code = $cb;
			} else {
				// ce n'est rien de tout ça, on prend la saisie telle quelle
				$code = $cb;
			}
		}
		$code_temp = $code;
	}
	if ($code_temp) {
		if ($bulletin) {
			if ($notice_id) $and_clause = " and bulletin_id!='".$notice_id."'" ;
				else $and_clause = "" ;
			$rqt_verif_code = "select count(1) from bulletins where bulletin_cb='".$code_temp."'".$and_clause ;
		} else {
			if ($notice_id) $and_clause = " and notice_id!='".$notice_id."'" ;
				else $and_clause = "" ;
			$rqt_verif_code = "select count(1) from notices where code ='".$code_temp."'".$and_clause ;
		}
		$res_verif_code = pmb_mysql_query($rqt_verif_code, $dbh);
		$nbr_verif_code = pmb_mysql_result($res_verif_code, 0, 0);
		if ($nbr_verif_code > 0) $alerte_code_double = 1 ;
			else $alerte_code_double = 0 ;
	}
}

if ($alerte_code_double) {
	?>
		<script>
			if (confirm("<?php echo $msg['isbn_duplicate_raz']; ?>")) {
				window.opener.document.forms['<?php echo $formulaire_appelant; ?>'].elements['<?php echo $objet_appelant; ?>'].value = '<?php echo $code_temp; ?>';
				window.close();
				}
			</script>
		<?php
	} elseif (isset($suite) && $suite) {
		?>
			<script>
			window.opener.document.forms['<?php echo $formulaire_appelant; ?>'].elements['<?php echo $objet_appelant; ?>'].value = '<?php echo $code_temp; ?>';
			window.close();
			</script>
		<?php
		}


?>
<div class='center'>
	<form class='form-catalog' name='setcb' action='./setcb.php' >
		<small><?php echo $msg[4056]; ?></small><br />
		<input type='text' name='cb' value=''>
		<input type='hidden' name='notice_id' value='<?php echo $notice_id; ?>'>
		<input type='hidden' name='formulaire_appelant' value='<?php echo $formulaire_appelant; ?>'>
		<input type='hidden' name='objet_appelant' value='<?php echo $objet_appelant; ?>'>
		<input type='hidden' name='bulletin' value='<?php echo $bulletin; ?>'>
		<input type='hidden' name='suite' value='1'>
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
