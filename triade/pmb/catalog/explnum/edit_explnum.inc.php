<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: edit_explnum.inc.php,v 1.19 2019-06-05 09:04:42 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $explnum_id, $id, $bul_id;

require_once($class_path."/entities/entities_records_explnum_controller.class.php");

// gestion des doc numeriques

$entities_records_explnum_controller = new entities_records_explnum_controller($explnum_id);
$entities_records_explnum_controller->set_record_id($id);
$entities_records_explnum_controller->set_bulletin_id($bul_id);
$entities_records_explnum_controller->set_action('explnum_form');
$entities_records_explnum_controller->proceed();

?>
	