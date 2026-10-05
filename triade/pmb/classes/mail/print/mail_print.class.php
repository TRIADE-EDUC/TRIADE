<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_print.class.php,v 1.2 2022/08/01 06:44:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_print extends mail_root {
	
	protected function _init_default_settings() {
		parent::_init_default_settings();
		$this->_init_setting_value('sender', 'user');
		$this->_init_setting_value('copy_bcc', '1');
	}
	
	protected function get_mail_object() {
		global $msg;
		global $opac_biblio_name, $emailobj;
		
		$date_today = formatdate(today()) ;
		$emailobj=$_SESSION['PRINT']['emailobj'];
		$mail_object = trim(stripslashes($emailobj));
		if (!$mail_object) {
			$mail_object=$msg['print_emailobj'].' '.$opac_biblio_name.' - '.$date_today;
		}
		return $mail_object;
	}
	
	protected function get_mail_headers() {
		global $charset;
		
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=".$charset."\n";
		return $headers;
	}
}