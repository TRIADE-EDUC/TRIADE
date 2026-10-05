<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_reader_temp_password.class.php,v 1.5.2.1 2024/08/02 11:41:18 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/emprunteur.class.php");

class mail_reader_temp_password extends mail_reader {
	
	protected $temp_password;
	
	protected function _init_default_settings() {
		parent::_init_default_settings();
		$this->_init_setting_value('copy_bcc', '0');
	}
	
	protected function get_mail_object() {
		global $empr_send_pwd_mail_obj;
		
		return $empr_send_pwd_mail_obj;
	}
	
	protected function get_mail_content() {
	    global $empr_send_pwd_mail_text, $opac_url_base;
	    
	    $empr = $this->get_empr_coords();
	    $location = emprunteur::get_location($this->mail_to_id);
	    $url = '<a href="' . $opac_url_base.'empr.php?lvl=change_password&database=' . LOCATION . '">' . $opac_url_base.'empr.php?lvl=change_password&database=' . LOCATION . '</a>';

	    return str_replace([
	        '!!pwd!!',
	        '!!url!!',
	        '!!empr_login!!',
	        '!!empr_name!!',
	        '!!location_name!!',
	        '!!location_adr1!!',
	        '!!location_adr2!!',
	        '!!location_cp!!',
	        '!!location_town!!',
	        '!!location_phone!!',
	        '!!location_email!!',
	        '!!location_website!!',
	        
	    ],[
	        $this->temp_password,
	        $url,
	        $empr->empr_login,
	        $this->get_mail_to_name(),
	        $location->name ?? "",
	        $location->adr1 ?? "",
	        $location->adr2 ?? "",
	        $location->cp ?? "",
	        $location->town ?? "",
	        $location->phone ?? "",
	        $location->email ?? "",
	        $location->website ?? "",
	    ], $empr_send_pwd_mail_text);
	}
	
	protected function get_mail_headers() {
		global $charset;
		
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=".$charset."\n";
		return $headers;
	}
	
	public function set_temp_password($temp_password) {
		$this->temp_password = $temp_password;
		return $this;
	}
}