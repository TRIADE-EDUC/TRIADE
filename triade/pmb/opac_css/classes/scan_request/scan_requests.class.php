<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_requests.class.php,v 1.9 2023/08/02 09:39:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path.'/scan_request/scan_request.class.php');
require_once($include_path.'/h2o/pmb_h2o.inc.php');

class scan_requests {
	
	/**
	 * Tableau des scan_requests de la liste
	 * @var scan_request
	 */
	protected $scan_requests;
	
	/**
	 * Identifiant de l'emprunteur
	 * @var int
	 */
	protected $empr_id;
	
	public function __construct($empr_id) {
		$this->empr_id = intval($empr_id);
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$this->scan_requests = array();
		
		$query = 'select id_scan_request from scan_requests 
				join scan_request_status on scan_request_status.id_scan_request_status = scan_requests.scan_request_num_status 
				where scan_request_num_dest_empr = '.$this->empr_id.' and scan_request_status_opac_show = 1 order by scan_request_date';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				$this->scan_requests[] = new scan_request($row->id_scan_request);
			}
		}
	}
	
	public function get_display_list() {
		global $msg, $include_path;
		
		if(count($this->scan_requests)) {
			$tpl = $include_path.'/templates/scan_request/scan_requests_list.tpl.html';
			if (file_exists($include_path.'/templates/scan_request/scan_requests_list_subst.tpl.html')) {
				$tpl = $include_path.'/templates/scan_request/scan_requests_list_subst.tpl.html';
			}
			$h2o = H2o_collection::get_instance($tpl);
			return $h2o->render(array('scan_requests' => $this));
		} else {
			return $msg['scan_request_list_empty'];
		}
	}
	
	public function get_display_header_list() {
	    global $include_path;
	    
	    $tpl = $include_path.'/templates/scan_request/scan_requests_header_list.tpl.html';
	    if (file_exists($include_path.'/templates/scan_request/scan_requests_header_list_subst.tpl.html')) {
	        $tpl = $include_path.'/templates/scan_request/scan_requests_header_list_subst.tpl.html';
	    }
	    $h2o = H2o_collection::get_instance($tpl);
	    return $h2o->render(array('scan_requests' => $this));
	}
	
	public function get_scan_requests() {
		return $this->scan_requests;
	}
	
	public static function get_scan_requests_on_record($empr_id, $record_id, $record_type) {
		$scan_requests_on_record = array();
		$query = 'select id_scan_request from scan_requests join scan_request_linked_records on scan_requests.id_scan_request = scan_request_linked_records.scan_request_linked_record_num_request';
		$query.= ' where scan_requests.scan_request_num_dest_empr = '.$empr_id;
		
		if ($record_type == 'bulletins') {
			$query.= ' and scan_request_linked_records.scan_request_linked_record_num_bulletin = '.$record_id;
		} else if ($record_type == 'notices') {
			$query.= ' and scan_request_linked_records.scan_request_linked_record_num_notice = '.$record_id;
		}
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				$scan_requests_on_record[] = $row->id_scan_request;
			}
		}
		return $scan_requests_on_record;
	}
}