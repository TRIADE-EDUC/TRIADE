<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: planificateur.inc.php,v 1.13 2023/11/29 13:41:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $id, $cmd, $pager;

require_once($class_path."/scheduler/scheduler_dashboard.class.php");
require_once($class_path."/connecteurs.class.php");

$id = intval($id);
switch($sub) {
	case 'get_report' :
		print encoding_normalize::utf8_normalize(scheduler_dashboard::get_report($id));
		break;
	case 'reporting':
		$pager = (!empty($pager) ? encoding_normalize::json_decode(stripslashes($pager),true) : array());
		if(!empty($pager['nb_results'])) {
			unset($pager['nb_results']);
		}
		$list_scheduler_dashboard_ui = new list_scheduler_dashboard_ui(array(), $pager);
		print encoding_normalize::utf8_normalize($list_scheduler_dashboard_ui->get_display_caption_list());
		print encoding_normalize::utf8_normalize($list_scheduler_dashboard_ui->get_display_header_list());
		print encoding_normalize::utf8_normalize($list_scheduler_dashboard_ui->get_display_content_list());
		break;
	case 'command':
		$scheduler_dashboard = new scheduler_dashboard();
		print encoding_normalize::utf8_normalize($scheduler_dashboard->command_waiting($id,$cmd));
		break;		
}