<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail.class.php,v 1.2 2019-03-12 11:29:17 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail {
    
	protected $id;
	
	protected $to_name;
	
	protected $to_mail;
	
	protected $object;
	
	protected $content;
	
	protected $from_name;
	
	protected $from_mail;
	
	protected $headers;
	
	protected $copy_cc;
	
	protected $copy_bcc;
	
	protected $do_nl2br;
	
	protected $attachments;
	
	protected $reply_name;
	
	protected $reply_mail;
	
	protected $date;
	
	protected static $server_configuration;
	
	public function construct($id=0) {
		$this->id = $id+0;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$this->to_name = '';
		$this->to_mail = array();
		$this->object = '';
		$this->content = '';
		$this->from_name = '';
		$this->from_mail = '';
		$this->headers = array();
		$this->copy_cc = array();
		$this->copy_bcc = array();
		$this->do_nl2br = 0;
		$this->attachments = array();
		$this->reply_name = '';
		$this->reply_mail = '';
		$this->date = date('Y-m-d H:i:s');
		if($this->id) {
			$query = "select * from mails_waiting where id_mail = ".$this->id;
			$result = pmb_mysql_query($query);
			$row = pmb_mysql_fetch_assoc($result);
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
		}
		
	}
	
	public function add() {
		$query = "insert into mails_waiting set
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
			mail_waiting_date = '".addslashes($this->date)."'";
		$result = pmb_mysql_query($query);
		if($result) {
			$this->id = pmb_mysql_insert_id();
			return true;
		} else {
			return false;
		}
	}
	
	public function delete() {
		$query = "delete from mails_waiting where id_mail = ".$this->id;
		pmb_mysql_query($query);
	}
	
	public function send() {
		global $opac_mail_methode,$opac_mail_html_format,$opac_mail_adresse_from;
		global $charset;
		
		$param = explode(",",$opac_mail_methode);
		if (!$param) {
			$param=array() ;
		}
		
		$mail = new PHPMailer();
		//$mail->SMTPDebug=1;
		$mail->CharSet = $charset;
		$mail->SMTPAutoTLS=false;
		
		switch ($param[0]) {
			case 'smtp':
				// $opac_mail_methode = méthode, hote:port, auth, name, pass
				$mail->isSMTP();
				$mail->Host=$param[1];
				if (isset($param[2]) && $param[2]) {
					$mail->SMTPAuth=true ;
					$mail->Username=$param[3] ;
					$mail->Password=$param[4] ;
					if (isset($param[5]) && $param[5]) {
						$mail->SMTPSecure = $param[5]; // pour traitement connexion SSL
						$mail->SMTPAutoTLS=true;
					}
				}
				break ;
			default:
			case 'php':
				$mail->isMail();
				$this->to_name = "";
				break;
		}
		
		if ($opac_mail_html_format) {
			$mail->isHTML(true);
		}
		
		if (trim($opac_mail_adresse_from)) {
			$tmp_array_email = explode(';',$opac_mail_adresse_from);
			if (!isset($tmp_array_email[1])) {
				$tmp_array_email[1]='';
			}
			$mail->setFrom($tmp_array_email[0],$tmp_array_email[1]);
			//Le paramètre ci-dessous est utilisé comme destinataires pour les réponses automatiques (erreur de destinataire, validation anti-spam, ...)
			$mail->Sender=$this->from_mail;
		} else {
			$mail->setFrom($this->from_mail,$this->from_name);
		}
		
		for ($i=0; $i<count($this->to_mail); $i++) {
			$mail->addAddress($this->to_mail[$i], $this->to_name);
		}
		for ($i=0; $i<count($this->copy_cc); $i++) {
			$mail->addCC($this->copy_cc[$i]);
		}
		for ($i=0; $i<count($this->copy_bcc); $i++) {
			$mail->addBCC($this->copy_bcc[$i]);
		}
		if($this->reply_mail && $this->reply_name) {
			$mail->addReplyTo($this->reply_mail, $this->reply_name);
		} else {
			$mail->addReplyTo($this->from_mail, $this->from_name);
		}
		$mail->Subject = $this->object;
		if ($opac_mail_html_format) {
			if ($this->do_nl2br) {
				$mail->Body=wordwrap(nl2br($this->content),70);
			} else {
				$mail->Body=wordwrap($this->content,70);
			}
			if ($opac_mail_html_format==2) {
				$mail->MsgHTML($mail->Body);
			}
		} else {
			$this->content=str_replace("<hr />",PHP_EOL."*******************************".PHP_EOL,$this->content);
			$this->content=str_replace("<hr />",PHP_EOL."*******************************".PHP_EOL,$this->content);
			$this->content=str_replace("<br />",PHP_EOL,$this->content);
			$this->content=str_replace("<br />",PHP_EOL,$this->content);
			$this->content=str_replace(PHP_EOL.PHP_EOL.PHP_EOL,PHP_EOL.PHP_EOL,$this->content);
			$this->content=strip_tags($this->content);
			$this->content=html_entity_decode($this->content,ENT_QUOTES, $charset) ;
			$mail->Body=wordwrap($this->content,70);
		}
		for ($i=0; $i<count($this->attachments) ; $i++) {
			if ($this->attachments[$i]["contenu"] && $this->attachments[$i]["nomfichier"]) {
				$mail->addStringAttachment($this->attachments[$i]["contenu"], $this->attachments[$i]["nomfichier"]) ;
			}
		}
		
		if (!$mail->send()) {
			$retour=false;
			global $error_send_mail ;
			$error_send_mail[] = $mail->ErrorInfo ;
			//echo $mail->ErrorInfo."<br /><br /><br /><br />";
			//echo $mail->Body ;
		} else {
			$retour=true ;
		}
		if ($param[0]=='smtp') {
			$mail->smtpClose();
		}
		unset($mail);
		
		return $retour ;
	}
	
	public function get_to_name() {
		return $this->to_name;
	}
	
	public function get_to_mail() {
		return $this->to_mail;
	}
	
	public function set_to_name($to_name) {
		$this->to_name = $to_name;
		return $this;
	}
	
	public function set_to_mail($to_mail) {
		$this->to_mail = $to_mail;
		return $this;
	}
	
	public function set_object($object) {
		$this->object = $object;
		return $this;
	}
	
	public function set_content($content) {
		$this->content = $content;
		return $this;
	}
	
	public function set_from_name($from_name) {
		$this->from_name = $from_name;
		return $this;
	}
	
	public function set_from_mail($from_mail) {
		$this->from_mail = $from_mail;
		return $this;
	}
	
	public function set_headers($headers) {
		$this->headers = $headers;
		return $this;
	}
	
	public function set_copy_cc($copy_cc) {
		$this->copy_cc = $copy_cc;
		return $this;
	}
	
	public function set_copy_bcc($copy_bcc) {
		$this->copy_bcc = $copy_bcc;
		return $this;
	}
	
	public function set_do_nl2br($do_nl2br) {
		$this->do_nl2br = $do_nl2br;
		return $this;
	}
	
	public function set_attachments($attachments) {
		$this->attachments = $attachments;
		return $this;
	}
	
	public function set_reply_name($reply_name) {
		$this->reply_name = $reply_name;
		return $this;
	}
	
	public function set_reply_mail($reply_mail) {
		$this->reply_mail = $reply_mail;
		return $this;
	}
	
	public static function set_server_configuration($server_configuration) {
		static::$server_configuration = $server_configuration;
	}
	
	public static function get_configuration_form($parameters=array()) {
	
	}
}
	
