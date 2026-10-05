<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bul_replace.inc.php,v 1.6 2021/04/23 06:26:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $serial_id, $bul_id;

require_once($class_path."/entities/entities_bulletinage_controller.class.php");

$entities_bulletinage_controller = new entities_bulletinage_controller($bul_id);
$entities_bulletinage_controller->set_serial_id($serial_id);
$entities_bulletinage_controller->set_action('replace');
$entities_bulletinage_controller->proceed();

?>