<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_section.inc.php,v 1.1 2011-09-14 08:44:13 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//echo window_title($database_window_title.$msg["cms_menu_editorial_sections"].$msg[1003].$msg[1001]);

switch($sub) {			
	case 'list':
		$cms_layout =str_replace('!!menu_sous_rub!!', " > ".$msg["cms_menu_editorial_sections_list"], $cms_layout);
		print $cms_layout;
		require_once($base_path."/cms/cms_sections/cms_sections_list.inc.php");
		break;
	case 'edit':
		$cms_layout =str_replace('!!menu_sous_rub!!', " > ".($id!= "new" ? $msg["cms_new_section_form_title"]:$msg["cms_section_form_title"]), $cms_layout);
		print $cms_layout;
		require_once($base_path."/cms/cms_sections/cms_section_edit.inc.php");
		break;
	case 'save':
		$cms_layout =str_replace('!!menu_sous_rub!!', " > ".$msg["cms_menu_editorial_sections_add"], $cms_layout);
		print $cms_layout;
		require_once($base_path."/cms/cms_sections/cms_section_save.inc.php");
		break;
	case 'del':
		$cms_layout =str_replace('!!menu_sous_rub!!', " > ".$msg["cms_menu_editorial_sections_delete"], $cms_layout);
		print $cms_layout;
		require_once($base_path."/cms/cms_sections/cms_section_delete.inc.php");
		break;
	default:
		$cms_layout =str_replace('!!menu_sous_rub!!', "", $cms_layout);
		print $cms_layout;
		include_once("$include_path/messages/help/$lang/portail_rubriques.txt");
		break;
}		