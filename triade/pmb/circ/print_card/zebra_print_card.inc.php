<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: zebra_print_card.inc.php,v 1.2 2014-05-12 15:28:40 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/printer.class.php");

$id_empr+=0;
$printer= new printer();
if($pdfcartelecteur_printer_card_handler==2) {
	$printer->printer_jzebra=true;
} else {
	$printer->printer_jzebra=false;
}
if($pdfcartelecteur_printer_card_name) {
	$printer->printer_name = $pdfcartelecteur_printer_card_name;
}
if($pdfcartelecteur_printer_card_url) {
	$printer->printer_url = $pdfcartelecteur_printer_card_url;
}

$card_tpl='';
if(file_exists($base_path."/circ/print_card/print_card.tpl.php")) {
	require_once ($base_path."/circ/print_card/print_card.tpl.php");
}

$printer->initialize();

switch($sub) {
	case 'one':
		$r=$printer->print_card($id_empr,$card_tpl);
		ajax_http_send_response($r);
		break;
	case 'get_script':	
		$r = $printer->get_script();
		ajax_http_send_response($r);
		break;
	default:
		ajax_http_send_error('400',"commande inconnue");
		break;		
}

