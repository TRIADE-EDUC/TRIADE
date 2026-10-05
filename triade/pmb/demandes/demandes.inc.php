<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes.inc.php,v 1.16.8.1 2025/01/10 15:26:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $base_path, $idaction, $iddemande, $act, $chk, $state;

require_once "$class_path/demandes_types.class.php";
require_once "$class_path/demandes.class.php";
require_once "$class_path/demandes_actions.class.php";
require_once "$class_path/rapport.class.php";
require_once "$base_path/demandes/export_format/report_to_rtf.class.php";

$iddemande = intval($iddemande);
$idaction = intval($idaction);

$demande = new demandes($iddemande);
$actions = new demandes_actions($idaction);
$rap = new rapport_demandes($iddemande);

switch ($act) {
	case 'new':
		$demande->show_modif_form();
		break;
	case 'save':
		$demande->set_properties_from_form();
		$demande->save();
		$demande->fetch_data($demande->id_demande);
		$demande->show_consult_form();
		break;	
	case 'modif':
		$demande->show_modif_form();
		break;
	case 'suppr_noti':
		$requete = "SELECT num_notice FROM demandes WHERE id_demande =$iddemande AND num_notice != 0";
		$result = pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($result) > 0) {
			$demande->suppr_notice_form();
		} else {
			demandes::delete($demande);
			print list_demandes_ui::get_instance()->get_display_list();
		}
		break;
	case 'suppr':
		demandes::delete($demande);
		print list_demandes_ui::get_instance()->get_display_list();
		break;
	case 'see_dmde':
		$demande->show_consult_form();
		break;
	case 'save_action':
		$actions->save();
		$demande->show_consult_form();
		break;
	case 'change_state':
		if (!empty($chk)) {
		    $nb_chk = count($chk);
		    for ($i = 0; $i < $nb_chk; $i++) {
				$dde = new demandes($chk[$i]);
				$dde->change_state($state);
			}
		} else {
			$demande->change_state($state);
			$demande->fetch_data($iddemande);
		}
		$demande->show_consult_form();
		break;
	case 'attach':
		$demande->show_docnum_to_attach();
		break;	
	case 'save_attach':
		$demande->attach_docnum();
		$demande->show_consult_form();
		break;	
	case 'notice':
		$demande->show_notice_form();
		break;
	case 'upd_notice':
		include "$base_path/demandes/update_notice.inc.php";
		$demande->show_consult_form();
		break;
	case 'rapport':
		$rap->showRapport();
		break;
	case 'create_notice':
		$demande->create_notice();		
		$demande->show_consult_form();
		break;
	case 'delete_notice':		
		$demande->delete_notice();
		$demande->show_consult_form();
		break;
	case 'final_response':
		$demande->show_repfinale_form();		
		break;
	case 'save_repfinale':
		$demande->save_repfinale();
		$demande->fetch_data($iddemande);
		$demande->show_consult_form();	
		break;
	case 'suppr_repfinale':
		$demande->suppr_repfinale();
		$demande->fetch_data($iddemande);
		$demande->show_consult_form();
		break;
	default:
		print list_demandes_ui::get_instance()->get_display_list();
		break;
}

?>