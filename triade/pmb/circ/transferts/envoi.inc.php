<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: envoi.inc.php,v 1.16 2022/10/04 09:20:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $action, $msg, $database_window_title;
global $transferts_validation_actif, $nb_per_page;
global $site_destination, $liste_transfert, $form_cb_expl, $motif_refus;
global $transferts_envoi_erreur, $transferts_envoi_OK;

require_once($class_path."/mono_display_expl.class.php");

// Titre de la fenetre
echo window_title($database_window_title.$msg['transferts_circ_menu_envoi'].$msg[1003].$msg[1001]);

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
}

if ($action=="") {

	get_cb_expl('', $msg[661], $msg['transferts_circ_envoi_exemplaire'], "./circ.php?categ=trans&sub=".$sub."&site_destination=".$site_destination."&nb_per_page=".$nb_per_page);

	if ($form_cb_expl != "") {
		//enregistrement de l'envoi
		$res_env = $obj_transfert->enregistre_envoi_cb($form_cb_expl);

		if ($res_env==false) {
			// l'envoi n'est pas valide
			echo $transferts_envoi_erreur;
		} else {
			// l'envoi est fait
			$expl = new mono_display_expl($form_cb_expl,0 ,0);			
			$aff=str_replace("!!cb_expl!!", $expl->header,$transferts_envoi_OK);
			echo str_replace("!!new_location!!", $obj_transfert->new_location_libelle,$aff);
		}
	}
	
	if ($transferts_validation_actif=="1") {
		$list_transferts_envoi_ui = new list_transferts_envoi_ui(array('etat_transfert' => 0, 'etat_demande' => 1));
		print $list_transferts_envoi_ui->get_display_list();
	} else {
		$list_transferts_envoi_ui = new list_transferts_envoi_ui(array('etat_transfert' => 0, 'etat_demande' => array(0,1)));
		print $list_transferts_envoi_ui->get_display_list();
	}
}
?>
