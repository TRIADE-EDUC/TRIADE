<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.2 2017-10-19 08:58:33 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/cms/cms_collections.class.php");

if ($categ == "collection") {
	$id += 0;
	$collection = new cms_collection($id);
	switch($action){
		case "get_documents_form" :
			if (!empty($expand_params)) {
				$expand_params_decode = json_decode(stripcslashes($expand_params), true);
				$documents_selected = array();
				if (!empty($expand_params_decode["selected"])) {
					$documents_selected = $expand_params_decode["selected"];					
				}
				$response['content'] = $collection->get_documents_form($documents_selected);
			}
			break;
	}
} elseif ($categ == "collections") {
	$collections = new cms_collections();
	switch ($action) {
		default:
			break;
	}
}

if(!empty($response['content'])) {
	if(empty($response['content-type'])) {
		$response['content-type'] = "text/html";
	}
	ajax_http_send_response($response['content'],$response['content-type']);
}