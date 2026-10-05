<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Notices.php,v 1.7.12.1 2025/03/14 08:07:34 qvarin Exp $
namespace Sabre\PMB;

class Notices extends Collection {

	public function __construct($notices,$config) {
		$this->notices = $notices;
		$this->config = $config;
		$this->type = "notices";
	}

	public function getChildren() {
		$children = array();
		for($i=0 ; $i<count($this->notices) ; $i++){
			$children[] = $this->getChild("(N".$this->notices[$i].")");
		}
		return $children;
	}

	public function getName() {
		return $this->format_name("[Notices]");
	}
}