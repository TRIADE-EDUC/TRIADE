<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_collection.inc.php,v 1.2 2021/02/13 16:19:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/cms/cms_collections.class.php");

switch($sub) {	
	case 'documents':
		$collection = new cms_collection($collection_id);
		print $collection->get_documents_list();
		break;
	case "collection" :
 	default:
		$collections = new cms_collections();
		$collections->process($action);
 		break;
}