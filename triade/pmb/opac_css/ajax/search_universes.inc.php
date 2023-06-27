<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_universes.inc.php,v 1.1 2018-04-26 14:52:32 tsamson Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/search_universes/search_universes_controller.class.php');

$search_universes_controller = new search_universes_controller($id);
$search_universes_controller->proceed_ajax();