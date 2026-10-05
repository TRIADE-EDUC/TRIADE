<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_serialcirc_copy.class.php,v 1.1 2022/08/01 09:50:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

abstract class mail_serialcirc_copy extends mail_root {
	
	protected $serialcirc_copy;
	
	protected function _init_default_settings() {
		parent::_init_default_settings();
		$this->_init_setting_value('sender', 'docs_location');
		$this->_init_setting_value('copy_bcc', '1');
	}
	
	protected function get_mail_to_name() {
		$empr = serialcirc::empr_info($this->mail_to_id);
		return $empr["prenom"]." ".$empr["nom"];
	}
	
	protected function get_mail_to_mail() {
		$empr = serialcirc::empr_info($this->mail_to_id);
		return $empr["mail"];
	}
	
	protected function get_mail_object() {
		global $msg;
		
		return $msg["serialcirc_circ_title"];
	}
	
	protected function get_mail_do_nl2br() {
		return 1;
	}
	
	public function set_serialcirc_copy($serialcirc_copy) {
		$this->serialcirc_copy = $serialcirc_copy;
		return $this;
	}
}