<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_opac_contact_form_confirm.class.php,v 1.1 2023/10/10 07:39:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_opac_contact_form_confirm extends mail_opac_contact_form {
	
	protected function _init_default_settings() {
        parent::_init_default_settings();
        $this->_init_setting_value('reply', '');
    }

    protected function get_recipient() {
        return array();
    }
    
    protected function get_mail_to_name() {
        return $this->get_applicant()['name'];
    }
    
    protected function get_mail_to_mail() {
        return $this->get_applicant()['email'];
    }
    
	protected function get_mail_object() {
	    global $msg;
	    
	    return parent::get_mail_object()." ".$msg['contact_form_send_copy_suffix'];
	}
	
	protected function get_mail_reply_name() {
	    return '';
	}
	
	protected function get_mail_reply_mail() {
	    return '';
	}
}