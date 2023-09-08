<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: douchette_ajax.inc.php,v 1.5 2019-06-05 13:13:19 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $idcaddie, $action, $form_cb_expl, $msg;

$param = new stdClass();
if($idcaddie) {
	$myCart = new caddie($idcaddie);
	switch ($action) {
		case 'add_item':
			$param = $myCart->get_item_info_from_expl_cb($form_cb_expl, 1);
			$param->form_cb_expl=$form_cb_expl;
			$res_ajout = $myCart->pointe_item($param->expl_id,"EXPL", $form_cb_expl, "EXPL_CB" );
			
			// form de saisie cb exemplaire
			if ($param->expl_ajout_ok) {
				if ($res_ajout==CADDIE_ITEM_OK) {
					$param->message_ajout_expl = $msg["caddie_".$myCart->type."_pointe"];					
				}
				if ($res_ajout==CADDIE_ITEM_NULL) {
					$param->message_ajout_expl = $msg['caddie_item_null'];
				}
				if ($res_ajout==CADDIE_ITEM_IMPOSSIBLE_BULLETIN) {
					$param->message_ajout_expl = $msg['caddie_pointe_item_impossible_bulletin'];
				}	
				if ($res_ajout==CADDIE_ITEM_INEXISTANT) {
					$param->message_ajout_expl = $msg['caddie_pointe_inconnu_panier'];
				}	
			} 			
			break;
		default:
			break;
	}
	$param->nb_item=$myCart->nb_item;
	$param->nb_item_pointe=$myCart->nb_item_pointe;
	$param->nb_item_base=$myCart->nb_item_base;
	$param->nb_item_base_pointe=$myCart->nb_item_base_pointe;
	$param->nb_item_blob=$myCart->nb_item_blob;
	$param->nb_item_blob_pointe=$myCart->nb_item_blob_pointe;
} 
$array[0]=$param;
$buf_xml = array2xml($array);		
ajax_http_send_response("$buf_xml","text/xml");
