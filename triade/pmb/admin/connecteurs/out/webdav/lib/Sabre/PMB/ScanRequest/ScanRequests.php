<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ScanRequests.php,v 1.2 2016-01-26 15:36:15 dgoron Exp $
namespace Sabre\PMB\ScanRequest;

class ScanRequests extends Collection {
	protected $scan_requests;
	public $config;

	function __construct($scan_requests,$config) {
		
		$this->scan_requests = $scan_requests;
		$this->config = $config;
		$this->type = "scan_requests";
	}
	
	public function get_scan_requests() {
		return $this->scan_requests;
	}
	
	function getChildren() {
		$children = array();
		for($i=0 ; $i<count($this->scan_requests) ; $i++){
			$children[] = $this->getChild("(R".$this->scan_requests[$i].")");
		}
		return $children;
	}

	function getName() {
		return $this->format_name("[Demandes]");
	}
}