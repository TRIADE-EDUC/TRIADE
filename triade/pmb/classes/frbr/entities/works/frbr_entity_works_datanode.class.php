<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_works_datanode.class.php,v 1.2 2017-04-25 15:22:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_works_datanode extends frbr_entity_common_entity_datanode {
	
	public function __construct($id=0){
		parent::__construct($id);
	}
}