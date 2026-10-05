<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_replace.inc.php,v 1.4 2021/04/23 06:26:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;

require_once($class_path."/entities/entities_records_controller.class.php");

$entities_records_controller = new entities_records_controller($id);
$entities_records_controller->set_action('replace');
$entities_records_controller->proceed();

?>