<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail.inc.php,v 1.31 2023/11/14 17:01:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once($class_path.'/mail.class.php');

function mailpmb(
    $to_name = "",
    $to_mail = "",
    $object = "",
    $content = "",
    $from_name = "",
    $from_mail = "",
    $headers = "",
    $copy_cc = "",
    $copy_bcc = "",
    $do_nl2br = 0,
    $attachments = array(),
    $reply_name = "",
    $reply_mail = "",
    $is_mailing = false,
    $type = "",
    $num_campaign = 0) {

    $embedded_attachments = mail::transformBase64ImgToEmbeddedAttachments($content);
        
	$mail = new mail();
	$mail->set_type($type)
		->set_to_name($to_name)
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
		->set_embedded_attachments($embedded_attachments)
		->set_reply_name($reply_name)
		->set_reply_mail($reply_mail)
		->set_is_mailing($is_mailing)
		->set_num_campaign($num_campaign);
	
	$sended = $mail->send();
	if($sended) {
		$mail->set_sended(1);
	} else {
		$mail->set_sended(0);
	}
	if(!empty($_SERVER['REQUEST_URI'])) {
		$mail->set_from_uri(substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], "/")+1));
	}
	//A-t-on perdu la connexion MySQL - délai de réponse du serveur de mail
	global $dbh;
	if(!pmb_mysql_ping($dbh)) {
	    $dbh = connection_mysql();
	}
	//Enregistrement en base
	$mail->add();
	return $sended;
}