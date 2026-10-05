<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RootNode.php,v 1.2 2019/07/05 13:25:14 btafforeau Exp $
namespace Sabre\PMB\ScanRequest;

class RootNode extends Collection {
	
	public function __construct($config){
		parent::__construct($config);
		$this->type = "rootNode";
	}
	
	public function getName() {
		return "";	
	}
}