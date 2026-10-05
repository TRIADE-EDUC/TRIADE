<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_serialcirc_copy_none.class.php,v 1.1 2022/08/01 09:50:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_serialcirc_copy_none extends mail_serialcirc_copy {
	
	protected function get_mail_content() {
		global $msg;
		global $biblio_name;
		
		$mail_content=$msg['serialcirc_copy_no_mail_text'];
		$mail_content = str_replace("!!issue!!", $this->serialcirc_copy->get_serial_tit1()."-".$this->serialcirc_copy->get_bulletin_numero(), $mail_content);
		$mail_content = str_replace("!!biblio_name!!", $biblio_name, $mail_content);
		return $mail_content;
	}
}