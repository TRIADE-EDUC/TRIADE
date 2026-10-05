<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_generic_authority_concept.class.php,v 1.1 2016/04/15 10:29:21 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_generic_authority_concept extends cms_module_common_selector_generic_authority_type{
	
	public function __construct($id=0){
		$this->authority_type = AUT_TABLE_CONCEPT;
		parent::__construct($id);
	}
}