<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sticks_sheet_models.inc.php,v 1.1 2016-07-26 13:38:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/sticks_sheet/sticks_sheets_controller.class.php");

$sticks_sheets_controller = new sticks_sheets_controller();
print $sticks_sheets_controller->proceed($id, $action);