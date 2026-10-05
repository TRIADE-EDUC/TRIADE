<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_section.inc.php,v 1.2 2021/02/13 16:18:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//echo window_title($database_window_title.$msg["cms_menu_editorial_sections"].$msg[1003].$msg[1001]);

switch($sub) {			
	case 'list':
		require_once($base_path."/cms/cms_sections/cms_sections_list.inc.php");
		break;
	case 'edit':
		require_once($base_path."/cms/cms_sections/cms_section_edit.inc.php");
		break;
	case 'save':
		require_once($base_path."/cms/cms_sections/cms_section_save.inc.php");
		break;
	case 'del':
		require_once($base_path."/cms/cms_sections/cms_section_delete.inc.php");
		break;
	default:
		include_once("$include_path/messages/help/$lang/portail_rubriques.txt");
		break;
}		