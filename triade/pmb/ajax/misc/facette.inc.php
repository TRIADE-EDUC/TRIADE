<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facette.inc.php,v 1.10.2.1.2.2 2025/01/24 13:29:52 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $charset, $facet_type, $search_mode, $sended_datas, $pmb_compare_notice_nb, $pmb_compare_notice_template;
global $page, $hidden_form_name;
global $action, $num_facettes_set, $reinit_facettes, $param_delete_facette, $check_facette;

require_once($class_path.'/facettes.class.php');
require_once($class_path.'/facette_search_compare.class.php');
require_once($class_path.'/encoding_normalize.class.php');

switch($sub){
 	case 'get_data':
 	    if (!isset($_SESSION['search'][$facet_type][$search_mode])) {
 	        $_SESSION['search'][$facet_type][$search_mode] = '';
 	    }
 		session_write_close();
 		if (!empty($hidden_form_name)) {
 		    facettes::set_hidden_form_name($hidden_form_name);
 		}
 		facettes::set_facet_type($facet_type);
 		ajax_http_send_response(encoding_normalize::json_encode(facettes::make_ajax_facette($_SESSION['search'][$facet_type][$search_mode])));
 		break;
 	case 'get_filtered_data':
 	    facettes::set_facet_type($facet_type);
 	    $num_facettes_set = intval($num_facettes_set);
 	    if($num_facettes_set) {
 	        facettes::set_session_facettes_set($num_facettes_set);
 	    }
 	    if (!isset($_SESSION['search'][$facet_type][$search_mode])) {
 	        $_SESSION['search'][$facet_type][$search_mode] = '';
 	    }
 	    if (!isset($_SESSION['filtered_search'][$facet_type][$search_mode])) {
 	        $_SESSION['filtered_search'][$facet_type][$search_mode] = '';
 	    }
 	    session_write_close();
 	    if (!empty($hidden_form_name)) {
 	        facettes::set_hidden_form_name($hidden_form_name);
 	    }
 	    facettes::set_facet_type($facet_type);
 	    // Prenons les objets déjà filtrés dans un premier temps. Sinon on prend tous les résultats
 	    if (!empty($_SESSION['filtered_search'][$facet_type][$search_mode])) {
 	        $objects_ids = $_SESSION['filtered_search'][$facet_type][$search_mode];
 	    } else {
 	        $objects_ids = $_SESSION['search'][$facet_type][$search_mode];
 	    }
 	    ajax_http_send_response(encoding_normalize::json_encode(facettes::make_ajax_facette($objects_ids)));
 	    break;
	case 'see_more':
		$sended_datas=encoding_normalize::utf8_decode(json_decode(stripslashes($sended_datas),true));
		ajax_http_send_response(facettes::see_more($sended_datas['json_facette_plus']));
		break;
	case 'compare_see_more':
		if($charset != "utf-8") $sended_datas=encoding_normalize::utf8_normalize($sended_datas);
		$sended_datas=encoding_normalize::utf8_decode(json_decode(stripslashes($sended_datas),true));
		$sended_datas['json_notices_ids']=implode(',',$sended_datas['json_notices_ids']);
		
		$tab_return=array();
		$tab_return['notices'] = encoding_normalize::utf8_normalize(facette_search_compare::call_notice_display($sended_datas['json_notices_ids'], $pmb_compare_notice_nb, $pmb_compare_notice_template));
		if($sended_datas['json_notices_ids']){
			$tab_return['see_more'] = encoding_normalize::utf8_normalize(facette_search_compare::get_compare_see_more($sended_datas['json_notices_ids']));
		}
		ajax_http_send_response(json_encode($tab_return));
		break;
	case 'filters':
	    if (!isset($_SESSION['search'][$facet_type][$search_mode])) {
	        $_SESSION['search'][$facet_type][$search_mode] = '';
	    }
	    $reinit_facettes = intval($reinit_facettes);
	    if($reinit_facettes) {
	        unset($_SESSION['facette']);
	        $_SESSION['filtered_search'][$facet_type][$search_mode] = $_SESSION['search'][$facet_type][$search_mode];
	    }
	    if((isset($param_delete_facette)) || (isset($check_facette) && is_array($check_facette))) {
	        facettes::checked_facette_search();
	    }
	    if($reinit_facettes || isset($param_delete_facette) || empty($_SESSION['filtered_search'][$facet_type][$search_mode])) {
	        $elements = explode(',', $_SESSION['search'][$facet_type][$search_mode]);
	    } else {
	        $elements = explode(',', $_SESSION['filtered_search'][$facet_type][$search_mode]);
	    }
	    $facettes_filters = new facettes_filters($elements);
	    $facettes_filters->set_type($facet_type);
	    $facettes_filters->set_search_mode($search_mode);
	    $page = intval($page);
	    if ($page) {
	       $facettes_filters->set_page($page);
	    }
	    if (!empty($hidden_form_name)) {
	        $facettes_filters->set_hidden_form_name($hidden_form_name);
	    }
	    $facettes_filters->filter_elements();
	    switch ($action) {
	        case 'get_elements':
	            $elements = [
	                   'elements_list_ui' => $facettes_filters->get_elements_list_ui(),
	                   'pager' => $facettes_filters->get_pager()
	            ];
	            ajax_http_send_response(encoding_normalize::json_encode($elements));
	            break;
	        case 'get_pager':
	            ajax_http_send_response($facettes_filters->get_pager());
	        	break;
	    }
	    break;
	case 'session_default_values':
	    facettes::set_facet_type($facet_type);
	    switch ($action) {
	        case 'define':
	            facettes::set_session_default_values();
	            ajax_http_send_response('1');
	            break;
	        case 'get':
	            ajax_http_send_response(facettes::get_session_default_values());
	            break;
	        case 'delete':
	            facettes::delete_session_default_values();
	            ajax_http_send_response('1');
	            break;
	    }
	    break;
}
