<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mails_waiting.class.php,v 1.1 2018-04-24 10:18:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/mail.class.php");
require_once($include_path."/templates/mails_waiting.tpl.php");

class mails_waiting {
	
	protected $data;
	
	public function __construct() {
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$query = "select valeur_param from parametres where type_param='pmb' and sstype_param = 'mails_waiting_data'";
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_assoc($result);
		if($row['valeur_param']) {
			$this->data = encoding_normalize::json_decode($row['valeur_param']);
		} else {
			$this->data = array(
					'attachments' => '',
					'max_by_send' => 25
			);
		}
		
	}
	
	public function get_form() {
		global $msg, $charset;
		global $mails_waiting_form_tpl;
		
		$form = $mails_waiting_form_tpl;
		$form = str_replace('!!attachments!!', $this->data['attachments'], $form);
		$form = str_replace('!!max_by_send!!', $this->data['max_by_send'], $form);
		return $form;
	}
	
	/**
	 * Données provenant d'un formulaire
	 */
	public function set_properties_from_form() {
		global $mails_waiting_attachments;
		global $mails_waiting_max_by_send;
		
		$this->data['attachments'] = stripslashes($mails_waiting_attachments);
		$this->data['max_by_send'] = $mails_waiting_max_by_send+0;
	}
	
	/**
	 * Sauvegarde
	 */
	public function save(){
	
		$query = "update parametres set 
			valeur_param = '".addslashes(encoding_normalize::json_encode($this->data))."'
			where type_param = 'pmb'
			and sstype_param = 'mails_waiting_data'
			";
		$result = pmb_mysql_query($query);
		if($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function send() {
		if(empty($this->data['max_by_send'])) {
			$this->data['max_by_send'] = 25;
		}
		// On traite les plus anciens en premier
		$query = "select id_mail from mails_waiting order mail_waiting_date limit ".$this->data['max_by_send'];
		$result = pmb_mysql_query($query);
		
		while($row = pmb_mysql_fetch_object($result)) {
			$mail = new mail($row->id_mail);
			$response = $mail->send();
			if($response) {
				$mail->delete();
			}
		}
	}
	
	public static function proceed() {
		global $action;
	
		$mails_waiting = new mails_waiting();
		switch($action) {
			case 'save':
				$mails_waiting->set_properties_from_form();
				$mails_waiting->save();
				print $mails_waiting->get_form();
				break;
			case 'edit':
			default:
				print $mails_waiting->get_form();
				break;
		}
	}
}