<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_extended_search.inc.php,v 1.8 2023/08/17 09:47:57 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/search.class.php");

if (!isset($search_xml_file)) $search_xml_file = '';
$sc = new search($search_xml_file);

switch ($sub) {
	case 'get_already_selected_fields' :
		if ($add_field && $delete_field==="") {
			if(empty($search)) {
				$search = array();
			}
			$search[] = $add_field;
		}
		print $sc->get_already_selected_fields();
		print '<script>';
		print $sc->get_script_window_onload();
		print '</script>';
		break;
		
    case 'get_data_search' :
        $data = array();
        $data['human_query'] = encoding_normalize::utf8_normalize($sc->make_human_query());
        $data['search'] = $sc->json_encode_search();
        $data['search_serialize'] = $sc->serialize_search();
        ajax_http_send_response($data);
        break;
            
        default :
            break;
}
