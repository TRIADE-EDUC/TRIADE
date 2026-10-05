<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_waiting.class.php,v 1.3 2022/08/02 13:56:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/mail.class.php");

class mail_waiting extends mail {
    
	public static $table_name = 'mails_waiting';
	
	protected function fetch_data() {
		$this->init_properties();
		if($this->id) {
			$query = "select * from ".static::$table_name." where id_mail = ".$this->id;
			$result = pmb_mysql_query($query);
			$row = pmb_mysql_fetch_assoc($result);
			$this->type = $row['mail_waiting_type'];
			$this->to_name = $row['mail_waiting_to_name'];
			$this->to_mail = explode(';', $row['mail_waiting_to_mail']);
			$this->object = $row['mail_waiting_object'];
			$this->content = $row['mail_waiting_content'];
			$this->from_name = $row['mail_waiting_from_name'];
			$this->from_mail = $row['mail_waiting_from_mail'];
			$this->headers = encoding_normalize::json_decode($row['mail_waiting_headers']);
			$this->copy_cc = explode(';', $row['mail_waiting_copy_cc']);
			$this->copy_bcc = explode(';', $row['mail_waiting_copy_bcc']);
			$this->do_nl2br = $row['mail_waiting_do_nl2br'];
			$this->attachments = encoding_normalize::json_decode($row['mail_waiting_attachments']);
			$this->reply_name = $row['mail_waiting_reply_name'];
			$this->reply_mail = $row['mail_waiting_reply_mail'];
			$this->date = $row['mail_waiting_date'];
			$this->from_uri = $row['mail_waiting_from_uri'];
			$this->num_campaign = $row['mail_waiting_num_campaign'];
		}
	}
	
	public function add() {
		if(!$this->table_exists()) {
			return false;
		}
		$query = "insert into ".static::$table_name." set
			mail_waiting_type = '".addslashes($this->type)."',
			mail_waiting_to_name = '".addslashes($this->to_name)."',
			mail_waiting_to_mail = '".addslashes(implode(';', $this->to_mail))."',
			mail_waiting_object = '".addslashes($this->object)."',
			mail_waiting_content = '".addslashes($this->content)."',
			mail_waiting_from_name = '".addslashes($this->from_name)."',
			mail_waiting_from_mail = '".addslashes($this->from_mail)."',
			mail_waiting_headers = '".addslashes(encoding_normalize::json_encode($this->headers))."',
			mail_waiting_copy_cc = '".addslashes(implode(';', $this->copy_cc))."',
			mail_waiting_copy_bcc = '".addslashes(implode(';', $this->copy_bcc))."',
			mail_waiting_do_nl2br = '".$this->do_nl2br."',
			mail_waiting_attachments = '".addslashes(encoding_normalize::json_encode($this->attachments))."',
			mail_waiting_reply_name = '".addslashes($this->reply_name)."',
			mail_waiting_reply_mail = '".addslashes($this->reply_mail)."',
			mail_waiting_date = '".addslashes($this->date)."',
			mail_waiting_from_uri = '".addslashes($this->from_uri)."',
			mail_waiting_num_campaign = '".intval($this->num_campaign)."'";
		$result = pmb_mysql_query($query);
		if($result) {
			$this->id = pmb_mysql_insert_id();
			return true;
		} else {
			return false;
		}
	}
}
	
