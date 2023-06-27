<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: publishers.inc.php,v 1.19 2019-06-03 07:04:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $id;

require_once($class_path."/entities/entities_publishers_controller.class.php");

// gestion des éditeurs
print '<h1>'.$msg[140].'&nbsp;: '. $msg[135].'</h1>';

$entities_publishers_controller = new entities_publishers_controller($id);
$entities_publishers_controller->set_url_base('autorites.php?categ=editeurs');
$entities_publishers_controller->proceed();