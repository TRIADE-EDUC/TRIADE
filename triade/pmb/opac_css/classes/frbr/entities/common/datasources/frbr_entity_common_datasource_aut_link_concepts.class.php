<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_aut_link_concepts.class.php,v 1.1 2017/09/15 14:47:30 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_datasource_aut_link_concepts extends frbr_entity_common_datasource_aut_link_authorities {

	protected $authority_type = AUT_TABLE_CONCEPT;
	
	public function __construct($id=0){
		$this->entity_type = "concepts";
		parent::__construct($id);
	}
}