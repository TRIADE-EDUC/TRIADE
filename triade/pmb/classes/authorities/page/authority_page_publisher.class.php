<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authority_page_publisher.class.php,v 1.3 2021/06/14 07:38:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/authorities/page/authority_page.class.php");

/**
 * class authority_page_publisher
 * Controler d'une page d'une autorité éditeur
 */
class authority_page_publisher extends authority_page {
	/**
	 * Constructeur
	 * @param int $id Identifiant de l'éditeur
	 */
	public function __construct($id) {
		$this->id = intval($id);
		$query = "select ed_id from publishers where ed_id = ".$this->id;
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)){
			$this->authority = new authority(0, $this->id, AUT_TABLE_PUBLISHERS);
		}
	}
	
}