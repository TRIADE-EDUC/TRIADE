<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_ontologies.class.php,v 1.4 2018/12/04 10:26:44 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_element.class.php");
require_once($class_path."/ontology.class.php");

class vedette_ontologies extends vedette_element{
	
	protected $type = TYPE_ONTOLOGY;
	
	public function __construct($type, $id, $isbd = "", $params = array()){
		if(!is_int($id)){
			$id = onto_common_uri::get_id($id);
		}
		parent::__construct($type, $id, $isbd, $params);
	}

	public function set_vedette_element_from_database(){
		$ontology = new ontology($this->params['id_ontology']);
 		$this->isbd = $ontology->get_instance_label(onto_common_uri::get_uri($this->id));
	}
}
