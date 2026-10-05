<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso_form_vedette.inc.php,v 1.3 2023/08/28 14:01:14 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $role_field, $index;

require_once($class_path."/vedette/vedette_ui.class.php");

$vedette_ui = new vedette_ui(new vedette_composee(0, 'responsabilities'));
$form= $vedette_ui->get_form($role_field, $index, 'saisie_authperso');
print encoding_normalize::utf8_normalize($form);