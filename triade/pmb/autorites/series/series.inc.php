<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: series.inc.php,v 1.18 2021/04/22 11:53:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;

require_once($class_path."/entities/entities_series_controller.class.php");

$entities_series_controller = new entities_series_controller($id);
$entities_series_controller->set_url_base('autorites.php?categ=series');
$entities_series_controller->proceed();

?>