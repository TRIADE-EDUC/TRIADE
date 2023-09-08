<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail-relance-adhesion.inc.php,v 1.28 2019-05-24 15:47:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/mail/reader/mail_reader_relance_adhesion.class.php");
$mail_reader_relance_adhesion = new mail_reader_relance_adhesion();
$mail_reader_relance_adhesion->send_mail($id_empr);