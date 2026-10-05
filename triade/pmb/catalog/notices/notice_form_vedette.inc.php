<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_form_vedette.inc.php,v 1.2 2023/08/28 14:01:13 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/vedette/vedette_ui.class.php");

$vedette_ui = new vedette_ui(new vedette_composee(0, 'notice_authors'));
$form= $vedette_ui->get_form($role_field, $index, 'notice');
print encoding_normalize::utf8_normalize($form);