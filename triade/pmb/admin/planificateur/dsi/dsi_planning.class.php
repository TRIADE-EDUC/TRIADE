<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dsi_planning.class.php,v 1.6 2023/06/22 09:15:57 qvarin Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_planning.class.php");
require_once($class_path."/bannette.class.php");

class dsi_planning extends scheduler_planning {
	
	protected function get_dsi_selections() {
		return array( 
				'1' => $this->msg["planificateur_dsi_bannette_all"],
				'2' => $this->msg["planificateur_dsi_bannette_public"],
				'3' => $this->msg["planificateur_dsi_bannette_private"],
				'4' => $this->msg["planificateur_dsi_bannette_manual"],
		);
	}
	
	protected function get_content_form_dsi_selections($param=array()) {
		//paramètres pré-enregistré
		$liste_bannettes = array();
		if (isset($param['list_bann'])) {
			foreach ($param['list_bann'] as $id_bann) {
				$liste_bannettes[$id_bann] = $id_bann;
			}
		}
		if(!isset($param['radio_bannette'])) $param['radio_bannette'] = '';
		
		$query = "select id_bannette, if(nom_classement is not null,concat('(',nom_classement,') ',nom_bannette),nom_bannette) as nom_bannette,
					if(proprio_bannette>0,1,0) as ban_priv from bannettes left join classements on num_classement = id_classement where bannette_auto=1 order by 3, 2";
		$result = pmb_mysql_query($query);
		//size select
		$nb_rows = pmb_mysql_num_rows($result);
		if (($nb_rows > 0) && ($nb_rows < 10)) {
			$size_select = $nb_rows;
		} elseif ($nb_rows == 0) {
			$size_select = 1;
		} else {
			$size_select = 10;
		}
		
		$content_form = "
		<script type='text/javascript'>
			function changeSelectedOptionSelectorBannette() {
				if(document.getElementById('radio_bannette_4')) {
					document.getElementById('radio_bannette_4').checked = true;
				}
			}
		</script>
		<div class='row'>
			<div class='colonne3'>
				<label for='bannette'>".$this->msg["planificateur_dsi_bannette"]."</label>
			</div>
			<div class='colonne_suite' >
				<input type='radio' id='radio_bannette_1' name='radio_bannette' value='1' ".((($param['radio_bannette'] == "1") || (!$param['radio_bannette']))  ? "checked" : "")."/>".$this->msg["planificateur_dsi_bannette_all"]."
				<br />
				<input type='radio' id='radio_bannette_2' name='radio_bannette' value='2' ".(($param['radio_bannette'] == "2")  ? "checked" : "")."/>".$this->msg["planificateur_dsi_bannette_public"]."
				<br />
				<input type='radio' id='radio_bannette_3' name='radio_bannette' value='3' ".(($param['radio_bannette'] == "3")  ? "checked" : "")."/>".$this->msg["planificateur_dsi_bannette_private"]."
				<br />
				<input type='radio' id='radio_bannette_4' name='radio_bannette' value='4' ".($param['radio_bannette'] == "4" ? "checked" : "")."/>
				<select id='list_bann' style='vertical-align:middle' class='saisie-30em' name='list_bann[]' size='".$size_select."' multiple onchange=\"if(this.selectedIndex) {changeSelectedOptionSelectorBannette();}\">";
		while ($row = pmb_mysql_fetch_object($result)) {
			$content_form .= "<option  value='".$row->id_bannette."' ".(isset($liste_bannettes[$row->id_bannette]) && $liste_bannettes[$row->id_bannette] == $row->id_bannette ? 'selected=\'selected\'' : '' ).($row->ban_priv?" style='color:#ff0000'":"").">".$row->nom_bannette."</option>";
		}
		$content_form .="</select>
			</div>
		</div>";
		return $content_form;
	}
	
	protected function get_dsi_actions() {
		return array(
				'full' => $this->msg["task_dsi_full"],
				'flush' => $this->msg["task_dsi_flush"],
				'fill' => $this->msg["task_dsi_fill"],
				'diffuse' => $this->msg["task_dsi_diffuse"]
// 				'export' => $this->msg["task_dsi_export"]
		);
	}
	
	protected function get_content_form_dsi_actions($param=array()) {
		$liste_actions = array('full' => '', 'flush' => '', 'fill' => '', 'diffuse' => '');
		if (isset($param['action'])) {
			foreach ($param['action'] as $action) {
				$liste_actions[$action] = $action;
			}
		}
		$content_form = "
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
				<label for='bannette_options'>".$this->msg["planificateur_dsi_action"]."</label>
			</div>
			<div class='colonne_suite'>";
		$dsi_actions = $this->get_dsi_actions();
		foreach ($dsi_actions as $name=>$label) {
			$content_form .= "<input id='".$name."' type='checkbox' name='dsi_action[]' value='".$name."' ".($liste_actions[$name] == $name  ? "checked" : "")." onchange='changeActions(this.value);'/> ".$label."
					<br />";
		}
		$content_form .= "
			</div>
		</div>";
		return $content_form;
	}


	//formulaire spécifique au type de tâche
	public function show_form ($param=array()) {
		global $dsi_active;

		if ($dsi_active == 1) {
			//Choix de la bannette à diffuser
			$form = $this->get_content_form_dsi_selections($param);
			$form .="
			<div class='row' >&nbsp;</div>";
			$form .= $this->get_content_form_dsi_actions($param);
		} else {
		    $form = '<input type="hidden" name="radio_bannette" value="'. $this->param['radio_bannette'] .'">';
		    if ($this->param['radio_bannette'] == "4" && !empty($this->param['list_bann'])) {
		        foreach ($this->param['list_bann'] as $id_bann) {
	                $form .= '<input type="hidden" name="list_bann[]" value="'. addslashes($id_bann) . '">';
	            }
		    }
		    if (!empty($this->param['action'])) {
		        foreach ($this->param['action'] as $action) {
    		        $form .= '<input type="hidden" name="action[]" value="'. addslashes($action) . '">';
                }
		    }
		}
		return $form;
	}
	
	public function make_serialized_task_params() {
		global $list_bann, $radio_bannette, $dsi_action;
		$t = parent::make_serialized_task_params();

		if ($radio_bannette) {
			$t["radio_bannette"] = $radio_bannette;
			//liste de bannettes sélectionnées dans le cas où on choisi..
			if ($radio_bannette == "4") {
				$t["list_bann"] = array();
				if (!empty($list_bann) && is_countable($list_bann)) {
					foreach ($list_bann as $id_bann) {
						$t["list_bann"][$id_bann] = stripslashes($id_bann);
					}
				}
			}
		}
		$t["action"] = array();
		if (!empty($dsi_action) && is_countable($dsi_action)) {
			foreach ($dsi_action as $act) {
				$t["action"][$act] = $act;
			}
		}
    	return serialize($t);
	}
	
	public function get_formatted_settings() {
		$formatted_settings = [];
		$dsi_selections = $this->get_dsi_selections();
		if(!empty($dsi_selections[$this->param['radio_bannette']])) {
			$formatted_settings[] = $this->get_formatted_setting('radio_bannette', $this->msg["dsi_selection"], $dsi_selections[$this->param['radio_bannette']]);
		}
		if(!empty($this->param['action'])) {
			$values = array();
			$dsi_actions = $this->get_dsi_actions();
			foreach ($this->param['action'] as $name) {
				$values[] = $dsi_actions[$name];
			}
			$formatted_settings[] = $this->get_formatted_setting('dsi_action', $this->msg["dsi_actions"], implode(' / ', $values));
		}
		return $formatted_settings;
	}
}