<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: skos_page_concept.class.php,v 1.15 2022/03/10 15:19:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
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
		$this->id = intval($concept_id);
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
		return htmlentities($msg['authperso_doc_auth_title'], ENT_QUOTES, $charset);
		return "";
	}
}