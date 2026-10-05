<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authority_page_category.class.php,v 1.4 2023/05/04 09:26:33 jparis Exp $
if (stristr ( $_SERVER ['REQUEST_URI'], ".class.php" )) die ( "no access" );

global $class_path;
require_once ($class_path."/authorities/page/authority_page.class.php");

/**
 * class authority_page_category
 * Controler d'une page d'une autorité catégorie
 */
class authority_page_category extends authority_page {
	public $id;

	/**
	 * Constructeur
	 * @param int $id Identifiant de la catégorie
	 */
	public function __construct($id) {
		$this->id = intval($id);
		$query = "select id_noeud from noeuds where id_noeud = " . $this->id;
		$result = pmb_mysql_query($query);
		if ($result && pmb_mysql_num_rows($result)) {
			$this->authority = new authority (0, $this->id, AUT_TABLE_CATEG);
		}
	}
	
}