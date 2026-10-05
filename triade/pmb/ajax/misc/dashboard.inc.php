<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dashboard.inc.php,v 1.4 2019/05/29 12:03:09 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $current_dashboard;

require_once($class_path."/encoding_normalize.class.php");

$notification_zone = '';
//chargement du tableau de board du module...
$dashboard_module_name = substr($current_dashboard,0,strpos($current_dashboard,"."));


if(file_exists($class_path."/dashboard/dashboard_module_".$dashboard_module_name.".class.php")){
	//on récupère la classe;
	require_once($class_path."/dashboard/dashboard_module_".$dashboard_module_name.".class.php");
	$dashboard_class_name = "dashboard_module_".$dashboard_module_name;
	$dash = new $dashboard_class_name();

	$infos = $dash->render_infos();
	//Dans certains cas, l'affichage change...
	switch($dashboard_module_name){
		case "dashboard" : 
			break;
		default :				
			if(isset($infos[0])) {
				$notification_zone = encoding_normalize::utf8_normalize($infos[0]['html']);
			} 
			break;
	}
}
ajax_http_send_response(
		array(
			'state' => 1,
			'module' => $current_dashboard,
			'html_notifications' => $notification_zone
	)
);