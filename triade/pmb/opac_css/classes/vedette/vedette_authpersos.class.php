<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_authpersos.class.php,v 1.7 2023/05/12 14:31:20 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_element.class.php");

class vedette_authpersos extends vedette_element{
	public $params = array();
	
	/**
	 * Clé de l'autorité dans la table liens_opac
	 * @var string
	 */
	protected $key_lien_opac = "lien_rech_authperso";
	protected $type = TYPE_AUTHPERSO;
	
	public function __construct($type, $id, $isbd = "", $params = array()){
	    $this->entity = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => $id, 'type_object' => AUT_TABLE_AUTHPERSO]);
	    if (empty($params['id_authority'])) {
	        $params['id_authority'] = $this->entity->get_object_instance()->get_authperso_num();
	    }
	    if (empty($params['label'])){
    	    $params['authperso_name'] = $this->entity->get_type_label();
	    } else {
	        $params['authperso_name'] = $params['label'];
	    }
	    parent::__construct($type, $id, $isbd, $params);
	}
	
	public function set_vedette_element_from_database(){
		$this->isbd = "";
	    $this->entity = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => $this->id, 'type_object' => AUT_TABLE_AUTHPERSO]);
	    if ($this->entity->get_object_instance()->get_isbd($this->id)){
    	    $this->isbd = $this->entity->get_object_instance()->get_isbd($this->id);
	    } elseif(class_exists("onto_contribution_datatype_resource_selector")) {
	        $this->isbd = onto_contribution_datatype_resource_selector::get_properties_from_uri($this->id)['isbd'];
	    }
	}
	
	public function get_link_see(){
	    return str_replace("!!type!!", "authperso",$this->get_generic_link());
	}
}
