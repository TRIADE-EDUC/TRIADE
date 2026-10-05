<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authority_page_indexint.class.php,v 1.4 2021/06/14 07:38:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/authorities/page/authority_page.class.php");

/**
 * class authority_page_indexint
 * Controler d'une page d'une autorité indexation décimale
 */
class authority_page_indexint extends authority_page {
	/**
	 * Constructeur
	 * @param int $id Identifiant de l'indexation décimale
	 */
	public function __construct($id) {
		$this->id = intval($id);
		$query = "select indexint_id from indexint where indexint_id = ".$this->id;
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)){
			//$this->authority = new authority(0, $this->id, AUT_TABLE_INDEXINT);
			$this->authority = authorities_collection::get_authority('authority', 0, ['num_object' => $this->id, 'type_object' => AUT_TABLE_INDEXINT]);
		}
	}
	
	protected function get_title_recordslist() {
		global $msg, $charset;
		return htmlentities($msg['indexint_see_title']." ".$this->authority->get_object_instance()->name, ENT_QUOTES, $charset);
	}
	
	protected function get_clause_authority_id_recordslist() {
		return "indexint=".$this->id;
	}
	
	protected function get_mode_recordslist() {
		return "indexint_see";
	}
	
}