<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: departs.inc.php,v 1.15 2022/10/04 09:20:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $action, $msg, $database_window_title;
global $transferts_validation_actif, $nb_per_page;
global $site_destination, $liste_transfert, $form_cb_expl, $motif_refus;
global $transferts_retour_acceptation_erreur, $transferts_envoi_OK, $transferts_validation_acceptation_OK, $transferts_retour_acceptation_OK;

require_once($class_path."/mono_display_expl.class.php");

// Titre de la fenêtre
echo window_title($database_window_title.$msg['transferts_circ_menu_departs'].$msg['1003'].$msg['1001']);

//creation de l'objet transfert
$obj_transfert = new transfert();

switch ($action) {
		case "aff_env":
		$list_transferts_envoi_ui = new list_transferts_envoi_ui(array('etat_demande' => 1));
		print $list_transferts_envoi_ui->get_display_valid_list();
		break;
	case "env":
		//on valide les envois
		$obj_transfert->enregistre_envoi($liste_transfert);
		//on affiche l'ecran principal
		$action = "";
		break;
	case "aff_refus":
		//on affiche l'écran de saisie du refus
		$list_transferts_refus_ui = new list_transferts_refus_ui(array('etat_demande' => 1));
		print $list_transferts_refus_ui->get_display_valid_list();
		break;
	case "refus":
		//on enregistre les refus
		$obj_transfert->enregistre_refus($liste_transfert,$motif_refus);
		$action="";
		break;
	case "aff_val":
		//on affiche l'écran de validation
		$list_transferts_validation_ui = new list_transferts_validation_ui(array('etat_demande' => 0));
		print $list_transferts_validation_ui->get_display_valid_list();
		break;
	case "val":
		//on enregistre les validations des exemplaires sélectionnés
		$obj_transfert->enregistre_validation($liste_transfert);
		$action="";
		break;
		
	case "aff_ret":
		//on affiche l'écran de validation
		$list_transferts_retours_ui = new list_transferts_retours_ui(array('etat_demande' => 3));
		print $list_transferts_retours_ui->get_display_valid_list();
		break;
	case "ret":
		//on enregistre les validations des exemplaires sélectionnés
		$obj_transfert->enregistre_retour($liste_transfert);
		$action="";
		break;
}

if ($action == "") {
	//pas d'action donc affichage de la liste des validations en attente

	get_cb_expl('',	$msg['661'], $msg['transferts_circ_depart_exemplaire'], "./circ.php?categ=trans&sub=".$sub."&site_destination=".$site_destination."&nb_per_page=".$nb_per_page);
// 	print $transferts_parcours_filtres;
	//pour la validation d'un exemplaire
	if ($form_cb_expl != "") {	
		$expl = new mono_display_expl($form_cb_expl,0 ,0);
		$expl_display = $expl->header;
							
		//enregistre l'acceptation du transfert
		$res_val = $obj_transfert->enregistre_validation_cb($form_cb_expl);		
		if ($res_val==false) {
			// la validation ne s'est pas faite !
			// echo $transferts_validation_acceptation_erreur;
			//enregistrement de l'envoi
			$res_env = $obj_transfert->enregistre_envoi_cb($form_cb_expl);
	
			if ($res_env==false) {
				// l'envoi n'est pas valide on tente l'action retour du document
				// echo $transferts_envoi_erreur;
				$res_val = $obj_transfert->enregistre_retour_cb($form_cb_expl);		
				if ($res_val==false) {
					// la validation ne s'est pas faite !
					echo $transferts_retour_acceptation_erreur;
				} else {
					// la validation du retour est faite
					$aff=str_replace("!!cb_expl!!", $expl_display,$transferts_retour_acceptation_OK);
					echo str_replace("!!new_location!!", $obj_transfert->new_location_libelle,$aff);
				}		
				
			} else {
				// l'envoi est fait
				$aff=str_replace("!!cb_expl!!", $expl_display,$transferts_envoi_OK);
				echo str_replace("!!new_location!!", $obj_transfert->new_location_libelle,$aff);
			}			
		} else {
			// la validation de l'acceptation du transfert est faite
			$aff=str_replace("!!cb_expl!!", $expl_display,$transferts_validation_acceptation_OK);
			echo str_replace("!!new_location!!", $obj_transfert->new_location_libelle,$aff);
		}
		
	} 
	
 	// **************************** LISTE DES DEMANDES A VALIDER	
	$list_transferts_validation_ui = new list_transferts_validation_ui(array('etat_transfert' => 0, 'etat_demande' => 0));
	print $list_transferts_validation_ui->get_display_list();
	
	//$filtres="";
	// **************************** LISTE DES ENVOIS A EFFECTUER
	if ($transferts_validation_actif=="1") {
		$list_transferts_envoi_ui = new list_transferts_envoi_ui(array('etat_transfert' => 0, 'etat_demande' => 1));
		print $list_transferts_envoi_ui->get_display_list();
	} else {
		$list_transferts_envoi_ui = new list_transferts_envoi_ui(array('etat_transfert' => 0, 'etat_demande' => array(0,1)));
		print $list_transferts_envoi_ui->get_display_list();
	}
	
	if(!isset($f_etat_dispo)){
		$f_etat_dispo = 1;
	}
	// **************************** LISTE DES RETOUR A EFFECTUER	
	$list_transferts_retours_ui = new list_transferts_retours_ui(array('etat_transfert' => 0, 'type_transfert' => 1, 'etat_demande' => 3, 'f_etat_dispo' => 1), array(), array('by' => 'date_retour'));
	print $list_transferts_retours_ui->get_display_list();
}

?>