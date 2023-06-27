<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr.php,v 1.2 2018-01-12 13:36:54 dgoron Exp $


// définition du minimum nécessaire 
$base_path=".";                            
$base_auth = "CMS_AUTH";  
$base_title = "\$msg[cms_onglet_title]";  
                            
$base_use_dojo=1; 

require_once ("$base_path/includes/init.inc.php");  
require_once($include_path."/templates/cms.tpl.php");
require_once($class_path."/autoloader.class.php");
$autoloader = new autoloader();
if($cms_active && (SESSrights & CMS_BUILD_AUTH)) {
	$autoloader->add_register("cms_modules",true);
}

print " <script type='text/javascript' src='javascript/ajax.js'></script>";
print "<div id='att' style='z-Index:1000'></div>";

print $menu_bar;
print $extra;
print $extra2;
print $extra_info;



if($use_shortcuts) {
	include("$include_path/shortcuts/circ.sht");
}
echo window_title($database_window_title.$msg['frbr'].$msg[1003].$msg[1001]);

require_once($class_path."/modules/module_frbr.class.php");

$module_frbr = new module_frbr();
if(!isset($id)) $id = 0; else $id += 0;
$module_frbr->set_object_id($id);
$module_frbr->set_url_base($base_path.'/frbr.php?categ='.$categ);
$module_frbr->proceed();


// pied de page
print $footer;

// deconnection MYSql
pmb_mysql_close($dbh);