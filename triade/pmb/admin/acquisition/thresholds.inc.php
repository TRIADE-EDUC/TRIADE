<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: thresholds.inc.php,v 1.1 2016-07-28 12:14:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// // gestion des seuils
require_once($class_path.'/thresholds.class.php');
require_once($class_path.'/threshold.class.php');
require_once($class_path."/entites.class.php");

switch($action) {
	case 'edit':
		$threshold = new threshold($id);
		if($id_entity*1) {
			$threshold->set_entity($id_entity);
		}
		print $threshold->get_form();
		break;
	case 'save':
		$threshold = new threshold($id);
		$threshold->set_properties_from_form();
		$threshold->save();
		$thresholds = new thresholds($threshold->get_entity()->id_entite);
		print $thresholds->get_display_list();
		break;
	case 'delete':
		$threshold = new threshold($id);
		$threshold->delete();
		$thresholds = new thresholds($id_entity);
		print $thresholds->get_display_list();
		break;
	default:
		if($id_entity) {
			$thresholds = new thresholds($id_entity);
			print $thresholds->get_display_list();
		} else {
			$entities = entites::get_entities();
			$nb_entities = count($entities);
			if($nb_entities > 1) {
				print entites::get_display_list_entities($entities, 'thresholds');
			} elseif($nb_entities == 1) {
				$id_entity = $entities[0]['id'];
				$thresholds = new thresholds($id_entity);
				print $thresholds->get_display_list();
			} else {
				//Pas d'etablissements définis pour l'utilisateur
				$error_msg.= htmlentities($msg["acquisition_err_coord"],ENT_QUOTES, $charset)."<div class='row'></div>";
				error_message($msg[321], $error_msg.htmlentities($msg["acquisition_err_par"],ENT_QUOTES, $charset), '1', './admin.php?categ=acquisition');
			}
		}
		break;
}
