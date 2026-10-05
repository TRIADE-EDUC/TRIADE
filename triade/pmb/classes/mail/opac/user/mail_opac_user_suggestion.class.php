<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_opac_user_suggestion.class.php,v 1.3 2023/09/15 06:40:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_opac_user_suggestion extends mail_opac_user {
	
	protected $recipient;
	
	protected function _init_default_settings() {
	    parent::_init_default_settings();
	    $this->_init_setting_value('sender', 'docs_location');
	}
	
	protected function get_mail_object() {
		global $msg;
		
		return $msg["mail_sugg_obj"]." ".$this->recipient->aff_quand;
	}
	
	protected function get_mail_from_name() {
	    switch ($this->get_setting_value('sender')) {
	        case 'docs_location':
	            return $this->recipient->location_libelle;
	        default:
	            return parent::get_mail_from_name();
	    }
	}
	
	protected function get_mail_from_mail() {
	    switch ($this->get_setting_value('sender')) {
	        case 'docs_location':
	            return $this->recipient->user_email;
	        default:
	            return parent::get_mail_from_mail();
	    }
	}
	
	public function set_recipient($recipient) {
		$this->recipient = $recipient;
		return $this;
	}
}