<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mysql_planning.class.php,v 1.1 2017/07/10 15:50:02 dgoron Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_planning.class.php");

class mysql_planning extends scheduler_planning {
	
	//formulaire spécifique au type de tâche
	public function show_form ($param=array()) {		
		//paramètres pré-enregistré
		$tab_maintenance = array('CHECK' => '', 'ANALYZE' => '', 'REPAIR' => '', 'OPTIMIZE' => '');
		if (isset($param['mySQL'])) {
			foreach ($param['mySQL'] as $elem) {
				$tab_maintenance[$elem] = $elem;
			}
		}

		$form_task = "
		<div class='row'>
			<div class='colonne3'>
				<label for='bannette'>".$this->msg["planificateur_mysql_maintenance"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='checkbox' id='check' name='mySQL[]' value='CHECK' ".($tab_maintenance['CHECK'] == 'CHECK' ? 'checked' : '')."/><label for='check'>".$this->msg["planificateur_mysql_checkTable"]."</label>
				<br />
				<input type='checkbox' id='analyze' name='mySQL[]' value='ANALYZE' ".($tab_maintenance['ANALYZE'] == 'ANALYZE' ? 'checked' : '')."/><label for='analyze'>".$this->msg["planificateur_mysql_analyzeTable"]."</label>
				<br />
				<input type='checkbox' id='repair' name='mySQL[]' value='REPAIR' ".($tab_maintenance['REPAIR'] == 'REPAIR' ? 'checked' : '')."/><label for='repair'>".$this->msg["planificateur_mysql_repairTable"]."</label>
				<br />
				<input type='checkbox' id='optimize' name='mySQL[]' value='OPTIMIZE' ".($tab_maintenance['OPTIMIZE'] == 'OPTIMIZE' ? 'checked' : '')."/><label for='optimize'>".$this->msg["planificateur_mysql_optimizeTable"]."</label>		
			</div>
		</div>";
					
		return $form_task;
	}
	
	public function make_serialized_task_params() {
    	global $mySQL;

		$t = parent::make_serialized_task_params();
		
		if ($mySQL) {
			foreach ($mySQL as $elem) {
				$t["mySQL"][$elem]=stripslashes($elem);			
			}
		}

    	return serialize($t);
	}
}