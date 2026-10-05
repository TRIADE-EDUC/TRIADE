<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_aut_link_authpersos.class.php,v 1.3 2019/09/04 13:20:56 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_datasource_aut_link_authpersos extends frbr_entity_common_datasource_aut_link_authorities {
	
	protected $key_name = 'id_authperso_authority';
	protected $table_name = 'authperso_authorities';
	protected $authority_type = 1001;
	
	public function __construct($id=0){
		$this->entity_type = "authperso";
		parent::__construct($id);
	}
}