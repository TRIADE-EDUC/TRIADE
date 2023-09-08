<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Notices.php,v 1.6 2016-01-26 15:36:15 dgoron Exp $
namespace Sabre\PMB;

class Notices extends Collection {
	private $notices;
	public $config;

	function __construct($notices,$config) {
		
		$this->notices = $notices;
		$this->config = $config;
		$this->type = "notices";
	}
	
	function getChildren() {
		$children = array();
		for($i=0 ; $i<count($this->notices) ; $i++){
			$children[] = $this->getChild("(N".$this->notices[$i].")");
		}
		return $children;
	}

	function getName() {
		return $this->format_name("[Notices]");
	}
}