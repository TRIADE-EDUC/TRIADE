<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_authperso_datasource_authperso_custom_fields.class.php,v 1.1 2019/09/18 09:31:57 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_authperso_datasource_authperso_custom_fields extends frbr_entity_common_datasource_custom_fields {
    
    public function __construct($id=0) {
        $this->entity_type = "authperso";
        parent::__construct($id);
        $this->parameters->prefix = 'authperso';
    }
	
	public function set_authperso_id($id) {
	    $this->parameters->authperso_id = intval($id);
	}
}