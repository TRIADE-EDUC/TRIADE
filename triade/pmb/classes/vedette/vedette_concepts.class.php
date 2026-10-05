<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_concepts.class.php,v 1.8 2020/12/22 16:55:44 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_element.class.php");
require_once($class_path."/concept.class.php");

class vedette_concepts extends vedette_element{
	
	protected $type = TYPE_CONCEPT;
	
	public function __construct($type, $id, $isbd = "", $params = array()) {
	    if (is_numeric($id)) {
	        $id = onto_common_uri::get_uri($id);
	    } else {
	        $id = stripslashes($id);
	    }
		parent::__construct($type, $id, $isbd, $params);
	}
	
	public function set_vedette_element_from_database(){
		$this->entity = new authority(0, $this->get_db_id(), AUT_TABLE_CONCEPT);
		$this->isbd = $this->entity->get_object_instance()->get_display_label();
	}
	
	public function get_db_id() {
		if (!$this->db_id) {
			$this->db_id = onto_common_uri::get_id($this->id);
		}
		return $this->db_id;
	}
	
	public function get_link_see(){
		return str_replace("!!type!!", "concept",$this->get_generic_link());
	}
}