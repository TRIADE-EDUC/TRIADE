<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_notes.inc.php,v 1.9 2014-04-02 08:50:38 mhoestlandt Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/demandes.class.php");
require_once($class_path."/demandes_notes.class.php");
require_once($class_path."/demandes_actions.class.php");

$notes = new demandes_notes($idnote,$idaction);
$actions = new demandes_actions($idaction);

switch($act){
	
	case 'add_note':
		
		demandes_notes::get_values_from_form($notes);
		demandes_notes::save($notes);
		demandes_notes::note_majParent($notes->id_note, $notes->num_action, $actions->num_demande,"_gestion");
		demandes_notes::note_majParent($notes->id_note, $notes->num_action, $actions->num_demande,"_opac");
		$actions->fetch_data($notes->num_action,false);
		if($redirectto=="demandes-show_consult_form"){
			$demande=new demandes($actions->num_demande);
			$demande->show_consult_form($notes->num_action);
		}else{
			$actions->show_consultation_form();
		}
		break;
	case 'reponse':
		$notes->show_modif_form(true);
		break;
	case 'save_note':
		demandes_notes::get_values_from_form($notes);
		demandes_notes::save($notes);
		demandes_notes::note_majParent($notes->id_note, $notes->num_action, $actions->num_demande,"_gestion");
		demandes_notes::note_majParent($notes->id_note, $notes->num_action, $actions->num_demande,"_opac");
		$actions->fetch_data($idaction,false);
		$actions->show_consultation_form();
		break;
	case 'modif_note':
		$notes->show_modif_form();
		break;
	case 'suppr_note':
		demandes_notes::delete($notes);
		$actions->fetch_data($idaction,false);
		$actions->show_consultation_form();
		break;
		
	
}

?>