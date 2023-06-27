<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dsi.php,v 1.6 2015-04-03 11:16:23 jpermanne Exp $

// définition du minimum nécéssaire 
$base_path=".";
$base_auth = "DSI_AUTH";  
$base_use_dojo=1;
$base_title = "\$msg[dsi_menu_title]";    
require_once ("$base_path/includes/init.inc.php");  

// modules propres à autorites.php ou à ses sous-modules
require_once($class_path."/notice_tpl_gen.class.php");
require("$include_path/templates/dsi.tpl.php");


print "<div id='att' style='z-Index:1000'></div>";
print $menu_bar;
print $extra;
print $extra2;
print $extra_info;
if($use_shortcuts) {
	include("$include_path/shortcuts/circ.sht");
}

print $dsi_layout;

include("./dsi/main.inc.php");

print $dsi_layout_end;

// pied de page
print $footer;

// deconnection MYSql
pmb_mysql_close($dbh);
