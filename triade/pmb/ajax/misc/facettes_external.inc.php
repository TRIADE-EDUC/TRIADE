<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes_external.inc.php,v 1.4 2023/08/28 14:01:14 tsamson Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $charset, $sended_datas, $pmb_compare_notice_nb, $pmb_compare_notice_template;

require_once($class_path.'/facettes_external.class.php');
require_once($class_path.'/facettes_external_search_compare.class.php');
require_once($class_path.'/encoding_normalize.class.php');

switch($sub){
	case 'get_data':
		session_write_close();
		ajax_http_send_response(encoding_normalize::json_encode(facettes_external::make_ajax_facette($_SESSION['tab_result_external'])));
		break;
	case 'see_more':
		$sended_datas=encoding_normalize::utf8_decode(json_decode(stripslashes($sended_datas),true));
		ajax_http_send_response(facettes_external::see_more($sended_datas['json_facette_plus']));
		break;
	case 'compare_see_more':
		if($charset != "utf-8") $sended_datas=encoding_normalize::utf8_normalize($sended_datas);
		$sended_datas=encoding_normalize::utf8_decode(json_decode(stripslashes($sended_datas),true));
		$sended_datas['json_notices_ids']=implode(',',$sended_datas['json_notices_ids']);
		
		$tab_return=array();
		$tab_return['notices'] = encoding_normalize::utf8_normalize(facettes_external_search_compare::call_notice_display($sended_datas['json_notices_ids'], $pmb_compare_notice_nb, $pmb_compare_notice_template));
		if($sended_datas['json_notices_ids']){
			$tab_return['see_more'] = encoding_normalize::utf8_normalize(facettes_external_search_compare::get_compare_see_more($sended_datas['json_notices_ids']));
		}
		ajax_http_send_response(json_encode($tab_return));
		break;
}
