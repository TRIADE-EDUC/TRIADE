<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail-retard.inc.php,v 1.40 2019-05-24 15:47:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once ("$include_path/notice_authors.inc.php");  
require_once ($include_path."/mail.inc.php") ;
require_once ("$class_path/author.class.php");  
require_once ($class_path."/serie.class.php");

if (empty($relance)) $relance = 1;

require_once($class_path."/mail/reader/loans/mail_reader_loans_late.class.php");
mail_reader_loans_late::set_niveau_relance($relance);
$mail_reader_loans_late = new mail_reader_loans_late();
$mail_reader_loans_late->send_mail($id_empr);