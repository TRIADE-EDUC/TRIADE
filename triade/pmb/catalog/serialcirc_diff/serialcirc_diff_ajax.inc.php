<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_diff_ajax.inc.php,v 1.6.6.1 2025/03/04 15:50:02 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

global $class_path, $sub;
global $id_serialcirc, $num_abt, $id_diff, $tablo, $id_caddie;
global $abt_from, $abt_to;

require_once $class_path . "/serialcirc_diff.class.php";

$id_serialcirc = intval($id_serialcirc);
$num_abt = intval($num_abt);
$id_caddie = intval($id_caddie);

switch ($sub) {

    case 'option_form':
        $serialcirc_diff = new serialcirc_diff($id_serialcirc, $num_abt);
        ajax_http_send_response($serialcirc_diff->option_form());
        break;

    case 'ficheformat_form':
        $serialcirc_diff = new serialcirc_diff($id_serialcirc, $num_abt);
        ajax_http_send_response($serialcirc_diff->ficheformat_form());
        break;

    case 'empr_form':
        $serialcirc_diff = new serialcirc_diff($id_serialcirc, $num_abt);
        ajax_http_send_response($serialcirc_diff->empr_form($id_diff));
        break;

    case 'group_form':
        $serialcirc_diff = new serialcirc_diff($id_serialcirc, $num_abt);
        ajax_http_send_response($serialcirc_diff->group_form($id_diff));
        break;

    case 'up_order_circdiff':
        serialcirc_diff::up_order_circdiff($tablo);
        break;

    case 'up_order_circdiffprint':
        serialcirc_diff::up_order_circdiffprint($id_serialcirc, $tablo);
        break;

    case 'up_order_circdiffgroupdrop':
        serialcirc_diff::up_order_circdiffgroupdrop($tablo);
        break;

    case 'get_caddie':
        ajax_http_send_response(serialcirc_diff::get_caddie($id_caddie));
        break;

    case 'duplicate':
        $serialcirc_diff = new serialcirc_diff($id_serialcirc, $abt_from);
        ajax_http_send_response($serialcirc_diff->duplicate($abt_to));
        break;
}
