<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.4 2023/08/31 08:34:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $lvl, $action, $object_type;

if(empty($_SESSION["id_empr_session"])) {
	return;
}
switch ($lvl) {	
	case 'all':
	case 'old':
	    switch($action) {
	        case "list":
	            if(strpos($object_type, 'opac_loans_groups_reader_ui') !== false) {
	                $id_group = str_replace('opac_loans_groups_reader_ui_', '', $object_type);
	                list_opac_loans_groups_reader_ui::set_id_group($id_group);
	                $object_type='opac_loans_groups_reader_ui';
	            }
	            lists_controller::proceed_ajax($object_type);
	            break;
	    }
	    break;
	case 'make_sugg':
	case 'rss_see':
	case 'view_sugg':
		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type);
				break;
		}
		break;		
	break;
	default:
    	break;		
}