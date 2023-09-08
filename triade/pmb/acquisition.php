<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: acquisition.php,v 1.8 2016-02-11 15:48:17 dgoron Exp $


// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "ACQUISITION_AUTH";  
$base_title = "\$msg[acquisition_menu_title]";
$base_use_dojo = 1;
require_once ("$base_path/includes/init.inc.php");  

// modules propres à acquisition.php ou à ses sous-modules
require_once("$include_path/templates/acquisition.tpl.php");
print "<div id='att' style='z-Index:1000'></div>";
print $menu_bar;
print $extra;
print $extra2;
print $extra_info;
if($use_shortcuts) {
	require_once("$include_path/shortcuts/circ.sht");
}

print $acquisition_layout;

require_once("./acquisition/acquisition.inc.php");

print $acquisition_layout_end;

// pied de page
print $footer;

// deconnection MYSql
pmb_mysql_close($dbh);
?>