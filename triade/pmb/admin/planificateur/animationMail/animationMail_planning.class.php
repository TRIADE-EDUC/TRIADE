<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: animationMail_planning.class.php,v 1.3.4.1 2025/03/13 16:40:33 qvarin Exp $
use Pmb\Animations\Models\MailingTypeModel;

global $class_path;
require_once ($class_path . "/scheduler/scheduler_planning.class.php");

class animationMail_planning extends scheduler_planning
{

	public function show_form($param = array())
	{
		$liste = MailingTypeModel::getMailingsTypeList();
		$gen_select_mailing_list = "<select name='mailing_list[]' id='mailing_list' multiple>";
		if (!empty($liste)) {
			foreach ($liste as $valeur) {
				if (!empty($param['mailing_list_choice']) && in_array($valeur['idMailingType'], $param['mailing_list_choice'])) {
					$gen_select_mailing_list .= "<option value='" . $valeur['idMailingType'] . "' selected='selected'>" . $valeur['name'] . "</option>";
				} else {
					$gen_select_mailing_list .= "<option value='" . $valeur['idMailingType'] . "'>" . $valeur['name'] . "</option>";
				}
			}
		}
		$gen_select_mailing_list .= "</select>";

		// Choix du type de communication
		$form_task = "
            <div class='row'>
    			<div class='colonne3'>
    				<label for='mailing_list_choice'>" . $this->msg["planificateur_mailing_list_choice"] . "</label>
    			</div>
    			<div class='colonne_suite'>
    				" . $gen_select_mailing_list . "
    			</div>
    		</div>";

		return $form_task;
	}

	public function make_serialized_task_params()
	{
		global $mailing_list;

		$t = parent::make_serialized_task_params();
		$t["mailing_list_choice"] = $mailing_list;

		return serialize($t);
	}

	// sauvegarde des données du formulaire,
	public function save_property_form()
	{
		parent::save_property_form();
	}
}