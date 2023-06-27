<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms.php,v 1.13 2017-03-31 11:49:37 dgoron Exp $


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
echo window_title($database_window_title.$msg['cms_onglet_title'].$msg[1003].$msg[1001]);

if($cms_active && (SESSrights & CMS_BUILD_AUTH)) {
	$modules_parser = new cms_modules_parser();
	$managed_modules = $modules_parser->get_managed_modules();
	$managed_modules_menu = "";
	foreach($managed_modules as $managed_module){
		$managed_modules_menu.="
			<li><a href='".$managed_module['link']."'>".htmlentities($managed_module['name'],ENT_QUOTES,$charset)."</a></li>";
	}
	$cms_layout = str_replace("!!cms_managed_modules!!",$managed_modules_menu,$cms_layout);
}

switch($categ){
	case "build" :
		$cms_layout = str_replace("!!menu_contextuel!!",$cms_build_menu_tpl,$cms_layout);
		break;
	case "pages" :
		$cms_layout = str_replace("!!menu_contextuel!!",$cms_pages_menu_tpl,$cms_layout);
		break;
	case "frbr_pages" :
		$cms_layout = str_replace("!!menu_contextuel!!",$cms_frbr_pages_menu_tpl,$cms_layout);
		break;
	case "editorial" :
		$cms_layout = str_replace("!!menu_contextuel!!",$cms_editorial_menu_tpl,$cms_layout);	
		break;
	case "section" :
		$cms_layout = str_replace("!!menu_contextuel!!",$cms_section_menu_tpl,$cms_layout);	
		break;
	case "article" :
		$cms_layout = str_replace("!!menu_contextuel!!",$cms_article_menu_tpl,$cms_layout);	
		break;
	case "collection" :
		$cms_layout = str_replace("!!menu_contextuel!!",$cms_collection_menu_tpl,$cms_layout);
		break;
	case "manage" :
		// on gère le menu plus tard...
		break;	
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed("cms",$plugin,$sub,$cms_layout);
		if($file){
			include $file;
		}
		break;
	default : 
		$cms_layout = str_replace("!!menu_contextuel!!","",$cms_layout);
		break;
}
require_once("./cms/cms.inc.php");	

// pied de page
print $footer;

// deconnection MYSql
pmb_mysql_close($dbh);