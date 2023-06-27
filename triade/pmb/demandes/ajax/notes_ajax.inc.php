<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notes_ajax.inc.php,v 1.5 2015-05-20 14:39:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/demandes_actions.class.php");
require_once("$class_path/demandes_notes.class.php");
require_once("$class_path/demandes.class.php");
require_once("$include_path/templates/demandes_notes.tpl.php");

switch($quoifaire){
	
	case 'show_dialog':
		$action=new demandes_actions($id_action,false);
		ajax_http_send_response(demandes_notes::show_dialog($action->notes, $action->id_action,$action->num_demande,"demandes-show_consult_form",true));
	break;
	case 'change_read_note':
		$tab = json_decode(stripslashes($tab),true);
		$note = new demandes_notes($tab["id_note"],false);
		demandes_notes::change_read($note,"_gestion");		
		ajax_http_send_response(demandes_notes::note_majParent($tab["id_note"], $tab["id_action"], $tab["id_demande"],"_gestion"));
		break;
	case 'final_response':		
		$tab = json_decode(stripslashes($tab),true);
		$note = new demandes_notes($tab["id_note"],false);
		$f_message=addslashes($note->contenu);		
		$demande = new demandes($tab["id_demande"]);
		$demande->save_repfinale($tab["id_note"]);		
		ajax_http_send_response(demandes_notes::note_majParent($tab["id_note"], $tab["id_action"], $tab["id_demande"],"_gestion"));
		break;		

}
