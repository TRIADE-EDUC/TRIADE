<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: zebra_print_pret.inc.php,v 1.12 2019-01-04 13:54:51 ccraig Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/printer.class.php");

$id_empr+=0;
$printer= new printer();

if($pmb_printer_name) {
	$printer->printer_name = $pmb_printer_name;
}

if (substr($pmb_printer_name,0,9) == 'raspberry') {
	$printer->printer_driver = 'raspberry';
}

$ticket_tpl='';
if(file_exists($base_path."/circ/print_pret/print_ticket.tpl.php")) {
	require_once ($base_path."/circ/print_pret/print_ticket.tpl.php");
}

$printer->initialize();

switch($sub) {
	case 'one':
		$r=$printer->print_pret($id_empr,$cb_doc,$ticket_tpl);
		if ((substr($pmb_printer_name,0,9) == 'raspberry') && (isset($printer_type))) {
 			header("Content-Type: text/html; charset=utf-8");
			if ($charset != 'utf-8') {
				print utf8_encode($r[$printer_type]);
			} else {
				print $r[$printer_type];
			}
		} else {
			ajax_http_send_response($r);
		}
	break;
	case 'get_script':
		$r = $printer->get_script();
		ajax_http_send_response($r);
	break;
	case 'all':
		$r=$printer->print_all_pret($id_empr,$ticket_tpl);
		if ((substr($pmb_printer_name,0,9) == 'raspberry') && (isset($printer_type))) {
			header("Content-Type: text/html; charset=utf-8");
			if ($charset != 'utf-8') {
				print utf8_encode($r[$printer_type]);
			} else {
				print $r[$printer_type];
			}
		} else {
			ajax_http_send_response($r);
		}
		break;
	case 'transacash_ticket':
		$r=$printer->transacash_ticket($transacash_id,$ticket_tpl);
		ajax_http_send_response($r);
	break;
	case 'get_selected_printer':
		$r=$printer->get_selected_printer();
		ajax_http_send_response($r);
	break;
	default:
		ajax_http_send_error('400',"commande inconnue");
	break;		
}