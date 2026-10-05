<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_concepts_datasource_custom_fields.class.php,v 1.1 2018/03/28 09:23:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_concepts_datasource_custom_fields extends frbr_entity_common_datasource_custom_fields {
	
	public function __construct($id=0){
		$this->entity_type = "concepts";
		parent::__construct($id);
	}
}