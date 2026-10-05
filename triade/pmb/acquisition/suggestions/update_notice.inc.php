<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: update_notice.inc.php,v 1.41 2022/01/07 14:00:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $forcage, $class_path, $id, $ret_url, $f_tit1, $signature, $id_sug, $msg;

if(!isset($forcage)) $forcage = 0;

require_once($class_path."/entities/entities_records_controller.class.php");
require_once($class_path."/parametres_perso.class.php");

$entities_records_controller = new entities_records_controller($id);
if($entities_records_controller->has_rights()) {
	// On a besoin de récupérer le tit1 sur forcage
	if ($forcage == 1) {
		$tab= unserialize(stripslashes($ret_url));
		foreach($tab->GET as $key => $val){
			add_sl($val);
			$GLOBALS[$key] = $val;
		}
		foreach($tab->POST as $key => $val){
			add_sl($val);
			$GLOBALS[$key] = $val;
		}
	}
	$p_perso=new parametres_perso("notices");
	$nberrors=$p_perso->check_submited_fields();
	$tit1 = clean_string($f_tit1);
	if(trim($tit1)&&(!$nberrors)) {
		$myNotice = new notice($id);
		$myNotice->signature = $signature;
		$myNotice->target_link_on_error = "./acquisition.php?categ=sug&action=modif&id_bibli=0&id_sug=".$id_sug;
		$myNotice->set_properties_from_form();
		$saved = $myNotice->save();
		if($saved) {
			
		} else {
			// echec de la requete
			error_message('', $msg[281], 1, "./acquisition.php?categ=sug&action=modif&id_bibli=0&id_sug=".$id_sug);
		}
	} else {
		if (!trim($tit1)) {
			// erreur : le champ tit1 est vide
			if($id) {
				$notitle_message = $msg[280];
			} else {
				$notitle_message = $msg[279];
			}
			error_message('', $notitle_message, 1, "./acquisition.php?categ=sug&action=modif&id_bibli=0&id_sug=".$id_sug);
		} else {
			error_message_history($msg["notice_champs_perso"],$p_perso->error_message,1);
		}
	}
}