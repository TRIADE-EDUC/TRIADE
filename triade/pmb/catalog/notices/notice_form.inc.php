<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_form.inc.php,v 1.8 2017/08/11 06:51:06 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/entities/entities_records_controller.class.php");

// page de catalogage

$entities_records_controller = new entities_records_controller($id);
$entities_records_controller->set_action('form');
$entities_records_controller->proceed();

?>