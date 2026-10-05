<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_records_page.class.php,v 1.1 2017/05/05 07:43:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_records_page extends frbr_entity_common_entity_page {
	
	public function __construct($id=0){
		parent::__construct($id);
	}
}