<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_scan_requests.inc.php,v 1.6 2022/07/12 07:54:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $sub, $action, $msg;
global $record_type, $record_id;

require_once($base_path."/classes/scan_request/scan_request.class.php");

switch($sub){
	case 'form':
		switch ($action){
			case 'create':
				$scan_request=new scan_request();
				$scan_request->get_values_from_form();
				$saved = $scan_request->save();
				print '<span class="scan_request_submit">';
				if($saved) {
					print $msg['scan_request_saved'];
					print " ".str_replace('!!link!!', './empr.php?tab=scan_requests&lvl=scan_request&sub=display&id='.$scan_request->get_id(), $msg['scan_request_saved_see_link']);
				} else {
					print $msg['scan_request_cant_save'];
				}
				print '</span>';
				break;
			case 'edit':
				$scan_request=new scan_request();
				if($record_type == 'notices') {
				    $scan_request->set_title(strip_tags(aff_notice($record_id, 0, 1, 0, AFF_ETA_NOTICES_REDUIT, '', 1, 0)));
				} elseif($record_type == 'bulletins') {
				    $scan_request->set_title(strip_tags(bulletin_header($record_id)));
				}
				$scan_request->add_linked_records(array($record_type => array($record_id)));
				print $scan_request->get_form_in_record($record_id, $record_type);
				break;
		}
		break;
}
?>