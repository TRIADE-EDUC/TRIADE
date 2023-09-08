<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: delete_empr_passwords.inc.php,v 1.5 2019-03-29 11:54:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$v_state=urldecode($v_state);

print "<br /><br /><h2 class='center'>".htmlentities($msg["deleting_empr_passwords"], ENT_QUOTES, $charset)."</h2>";

$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["deleting_empr_passwords"], ENT_QUOTES, $charset)." : ";
$query = "show tables like 'empr_passwords'";
if (pmb_mysql_num_rows(pmb_mysql_query($query,$dbh))) {
	$query = "DROP TABLE empr_passwords";
	pmb_mysql_query($query);
}
$v_state.= "OK";

$spec = $spec - DELETE_EMPR_PASSWORDS;

// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec, '', '2');