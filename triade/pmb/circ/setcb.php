<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: setcb.php,v 1.17 2018-06-19 13:00:53 dgoron Exp $

// popup de saisie d'un code barre pour emprunteur

/* reste à faire :

sur soumission auto :
	document.location = soi-même
		verif code-barre non utilisé
			oui : afficher message et rester ouvert
			non : updateParent et close

dans maj emprunteur : tenir compte du code-barre pour l'update !

*/

$base_path="..";	
$current_module="circ";

require_once ("../includes/error_report.inc.php") ;
require_once ("../includes/global_vars.inc.php") ;
require_once ("../includes/config.inc.php");

$include_path      = "../".$include_path; 
$class_path        = "../".$class_path;
$javascript_path   = "../".$javascript_path;
$styles_path       = "../".$styles_path;

require("$include_path/db_param.inc.php");
require("$include_path/mysql_connect.inc.php");
// connection MySQL
$dbh = connection_mysql();

include("$include_path/error_handler.inc.php");
include("$include_path/sessions.inc.php");
include("$include_path/misc.inc.php");
include("$class_path/XMLlist.class.php");

if(!checkUser('PhpMyBibli')) {
	// localisation (fichier XML) (valeur par défaut)
	$messages = new XMLlist("$include_path/messages/$lang.xml", 0);
	$messages->analyser();
	$msg = $messages->table;
	print '<html><head><link rel=\"stylesheet\" type=\"text/css\" href=\"../../styles/$stylesheet; ?>\"></head><body>';
	require_once("$include_path/user_error.inc.php");
	error_message($msg[11], $msg[12], 1);
	print '</body></html>';
	exit;
	}

if(SESSlang) {
	$lang=SESSlang;
	$helpdir = $lang;
	}

// localisation (fichier XML)
$messages = new XMLlist("$include_path/messages/$lang.xml", 0);
$messages->analyser();
$msg = $messages->table;

require("$include_path/templates/common.tpl.php");

header ("Content-Type: text/html; charset=".$charset);

print "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" />
	<meta http-equiv='Pragma' content='no-cache'>
	<meta http-equiv='Cache-Control' content='no-cache'>";
print link_styles($stylesheet) ;
print "	<title>setcb</title>
	</head>
	<body>";

?>
<script type="text/javascript">


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
<script type="text/javascript">
self.focus();
		document.forms['setcb'].elements['cb'].focus();
</script>
</div>
</body>
</html>
