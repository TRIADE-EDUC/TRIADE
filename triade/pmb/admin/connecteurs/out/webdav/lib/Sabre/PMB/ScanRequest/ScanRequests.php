<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ScanRequests.php,v 1.3 2019/07/05 13:25:14 btafforeau Exp $
namespace Sabre\PMB\ScanRequest;

class ScanRequests extends Collection {
	protected $scan_requests;
	public $config;

	public function __construct($scan_requests,$config) {
		
		$this->scan_requests = $scan_requests;
		$this->config = $config;
		$this->type = "scan_requests";
	}
	
	public function get_scan_requests() {
		return $this->scan_requests;
	}
	
	public function getChildren() {
		$children = array();
		for($i=0 ; $i<count($this->scan_requests) ; $i++){
			$children[] = $this->getChild("(R".$this->scan_requests[$i].")");
		}
		return $children;
	}

	public function getName() {
		return $this->format_name("[Demandes]");
	}
}