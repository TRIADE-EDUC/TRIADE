<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dsi_planning.class.php,v 1.2 2017-08-10 13:44:50 jpermanne Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_planning.class.php");
require_once($class_path."/bannette.class.php");

class dsi_planning extends scheduler_planning {
	
	//formulaire spécifique au type de tâche
	public function show_form ($param=array()) {
		global $dbh;
		
		//paramètres pré-enregistré
		$liste_bannettes = array();
		if (isset($param['list_bann'])) {
			foreach ($param['list_bann'] as $id_bann) {
				$liste_bannettes[$id_bann] = $id_bann;
			}
		}
		$liste_actions = array('full' => '', 'flush' => '', 'fill' => '', 'diffuse' => '');
		if (isset($param['action'])) {
			foreach ($param['action'] as $action) {
				$liste_actions[$action] = $action;
			}
		}
		if(!isset($param['radio_bannette'])) $param['radio_bannette'] = '';
		
		$requete = "select id_bannette, if(nom_classement is not null,concat('(',nom_classement,') ',nom_bannette),nom_bannette) as nom_bannette, 
					if(proprio_bannette>0,1,0) as ban_priv from bannettes left join classements on num_classement = id_classement where bannette_auto=1 order by 3, 2";
		$result = pmb_mysql_query($requete,$dbh);
		//size select
		$nb_rows = pmb_mysql_num_rows($result);
		if (($nb_rows > 0) && ($nb_rows < 10)) {
			$size_select = $nb_rows;	
		} elseif ($nb_rows == 0) {
			$size_select = 1;
		} else {
			$size_select = 10;
		}
		
		//Choix de la bannette à diffuser
		$form_task = "
		<script type='text/javascript'>
		function changeActions(operator) {
			if (operator == 'full') {
				if (document.getElementById('full').checked == true) {
					document.getElementById('flush').checked = false;
					document.getElementById('fill').checked = false;
					document.getElementById('diffuse').checked = false;
				} else {
					if ((document.getElementById('flush').checked == false)
						&& (document.getElementById('fill').checked == false)
						&& (document.getElementById('diffuse').checked == false)
						&& (document.getElementById('export').checked == false)){
							document.getElementById('full').checked = true;
					}
				}
			} else {
				if ((document.getElementById('flush').checked == true)
					|| (document.getElementById('fill').checked == true)
					|| (document.getElementById('diffuse').checked == true)){
						document.getElementById('full').checked = false;
				} else if ((document.getElementById('full').checked == false)
					&& (document.getElementById('flush').checked == false)
					&& (document.getElementById('fill').checked == false)
					&& (document.getElementById('diffuse').checked == false)
					&& (document.getElementById('export').checked == false)){
						document.getElementById(operator).checked = true;
				}
			}
		}
		</script>
		<div class='row'>
			<div class='colonne3'>
				<label for='bannette'>".$this->msg["planificateur_dsi_bannette"]."</label>
			</div>
			<div class='colonne_suite' >
				<input type='radio' name='radio_bannette' value='1' ".((($param['radio_bannette'] == "1") || (!$param['radio_bannette']))  ? "checked" : "")."/>".$this->msg["planificateur_dsi_bannette_all"]."
				<br />
				<input type='radio' name='radio_bannette' value='2' ".(($param['radio_bannette'] == "2")  ? "checked" : "")."/>".$this->msg["planificateur_dsi_bannette_public"]."
				<br />
				<input type='radio' name='radio_bannette' value='3' ".(($param['radio_bannette'] == "3")  ? "checked" : "")."/>".$this->msg["planificateur_dsi_bannette_private"]."
				<br />
				<input type='radio' name='radio_bannette' value='4' ".($param['radio_bannette'] == "4" ? "checked" : "")."/>
				<select id='list_bann' style='vertical-align:middle' class='saisie-30em' name='list_bann[]' size='".$size_select."' multiple>";
					while ($row = pmb_mysql_fetch_object($result)) {
							$form_task .= "<option  value='".$row->id_bannette."' ".(isset($liste_bannettes[$row->id_bannette]) && $liste_bannettes[$row->id_bannette] == $row->id_bannette ? 'selected=\'selected\'' : '' ).($row->ban_priv?" style='color:#ff0000'":"").">".$row->nom_bannette."</option>";
					}		
		$form_task .="</select>
			</div>
		</div>
		<div class='row' >&nbsp;</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='bannette_options'>".$this->msg["planificateur_dsi_action"]."</label>
			</div>
			<div class='colonne_suite'>
				<input id='full' type='checkbox' name='action[]' value='full' ".($liste_actions['full'] == "full"  ? "checked" : "")." onchange='changeActions(this.value);'/>".$this->msg["task_dsi_full"]."
				<br />
				<input id='flush' type='checkbox' name='action[]' value='flush' ".($liste_actions['flush'] == "flush" ? "checked" : "")." onchange='changeActions(this.value);'/>".$this->msg["task_dsi_flush"]."
				<br />
				<input id='fill' type='checkbox' name='action[]' value='fill' ".($liste_actions['fill'] == "fill" ? "checked" : "")." onchange='changeActions(this.value);'/>".$this->msg["task_dsi_fill"]."
				<br />
				<input id='diffuse' type='checkbox' name='action[]' value='diffuse' ".($liste_actions['diffuse'] == "diffuse" ? "checked" : "")." onchange='changeActions(this.value);'/>".$this->msg["task_dsi_diffuse"]."";
//				<br />
//				<input id='export' type='checkbox' name='action[]' value='export' ".($liste_actions['export'] == "export" ? "checked" : "")." onchange='changeActions(this.value);'/>".$this->msg["task_dsi_export"]."
			$form_task .= "</div>
		</div>";	
			
		return $form_task;
	}
	
	public function make_serialized_task_params() {
    	global $list_bann, $radio_bannette, $action;
		$t = parent::make_serialized_task_params();
		
		if ($radio_bannette) {
			$t["radio_bannette"]=$radio_bannette;
			//liste de bannettes sélectionnées dans le cas où on choisi..
			if ($radio_bannette == "4") {
				if ($list_bann) {
					foreach ($list_bann as $id_bann) {
						$t["list_bann"][$id_bann]=stripslashes($id_bann);			
					}
				}
			}
		}
		if ($action) {
			foreach ($action as $act) {
				$t["action"][$act]=$act;			
			}
		}

    	return serialize($t);
	}
}