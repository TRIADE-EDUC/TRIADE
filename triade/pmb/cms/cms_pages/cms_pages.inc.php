<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_pages.inc.php,v 1.4 2021/04/07 13:54:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $sub, $lang, $id;
require_once($class_path."/cms/cms_pages.class.php");

switch($sub) {			
	case 'list':
		print list_cms_pages_ui::get_instance()->get_display_list();
		break;
	case 'edit':
		$page = new cms_page($id);
		print $page->get_form();
		break;
	case 'save':
		$page = new cms_page();
		$page->get_from_form();
		$page->save();
		
		print list_cms_pages_ui::get_instance()->get_display_list();
		break;
	case 'del':
		$page = new cms_page($id);
		$page->delete();
		
		print list_cms_pages_ui::get_instance()->get_display_list();
		break;
	default:
		include_once("$include_path/messages/help/$lang/cms_pages.txt");
		break;
}		