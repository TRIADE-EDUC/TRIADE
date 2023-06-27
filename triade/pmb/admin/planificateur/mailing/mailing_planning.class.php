<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mailing_planning.class.php,v 1.5 2019-06-13 15:26:51 btafforeau Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_planning.class.php");
require_once($class_path."/mailtpl.class.php");
require_once($class_path."/empr_caddie.class.php");
require_once($class_path."/list/configuration/search_perso/list_configuration_search_perso_ui.class.php");

class mailing_planning extends scheduler_planning {
	
	//formulaire spécifique au type de tâche
	public function show_form ($param=array()) {
		global $dbh, $PMBuserid;
		
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
		<div class='row' >&nbsp;</div>";
		
		$liste = empr_caddie::get_cart_list();
		$gen_select_empr_caddie = "<select name='empr_caddie' id='empr_caddie'>";
		if (sizeof($liste)) {
		    foreach ($liste as $cle => $valeur) {
				$rqt_autorisation=explode(" ",$valeur['autorisations']);
				if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $PMBuserid==1) {
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

	public function make_serialized_task_params() {
    	global $empr_caddie, $mailtpl_id, $email_cc;
    	global $empr_choice, $empr_search_perso;
		$t = parent::make_serialized_task_params();
		
		$t["empr_choice"] = $empr_choice;
		$t["empr_search_perso"] = $empr_search_perso;
		$t["empr_caddie"] = $empr_caddie;
		$t["mailtpl_id"] = $mailtpl_id;
		$t["email_cc"] = $email_cc;

    	return serialize($t);
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