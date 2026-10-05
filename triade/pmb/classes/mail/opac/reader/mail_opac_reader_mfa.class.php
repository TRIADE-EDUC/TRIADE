<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_opac_reader_mfa.class.php,v 1.4 2023/07/18 09:10:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_opac_reader_mfa extends mail_opac_reader {
	
	protected $mfa_mail;
	public static $temp_mfa_secret_code = "";
	
	protected function _init_default_settings() {
		parent::_init_default_settings();
		$this->_init_setting_value('sender', 'docs_location');
	}
	
	protected function get_mail_object() {
		return $this->get_formatted_patterns($this->mfa_mail->getTranslatedObject());
	}
	
	protected function get_mail_content() {
		return $this->get_formatted_patterns($this->mfa_mail->getTranslatedContent());
	}
	
	protected function get_mail_do_nl2br() {
		return 1;
	}

	public function set_mfa_mail($mfa_mail) {
		$this->mfa_mail = $mfa_mail;
		return $this;
	}

	public function set_temp_mfa_secret_code($temp_mfa_secret_code) {
		static::$temp_mfa_secret_code = $temp_mfa_secret_code;
		return $this;
	}
}