<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: backup_planning.class.php,v 1.1 2017/07/10 15:50:01 dgoron Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_planning.class.php");

class backup_planning extends scheduler_planning {
	
	//formulaire spécifique au type de tâche
	public function show_form ($param=array()) {
		
		//paramètres pré-enregistré
		$value_param = array();
		if (isset($param['form_jeu_sauv'])) {
			foreach ($param['form_jeu_sauv'] as $jeu_sauvegarde) {
				$value_param[$jeu_sauvegarde] = $jeu_sauvegarde;
			}
		}
		
		$requete = "select sauv_sauvegarde_id, sauv_sauvegarde_nom from sauv_sauvegardes";
		$result = pmb_mysql_query($requete);
		$nb_rows = pmb_mysql_num_rows($result);
		//taille du selecteur
		if ($nb_rows < 3) $nb=3;
		else if ($nb_rows > 10) $nb=10;
		else $nb = $nb_rows;
			
		//Choix du ou des jeux de sauvegardes
		$form_task = "
		<div class='row'>
			<div class='colonne3'>
				<label for='jeu_sauv'>".$this->msg["planificateur_backup_choice"]."</label>
			</div>
			<div class='colonne_suite'>
				<select id='form_jeu_sauv' class='saisie-50em' name='form_jeu_sauv[]' size='".$nb."' multiple>";
					while ($row = pmb_mysql_fetch_object($result)) {
							$form_task .= "<option  value='".$row->sauv_sauvegarde_id."' ".(isset($value_param[$row->sauv_sauvegarde_id]) && $value_param[$row->sauv_sauvegarde_id] == $row->sauv_sauvegarde_id ? 'selected=\'selected\'' : '' ).">".$row->sauv_sauvegarde_nom."</option>";
					}
		$form_task .="</select>";
		$form_task .= "</div></div>";		
			
		return $form_task;
	}
	
	public function make_serialized_task_params() {
    	global $form_jeu_sauv;
		$t = parent::make_serialized_task_params();
		if ($form_jeu_sauv) {
			foreach ($form_jeu_sauv as $jeu_sauvegarde) {
				$t["form_jeu_sauv"][$jeu_sauvegarde]=stripslashes($jeu_sauvegarde);			
			}
		}

    	return serialize($t);
	}
}