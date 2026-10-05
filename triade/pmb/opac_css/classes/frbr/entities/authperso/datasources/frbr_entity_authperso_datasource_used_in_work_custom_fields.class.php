<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_authperso_datasource_used_in_work_custom_fields.class.php,v 1.2 2021/01/21 10:31:58 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_authperso_datasource_used_in_work_custom_fields extends frbr_entity_common_datasource_used_in_custom_fields {
	
	public function __construct($id=0){
		$this->entity_type = 'works';
		$this->origin_entity = 'authperso';
		$this->prefix = 'tu';
		parent::__construct($id);
	}
}