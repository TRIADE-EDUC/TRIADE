<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rapport_tache.inc.php,v 1.5 2017-07-18 17:00:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/scheduler/scheduler_dashboard.class.php");

switch($pdfdoc) {
	case "rapport_tache" :
		scheduler_dashboard::show_pdf_report();
		break;
	default :
		break;
}

