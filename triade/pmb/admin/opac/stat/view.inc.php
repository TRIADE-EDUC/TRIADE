<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: view.inc.php,v 1.2 2022/03/23 10:30:24 dgoron Exp $at

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $act;

require_once("$class_path/stat_view.class.php");

$stat_view = new stat_view($act);

$stat_view->proceed();


?>