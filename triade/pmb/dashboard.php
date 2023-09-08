<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dashboard.php,v 1.3 2015-04-03 11:16:23 jpermanne Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "";  
$base_title = "\$msg[dashboard]";
$base_use_dojo=1;

require_once ("$base_path/includes/init.inc.php");  
// ini_set('errors_display',1);
// error_reporting(E_ALL);
require_once($class_path."/dashboard/dashboard.class.php");
print "<div id='att' style='z-Index:1000'></div>";

print $menu_bar;
print $extra;
print $extra2;
print $extra_info;
if($use_shortcuts) {
	include("$include_path/shortcuts/circ.sht");
}
print $dashboard_layout;
$dashboard = new dashboard();
print $dashboard->render();
print $dashboard_layout_end;
print $footer;
pmb_mysql_close($dbh);