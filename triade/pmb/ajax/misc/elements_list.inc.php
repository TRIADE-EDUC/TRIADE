<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_list.inc.php,v 1.1.2.2 2025/01/24 13:29:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $type, $search_mode, $page, $hidden_form_name, $action;

if (!isset($_SESSION['search'][$type][$search_mode])) {
    $_SESSION['search'][$type][$search_mode] = '';
}
if(empty($_SESSION['filtered_search'][$type][$search_mode])) {
    $elements = explode(',', $_SESSION['search'][$type][$search_mode]);
} else {
    $elements = explode(',', $_SESSION['filtered_search'][$type][$search_mode]);
}

$elements_list = new elements_list($elements);
$elements_list->set_type($type);
$elements_list->set_search_mode($search_mode);
$elements_list->set_page($page);
if (!empty($hidden_form_name)) {
    $elements_list->set_hidden_form_name($hidden_form_name);
}
switch ($action) {
    case 'get_elements':
        $elements = [
        'elements_list_ui' => $elements_list->get_elements_list_ui(),
        'pager' => $elements_list->get_pager()
        ];
        ajax_http_send_response(encoding_normalize::json_encode($elements));
        break;
    case 'get_pager':
        ajax_http_send_response($elements_list->get_pager());
        break;
}