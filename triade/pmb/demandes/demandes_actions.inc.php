<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_actions.inc.php,v 1.16 2017-01-31 15:41:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($idaction)) $idaction = 0;
if(!isset($idnote)) $idnote = 0;
if(!isset($iddocnum)) $iddocnum = 0;
if(!isset($act)) $act = '';

require_once($class_path."/demandes_actions.class.php");
require_once($class_path."/demandes.class.php");
require_once($class_path."/demandes_notes.class.php");
require_once($class_path."/explnum_doc.class.php");

$actions = new demandes_actions($idaction);
$demandes = new demandes($iddemande);
$notes = new demandes_notes($idnote,$idaction);
$explnum_doc = new explnum_doc($iddocnum);

switch($sub){
	case 'com':
		switch($act){
			case 'close_fil':
				$actions->close_fil();
			break;
		}
		$actions->show_com_form();
		break;
	case 'rdv_plan':
		switch($act){
			case 'close_rdv':
				$actions->close_rdv();
			break;
		}
		$actions->show_planning_form();
		break;
	case 'rdv_val':
		switch($act){
			case 'val_rdv':
				$actions->valider_rdv();
			break;
		}
		$actions->show_rdv_val_form();
		break;
	default:
		switch($act){		
			case 'add_action':
				$actions->show_modif_form();
			break;
			case 'save_action':
				demandes_actions::get_values_from_form($actions);
				demandes_actions::save($actions);
				$actions->fetch_data($actions->id_action,false);
				$actions->show_consultation_form();
			break;
			case 'modif':
				$actions->show_modif_form();
			break;
			case 'change_statut':
				demandes_actions::change_statut($idstatut,$actions);
				$actions->fetch_data($idaction,false);
				$actions->show_consultation_form();
				break;
			case 'see':
				$actions->fetch_data($idaction,false);
				$actions->show_consultation_form();
			break;
			case 'suppr_action':
				$chk = ${"chk_action_".$iddemande};
				if(sizeof($chk)){
					for($i=0;$i<count($chk);$i++){
						$action = new demandes_actions($chk[$i]);
						demandes_actions::delete($action);
					}
				}else{
					demandes_actions::delete($actions);					
				}
				$demandes->fetch_data($iddemande,false);				
				$demandes->show_consult_form();
			break;
			case 'add_docnum':
				$actions->show_docnum_form();
			break;
			case 'save_docnum':
				demandes_actions::get_docnum_values_from_form($explnum_doc);
				demandes_actions::save_docnum($actions, $explnum_doc);
				$actions->fetch_data($actions->id_action,false);
				$actions->show_consultation_form();
			break;
			case 'suppr_docnum':
				demandes_actions::delete_docnum($explnum_doc);
				$actions->fetch_data($actions->id_action,false);
				$actions->show_consultation_form();
			break;
			case 'modif_docnum':
				$actions->show_docnum_form();
			break;
		}
	break;
}


?>