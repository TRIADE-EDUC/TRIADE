<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authority_page_authperso.class.php,v 1.2 2018-07-26 15:25:52 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


require_once($class_path."/authorities/page/authority_page.class.php");

/**
 * class authority_page_authperso
 * Controler d'une page d'une autorité perso
 */
class authority_page_authperso extends authority_page {
	
	/**
	 * Constructeur
	 * @param int $id Identifiant de l'autorité perso
	 */
	public function __construct($id) {
	$this->id = $id*1;
		$query = "select authperso_authority_authperso_num from authperso_authorities where id_authperso_authority= ".$this->id;
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)){
			//$this->authority = new authority(0, $this->id, AUT_TABLE_AUTHPERSO);
			$this->authority = authorities_collection::get_authority('authority', 0, ['num_object' => $this->id, 'type_object' => AUT_TABLE_AUTHPERSO]);
		}
	}

	protected function get_title_recordslist() {
		global $msg, $charset;
		return htmlentities($msg['authperso_doc_auth_title'], ENT_QUOTES, $charset);
	}
	
	protected function get_join_recordslist() {
		return "JOIN notices_authperso ON notice_authperso_notice_num = notice_id";
	}
	
	protected function get_clause_authority_id_recordslist() {
		return "notice_authperso_authority_num=".$this->id;
	}
	
	protected function get_mode_recordslist() {
		return "authperso_see";
	}
}