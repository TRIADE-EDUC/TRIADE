<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_pages.inc.php,v 1.1 2012-03-05 16:25:01 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {			
	case 'list':
		$cms_layout =str_replace('!!menu_sous_rub!!', " > ".$msg["cms_menu_page_list"], $cms_layout);
		print $cms_layout;
		require_once($base_path."/cms/cms_pages/cms_pages_list.inc.php");
		break;
	case 'edit':
		$cms_layout =str_replace('!!menu_sous_rub!!', " > ".(!$id ? $msg["cms_new_page_form_title"]:$msg["cms_page_form_title"]), $cms_layout);
		print $cms_layout;
		require_once($base_path."/cms/cms_pages/cms_page_edit.inc.php");
		break;
	case 'save':
		$cms_layout =str_replace('!!menu_sous_rub!!', " > ".$msg["cms_menu_page_add"], $cms_layout);
		print $cms_layout;
		require_once($base_path."/cms/cms_pages/cms_page_save.inc.php");
		break;
	case 'del':
		$cms_layout =str_replace('!!menu_sous_rub!!', " > ".$msg["cms_menu_page_delete"], $cms_layout);
		print $cms_layout;
		require_once($base_path."/cms/cms_pages/cms_page_delete.inc.php");
		break;
	default:
		$cms_layout =str_replace('!!menu_sous_rub!!', "", $cms_layout);
		print $cms_layout;
		include_once("$include_path/messages/help/$lang/cms_pages.txt");
		break;
}		