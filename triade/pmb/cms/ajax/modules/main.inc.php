<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.35.8.1 2025/04/30 12:52:10 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $var, $in_page, $include_path, $class_path, $cms_module_class, $elem, $id, $cms_module_common_module_id, $action, $cms_build_info;
global $callback, $cancel_callback, $delete_callback, $id_cadre, $classement, $pageid, $name;

require_once($include_path.'/misc.inc.php');

//si l'id n'est pas passé en GET, on récupère le hidden qui se balade dans les posts...
if(!$id){
	if(isset($cms_module_common_module_id)) {
	    $id = (int) $cms_module_common_module_id;
	} else {
		$id = 0;
	}
}
switch($action){
	case "save_form" :
		$element = new $elem($id);
		$cms_build_info = @unserialize(rawurldecode(stripslashes($cms_build_info)));
		$element->set_cms_build_env($cms_build_info);
		$response = $element->save_form();
		break;
	case "delete" :
		$element = new $elem($id);
		$response = $element->delete();
		break;
	case "confirm_delete" :
		$element = new $elem($id);
		$response = $element->confirm_delete();
		break;
	case "cadres_list_in_page" :
		$cms= new cms_build();
		$response=$cms->build_cadres_list_in_page($in_page);
		break;
	case "cadres_list_not_in_page" :
		$cms= new cms_build();
		$response=$cms->build_cadres_list_not_in_page($in_page);
		break;
	case "cadres_list_not_in_cms" :
		$cms= new cms_build();
		$response=$cms->build_cadres_list_not_in_cms($in_page);
		break;
	case "frbr_cadres_list_in_page" :
	    $cms= new cms_build();
	    $response=$cms->build_frbr_cadres_list_in_page($in_page);
	    break;
	case "cadre_save_classement" :
		$cms= new cms_build();
		$response=$cms->save_cadre_classement($id_cadre,$classement);
		break;
	case "unchain_cadre" :
		$cms= new cms_build();
		$response=$cms->unchain_cadre($id_cadre);
		break;
	case "get_env":
		$element = new $elem();
		$response = $element->get_page_env_select($pageid,$name,$var);
		break;
	case "ajax" :
		$element = new $elem($id);
		$response = $element->execute_ajax();
		ajax_http_send_response($response['content'],$response['content-type']);
		break;
	case "duplicate_form" :
	    if(!isset($callback)) $callback = "";
	    if(!isset($cancel_callback) || !$cancel_callback) $cancel_callback = "";
	    if(!isset($delete_callback)) $delete_callback = "";
	    $element = new $elem($id);
		if(isset($cms_module_class) && $cms_module_class){
			$element->set_module_class_name($cms_module_class);
		}
		$element->set_cms_build_env(restore_cms_env($cms_build_info));
	    $element->clean_duplication();
		$response = $element->save_form();
	    break;
	case "get_form" :
	default :
		if (!isset($callback)) $callback = "";
		if (!isset($cancel_callback) || !$cancel_callback) $cancel_callback = "";
		if (!isset($delete_callback)) $delete_callback = "";
		$response = '';
		if (!empty($elem)) {
    		$element = new $elem($id);
    		if (! empty($cms_module_class)) {
    			$element->set_module_class_name($cms_module_class);
    		}
    		$element->set_cms_build_env(restore_cms_env($cms_build_info));
    		$response = $element->get_form(true, $callback, $cancel_callback, $delete_callback);
		}
		break;
}

if($action!="ajax"){
	ajax_http_send_response($response);
}

function restore_cms_env($infos){
	global $cms_build_info;

	$cms_build_info = pmb_base64_decode(unserialize(stripslashes($infos ?? '')));
	return $cms_build_info;
}
