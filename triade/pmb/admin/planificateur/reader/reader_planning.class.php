<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reader_planning.class.php,v 1.1.18.1 2025/03/13 16:40:33 qvarin Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_planning.class.php");
require_once($class_path."/docs_location.class.php");

class reader_planning extends scheduler_planning {

	//formulaire spécifique au type de tâche
	public function show_form ($param=array()) {
		global $msg, $pmb_lecteurs_localises;
		//paramètres pré-enregistré
		$lst_opt = array();
		if ($param['chk_reader']) {
			foreach ($param['chk_reader'] as $elem) {
				$lst_opt[$elem] = $elem;
			}
		}
		$loc_selected = ($param["empr_location_id"] ? $param["empr_location_id"] : "");
		$statut_selected = ($param["empr_statut_edit"] ? $param["empr_statut_edit"] : "");

		//Choix de l'action à réaliser
		$form_task = "
		<div class='row'>
			<div class='colonne3'>
				<label for='loan'>".$this->msg["planificateur_reader_abon"]."</label>
			</div>
			<div class='colonne_suite'>
			<input type='checkbox' name='chk_reader[]' value='reader_abon_fin_proche' ".(($lst_opt["reader_abon_fin_proche"] == "reader_abon_fin_proche")  ? "checked" : "")."/>".$this->msg["planificateur_reader_abon_fin_proche"]."
				<br /><input type='checkbox' name='chk_reader[]' value='reader_abon_depasse' ".(($lst_opt["reader_abon_depasse"] == "reader_abon_depasse")  ? "checked" : "")."/>".$this->msg["planificateur_reader_abon_depasse"]."";

//				<input type='checkbox' name='chk_reader[]' value='reader_abon_fin_proche_mail' ".(($lst_opt["reader_abon_fin_proche_mail"] == "reader_abon_fin_proche_mail")  ? "checked" : "")."/>".$this->msg["planificateur_reader_abon_fin_proche_mail"]."
//				<br /><input type='checkbox' name='chk_reader[]' value='reader_abon_fin_proche_pdf' ".(($lst_opt["reader_abon_fin_proche_pdf"] == "reader_abon_fin_proche_pdf")  ? "checked" : "")."/>".$this->msg["planificateur_reader_abon_fin_proche_pdf"]."
//				<br /><input type='checkbox' name='chk_reader[]' value='reader_abon_depasse_mail' ".(($lst_opt["reader_abon_depasse_mail"] == "reader_abon_depasse_mail")  ? "checked" : "")."/>".$this->msg["planificateur_reader_abon_depasse_mail"]."
//				<br /><input type='checkbox' name='chk_reader[]' value='reader_abon_depasse_pdf' ".(($lst_opt["reader_abon_depasse_pdf"] == "reader_abon_depasse_pdf")  ? "checked" : "")."/>".$this->msg["planificateur_reader_abon_depasse_pdf"]."
			$form_task .= "</div>
		</div>
		<div class='row'>&nbsp;</div>";

		//Choix de la localisation
		if ($pmb_lecteurs_localises) {
			$form_task .= "
			<div class='row'>
				<div class='colonne3'>
					<label for='loan'>".$this->msg["planificateur_reader_loc"]."</label>
				</div>
				<div class='colonne_suite'>".
				docs_location::gen_combo_box_empr($loc_selected)."
				</div>
			</div>
			<div class='row'>&nbsp;</div>";
		}

		//Choix du statut
		$form_task .= "
		<div class='row'>
			<div class='colonne3'>
				<label for='loan'>".$this->msg["planificateur_reader_statut"]."</label>
			</div>
			<div class='colonne_suite'>".
			gen_liste("select idstatut, statut_libelle from empr_statut","idstatut","statut_libelle","empr_statut_edit","",$statut_selected,"","",0,$msg["all_statuts_empr"])."
			</div>
		</div>";

		return $form_task;
	}

	public function make_serialized_task_params() {
    	global $chk_reader,$empr_location_id,$empr_statut_edit;

    	$t = parent::make_serialized_task_params();

		if (!empty($chk_reader)) {
			for ($i=0; $i<count($chk_reader); $i++) {
				$t["chk_reader"]=$chk_reader;
			}
		}
		$t["empr_location_id"] = $empr_location_id;
		$t["empr_statut_edit"] = $empr_statut_edit;

    	return serialize($t);
	}
}