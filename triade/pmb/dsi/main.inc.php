<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.18.6.1.2.1 2025/05/23 08:13:29 jparis Exp $

use Pmb\DSI\Models\DSIMigration;

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $include_path, $lang, $categ, $pmb_set_time_limit, $plugin, $sub;

include_once("$class_path/bannette.class.php");
include_once("$class_path/equation.class.php");
include_once("$class_path/classements.class.php");
require_once("$class_path/docs_location.class.php");
include_once("$class_path/rss_flux.class.php");
require_once("./dsi/func_pro.inc.php");
require_once("./dsi/func_common.inc.php");
require_once("$base_path/admin/convert/start_export.class.php");

switch($categ) {
	case 'options':
		include('./dsi/options/main.inc.php');
		break;
	case 'equations':
		include('./dsi/equations/main.inc.php');
		break;
	case 'bannettes':
		include('./dsi/bannettes/main.inc.php');
		break;
	case 'diffuser':
		@set_time_limit($pmb_set_time_limit) ;
		include('./dsi/diffuser/main.inc.php');
		break;
	case 'fluxrss':
		include('./dsi/rss/main.inc.php');
		break;
	case 'docwatch' :
		include_once("./dsi/docwatch/main.inc.php");
		break;
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed("dsi",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	case 'migrate' :
		global $action;
		$migration = new DSIMigration();

		if($action == "start"){
			$migration->migrate();
			parameter::update("dsi", "active", 2);

		} elseif($action == "confirm") {
			DSIMigration::showStartForm();

		} else {
			DSIMigration::showConfirmationForm();
			
		}

		break;
	default:
        include("$include_path/messages/help/$lang/dsi.txt");
		break;
}
