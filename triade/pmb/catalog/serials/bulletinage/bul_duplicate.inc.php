<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bul_duplicate.inc.php,v 1.4 2017-09-06 12:29:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/entities/entities_bulletinage_controller.class.php");

$entities_bulletinage_controller = new entities_bulletinage_controller($bul_id);
$entities_bulletinage_controller->set_serial_id($serial_id);
$entities_bulletinage_controller->set_action('duplicate');
$entities_bulletinage_controller->proceed();

?>