<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: publishers.inc.php,v 1.20 2021/04/22 11:53:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;

require_once($class_path."/entities/entities_publishers_controller.class.php");

// gestion des éditeurs
$entities_publishers_controller = new entities_publishers_controller($id);
$entities_publishers_controller->set_url_base('autorites.php?categ=editeurs');
$entities_publishers_controller->proceed();