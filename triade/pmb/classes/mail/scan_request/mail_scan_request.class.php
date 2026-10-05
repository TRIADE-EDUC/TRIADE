<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_scan_request.class.php,v 1.2.6.1 2024/11/20 07:43:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/docs_location.class.php");

class mail_scan_request extends mail_root {
	
	protected $request_creation = true;
    
	protected $scan_request;
	
	protected function _init_default_settings() {
		parent::_init_default_settings();
		$this->_init_setting_value('sender', 'user');
		$this->_init_setting_value('copy_bcc', '1');
	}
	
	protected function get_mail_to_name() {
		if ($this->request_creation) {
			$location = new docs_location($this->mail_to_id);
			return $location->libelle;
		} else {
			return $this->scan_request->get_lib_empr($this->scan_request->get_num_dest_empr());
		}
	}
	
	protected function get_mail_to_mail() {
		if ($this->request_creation) {
			$location = new docs_location($this->mail_to_id);
			return $location->email;
		} else {
			return $this->scan_request->get_mail_empr($this->scan_request->get_num_dest_empr());
		}
	}
	
	protected function get_mail_object() {
		global $msg;
		
		if (!$this->request_creation) {
			return $msg["scan_request_update_mail_title"];
		} else {
			return $msg["scan_request_creation_mail_title"];
		}
	}
	
	protected function get_formatted_permalinks() {
		global $opac_url_base;
		
		$permalinks = array();
		$linked_records = $this->scan_request->get_linked_records();
		if(!empty($linked_records)) {
			foreach($linked_records as $record){
				$permalink = $opac_url_base."index.php?lvl=notice_display&id=".$record['id'];
				$permalinks[] = "<a href='".$permalink."&database=".DATA_BASE."'>".$permalink."</a>";
			}
		}
		$linked_bulletin = $this->scan_request->get_linked_bulletin();
		if(!empty($linked_bulletin)) {
			foreach($linked_bulletin as $bulletin){
				$permalink = $opac_url_base."index.php?lvl=bulletin_display&id=".$bulletin['id'];
				$permalinks[] = "<a href='".$permalink."&database=".DATA_BASE."'>".$permalink."</a>";
			}
		}
		return implode(" / ", $permalinks);
	}
	
	protected function get_mail_content() {
		global $msg;
		
		if (!$this->request_creation) {
			$mail_content = $msg["scan_request_update_mail_content"];
		} else {
			$mail_content = $msg["scan_request_creation_mail_content"];
		}
		$mail_content = str_replace("!!scan_title!!", $this->scan_request->get_title(), $mail_content);
		$mail_content = str_replace("!!scan_desc!!", $this->scan_request->get_desc(), $mail_content);
		$mail_content = str_replace("!!scan_nb_scanned_pages!!", $this->scan_request->get_nb_scanned_pages(), $mail_content);
		$mail_content = str_replace("!!scan_dest!!", $this->scan_request->get_lib_empr($this->scan_request->get_num_dest_empr()), $mail_content);
		$mail_content = str_replace("!!scan_status!!", $this->scan_request->get_status()->get_label(), $mail_content);
		$mail_content = str_replace("!!scan_comment!!", $this->scan_request->get_comment(), $mail_content);
		$mail_content = str_replace("!!permalink!!", $this->get_formatted_permalinks(), $mail_content);
		return $mail_content;
	}
	
	protected function get_mail_headers() {
		global $charset;
		
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=".$charset."\n";
		return $headers;
	}
	
	public function set_request_creation($request_creation) {
		$this->request_creation = $request_creation;
	}
	
	public function set_scan_request($scan_request) {
		$this->scan_request = $scan_request;
		return $this;
	}
}