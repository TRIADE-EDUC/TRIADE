<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_serialcirc_ask_accept.class.php,v 1.3 2023/08/28 14:01:11 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_serialcirc_ask_accept extends mail_serialcirc_ask {
	
	protected function get_mail_content() {
		global $charset, $serialcirc_inscription_accepted_mail,$serialcirc_inscription_end_mail;
		
		if ($charset=="utf-8") { //Templates écrits dans un fichier .tpl.php ISO-8859-1
			$serialcirc_inscription_accepted_mail = encoding_normalize::utf8_normalize($serialcirc_inscription_accepted_mail);
			$serialcirc_inscription_end_mail = encoding_normalize::utf8_normalize($serialcirc_inscription_end_mail);
		}
		
		$mail_content = '';
		
		if($this->serialcirc_ask->ask_info['type']) {
			$mail_content = $serialcirc_inscription_end_mail;
		} else {
			$mail_content = $serialcirc_inscription_accepted_mail;
		}
		$mail_content = str_replace("!!issue!!", $this->serialcirc_ask->ask_info['perio']['header'], $mail_content);
		return $mail_content;
	}
}