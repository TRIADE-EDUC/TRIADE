<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authority_page_titre_uniforme.class.php,v 1.2 2018-07-26 15:25:52 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


require_once($class_path."/authorities/page/authority_page.class.php");
require_once($class_path."/authorities_collection.class.php");

/**
 * class authority_page_titre_uniforme
 * Controler d'une page d'une autorité titre uniforme
 */
class authority_page_titre_uniforme extends authority_page {
	/**
	 * Constructeur
	 * @param int $id Identifiant du titre uniforme
	 */
	public function __construct($id) {
		$this->id = $id*1;
		$query = "select tu_id from titres_uniformes where tu_id = ".$this->id;
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)){
			//$this->authority = new authority(0, $this->id, AUT_TABLE_TITRES_UNIFORMES);
			$this->authority = authorities_collection::get_authority('authority', 0, ['num_object' => $this->id, 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
		}
	}
	
	protected function get_title_recordslist() {
		global $msg, $charset;
		return htmlentities($msg['doc_titre_uniforme_title'], ENT_QUOTES, $charset);
	}
	
	protected function get_join_recordslist() {
		return "JOIN notices_titres_uniformes ON ntu_num_notice=notice_id";
	}
	
	protected function get_clause_authority_id_recordslist() {
		return "ntu_num_tu=".$this->id;
	}
	
	protected function get_mode_recordslist() {
		return "titre_uniforme_see";
	}
}