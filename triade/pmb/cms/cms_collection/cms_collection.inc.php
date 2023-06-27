<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_collection.inc.php,v 1.1 2013-07-04 12:55:50 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//echo window_title($database_window_title.$msg["cms_menu_editorial_sections"].$msg[1003].$msg[1001]);

require_once($class_path."/cms/cms_collections.class.php");

switch($sub) {	
	case 'documents':
		$cms_layout =str_replace('!!menu_sous_rub!!', " > documents >".$msg["cms_menu_editorial_sections_list"], $cms_layout);
		print $cms_layout;
		$collection = new cms_collection($collection_id);
		print $collection->get_documents_list();
		break;
	case "collection" :
 	default:
 		$cms_layout =str_replace('!!menu_sous_rub!!', " > ".$msg["cms_menu_editorial_sections_list"], $cms_layout);
 		print $cms_layout;
		$collections = new cms_collections();
		$collections->process($action);
 		break;
}