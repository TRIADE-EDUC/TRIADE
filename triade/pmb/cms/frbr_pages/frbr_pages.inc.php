<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_pages.inc.php,v 1.10 2022/04/15 12:16:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $action, $id;

require_once($class_path."/frbr/frbr_pages.class.php");
require_once($class_path."/frbr/frbr_page.class.php");
require_once($class_path."/list/frbr/list_frbr_cadres_ui.class.php");

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
		print list_frbr_pages_ui::get_instance()->get_display_list();
		break;
	case 'edit':
		$frbr_page = new frbr_page($id);
		print $frbr_page->get_form();
		break;
	case 'save':
		$frbr_page = new frbr_page($id);
		$frbr_page->set_properties_from_form();
		$frbr_page->save();
		print list_frbr_pages_ui::get_instance()->get_display_list();
		break;
	case 'del':
		frbr_page::delete($id);
		print list_frbr_pages_ui::get_instance()->get_display_list();
		break;
	case 'build':
		$frbr_page = new frbr_entity_common_entity_page($num_page);
		print $frbr_page->get_form_build();
		break;
	case 'cadres':
	    if(!isset($applied_sort)){
	        $applied_sort = array();
	    }
	    $list_frbr_cadres_ui = new list_frbr_cadres_ui(array(), array(), $applied_sort);
	    $list_frbr_cadres_ui->set_applied_sort_from_form();
	    
	    switch($dest) {
	        case "TABLEAU":
	            $list_frbr_cadres_ui->get_display_spreadsheet_list();
	            break;
	        case "TABLEAUHTML":
	            print $list_frbr_cadres_ui->get_display_html_list();
	            break;
	        default:
	            print $list_frbr_cadres_ui->get_display_list();
	            break;
	    }
	    break;
	default:
		include_once("$include_path/messages/help/$lang/frbr_pages.txt");
		break;
}		