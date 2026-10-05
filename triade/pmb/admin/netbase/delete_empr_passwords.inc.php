<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: delete_empr_passwords.inc.php,v 1.8 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg;
global $v_state, $spec;

$v_state=urldecode($v_state);

print netbase::get_display_progress_title($msg["deleting_empr_passwords"]);

$query = "show tables like 'empr_passwords'";
if (pmb_mysql_num_rows(pmb_mysql_query($query))) {
	$query = "DROP TABLE empr_passwords";
	pmb_mysql_query($query);
}
$v_state .= netbase::get_display_progress_v_state($msg["deleting_empr_passwords"], 'OK');

$spec = $spec - DELETE_EMPR_PASSWORDS;

// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec);