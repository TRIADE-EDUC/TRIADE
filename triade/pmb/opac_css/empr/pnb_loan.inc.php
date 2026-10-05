<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb_loan.inc.php,v 1.3 2018/06/05 08:31:02 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// if (!$opac_contribution_area_activate || !$allow_contribution) {
// 	print $msg['empr_contribution_area_unauthorized'];
// 	return false;
// }

require_once($class_path.'/pnb/pnb_controller.class.php');

$pnb_controller = new pnb_controller();
$pnb_controller->proceed();