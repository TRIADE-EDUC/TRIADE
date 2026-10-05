<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: misc.inc.php,v 1.5 2022/10/28 13:22:26 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$func_format = array(
	"verifdate" => "ajax_verif_date",
	"session" => "ajax_session",
);

if (empty($fname)) {
	exit;
}

if (array_key_exists($fname, $func_format) && function_exists($func_format[$fname])) {
	$ret = $func_format[$fname]();
}

/***********************************************
 *Fonction ajax_verif_date
 *	Check la date saisie en format local 
 *input :
 *	- $p1, date envoyée par POST or GET metod
 *Output:
 * retourne la date ou un code d'erreur http
 */	
function ajax_verif_date() {
	global $msg, $p1;
	
	$mysql_date = extraitdate($p1);
	$rqt= "SELECT DATE_ADD('" .$mysql_date. "', INTERVAL 0 DAY)";
	$result = pmb_mysql_query($rqt);

	if (pmb_mysql_num_rows($result)) {
		$row = pmb_mysql_fetch_row($result);
		if ($row[0]) {
			return ajax_http_send_response($row[0]);
		}
	}
	ajax_http_send_error('400', $msg['error_message_invalid_date']);
}

/***********************************************
 *Fonction ajax_session
 *	Set une valeur en session
 *input :
 *	- $key, clé envoyée par POST or GET metod
 *	- $value, valeur envoyée par POST or GET metod
 *Output:
 * void
 */
function ajax_session() {
	global $session_key,$session_value;
	
	$allow_seesion_key = [
		"accessibility",
		"abon_edit_bannette_priv"
	];
	
	if (!empty($session_key) && in_array($session_key, $allow_seesion_key)) {
		if (!$session_value) {
			unset($_SESSION[$session_key]);
		} else {
			$_SESSION[$session_key] = $session_value;
		}
	}
}