<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa_planning.class.php,v 1.1.18.1 2025/03/13 16:40:33 qvarin Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_planning.class.php");
require_once($class_path."/docs_location.class.php");

class resa_planning extends scheduler_planning {

	//formulaire spécifique au type de tâche
	public function show_form ($param=array()) {
		global $msg,$pmb_transferts_actif,$pmb_location_reservation;

		//paramètres pré-enregistré
		$lst_opt = array();
		if ($param['chk_resa']) {
			foreach ($param['chk_resa'] as $elem) {
				$lst_opt[$elem] = $elem;
			}
		}
		$empr_location_id = $param['empr_location_id'];

		//Choix de l'action à réaliser
		$form_task = "
		<div class='row'>
			<div class='colonne3'>
				<label for='resa'>".$this->msg["planificateur_resa_empr"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='checkbox' name='chk_resa[]' value='resa_en_cours_noconf' ".(($lst_opt["resa_en_cours_noconf"] == "resa_en_cours_noconf")  ? "checked" : "")." />".$this->msg["resa_en_cours_noconf"]."
				<br /><input type='checkbox' name='chk_resa[]' value='resa_depassee_noconf' ".(($lst_opt["resa_depassee_noconf"] == "resa_depassee_noconf")  ? "checked" : "")." />".$this->msg["resa_depassee_noconf"]."
			</div>
		</div>
		<div class='row' >&nbsp;</div>";

		if ($pmb_transferts_actif=="1" || $pmb_location_reservation) {
			//Choix de la localisation
			$form_task .= "
			<div class='row'>
				<div class='colonne3'>
					<label for='resa'>".$this->msg["planificateur_resa_loc"]."</label>
				</div>
				<div class='colonne_suite'>".
				docs_location::gen_combo_box_empr($empr_location_id)."
				</div>
			</div>
			<div class='row' >&nbsp;</div>";
		}

		return $form_task;
	}

	public function make_serialized_task_params() {
    	global $chk_resa,$montrerquoi,$empr_location_id;
		$t = parent::make_serialized_task_params();

		if (!empty($chk_resa)) {
			for ($i=0; $i<count($chk_resa); $i++) {
				$t["chk_resa"]=$chk_resa;
			}
		}
		$t["empr_location_id"] = $empr_location_id;

    	return serialize($t);
	}
}