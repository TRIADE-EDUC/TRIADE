<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.4 2018-01-05 16:26:06 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($caller)) $caller = '';

require_once($class_path."/cms/cms_document.class.php");

$id+=0;
$document = new cms_document($id);
switch($action){
	case "get_form" :
		$action="./ajax.php?module=cms&categ=documents&action=save_form&id=";
		if($caller =="editorial_form"){
			$action="./ajax.php?module=cms&categ=documents&caller=editorial_form&action=save_form&id=";
		}
		$response['content'] = $document->get_form($action);
		break;
	case "save_form" :
		$response['content'] = $document->save_form($caller);
		break;
	case "delete" :
		$response['content'] = $document->delete();
		break;
	case "delete_use" :
		$response['content'] = $document->delete_use();
		break;		
	case "thumbnail" :
		$document->render_thumbnail();
		break;
	case "render" :
		$document->render_doc();
		break;
}

if($response['content']){
	if(empty($response['content-type']))$response['content-type'] = "text/html";
	ajax_http_send_response($response['content'],$response['content-type']);
}