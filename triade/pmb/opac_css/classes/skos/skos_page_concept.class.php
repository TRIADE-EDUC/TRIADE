<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: skos_page_concept.class.php,v 1.13 2017-10-11 14:22:16 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/authorities/page/authority_page.class.php");

/**
 * class skos_page_concept
 * Controler d'une Page OPAC représentant un concept de SKOS
 */
class skos_page_concept extends authority_page {
	
	/**
	 * Constructeur d'une page concept
	 * @param int $concept_id Identifiant du concept à représenter
	 * @return void
	 */
	public function __construct($concept_id) {
		$this->id = $concept_id*1;
		$this->authority = new authority(0, $this->id, AUT_TABLE_CONCEPT);
	}

	protected function get_join_recordslist() {
		return "JOIN index_concept ON notice_id = num_object";
	}
	
	protected function get_clause_authority_id_recordslist() {
		return "num_concept = ".$this->id." AND type_object = ".TYPE_NOTICE;
	}
	
	protected function get_mode_recordslist() {
		return "concept_see";
	}
	
	protected function get_title_recordslist() {
		global $msg, $charset;
		return "";
	}
}