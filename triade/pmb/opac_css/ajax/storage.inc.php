<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: storage.inc.php,v 1.1 2015-04-14 10:08:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/notice_affichage.class.php");
require_once("$class_path/notice_affichage.ext.class.php");

if($opac_notice_affichage_class == "") $opac_notice_affichage_class = "notice_affichage";

switch($sub){
	case 'save':
		if ($id && $datetime && $token) {
			if ($opac_notice_affichage_class::check_token($id, $datetime, $token)) {
				add_value_session('tab_result_read',$id);
				if($pmb_logs_activate) {
					global $infos_notice,$infos_expl;
					$infos_notice = $opac_notice_affichage_class::get_infos_notice($id);
					$infos_expl = $opac_notice_affichage_class::get_infos_expl($id);
					generate_log();
				}
			}
		}
		break;
	case 'save_all':
		if ($records) {
			$datas = json_decode(stripslashes($records),true);
			if (is_array($datas) && count($datas)) {
				foreach ($datas as $data) {
					if ($data["id"] && $data["datetime"] && $data["token"]) {
						if ($opac_notice_affichage_class::check_token($data["id"], $data["datetime"], $data["token"])) {
							add_value_session('tab_result_read',$data["id"]);
							if($pmb_logs_activate) {
								global $infos_notice,$infos_expl;
								$infos_notice = $opac_notice_affichage_class::get_infos_notice($data["id"]);
								$infos_expl = $opac_notice_affichage_class::get_infos_expl($data["id"]);
								generate_log();
							}
						}
					}
				}
			}
		}
		break;
}
?>