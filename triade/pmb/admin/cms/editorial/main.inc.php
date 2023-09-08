<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3 2017-02-01 15:19:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

echo window_title($database_window_title.$msg['editorial_content'].$msg[1003].$msg[1001]);
switch($sub) {
	case "type" :
		switch ($elem) {
			case 'section':
				$admin_layout = str_replace('!!menu_sous_rub!!', $msg['editorial_content_type_section'], $admin_layout);
				break;
			case 'article':
				$admin_layout = str_replace('!!menu_sous_rub!!', $msg['editorial_content_type_article'], $admin_layout);
				break;
			default:
				$admin_layout = str_replace('!!menu_sous_rub!!', '', $admin_layout);
				break;
		}
		print $admin_layout;
		include("./admin/cms/editorial/types.inc.php");
		break;
	case 'publication_state':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['editorial_content_publication_state'], $admin_layout);
		print $admin_layout;
		include("./admin/cms/editorial/publication_states.inc.php");
		break;
	default:
		$admin_layout = str_replace('!!menu_sous_rub!!', "", $admin_layout);
		print $admin_layout;
		include("$include_path/messages/help/$lang/admin_cms_editorial.txt");
		break;
}