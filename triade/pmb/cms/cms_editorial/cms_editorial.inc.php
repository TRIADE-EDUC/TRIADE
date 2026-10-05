<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_editorial.inc.php,v 1.4 2021/02/13 16:18:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//echo window_title($database_window_title.$msg["cms_menu_editorial_sections"].$msg[1003].$msg[1001]);

switch($sub) {			
	case 'list':
		if($action=='clean_cache'){
			cms_cache::clean_cache();
		}
		if($action=='clean_cache_img'){
			cms_cache::clean_cache_img();
		}
		require_once($base_path."/cms/cms_editorial/cms_editorial_list.inc.php");
		break;
	default:
		include_once("$include_path/messages/help/$lang/portail_rubriques.txt");
		break;
}	