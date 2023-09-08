<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_authors_datasource_used_in_authperso_custom_fields.class.php,v 1.1 2018-06-29 13:02:47 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_authors_datasource_used_in_authperso_custom_fields extends frbr_entity_common_datasource_used_in_custom_fields {
	
	public function __construct($id=0){
		$this->entity_type = 'authperso';
		$this->origin_entity = 'authors';
		$this->prefix = 'authperso';
		parent::__construct($id);
	}
}