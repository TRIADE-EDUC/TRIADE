<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alerts_scan_request.class.php,v 1.1 2020/12/24 11:01:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class alerts_scan_request extends alerts {
	
	protected function get_module() {
		return 'circ';
	}
	
	protected function get_section() {
		return 'alerte_scan_requests';
	}
	
	protected function fetch_data() {
		global $status_search, $opac_scan_request_create_status;
		
		$this->data = array();
		
		$restore_status_search = $status_search;
		$status_search = $opac_scan_request_create_status;
		
		$scan_requests = new scan_requests(false);
		$requests = $scan_requests->get_scan_requests();
		
		if($number = count($requests)){
			$this->add_data('scan_request', 'alerte_scan_requests_to_validate', 'list', '&status_search='.$status_search, $number);
		}
		$status_search = $restore_status_search;
	}
}