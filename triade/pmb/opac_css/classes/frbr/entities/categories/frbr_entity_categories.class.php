<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_categories.class.php,v 1.1 2017/04/27 16:02:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_categories extends frbr_entity_common_entity {
	
	public function __construct($id=0){
		parent::__construct($id);
	}
}