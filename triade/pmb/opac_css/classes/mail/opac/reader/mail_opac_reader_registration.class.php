<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_opac_reader_registration.class.php,v 1.3 2023/07/18 09:10:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_opac_reader_registration extends mail_opac_reader {
	
	protected function _init_default_settings() {
		parent::_init_default_settings();
		$this->_init_setting_value('sender', 'parameter');
	}
	
	protected function get_formatted_patterns($text) {
		global $opac_biblio_name;
		
		$text = str_replace("!!biblio_name!!", $opac_biblio_name, $text);
		return parent::get_formatted_patterns($text);
	}
	
	protected function get_mail_object() {
		global $msg;
		
		return $this->get_formatted_patterns($msg['subs_mail_obj']);
	}
	
	protected function get_mail_content() {
		global $msg;
		global $opac_url_base;
		
		$mail_content = $this->get_formatted_patterns($msg['subs_mail_corps']);
		
		// nouvelle clé de validation :
		$alphanum  = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
		$cle_validation = substr(str_shuffle($alphanum), 0, 20);
		$query = "UPDATE empr set cle_validation = '".$cle_validation."' WHERE id_empr = ".$this->mail_to_id;
		pmb_mysql_query($query);
		
		$lien_validation = "<a href='".$opac_url_base."subscribe.php?subsact=validation&login=".urlencode($this->empr->login)."&cle_validation=$cle_validation'>".$opac_url_base."subscribe.php?subsact=validation&login=".$this->empr->login."&cle_validation=$cle_validation</a>";
		$mail_content = str_replace("!!lien_validation!!",$lien_validation,$mail_content) ;
		
		return $mail_content;
	}
}