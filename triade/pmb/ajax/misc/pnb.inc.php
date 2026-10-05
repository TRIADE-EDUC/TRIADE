<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb.inc.php,v 1.3 2019/05/29 12:03:09 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $action, $record_id, $line_id;

require_once($class_path."/pnb/pnb_record_orders.class.php");

switch($sub){
	case 'offer': 
		switch($action){
			case 'get_loans_completed_number':
				$pnb_record_orders = new pnb_record_orders($record_id);
				print encoding_normalize::json_encode(
					array(
						'record_id' => $record_id,
						'line_id' => $line_id,
						'loans_completed_number' => $pnb_record_orders->get_loans_completed_number($line_id),						
					)
				);
				break;			
			case 'get_loans_completed_number_by_line_id':
				$pnb_record_orders = new pnb_record_orders(0);
				print encoding_normalize::json_encode(
					array(
						'line_id' => $line_id,
						'loans_completed_number' => $pnb_record_orders->get_loans_completed_number($line_id),						
					)
				);
				break;			
		}
		break;
}