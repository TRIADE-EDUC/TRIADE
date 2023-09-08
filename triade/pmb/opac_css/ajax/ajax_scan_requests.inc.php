<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_scan_requests.inc.php,v 1.4 2017-04-11 09:13:55 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($base_path."/classes/scan_request/scan_request.class.php");

switch($sub){
	case 'form':
		switch ($action){
			case 'create':
				$scan_request=new scan_request();
				$scan_request_deadline_date = extraitdate($scan_request_deadline_date);
				$scan_request_wish_date = extraitdate($scan_request_wish_date);
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
				$scan_request->add_linked_records(array($record_type => array($record_id)));
				print $scan_request->get_form_in_record($record_id, $record_type);
				break;
		}
		break;
}
?>