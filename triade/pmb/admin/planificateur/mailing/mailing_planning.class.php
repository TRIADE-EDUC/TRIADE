<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mailing_planning.class.php,v 1.10 2020/11/16 14:26:49 dgoron Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_planning.class.php");
require_once($class_path."/mailtpl.class.php");
require_once($class_path."/empr_caddie.class.php");
require_once($class_path."/list/configuration/search_perso/list_configuration_search_perso_ui.class.php");

class mailing_planning extends scheduler_planning {
	
	//formulaire spécifique au type de tâche
	public function show_form ($param=array()) {
		global $PMBuserid;
		global $deflt_associated_campaign;
		
		$form_task = '';
		//paramètres pré-enregistré
		if (isset($param['mailtpl_id'])) {
		    $id_sel = (int) $param['mailtpl_id'];
		} else {
			$id_sel=0;
		}
		//choix d'emprunteurs
		$empr_choice = 1;
		if (isset($param['empr_choice'])) {
		    $empr_choice = $param['empr_choice'];
		}
		//panier de lecteurs
		if (isset($param['empr_caddie'])) {
		    $idemprcaddie_sel = (int) $param['empr_caddie'];
		} else {
			$idemprcaddie_sel = 0;
		}
		//prédéfinie d'emprunteurs
		$idempr_search_perso_sel = 0;
		if (isset($param['empr_search_perso'])) {
		    $idempr_search_perso_sel = $param['empr_search_perso'];
		}
		//copies cachées
		if (isset($param['email_cc'])) {
			$email_cc = trim($param['email_cc']);
		} else {
			$email_cc = "";
		}
		//Pièces jointes
		if (isset($param['pieces_jointes_mailing'])) {
			$attachments = $param['pieces_jointes_mailing'];
		} else {
			$attachments = array();
		}
		//Campagne de mails
		$associated_campaign = intval($deflt_associated_campaign);
		if (isset($param['associated_campaign'])) {
			$associated_campaign = $param['associated_campaign'];
		}
		
		$mailtpl = new mailtpls();

		//Choix du template de mail
		$form_task .= "
		<div class='row'>
			<div class='colonne3'>
				<label for='mailing_template'>".$this->msg["planificateur_mailing_template"]."</label>
			</div>
			<div class='colonne_suite' >
				".$mailtpl->get_sel('mailtpl_id',$id_sel)."
			</div>
		</div>
		<div class='row' >&nbsp;</div>
        <div class='row'>
			<div class='colonne3'>
				<label for='mailing_attachments'>".$this->msg["planificateur_mailing_attachments"]."</label>
			</div>
			<div class='colonne_suite' >";
		$form_task .= $this->get_attachments_form($attachments);
		$form_task .=	"
			</div>
		</div>
		<div class='row' >&nbsp;</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='associated_campaign' class='etiquette'>".$this->msg["planificateur_mailing_associated_campaign"]."</label>
			</div>
			<div class='colonne_suite' >
				<input type='checkbox' name='associated_campaign' value=\"1\" ".($associated_campaign ? "checked='checked'" : "")." />
			</div>
		</div>
		<div class='row' >&nbsp;</div>";
		
		$liste = empr_caddie::get_cart_list();
		$gen_select_empr_caddie = "<select name='empr_caddie' id='empr_caddie'>";
		if (!empty($liste)) {
		    foreach ($liste as $valeur) {
				$rqt_autorisation=explode(" ",$valeur['autorisations']);
				if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $valeur['autorisations_all'] || $PMBuserid==1) {
					if($valeur['idemprcaddie']==$idemprcaddie_sel){
						$gen_select_empr_caddie .= "<option value='".$valeur['idemprcaddie']."' selected='selected'>".$valeur['name']."</option>";
					} else {
						$gen_select_empr_caddie .= "<option value='".$valeur['idemprcaddie']."'>".$valeur['name']."</option>";
					}		
					
				}
			}	
		}
		$gen_select_empr_caddie .= "</select>";
        
		$form_task .= "
        <hr/>
        <fieldset>
            <legend><label>".$this->msg["planificateur_mailing_empr_choice"]."</label></legend>
        ";
		//Choix du panier d'emprunteurs
		$form_task .= "
            <div class='row'>
    			<div class='colonne3'>
                    <input type='radio' id='empr_caddie_choice' name='empr_choice' value='1' ".($empr_choice == 1 ? "checked" : "").">
    				<label for='empr_caddie_choice'>".$this->msg["planificateur_mailing_caddie_empr"]."</label>
    			</div>
    			<div class='colonne_suite'>
    				".$gen_select_empr_caddie."
    			</div>
    		</div>";

		//Choix de la prédéfinie d'emprunteurs
		$form_task .= "
            <div class='row'>
    			<div class='colonne3'>
    				<input type='radio' id='empr_search_perso_choice' name='empr_choice' value='2' ".($empr_choice == 2 ? "checked" : "").">
                    <label for='empr_search_perso_choice'>".$this->msg["planificateur_mailing_empr_search_perso"]."</label>
    			</div>
    			<div class='colonne_suite'>
    				".$this->get_empr_search_perso($idempr_search_perso_sel)."
    			</div>                
    		</div>";
		
		$form_task .= "
        </fieldset>
        <hr/>";

		//Destinataire supplémentaire
		$form_task .= "<div class='row'>
			<div class='colonne3'>
				<label for='mailing_caddie'>".$this->msg["planificateur_mailing_email_cc"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' class='saisie-30em' name='email_cc' id='email_cc' value='".$email_cc."'>
			</div>
		</div>";
		
		return $form_task;
	}

	protected function get_attachments_form($pieces_jointes_mailing = array()) {
		global $msg, $charset;
		global $pmb_attachments_folder;

		$attachments_form = '';
		if(is_dir($pmb_attachments_folder)){
			if(($objects = @scandir($pmb_attachments_folder)) !== false) {
				$i = 0;
				foreach ($objects as $object) {
					if($object != '.' && $object != '..') {
						if (filetype($pmb_attachments_folder."/".$object) != "dir") {
							$attachments_form .= "
								<div class='row'>
									<input type='checkbox' id='pieces_jointes_".$i."' name='pieces_jointes[".$i."]' value='".htmlentities($object, ENT_QUOTES, $charset)."' ".(in_array($object, $pieces_jointes_mailing) ? "checked='checked'" : "")." />
									".$object."
								</div>";
							$i++;
						}
					}
				}
			}
			$attachments_form .= mailtpl::get_attachments_form();
		} else {
			$attachments_form .= $msg["admin_files_gestion_error_is_no_path"].$pmb_attachments_folder."<br />".$msg["admin_files_gestion_error_param_attachments_folder"];
		}
		return $attachments_form;
	}
	
	public function make_serialized_task_params() {
    	global $empr_caddie, $mailtpl_id, $email_cc;
    	global $empr_choice, $empr_search_perso;
    	global $pieces_jointes, $associated_campaign;
		$t = parent::make_serialized_task_params();
		
		$t["empr_choice"] = $empr_choice;
		$t["empr_search_perso"] = $empr_search_perso;
		$t["empr_caddie"] = $empr_caddie;
		$t["mailtpl_id"] = $mailtpl_id;
		$t["email_cc"] = $email_cc;
		$t["pieces_jointes_mailing"] = array();
		if(!empty($pieces_jointes)) {
			foreach ($pieces_jointes as $piece_jointe) {
				$t["pieces_jointes_mailing"][] = $piece_jointe;
			}
		}
		if(!empty($_FILES['pieces_jointes_mailing']['name'])) {
			for($i=0; $i<count($_FILES['pieces_jointes_mailing']['name']); $i++) {
				if(!empty($_FILES['pieces_jointes_mailing']['name'][$i])) {
					$t["pieces_jointes_mailing"][] = $_FILES['pieces_jointes_mailing']['name'][$i];
				}
			}
		}
		$t["associated_campaign"] = intval($associated_campaign);
    	return serialize($t);
	}
	
	//sauvegarde des données du formulaire,
	public function save_property_form() {
		global $pmb_attachments_folder;
		
		parent::save_property_form();
		if(is_dir($pmb_attachments_folder)){
			mailtpl::upload_attachments_form($pmb_attachments_folder);
		}
	}
	
	private function get_empr_search_perso($selected = 0) {
	    $searches = array();
	    $empr_search_perso = list_configuration_search_perso_ui::get_instance(array('type' => "EMPR"));
	    $searches = $empr_search_perso->get_objects();
	    $html = "
            <select name='empr_search_perso' id='empr_search_perso'>";
	    foreach($searches as $search) {
	        $html .= "<option value='$search->id' ".($selected == $search->id ? "selected" : "").">$search->search_name</option>";
	    }
        $html .= "
            </select>";
	    return $html;
	}
}