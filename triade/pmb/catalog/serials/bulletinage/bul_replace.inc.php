<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bul_replace.inc.php,v 1.4 2017-09-06 12:29:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/entities/entities_bulletinage_controller.class.php");

print "<h1>".$msg['catal_rep_bul_h1']."</h1>";
$entities_bulletinage_controller = new entities_bulletinage_controller($bul_id);
$entities_bulletinage_controller->set_serial_id($serial_id);
$entities_bulletinage_controller->set_action('replace');
$entities_bulletinage_controller->proceed();

?>		
