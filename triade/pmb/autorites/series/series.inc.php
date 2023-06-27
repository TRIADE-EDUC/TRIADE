<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: series.inc.php,v 1.17 2019-06-03 07:04:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $id;

require_once($class_path."/entities/entities_series_controller.class.php");

print '<h1>'.$msg[140].'&nbsp;: '. $msg[333].'</h1>';

$entities_series_controller = new entities_series_controller($id);
$entities_series_controller->set_url_base('autorites.php?categ=series');
$entities_series_controller->proceed();

?>