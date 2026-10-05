<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_notes_controller.class.php,v 1.1.4.3 2025/05/06 16:06:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class demandes_notes_controller extends lists_controller {
	
	protected static $model_class_name = 'demandes_note';
	protected static $list_ui_class_name = '';
	
	public static function proceed($id=0) {
	    global $class_path, $act, $idaction;
	    global $redirectto;
		
	    $note = new demandes_note($id, $idaction);
		$actions = new demandes_actions($idaction);
		switch($act){
		    case 'add_note':
		        $note->set_properties_from_form();
		        $note->save();
		        $note->note_majParent($actions->num_demande,"_gestion");
		        $note->note_majParent($actions->num_demande,"_opac");
		        $actions->fetch_data($note->num_action,false);
		        if($redirectto=="demandes-show_consult_form"){
		            $demande=new demandes($actions->num_demande);
		            $demande->show_consult_form($note->num_action);
		        }else{
		            $actions->show_consultation_form();
		        }
		        break;
		    case 'reponse':
		        $note->show_modif_form(true);
		        break;
		    case 'save_note':
		        $note->set_properties_from_form();
		        $note->save();
		        $note->note_majParent($actions->num_demande,"_gestion");
		        $note->note_majParent($actions->num_demande,"_opac");
		        $actions->fetch_data($idaction,false);
		        $actions->show_consultation_form();
		        break;
		    case 'modif_note':
		        $note->show_modif_form();
		        break;
		    case 'suppr_note':
		        demandes_note::delete($note);
		        $actions->fetch_data($idaction,false);
		        $actions->show_consultation_form();
		        break;
		}
	}
}
