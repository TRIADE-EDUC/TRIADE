<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_concepts.class.php,v 1.2 2015-03-11 16:18:59 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_element.class.php");
require_once($class_path."/skos/skos_concept.class.php");

class vedette_concepts extends vedette_element{
	
	/**
	 * Clé de l'autorité dans la table liens_opac
	 * @var string
	 */
	protected $key_lien_opac = "lien_rech_concept";
	
	public function __construct($type, $id, $isbd = "") {
		if ($id*1) {
			$id = onto_common_uri::get_uri($id);
		}
		parent::__construct($type, $id, $isbd);
	}
	
	public function set_vedette_element_from_database(){
		$concept = new skos_concept($this->get_db_id());
		$this->isbd = $concept->get_display_label();
	}
	
	public function get_db_id() {
		if (!$this->db_id) {
			$this->db_id = onto_common_uri::get_id($this->id);
		}
		return $this->db_id;
	}
}