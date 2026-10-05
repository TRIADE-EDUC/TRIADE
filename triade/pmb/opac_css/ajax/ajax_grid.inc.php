<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_grid.inc.php,v 1.1 2017/01/06 16:10:53 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($base_path."/classes/grid.class.php");

$grid = new grid($type);
print grid::json_response($grid->get_status(), $grid->get_data());
?>