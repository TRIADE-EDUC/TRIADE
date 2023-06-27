<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail.inc.php,v 1.33 2018-03-09 13:44:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/class.phpmailer.php');
require_once($class_path.'/class.smtp.php');
require_once($class_path.'/mail.class.php');

if (!defined('PHP_EOL')) define ('PHP_EOL', strtoupper(substr(PHP_OS,0,3) == 'WIN') ? "\r\n" : "\n");

function mailpmb($to_name="", $to_mail, $object="", $content="", $from_name="", $from_mail, $headers, $copy_cc="", $copy_bcc="", $do_nl2br=0, $attachments=array(),$reply_name="",$reply_mail="") {
	
	$mail = new mail();
	$mail->set_to_name($to_name)
		->set_to_mail(explode(';', $to_mail))
		->set_object($object)
		->set_content($content)
		->set_from_name($from_name)
		->set_from_mail($from_mail)
		->set_headers($headers)
		->set_copy_cc(explode(';', $copy_cc))
		->set_copy_bcc(explode(';', $copy_bcc))
		->set_do_nl2br($do_nl2br)
		->set_attachments($attachments)
		->set_reply_name($reply_name)
		->set_reply_mail($reply_mail);
	
	return $mail->send();
}