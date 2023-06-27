<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: liste_demande.inc.php,v 1.8 2019-06-04 12:24:33 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($iddemande)) $iddemande = 0; else $iddemande = intval($iddemande);
if(!isset($idaction)) $idaction = 0; else $idaction = intval($idaction);
if(!isset($idnote)) $idnote = 0; else $idnote = intval($idnote);
if(!isset($idaction)) $idaction = 0; else $idaction = intval($idaction);
if(!isset($last_modified)) $last_modified = 0; else $idaction = intval($last_modified);

require_once($base_path.'/includes/templates/demandes.tpl.php');
require_once($base_path.'/classes/demandes.class.php');
require_once($base_path.'/classes/demandes_actions.class.php');

print "<script type='text/javascript' src='./includes/javascript/http_request.js'></script>
<script type='text/javascript' src='./includes/javascript/demandes.js'></script>"; 

$demandes = new demandes($iddemande);
$demandes_action = new demandes_actions($idaction);
$demande_note=new demandes_notes($idnote,$idaction);

switch($sub){
	case 'save_action':
		
		demandes_actions::get_values_from_form($demandes_action);
		foreach($demandes->allowed_actions as $key=>$value){
			if($value['active'] && $demandes_action->type_action==$value['id']){
				demandes_actions::save($demandes_action);
			}
		}
		demandes::dmde_majRead($demandes_action->num_demande,"_opac");
		$demandes->fetch_data($demandes_action->num_demande,false);
		$demandes->show_consult_form($demandes_action->id_action);
		break;
	case 'save_demande':
		demandes::get_values_from_form($demandes);
		demandes::save($demandes);
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
		demandes_notes::get_values_from_form($demande_note);
		demandes_notes::save($demande_note);
		demandes_notes::note_majParent($demande_note->id_note, $demande_note->num_action, $demandes_action->num_demande,"_gestion");
		demandes_notes::note_majParent($demande_note->id_note, $demande_note->num_action, $demandes_action->num_demande,"_opac");
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