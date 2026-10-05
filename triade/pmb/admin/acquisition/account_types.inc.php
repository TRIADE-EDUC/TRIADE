<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: account_types.inc.php,v 1.5 2019/08/20 09:18:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $id_entity, $id_exercice, $msg, $charset;

// gestion des types de décompte
require_once($class_path.'/rent/rent_account_types.class.php');
require_once($class_path.'/entites.class.php');

switch($action) {
	case 'save':
		$rent_account_types = new rent_account_types($id_entity, $id_exercice);
		$rent_account_types->set_properties_from_form();
		$saved = $rent_account_types->save();
		if($saved) {
			$rent_account_types->set_messages($msg['account_types_success_saved']);
		} else {
			$rent_account_types->set_messages($msg['account_types_error_saved']);
		}
		print $rent_account_types->get_request_type_pref_account_list();
		print $rent_account_types->get_list();
		break;
	case 'save_request_type_pref_account':
		$rent_account_types = new rent_account_types($id_entity, $id_exercice);
		$saved = $rent_account_types->save_request_type_pref_account();		
		if($saved) {
			$rent_account_types->set_request_type_pref_account_messages($msg['account_types_success_saved']);
		} else {
			$rent_account_types->set_request_type_pref_account_messages($msg['account_types_error_saved']);
		}
		print $rent_account_types->get_request_type_pref_account_list();
		print $rent_account_types->get_list();
		break;
	default:
		if($id_entity) {
			$rent_account_types = new rent_account_types($id_entity, $id_exercice);
			print $rent_account_types->get_request_type_pref_account_list();
			print $rent_account_types->get_list();
		} else {
			$entities = entites::get_entities();
			$nb_entities = count($entities);
			if($nb_entities > 1) {
				print entites::get_display_list_entities($entities, 'account_types');
			} elseif($nb_entities == 1) {
				$id_entity = $entities[0]['id'];
				$rent_account_types = new rent_account_types($id_entity, $id_exercice);
				print $rent_account_types->get_request_type_pref_account_list();
				print $rent_account_types->get_list();
			} else {
				//Pas d'etablissements définis pour l'utilisateur
				$error_msg.= htmlentities($msg["acquisition_err_coord"],ENT_QUOTES, $charset)."<div class='row'></div>";
				error_message($msg[321], $error_msg.htmlentities($msg["acquisition_err_par"],ENT_QUOTES, $charset), '1', './admin.php?categ=acquisition');
			}
		}
		break;
}
