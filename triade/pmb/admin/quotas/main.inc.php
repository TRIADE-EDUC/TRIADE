<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.16 2021/02/19 08:54:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub;
global $qt, $quota, $elements;

require_once($class_path."/quotas.class.php");

//Parse des quotas possibles
if ($sub) $qt=new quota($sub); else quota::parse_quotas();

if ($quota) 
	include("./admin/quotas/quota_test.inc.php");
else {

	switch ($sub) {
		case "":
			break;
		default:
			if (!$elements)
				include("./admin/quotas/quotas_list.inc.php");
			else
				include("./admin/quotas/quota_table.inc.php");
			break;
	}
}
?>