<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: confirm_pret.inc.php,v 1.4 2019-02-20 12:45:53 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/ajax_pret.class.php");
// init de la class
$expl = new do_pret();
	
if ( is_array($id_expl)) {
	foreach($id_expl as $id) {
		$status= $expl->confirm_pret($id_empr, $id, $short_loan, 'gestion_rfid');		
	}
	ajax_http_send_response("$status","text/xml");	
} else {
	$status = $expl->confirm_pret($id_empr, $id_expl, $short_loan, 'gestion_rfid');
	ajax_http_send_response("$status","text/xml");
}
?>