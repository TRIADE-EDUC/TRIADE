<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id$

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/entities/entities_serials_explnum_controller.class.php");

// suppression d'un exemplaire numérique de pério
echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg['explnum_doc_associe'], $serial_header);

$entities_serials_explnum_controller = new entities_serials_explnum_controller($explnum_id);
$entities_serials_explnum_controller->set_serial_id($serial_id);
$entities_serials_explnum_controller->set_action('explnum_delete');
$entities_serials_explnum_controller->proceed();


		

