<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rapport_tache.inc.php,v 1.7 2023/03/28 13:02:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $pdfdoc, $id;
require_once($class_path."/scheduler/scheduler_dashboard.class.php");

switch($pdfdoc) {
	case "rapport_tache" :
		scheduler_dashboard::show_pdf_report($id);
		break;
	default :
		break;
}

