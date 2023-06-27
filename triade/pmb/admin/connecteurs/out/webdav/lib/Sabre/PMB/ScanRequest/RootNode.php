<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RootNode.php,v 1.1 2016-01-26 10:22:24 dgoron Exp $
namespace Sabre\PMB\ScanRequest;

class RootNode extends Collection {
	
	function __construct($config){
		parent::__construct($config);
		$this->type = "rootNode";
	}
	
	function getName() {
		return "";	
	}
}