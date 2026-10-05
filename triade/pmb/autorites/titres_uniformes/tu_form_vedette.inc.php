<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tu_form_vedette.inc.php,v 1.4 2023/08/28 14:01:13 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $role_field, $index;

require_once($class_path."/vedette/vedette_ui.class.php");

$vedette_ui = new vedette_ui(new vedette_composee(0, 'tu_authors'));
$form= $vedette_ui->get_form($role_field, $index, 'saisie_titre_uniforme');
print encoding_normalize::utf8_normalize($form);