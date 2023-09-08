<?php
// +-------------------------------------------------+
// Â© 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: retours.inc.php,v 1.14 2018-12-27 10:05:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/list/transferts/list_transferts_retours_ui.class.php");
require_once($class_path."/mono_display_expl.class.php");

// Titre de la fenêtre
echo window_title($database_window_title.$msg['transferts_circ_menu_retour'].$msg[1003].$msg[1001]);

//creation de l'objet transfert
$obj_transfert = new transfert();

switch ($action) {
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
	if(!isset($site_destination)) $site_destination='';
	get_cb_expl($msg['transferts_circ_menu_titre']." > ".$msg['transferts_circ_menu_retour'],
					$msg[661], $msg['transferts_circ_retour_exemplaire'], "./circ.php?categ=trans&sub=".$sub."&site_destination=".$site_destination."&nb_per_page=".$nb_per_page);

	//pour la validation d'un exemplaire
	if ($form_cb_expl != "") {
		
		//enregistre l'acceptation du transfert
		$res_val = $obj_transfert->enregistre_retour_cb($form_cb_expl);
		
		if ($res_val==false) {
			// la validation ne s'est pas faite !
			echo $transferts_retour_acceptation_erreur;
		} else {
			// la validation est faite
			$expl = new mono_display_expl($form_cb_expl,0 ,0);			
			$aff=str_replace("!!cb_expl!!", $expl->header,$transferts_retour_acceptation_OK);
			echo str_replace("!!new_location!!", $obj_transfert->new_location_libelle,$aff);
		}
	} 
	
	$list_transferts_retours_ui = new list_transferts_retours_ui(array('etat_transfert' => 0, 'type_transfert' => 1, 'etat_demande' => 3, 'site_destination' => $deflt_docs_location), array(), array('by' => 'date_retour'));
	print $list_transferts_retours_ui->get_display_list();
}

?>