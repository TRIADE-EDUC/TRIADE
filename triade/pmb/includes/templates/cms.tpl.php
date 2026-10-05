<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms.tpl.php,v 1.13 2020/11/23 09:02:57 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $include_path;
global $cms_active,$cms_layout,$current_module,$cms_layout_end;

$module_cms = module_cms::get_instance();
$cms_menu = $module_cms->get_left_menu();

$cms_layout = "<div id='conteneur' class='$current_module'>
	$cms_menu
	<div id='contenu'>
	!!menu_contextuel!!
";

$cms_layout_end = "
		</div>
	</div>
";
