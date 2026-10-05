<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_user_auth.class.php,v 1.1.4.2 2025/05/20 14:00:08 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_user_auth extends mail_user {

	protected function _init_default_settings() {
		parent::_init_default_settings();
		$this->_init_setting_value('sender', 'docs_location');
	}

	public function load_message($lang = 'fr_FR') {
		$this->set_language($lang);
		return $this;
	}

	protected function get_mail_do_nl2br() {
		return 1;
	}
}