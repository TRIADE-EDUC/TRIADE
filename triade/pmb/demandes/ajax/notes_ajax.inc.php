<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notes_ajax.inc.php,v 1.6.8.1 2025/05/06 15:16:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $action, $quoifaire;
global $id_action;

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
		$note = new demandes_note($tab["id_note"], $tab["id_action"]);
		$note->change_read("_gestion");
		ajax_http_send_response($note->note_majParent($tab["id_demande"],"_gestion"));
		break;
	case 'final_response':		
		$tab = json_decode(stripslashes($tab),true);
		$note = new demandes_note($tab["id_note"], $tab["id_action"]);
		$f_message=addslashes($note->contenu);		
		$demande = new demandes($tab["id_demande"]);
		$demande->save_repfinale($tab["id_note"]);		
		ajax_http_send_response($note->note_majParent($tab["id_demande"],"_gestion"));
		break;		

}
