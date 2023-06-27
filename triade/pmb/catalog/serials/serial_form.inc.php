<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serial_form.inc.php,v 1.9 2017-09-06 12:29:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/entities/entities_serials_controller.class.php");

$entities_serials_controller = new entities_serials_controller($id);
$entities_serials_controller->set_action('form');
$entities_serials_controller->proceed();

?>