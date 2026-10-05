<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dashboard.php,v 1.5.2.1 2024/10/15 13:38:22 jparis Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "";  
$base_title = "\$msg[dashboard]";
$base_use_dojo=1;

require_once("$base_path/includes/init.inc.php");
use Pmb\Dashboard\Controller\DashboardController;

global $pmb_dashboard_active;

print "<div id='att' style='z-Index:1000'></div>";

print $menu_bar;
print $extra;
print $extra2;
print $extra_info;

if($use_shortcuts) {
	include("$include_path/shortcuts/circ.sht");
}

if($pmb_dashboard_active) {
    $controller = new DashboardController();
    print $controller->proceed();
} else {
    require_once($class_path."/dashboard/dashboard.class.php");
    
    print $dashboard_layout;
    
    $dashboard = new dashboard();
    print $dashboard->render();
    print $dashboard_layout_end;
}

print $footer;

html_builder();
pmb_mysql_close();