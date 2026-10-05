<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_universes.inc.php,v 1.4 2022/04/15 12:16:06 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;

require_once "$class_path/search_universes/search_universes_controller.class.php";

$search_universes_controller = new search_universes_controller($id);
$search_universes_controller->proceed_ajax();