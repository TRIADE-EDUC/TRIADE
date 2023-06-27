<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: autorites.php,v 1.15 2017-05-06 12:03:22 dgoron Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "AUTORITES_AUTH";  
$base_title = "\$msg[132]";    
$base_use_dojo = 1;
require_once ("$base_path/includes/init.inc.php");  
require_once($class_path."/authperso.class.php");

// modules propres à autorites.php ou à ses sous-modules
require("$include_path/templates/autorites.tpl.php");
print "<div id='att' style='z-Index:1000'></div>";

print $menu_bar;
print $extra;
print $extra2;
print $extra_info;
if($use_shortcuts) {
	include("$include_path/shortcuts/circ.sht");
}

$authpersos=new authpersos();
$autorites_layout=str_replace('!!authpersos!!',$authpersos->get_menu(), $autorites_layout);

if($categ != 'caddie') {
	print $autorites_layout;
}

include("./autorites/autorites.inc.php");

print $autorites_layout_end;

// pied de page
print $footer;

// deconnection MYSql
pmb_mysql_close($dbh);
