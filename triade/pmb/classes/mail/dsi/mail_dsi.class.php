<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_dsi.class.php,v 1.4 2023/03/07 15:19:31 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/emprunteur.class.php");

class mail_dsi extends mail_root {
	
	protected function _init_default_settings() {
		parent::_init_default_settings();
		$this->_init_setting_value('sender', 'user');
	}
	
	protected function get_mail_to_name() {
		if(isset($this->mail_to_name)) {
			return $this->mail_to_name;
		}
		return emprunteur::get_name($this->mail_to_id, 1);
	}
	
	protected function get_mail_to_mail() {
		if(isset($this->mail_to_mail)) {
			return $this->mail_to_mail;
		}
		return emprunteur::get_mail_empr($this->mail_to_id);
	}
	
	protected function get_mail_headers() {
		global $charset;
		
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=".$charset."\n";
		return $headers;
	}
	
	protected function get_mail_is_mailing() {
		return true;
	}
	
	public function set_mail_to_id($mail_to_id) {
		$this->mail_to_id = intval($mail_to_id);
		if(! empty($this->mail_to_id)) {
			$this->mail_to_name = emprunteur::get_name($this->mail_to_id, 1);
			$this->mail_to_mail = emprunteur::get_mail_empr($this->mail_to_id);
		}
		return $this;
	}
	
}