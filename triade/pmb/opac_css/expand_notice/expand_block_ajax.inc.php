<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expand_block_ajax.inc.php,v 1.10.4.1 2025/02/07 13:49:04 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

require_once("$class_path/notice_affichage.class.php");
require_once("$class_path/notice_affichage.ext.class.php");

$html = "";
$cmd_tab = explode("|*|*|", $display_cmd);
foreach($cmd_tab as $cmd) {
    if(trim($cmd)) {
        $html .= read_notice_contenu($cmd).'|*|*|';
    }
}

ajax_http_send_response(substr($html, 0, -5));

function read_notice_contenu($cmd)
{
    global $opac_notice_affichage_class,$pmb_logs_activate;

    $param = encoding_normalize::json_decode(stripslashes($cmd), true);
    if($opac_notice_affichage_class == "") {
        $opac_notice_affichage_class = "notice_affichage";
    }
    $display = new $opac_notice_affichage_class($param['id'], $param['aj_liens'], $param['aj_cart'], $param['aj_to_print'], $param['aj_header_only'], !$param['aj_no_header']);
    //$display->do_header_without_html();
    if($param['aj_nodocnum']) {
        $display->docnum_allowed = 0;
    }
    $flag_no_onglet_perso = 0;
    $type_aff = $param['aj_type_aff'];
    switch ($type_aff) {
        case AFF_ETA_NOTICES_ISBD :
            $display->do_isbd();
            $display->genere_simple(0, 'ISBD') ;
            break;
        case AFF_ETA_NOTICES_PUBLIC :
            $display->do_public();
            $display->genere_simple(0, 'PUBLIC') ;
            break;
        case AFF_ETA_NOTICES_BOTH :
            $display->do_isbd();
            $display->do_public();
            $display->genere_double(0, 'PUBLIC') ;
            break ;
        case AFF_ETA_NOTICES_BOTH_ISBD_FIRST :
            $display->do_isbd();
            $display->do_public();
            $display->genere_double(0, 'ISBD') ;
            break ;
        default:
            $display->do_isbd();
            $display->do_public();
            $display->genere_double(0, 'autre') ;
            $flag_no_onglet_perso = 1;
            break ;
    }
    $html = $display->result;
    if(!$flag_no_onglet_perso) {
        $onglet_perso = new notice_onglets();
        $html = $onglet_perso->insert_onglets($param['id'], $html);
    }
    if ($param['id'] && $param['datetime'] && $param['token']) {
        if ($opac_notice_affichage_class::check_token($param['id'], $param['datetime'], $param['token'])) {
            add_value_session('tab_result_read', $param['id']);
            if($pmb_logs_activate) {
                global $infos_notice,$infos_expl;
                $infos_notice = $opac_notice_affichage_class::get_infos_notice($param['id']);
                $infos_expl = $opac_notice_affichage_class::get_infos_expl($param['id']);
                $record_log = generate_log('expand_block_ajax', [], true);
            }
        }
    }
    return $param['id'].'|*|'.$html;
}
