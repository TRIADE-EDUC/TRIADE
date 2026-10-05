<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: storage.inc.php,v 1.2.6.1 2025/02/07 13:49:04 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

global $class_path, $sub;
global $opac_notice_affichage_class, $pmb_logs_activate;
global $id, $datetime, $token;
global $records;

require_once("$class_path/notice_affichage.class.php");
require_once("$class_path/notice_affichage.ext.class.php");

if($opac_notice_affichage_class == "") {
    $opac_notice_affichage_class = "notice_affichage";
}

switch($sub) {

    case 'save':
        $id = intval($id);
        if ($id && $datetime && $token) {
            if ($opac_notice_affichage_class::check_token($id, $datetime, $token)) {
                add_value_session('tab_result_read', $id);
                if($pmb_logs_activate) {
                    global $infos_notice,$infos_expl;
                    $infos_notice = $opac_notice_affichage_class::get_infos_notice($id);
                    $infos_expl = $opac_notice_affichage_class::get_infos_expl($id);
                    generate_log('ajax_storage', [], true);
                }
            }
        }
        break;

    case 'save_all':
        if ($records) {
            $datas = json_decode(stripslashes($records), true);
            if (is_array($datas) && count($datas)) {
                foreach ($datas as $data) {
                    if ($data["id"] && $data["datetime"] && $data["token"]) {
                        if ($opac_notice_affichage_class::check_token($data["id"], $data["datetime"], $data["token"])) {
                            add_value_session('tab_result_read', $data["id"]);
                            if($pmb_logs_activate) {
                                global $infos_notice,$infos_expl;
                                $infos_notice = $opac_notice_affichage_class::get_infos_notice($data["id"]);
                                $infos_expl = $opac_notice_affichage_class::get_infos_expl($data["id"]);
                                generate_log('ajax_storage', [], true);
                            }
                        }
                    }
                }
            }
        }
        break;

    default:
        http_response_code(404);
        break;
}
