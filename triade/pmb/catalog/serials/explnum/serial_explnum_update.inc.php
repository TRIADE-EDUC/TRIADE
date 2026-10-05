<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id$

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/entities/entities_serials_explnum_controller.class.php");

// mise à jour de l'entete de page
echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg['explnum_doc_associe'], $serial_header);

$entities_serials_explnum_controller = new entities_serials_explnum_controller($f_explnum_id);
$entities_serials_explnum_controller->set_serial_id($f_notice);
$entities_serials_explnum_controller->set_action('explnum_update');
$entities_serials_explnum_controller->proceed();

?>