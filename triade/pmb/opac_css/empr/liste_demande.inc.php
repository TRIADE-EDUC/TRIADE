<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: liste_demande.inc.php,v 1.10.4.1 2025/05/16 14:07:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $sub;
global $iddemande, $idaction, $idnote, $idaction, $last_modified;

$iddemande = intval($iddemande);
$idaction = intval($idaction);
$idnote = intval($idnote);
$idaction = intval($idaction);
$last_modified = intval($last_modified);

require_once($base_path.'/includes/templates/demandes.tpl.php');
require_once($base_path.'/classes/demandes.class.php');
require_once($base_path.'/classes/demandes_actions.class.php');

print "<script src='./includes/javascript/http_request.js'></script>
<script src='./includes/javascript/demandes.js'></script>"; 

$demandes = new demandes($iddemande);
$demandes_action = new demandes_actions($idaction);
$demande_note=new demandes_note($idnote,$idaction);

switch($sub){
	case 'save_action':
		$demandes_action->set_properties_from_form();
		foreach($demandes->allowed_actions as $key=>$value){
			if($value['active'] && $demandes_action->type_action==$value['id']){
				$demandes_action->save();
			}
		}
		demandes::dmde_majRead($demandes_action->num_demande,"_opac");
		$demandes->fetch_data($demandes_action->num_demande,false);
		$demandes->show_consult_form($demandes_action->id_action);
		break;
	case 'save_demande':
		$demandes->set_properties_from_form();
		$demandes->save();
		$demandes->fetch_data($demandes->id_demande,false);
		$demandes->show_consult_form();
		break;
	case 'add_demande':
		$demandes->show_modif_form();
		break;
	case 'add_action':
		$demandes_action->type_action=$type_action;
		$demandes_action->num_demande=$iddemande;
		$demandes_action->show_modif_form();
		break;
	case 'add_note':
		$demande_note->set_properties_from_form();
		$demande_note->save();
		$demande_note->note_majParent($demandes_action->num_demande,"_gestion");
		$demande_note->note_majParent($demandes_action->num_demande,"_opac");
		$demandes_action->fetch_data($demande_note->num_action,false);
		$demandes->show_consult_form($demande_note->num_action);
		break;
	case 'open_action':
		$demandes_action->fetch_data($demandes_action->id_action,false);
		$demandes_action->show_consultation_form();
		break;
	case 'open_demande':
		$demandes->fetch_data($demandes->id_demande,false);
		$demandes->show_consult_form($last_modified);
	break;
	case 'see_demandes':
	default :
		$demandes->show_list_form();
	break;
}
?>