<?php
// +-------------------------------------------------+
// Â© 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: validation.inc.php,v 1.18 2022/10/04 09:20:22 dgoron Exp $


if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $action, $msg, $database_window_title;
global $nb_per_page;
global $site_destination, $liste_transfert, $form_cb_expl, $motif_refus;
global $transferts_validation_acceptation_erreur, $transferts_validation_acceptation_OK;

require_once($class_path."/mono_display_expl.class.php");

// Titre de la fenêtre
echo window_title($database_window_title.$msg['transferts_circ_menu_validation'].$msg[1003].$msg[1001]);

//creation de l'objet transfert
$obj_transfert = new transfert();

switch ($action) {
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
	case "aff_refus":
		//on affiche l'écran de saisie du refus
		$list_transferts_refus_ui = new list_transferts_refus_ui(array('etat_demande' => 0));
		print $list_transferts_refus_ui->get_display_valid_list();
		break;
	case "refus":
		//on enregistre les refus
		$obj_transfert->enregistre_refus($liste_transfert,$motif_refus);
		$action="";
		break;
}

if ($action == "") {
	//pas d'action donc affichage de la liste des validations en attente

	get_cb_expl('',	$msg[661], $msg['transferts_circ_validation_exemplaire'], "./circ.php?categ=trans&sub=".$sub."&site_destination=".$site_destination."&nb_per_page=".$nb_per_page);

	//pour la validation d'un exemplaire
	if ($form_cb_expl != "") {
		
		//enregistre l'acceptation du transfert
		$res_val = $obj_transfert->enregistre_validation_cb($form_cb_expl);
		
		if ($res_val==false) {
			// la validation ne s'est pas faite !
			echo $transferts_validation_acceptation_erreur;
		} else {
			// la validation est faite
			$expl = new mono_display_expl($form_cb_expl,0 ,0);		
			$aff=str_replace("!!cb_expl!!", $expl->header,$transferts_validation_acceptation_OK);
			echo str_replace("!!new_location!!", $obj_transfert->new_location_libelle,$aff);
		}
	} 

	$list_transferts_validation_ui = new list_transferts_validation_ui(array('etat_transfert' => 0, 'etat_demande' => 0));
	print $list_transferts_validation_ui->get_display_list();
}

?>
