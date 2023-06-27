<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serial_replace.inc.php,v 1.3 2017-09-06 12:29:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/entities/entities_serials_controller.class.php");

print "<h1>".$msg['catal_rep_per_h1']."</h1>";
$entities_serials_controller = new entities_serials_controller($serial_id);
$entities_serials_controller->set_action('replace');
$entities_serials_controller->proceed();

?>