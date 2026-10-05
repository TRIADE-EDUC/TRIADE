<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serial_delete.inc.php,v 1.28 2023/04/07 09:12:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $serial_header;
global $serial_id;

echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg['catalog_serie_suppression'], $serial_header);

require_once($class_path."/entities/entities_serials_controller.class.php");

$entities_serials_controller = new entities_serials_controller($serial_id);
$entities_serials_controller->set_action('delete');
$entities_serials_controller->proceed();