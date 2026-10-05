<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_mails_waiting_planning.class.php,v 1.1 2018/03/09 13:44:08 dgoron Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_planning.class.php");

class scheduler_mails_waiting_planning extends scheduler_planning {
	
	//formulaire spécifique au type de tâche
	public function show_form ($param=array()) {

		$form = "
		<div class='row'>
			<div class='colonne3'>
				<label>".$this->msg["scheduler_mails_waiting_server_configuration"]."</label>
			</div>
			<div class='colonne_suite'>";
		if(!isset($param['scheduler_mails_waiting_server_configuration'])) {
			$param['scheduler_mails_waiting_server_configuration'] = array();
		}
		$form .= mail::get_configuration_form($param['scheduler_mails_waiting_server_configuration']);
		$form .= "
			</div>
		</div>
		<div class='row'>&nbsp;</div>";
		
		return $form;
	}

	public function make_serialized_task_params() {
		global $parameters;
		
		$t = parent::make_serialized_task_params();
		
		$t['scheduler_mails_waiting_server_configuration'] = $parameters;

    	return serialize($t);
	}
}