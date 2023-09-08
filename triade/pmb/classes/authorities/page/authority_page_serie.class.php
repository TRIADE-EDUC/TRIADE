<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authority_page_serie.class.php,v 1.2 2016-01-04 10:39:01 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


require_once($class_path."/authorities/page/authority_page.class.php");

/**
 * class authority_page_serie
 * Controler d'une page d'une autorité série
 */
class authority_page_serie extends authority_page {
	/**
	 * Constructeur
	 * @param int $id Identifiant de la série
	 */
	public function __construct($id) {
		$this->id = $id*1;
		$query = "select serie_id from series where serie_id = ".$this->id;
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)){
			$this->authority = new authority(0, $this->id, AUT_TABLE_SERIES);
		}
	}
	
}