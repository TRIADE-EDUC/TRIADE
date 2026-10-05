<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_serialcirc_ask_refus.class.php,v 1.3 2023/08/28 14:01:11 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_serialcirc_ask_refus extends mail_serialcirc_ask {
	
	protected function get_mail_content() {
		global $charset, $serialcirc_inscription_no_mail;
		
		if ($charset=="utf-8") { //Template écrit dans un fichier .tpl.php ISO-8859-1
			$serialcirc_inscription_no_mail = encoding_normalize::utf8_normalize($serialcirc_inscription_no_mail);
		}
		$mail_content = $serialcirc_inscription_no_mail;
		
		$mail_content = str_replace("!!issue!!", $this->serialcirc_ask->ask_info['perio']['header'], $mail_content);
		return $mail_content;
	}
}