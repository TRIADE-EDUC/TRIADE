<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail-prets.inc.php,v 1.14 2019-05-24 15:47:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!$relance) $relance=1;	

require_once($class_path."/mail/reader/loans/mail_reader_loans.class.php");
$mail_reader_loans = new mail_reader_loans();
$mail_reader_loans->send_mail($id_empr, $id_groupe);