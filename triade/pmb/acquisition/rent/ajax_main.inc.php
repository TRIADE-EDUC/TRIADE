<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.24.4.1 2025/02/12 12:34:09 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $sort_asc_desc, $class_path, $sub, $id, $action, $msg, $from, $value, $with;
global $id_entity, $id_invoice, $deleted, $uniform_title_id, $invoice_id, $publisher_id, $num_exercice, $objects_type, $object_type;

if(!isset($sort_asc_desc)) $sort_asc_desc = '';

require_once($class_path."/rent/rent_pricing_system_grid.class.php");
require_once($class_path."/rent/rent_root.class.php");
require_once($class_path."/rent/rent_invoice.class.php");
require_once($class_path.'/encoding_normalize.class.php');
require_once($class_path.'/form_mapper/form_mapper.class.php');
require_once($class_path.'/editor.class.php');

switch($sub){
	case 'requests' :
	case 'accounts' :
		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type, 'rent');
				break;
		}
		break;
	case 'get_grid':
		$rent_pricing_system_grid = new rent_pricing_system_grid($id);
		switch ($action) {
			case 'get_price' :
				switch ($from) {
					case 'time' :
						ajax_http_send_response($rent_pricing_system_grid->calc_price_from_time($value, $with));
						break;
					case 'percent' :
						ajax_http_send_response($rent_pricing_system_grid->calc_price_from_percent($value, $with));
						break;
				}
				break;
			default :
				ajax_http_send_response($rent_pricing_system_grid->get_display_in_layer());
				break;
		}
		break;
	case 'get_exercices':
		ajax_http_send_response(rent_root::gen_selector_exercices($id_entity, $objects_type));
		break;
	case 'invoices':
		switch($action) {
			case 'delete_account':
				if($id_invoice && $id) {
					$rent_invoice = new rent_invoice($id_invoice);
					$deleted = $rent_invoice->delete_account($id);
					$rent_invoice->save();
					if($deleted) {
						ajax_http_send_response('1');
					} else {
						ajax_http_send_response('0');
					}
				} else {
					ajax_http_send_response('0');
				}
				break;
			case "list":
				lists_controller::proceed_ajax($object_type, 'rent');
				break;
		}
		break;
	case 'get_uniform_title_fields':
		if($uniform_title_id*1) {
			$mapper = form_mapper::getMapper('tu');
			if($mapper){
				$mapper->setId($uniform_title_id);
				$mapping = $mapper->getMapping('account');
				print encoding_normalize::json_encode($mapping);
			}else{
				print encoding_normalize::json_encode(array('mapping'=> 'false'));
			}
		}
		break;
	case 'show_invoices_selector':
		$rent_account = new rent_account($id);
		print encoding_normalize::utf8_normalize($rent_account->get_invoices_to_select());
		break;
	case 'add_account_in_invoice':
		$rent_account = new rent_account($id);
		if($rent_account->get_request_status() != 3) {
			$rent_account->set_request_status(3);
		}
		$response=$rent_account->add_account_in_invoice($invoice_id);
		$rent_account->save();
		print encoding_normalize::json_encode($response);
		break;
	case 'get_supplier_from_publisher':
		$response = array(
				'id_entite' => 0,
				'state' => false
		);
		if($publisher_id*1) {
			$editeur = new editeur($publisher_id);
			if($editeur->supplier->id_entite) {
				$response = array(
						'id_entite' => $editeur->supplier->id_entite,
						'raison_sociale' => $editeur->supplier->raison_sociale,
						'state' => true
				);
			}
		}
		print encoding_normalize::json_encode($response);
		break;
	case 'get_pricing_systems' :
		$response = array(array('id' => 0, 'label' => $msg['acquisition_account_pricing_system_except']));
		$query = 'select id_pricing_system, pricing_system_label from rent_pricing_systems where pricing_system_num_exercice = '.intval($num_exercice);
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				$response[] = array(
						'id' => $row->id_pricing_system,
						'label' => $row->pricing_system_label
				);
			}
		}
		print encoding_normalize::json_encode($response);
		break;
	default:
		break;
}
