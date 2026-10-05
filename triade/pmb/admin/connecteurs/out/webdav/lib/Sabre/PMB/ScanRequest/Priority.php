<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Priority.php,v 1.3 2019/07/05 13:25:14 btafforeau Exp $
namespace Sabre\PMB\ScanRequest;

class Priority extends Collection {
	protected $scan_request_priority;

	public function __construct($name,$config) {
		parent::__construct($config);
		
		$id = substr($this->get_code_from_name($name),1);
		$this->scan_request_priority = new \scan_request_priority($id);
		$this->type = "scan_request_priority";
	}

	public function getName() {
		return $this->format_name($this->scan_request_priority->get_label()." (P".$this->scan_request_priority->get_id().")");
	}
		
	public function getScanRequests(){
		$this->scan_requests = array();
		$query = 'select id_scan_request from scan_requests where scan_request_num_priority = '.$this->scan_request_priority->get_id();
		$this->filterScanRequests($query);
		return $this->scan_requests;
	}
}