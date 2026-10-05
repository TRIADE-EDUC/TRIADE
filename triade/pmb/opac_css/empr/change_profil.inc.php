<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: change_profil.inc.php,v 1.4 2020/09/25 07:20:16 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg;
global $action, $id_empr, $renewal_form_fields;

require $class_path.'/emprunteur.class.php';

if(!isset($action)) $action = '';
$id_empr = intval($id_empr);

switch ($action) {
	case "save" :
		
		//verification login
		$check_login_pattern = true;
		if(isset($renewal_form_fields['empr_login'])) {
			$check_login_pattern = emprunteur::check_login_pattern($renewal_form_fields['empr_login']);
		}
		if(!$check_login_pattern) {
			print '<script>window.location = "./empr.php?lvl=change_profil";</script>';
			break;
		}
		
		//login modifie ?
		$check_login_changed = false;
		if(isset($renewal_form_fields['empr_login']) && ($empr_login != $renewal_form_fields['empr_login']) ) {
			$check_login_changed = true;
		}
		
		$emprunteur_datas = emprunteur_display::get_emprunteur_datas($id_empr);
		$emprunteur_datas->set_from_form();
		$emprunteur_datas->save();
		
		//on se deconnecte si le login a change
		if($check_login_changed) {
			print '<script>window.location = "./index.php?logout=1";</script>';
			break;
		}
		
		print '<script>window.location = "./empr.php";</script>';
		break;
		
	case "get_form" :
	default :
		print emprunteur_display::get_display_profil($id_empr);
		break;
}