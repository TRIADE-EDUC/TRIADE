<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_pages.inc.php,v 1.6 2017-05-10 16:08:23 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/frbr/frbr_pages.class.php");
require_once($class_path."/frbr/frbr_page.class.php");

if(!isset($autoloader)) {
	$autoloader = new autoloader();
}
$autoloader->add_register("frbr_entities",true);

switch($sub) {			
	case 'list':
		switch ($action) {
			case 'up':
				$frbr_page = new frbr_page($id);
				$frbr_page->up_order();
				break;
			case 'down':
				$frbr_page = new frbr_page($id);
				$frbr_page->down_order();
				break;
		}
		$cms_layout =str_replace('!!menu_sous_rub!!', " > ".$msg["frbr_page_list_menu"], $cms_layout);
		print $cms_layout;
		$frbr_pages = new frbr_pages();
		print $frbr_pages->get_display_list();
		break;
	case 'edit':
		$cms_layout =str_replace('!!menu_sous_rub!!', " > ".(!$id ? $msg["frbr_page_add"]:$msg["frbr_page_edit"]), $cms_layout);
		print $cms_layout;
		$frbr_page = new frbr_page($id);
		print $frbr_page->get_form();
		break;
	case 'save':
		$cms_layout =str_replace('!!menu_sous_rub!!', " > ".$msg["frbr_page_list_menu"], $cms_layout);
		print $cms_layout;
		$frbr_page = new frbr_page($id);
		$frbr_page->set_properties_from_form();
		$frbr_page->save();
		$frbr_pages = new frbr_pages();
		print $frbr_pages->get_display_list();
		break;
	case 'del':
		$cms_layout =str_replace('!!menu_sous_rub!!', " > ".$msg["frbr_page_list_menu"], $cms_layout);
		print $cms_layout;
		frbr_page::delete($id);
		$frbr_pages = new frbr_pages();
		print $frbr_pages->get_display_list();
		break;
	case 'build':
		$cms_layout =str_replace('!!menu_sous_rub!!', " > ".$msg["frbr_page_tree_build"], $cms_layout);
		print $cms_layout;
		$frbr_page = new frbr_entity_common_entity_page($num_page);
		print $frbr_page->get_form_build();
		break;
	default:
		$cms_layout =str_replace('!!menu_sous_rub!!', "", $cms_layout);
		print $cms_layout;
		include_once("$include_path/messages/help/$lang/frbr_pages.txt");
		break;
}		