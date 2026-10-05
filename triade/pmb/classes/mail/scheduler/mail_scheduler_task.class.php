<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_scheduler_task.class.php,v 1.2.6.1 2024/09/24 13:17:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_scheduler_task extends mail_root {
	
	protected $scheduler_task;
	
	protected function _init_default_settings() {
		parent::_init_default_settings();
		$this->_init_setting_value('sender', 'user');
	}
	
	protected function get_mail_object() {
		global $msg;
		
		return $msg["task_alert_user_mail_obj"];
	}
	
	protected function get_mail_content() {
		global $msg;
		global $pmb_url_base;
		
		$mail_content = str_replace("!!task_name!!",$this->scheduler_task->get_libelle_tache(),$msg["task_alert_user_mail_corps"]) ;
		$mail_content = str_replace("!!percent!!",$this->scheduler_task->get_indicat_progress(),$mail_content) ;
		$mail_content = str_replace("!!pmb_url_base!!",$pmb_url_base,$mail_content) ;
		return $mail_content;
	}
	
	protected function get_mail_headers() {
		global $charset;
		
		return "Content-type: text/plain; charset=".$charset."\n";
	}
	
	public function send_mail() {
		global $lang;
		
		$this->set_language($lang);
		$sended = $this->mailpmb();
		$this->restaure_language();
		return $sended;
	}
	
	public function set_scheduler_task($scheduler_task) {
		$this->scheduler_task = $scheduler_task;
		return $this;
	}
}