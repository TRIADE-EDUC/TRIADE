<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_actions.inc.php,v 1.20.10.1 2025/05/06 15:25:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $idaction, $idnote, $iddocnum, $act, $class_path, $msg, $iddemande, $sub, $idstatut;

$iddemande = intval($iddemande);
$idaction = intval($idaction);
$idnote = intval($idnote);
$iddocnum = intval($iddocnum);

require_once "$class_path/demandes_actions.class.php";
require_once "$class_path/demandes.class.php";
require_once "$class_path/explnum_doc.class.php";

$actions = new demandes_actions($idaction);
$demandes = new demandes($iddemande);
$explnum_doc = new explnum_doc($iddocnum);

switch ($sub) {
	case 'com':
		switch ($act) {
			case 'close_fil':
				$actions->close_fil();
				break;
		}
		/*
		 * Liste des actions Questions/Réponses ouverte ou en attente
		 */
		print list_demandes_actions_ui::get_instance(array('type_action' => 1, 'statut_action' => array(1, 2), 'num_user' => SESSuserid))->get_display_list();
		break;
	case 'rdv_plan':
		switch ($act) {
			case 'close_rdv':
				$actions->close_rdv();
				break;
		}
		/*
		 * Liste des RDV planifiés
		 */
		print list_demandes_actions_ui::get_instance(array('type_action' => 4, 'statut_action' => array(1), 'num_user' => SESSuserid))->get_display_list();
		break;
	case 'rdv_val':
		switch ($act) {
			case 'val_rdv':
				$actions->valider_rdv();
				break;
		}
		/*
		 * Formulaire qui gère l'affichage des actions
		 */
		print list_demandes_actions_ui::get_instance(array('type_action' => 4, 'statut_action' => array(2), 'num_user' => SESSuserid))->get_display_list();
		break;
	default:
		switch ($act) {		
			case 'add_action':
			case 'modif':
				$actions->show_modif_form();
				break;
			case 'save_action':
				$actions->set_properties_from_form();
				$actions->save();
				$actions->fetch_data($actions->id_action, false);
				$actions->show_consultation_form();
				break;
			case 'change_statut':
				$actions->change_statut($idstatut);
				$actions->fetch_data($idaction, false);
				$actions->show_consultation_form();
				break;
			case 'see':
				$actions->fetch_data($idaction, false);
				$actions->show_consultation_form();
				break;
			case 'suppr_action':
				$chk = ${"chk_action_".$iddemande};
				if (!empty($chk)) {
				    $nb_chk = count($chk);
				    for ($i = 0; $i < $nb_chk; $i++) {
						$action = new demandes_actions($chk[$i]);
						demandes_actions::delete($action);
					}
				} else {
					demandes_actions::delete($actions);					
				}
				$demandes->fetch_data($iddemande, false);				
				$demandes->show_consult_form();
				break;
			case 'add_docnum':
			case 'modif_docnum':
				$actions->show_docnum_form();
				break;
			case 'save_docnum':
				demandes_actions::get_docnum_values_from_form($explnum_doc);
				demandes_actions::save_docnum($actions, $explnum_doc);
				$actions->fetch_data($actions->id_action, false);
				$actions->show_consultation_form();
				break;
			case 'suppr_docnum':
				demandes_actions::delete_docnum($explnum_doc);
				$actions->fetch_data($actions->id_action, false);
				$actions->show_consultation_form();
				break;
		}
		break;
}

?>