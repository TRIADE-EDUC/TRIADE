<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request.inc.php,v 1.4 2019/06/05 09:04:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $idcaddie, $action, $msg;

require_once("./classes/notice_tpl_gen.class.php");
require_once("./classes/progress_bar.class.php");
if($idcaddie) {
	$myCart= new caddie($idcaddie);
	print pmb_bidi($myCart->aff_cart_titre());
	switch ($action) {
		case 'choix_quoi':
			print pmb_bidi($myCart->aff_cart_nb_items()) ;						
			print $myCart->get_choix_quoi_form("./circ.php?categ=scan_request&sub=request&action=edit&from_caddie=".$idcaddie, "./catalog.php?categ=caddie&sub=action&quelle=scan_request&action=&idcaddie=0", $msg["scan_request_record_button"], $msg["scan_request_record_button"],"");
			break;
		default:
			break;
	}
} else{	
	aff_paniers($idcaddie, "NOTI", "./catalog.php?categ=caddie&sub=action&quelle=scan_request", "choix_quoi", $msg["scan_request_record_button"], "", 0, 0, 0);
}
