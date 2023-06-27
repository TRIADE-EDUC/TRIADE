<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_authpersos.class.php,v 1.3 2015-03-16 16:14:45 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_element.class.php");
require_once($class_path."/authperso_authority.class.php");

class vedette_authpersos extends vedette_element{
	public $params = array();
	
	/**
	 * Clé de l'autorité dans la table liens_opac
	 * @var string
	 */
	protected $key_lien_opac = "lien_rech_authperso";
	
	public function __construct($params,$type, $id, $isbd = ""){
		$this->params = $params;
		parent::__construct($type, $id, $isbd);
	}

	public function set_vedette_element_from_database(){
		$auth = new authperso_authority($this->params['id_authority']);
 		$this->isbd = $auth->get_isbd();
	}
}
