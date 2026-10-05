<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb.inc.php,v 1.1 2018/06/05 08:31:02 vtouchard Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/pnb/pnb_controller.class.php');

$pnb_controller = new pnb_controller();
$pnb_controller->proceed_ajax($action);