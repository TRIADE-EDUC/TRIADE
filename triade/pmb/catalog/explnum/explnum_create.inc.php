<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_create.inc.php,v 1.18 2022/09/14 14:38:50 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;

require_once($class_path."/entities/entities_records_explnum_controller.class.php");

// gestion des exemplaires numériques

$entities_records_explnum_controller = new entities_records_explnum_controller();
$entities_records_explnum_controller->set_record_id($id);
$entities_records_explnum_controller->set_action('explnum_form');
$entities_records_explnum_controller->proceed();
