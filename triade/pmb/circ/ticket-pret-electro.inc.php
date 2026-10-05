<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ticket-pret-electro.inc.php,v 1.6 2022/08/01 06:44:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $id_empr, $id_groupe;

require_once("$base_path/circ/pret_func.inc.php");
require_once("$class_path/emprunteur.class.php");

// liste des prêts et réservations
$mail_reader_loans_ticket = new mail_reader_loans_ticket();
if (isset($id_groupe)) {
	$mail_reader_loans_ticket->set_id_group($id_groupe);
} else {
	$mail_reader_loans_ticket->set_mail_to_id($id_empr);
}
$res_envoi = $mail_reader_loans_ticket->send_mail();
if ($res_envoi) {
	echo $mail_reader_loans_ticket->get_display_sent_succeed();
} else {
	echo $mail_reader_loans_ticket->get_display_sent_failed();
}
