<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Tree.php,v 1.1 2016-02-05 10:06:14 vtouchard Exp $

namespace Sabre\PMB\Music;

use Sabre\DAV;
use Sabre\PMB;

class Tree extends PMB\Tree {
	
	function getRootNode(){
		$this->rootNode = new RootNode($this->config);
	}
	
	protected function get_restricted_objects_query() {
		return "select notice_id as object_id from notices " ;
	}
}