<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_opac_scan_request.class.php,v 1.3 2023/09/08 06:06:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/docs_location.class.php");

class mail_opac_scan_request extends mail_opac {
	
	protected $request_creation = true;
    
	protected $scan_request;
	
	protected function _init_default_settings() {
	    global $opac_biblio_email;
	    
		parent::_init_default_settings();
		if(!empty($opac_biblio_email)) {
		    $this->_init_setting_value('sender', 'parameter');
		} else {
		    $this->_init_setting_value('sender', 'docs_location');
		}
		$this->_init_setting_value('reply', 'reader');
	}
	
	protected function get_mail_to_name() {
		$location = new docs_location($this->mail_to_id);
		return $location->libelle;
	}
	
	protected function get_mail_to_mail() {
		$location = new docs_location($this->mail_to_id);
		return $location->email;
	}
	
	protected function get_mail_object() {
		global $msg;
		
		return $msg["scan_request_creation_mail_title"];
	}
	
	protected function get_formatted_permalinks() {
		global $opac_url_base;
		
		$permalinks = array();
		$linked_records = $this->scan_request->get_linked_records();
		if(!empty($linked_records)) {
			foreach($linked_records as $record){
				if($record['bulletin_id']) {
					$permalink = $opac_url_base."index.php?lvl=bulletin_display&id=".$record['bulletin_id'];
				} else {
					$permalink = $opac_url_base."index.php?lvl=notice_display&id=".$record['notice_id'];
				}
				$permalinks[] = "<a href='".$permalink."&database=".DATA_BASE."'>".$permalink."</a>";
			}
		}
		return implode(" / ", $permalinks);
	}
	
	protected function get_mail_content() {
		global $msg;
		
		$mail_content = $msg["scan_request_creation_mail_content"];
		$mail_content = str_replace("!!scan_title!!", $this->scan_request->get_title(), $mail_content);
		$mail_content = str_replace("!!scan_desc!!", $this->scan_request->get_desc(), $mail_content);
		$mail_content = str_replace("!!scan_nb_scanned_pages!!", $this->scan_request->get_nb_scanned_pages(), $mail_content);
		$mail_content = str_replace("!!scan_dest!!", $this->scan_request->get_lib_empr($this->scan_request->get_num_dest_empr()), $mail_content);
		$mail_content = str_replace("!!permalink!!", $this->get_formatted_permalinks(), $mail_content);
		return $mail_content;
	}
	
	public function set_request_creation($request_creation) {
		$this->request_creation = $request_creation;
		return $this;
	}
	
	public function set_scan_request($scan_request) {
		$this->scan_request = $scan_request;
		return $this;
	}
}