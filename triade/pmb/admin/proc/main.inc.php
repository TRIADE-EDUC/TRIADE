<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.7 2021/02/09 07:25:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/parameters.class.php");
require_once ($include_path."/templates/procs_exp_imp.tpl.php");

switch($sub) {
	case 'clas':
		include("./admin/proc/clas.inc.php");
		break;
	case 'req':
		include("./admin/proc/req.inc.php");
		break;
	case 'proc':
	default:
		include("./admin/proc/proc.inc.php");
		break;
}
