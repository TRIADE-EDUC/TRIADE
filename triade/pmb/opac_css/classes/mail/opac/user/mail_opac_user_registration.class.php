<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_opac_user_registration.class.php,v 1.1 2023/09/13 13:53:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_opac_user_registration extends mail_opac_user {
	
	protected function _init_default_settings() {
		parent::_init_default_settings();
		$this->_init_setting_value('sender', 'parameter');
	}
	
	protected function get_mail_object() {
		global $msg, $opac_biblio_name;
		global $f_nom, $f_prenom;
		
		$mail_object = str_replace("!!biblio_name!!",$opac_biblio_name,$msg['subs_alert_user_mail_obj']) ;
		$mail_object = str_replace("!!empr_name!!", stripslashes($f_nom),$mail_object);
		$mail_object = str_replace("!!empr_first_name!!", stripslashes($f_prenom),$mail_object);
		return $mail_object;
	}
	
	protected function get_mail_content() {
		global $msg;
		global $opac_biblio_name,$pmb_url_base;
		global $f_nom, $f_prenom, $pe_emprcb;
		
		$mail_content = str_replace("!!biblio_name!!",$opac_biblio_name,$msg['subs_alert_user_mail_corps']) ;
		$mail_content = str_replace("!!empr_name!!", stripslashes($f_nom),$mail_content);
		$mail_content = str_replace("!!empr_first_name!!", stripslashes($f_prenom),$mail_content);
		$empr_link = str_replace("!!pmb_url_base!!",$pmb_url_base,$msg['subs_alert_user_mail_empr_link']) ;
		$empr_link = str_replace("!!empr_cb!!",$pe_emprcb,$empr_link);
		$mail_content = str_replace("!!empr_link!!", $empr_link,$mail_content);
		
		return $mail_content;
	}
}