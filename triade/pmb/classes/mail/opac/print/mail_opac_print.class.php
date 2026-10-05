<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_opac_print.class.php,v 1.2 2022/08/01 06:44:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_opac_print extends mail_opac {
	
	protected function _init_default_settings() {
		parent::_init_default_settings();
		$this->_init_setting_value('sender', 'parameter');
	}
	
	protected function get_mail_object() {
		global $msg;
		global $opac_biblio_name, $emailobj;
		
		$date_today = formatdate(today()) ;
		$emailobj = trim(stripslashes($emailobj));
		if (!$emailobj) {
			$emailobj=$msg['print_emailobjet'].' '.$opac_biblio_name.' - '.$date_today;
		}
		return $emailobj;
	}
	
	protected function get_mail_reply_name() {
		global $opac_print_email_sender, $emailexp;
		if($opac_print_email_sender) {
			return $emailexp;
		}
		return '';
	}
	
	protected function get_mail_reply_mail() {
		global $opac_print_email_sender, $emailexp;
		if($opac_print_email_sender) {
			return $emailexp;
		}
		return '';
	}
}