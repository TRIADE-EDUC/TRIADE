<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authority_page_author.class.php,v 1.4 2021/06/14 07:38:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/authorities/page/authority_page.class.php");
require_once($class_path."/authorities/tabs/authority_tabs_author.class.php");
/**
 * class authority_page_author
 * Controler d'une page d'une autorité auteur
 */
class authority_page_author extends authority_page {
	
	/**
	 * Constructeur
	 * @param int $id Identifiant de l'auteur
	 */
	public function __construct($id) {
		$this->id = intval($id);
		$query = "select author_id from authors where author_id = ".$this->id;
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)) {
		    $this->authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0,['num_object'=> $this->id,'type_object' =>AUT_TABLE_AUTHORS]);//new authority(0, $this->id, AUT_TABLE_AUTHORS);
		}
	}
	
	/**
	 * @see authority_page::get_authority_tabs()
	 */
	protected function get_authority_tabs(){
		if(!$this->authority_tabs){
			$this->authority_tabs = new authority_tabs_author($this->authority);
		}
		return $this->authority_tabs;
	}
}