<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Tree.php,v 1.1 2016-01-26 10:22:24 dgoron Exp $

namespace Sabre\PMB\ScanRequest;

use Sabre\DAV;
use Sabre\PMB;

class Tree extends PMB\Tree {
	
	function getRootNode(){
		$this->rootNode = new RootNode($this->config);
	}
	
	protected function get_restricted_objects_query() {
		return "select id_scan_request as object_id from scan_requests";
	}
}