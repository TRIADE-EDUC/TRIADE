<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_manage_module.inc.php,v 1.3.30.1 2025/04/10 06:56:23 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$cms_module_class_name = "cms_module_".$sub;

$cms_module_class = new $cms_module_class_name();
$managed_module = ($cms_module_class->informations['managed'] && ((SESSrights & CMS_BUILD_AUTH) || $cms_module_class->informations['managedWithoutBuild']));
$menu_contextuel = "";
if ($managed_module) {
	$menu_contextuel = 	"
		<h1>".$msg["cms_manage_module"]." > <span>".htmlentities($cms_module_class->informations['name'],ENT_QUOTES,$charset)."</span></h1>
		<div class='hmenu'>".
			$cms_module_class->get_manage_menu()
		."</div>";
}

$cms_layout = str_replace("!!menu_contextuel!!",$menu_contextuel,$cms_layout);
print $cms_layout;
print "
<script type='text/javascript'>
	require(['dijit/layout/BorderContainer','dijit/layout/ContentPane']);
</script>";

if ($managed_module) {
	switch($action){
		case "save_form" :
			$cms_module_class->save_manage_forms();
		case "get_form" :
		default : 
			print $cms_module_class->get_manage_forms();
			break;
	}
}