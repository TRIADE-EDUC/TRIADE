<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_requests.inc.php,v 1.8 2023/08/17 09:47:55 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $lvl, $sub, $from, $msg;
global $id, $notice;

require_once($class_path.'/scan_request/scan_requests.class.php');
require_once($class_path.'/scan_request/scan_request.class.php');

switch($lvl){
	case 'scan_request':
		$scan_request = new scan_request($id);
		switch($sub) {
			case 'edit':
				switch ($from) {
					case 'caddie':
						$notices = $_SESSION['cart'];
						$scan_request->add_linked_records_from_notices_ids($notices);
						break;
					case 'checked':
						$notices = $notice;
						$scan_request->add_linked_records_from_notices_ids($notices);
						break;
				}
				if ($id || $scan_request->has_scannable_linked_record()) {
					print $scan_request->get_form();
				} else {
					print '<script>alert("'.addslashes($msg['scan_request_linked_record_no_scannable_from_caddie']).'");history.go(-1);</script>';
				}
				break;
			case 'save':
				$scan_request->get_values_from_form();
				$scan_request->save();
				print $scan_request->get_display();
				break;
			case 'cancel':
				if ($_SESSION['id_empr_session'] && $scan_request->get_status() && $scan_request->get_status()->is_cancelable()) {
					$scan_request->delete();
					print '<div class="alerte">'.$msg['scan_request_deleted'].'</div>';
				} else {
					print '<div class="alerte">'.$msg['scan_request_cant_delete'].'</div>';
				}
				$list_opac_scan_requests_ui = list_opac_scan_requests_ui::get_instance(array('empr' => array($_SESSION['id_empr_session'])));
				if(count($list_opac_scan_requests_ui->get_objects())) {
				    print $list_opac_scan_requests_ui->get_display_list();
				} else {
				    print $msg['scan_request_list_empty'];
				}
				break;
			case 'display':
			default :
				print $scan_request->get_display();
				break;
		}
		break;
	case 'scan_requests_list':
	default :
	    $list_opac_scan_requests_ui = list_opac_scan_requests_ui::get_instance(array('empr' => array($_SESSION['id_empr_session'])));
	    if(count($list_opac_scan_requests_ui->get_objects())) {
	        print $list_opac_scan_requests_ui->get_display_list();
	    } else {
	        print $msg['scan_request_list_empty'];
	    }
	break;
}