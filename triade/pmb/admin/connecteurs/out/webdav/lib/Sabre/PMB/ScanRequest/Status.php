<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Status.php,v 1.3 2019/07/05 13:25:14 btafforeau Exp $
namespace Sabre\PMB\ScanRequest;

class Status extends Collection {
	protected $scan_request_status;

	public function __construct($name,$config) {
		parent::__construct($config);
		
		$id = substr($this->get_code_from_name($name),1);
		$this->scan_request_status = new \scan_request_status($id);
		$this->type = "scan_request_status";
	}

	public function getName() {
		return $this->format_name($this->scan_request_status->get_label()." (S".$this->scan_request_status->get_id().")");
	}
		
	public function getScanRequests(){
		$this->scan_requests = array();
		$query = 'select id_scan_request from scan_requests where scan_request_num_status = '.$this->scan_request_status->get_id();
		$this->filterScanRequests($query);
		return $this->scan_requests;
	}
}