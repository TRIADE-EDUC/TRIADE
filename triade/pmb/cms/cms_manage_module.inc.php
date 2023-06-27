<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_manage_module.inc.php,v 1.3 2012-09-20 15:35:47 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$cms_module_class_name = "cms_module_".$sub;

$cms_module_class = new $cms_module_class_name();
$menu_contextuel = 	"
	<h1>".$msg["cms_manage_module"]." > <span>".htmlentities($cms_module_class->informations['name'],ENT_QUOTES,$charset)."</span></h1>
	<div class='hmenu'>".
		$cms_module_class->get_manage_menu()
	."</div>";


$cms_layout = str_replace("!!menu_contextuel!!",$menu_contextuel,$cms_layout);


print $cms_layout;

print "
<script type='text/javascript'>
	require(['dijit/layout/BorderContainer','dijit/layout/ContentPane']);
</script>";
switch($action){
	case "save_form" :
		$cms_module_class->save_manage_forms();
	case "get_form" :
	default : 
		print $cms_module_class->get_manage_forms();
		break;
}

 