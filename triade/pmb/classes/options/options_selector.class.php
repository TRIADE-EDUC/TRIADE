<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_selector.class.php,v 1.1 2021/05/10 07:54:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/options/options.class.php");

class options_selector extends options {
	    
	public function init_default_parameters() {
		parent::init_default_parameters();
	}
	
	public function get_content_form() {
		global $msg;
		
		$method_checked = array();
		if(isset($this->parameters["METHOD"]["0"]["value"]) && $this->parameters["METHOD"]["0"]["value"]) {
			$method_checked[$this->parameters["METHOD"]["0"]["value"]]="checked";
		} else {
			$method_checked[1]="checked";
		}
		$data_type_selected = array();
		if(isset($this->parameters["DATA_TYPE"]["0"]["value"])) {
			$data_type_selected[$this->parameters["DATA_TYPE"]["0"]["value"]]="selected";
		}
		
		$options_authperso='';
		$authpersos=authpersos::get_authpersos();
		foreach ($authpersos as $authperso){
			$options_authperso.="<option value='".($authperso['id'] + 1000)."' ".(isset($data_type_selected[($authperso['id'] + 1000)]) ? $data_type_selected[($authperso['id'] + 1000)] : '')." >".$authperso['name']."</option>";
		}
		
		$content_form = "
		<tr>
			<td>".$msg['parperso_include_option_methode']."</td>
			<td>
				<table style='width:100%; vertical-align:center'>
					<tr><td class='center'>".$msg['parperso_include_option_selectors_id']."
					<br />
					<input type='radio' name='METHOD' value='1' ".(isset($method_checked[1]) ? $method_checked[1] : '').">
					</td>
					<td class='center'>".$msg['parperso_include_option_selectors_label']."
					<br />
					<input type='radio' name='METHOD' value='2' ".(isset($method_checked[2]) ? $method_checked[2] : '').">
					</td></tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>".$msg['include_option_type_donnees']."</td>
			<td>
				<select name='DATA_TYPE'>
					<option value='1' ".(isset($data_type_selected[1]) ? $data_type_selected[1] : '')." >".$msg['133']."</option>
					<option value='2' ".(isset($data_type_selected[2]) ? $data_type_selected[2] : '')." >".$msg['134']."</option>
					<option value='3' ".(isset($data_type_selected[3]) ? $data_type_selected[3] : '')." >".$msg['135']."</option>
					<option value='4' ".(isset($data_type_selected[4]) ? $data_type_selected[4] : '')." >".$msg['136']."</option>
					<option value='5' ".(isset($data_type_selected[5]) ? $data_type_selected[5] : '')." >".$msg['137']."</option>
					<option value='6' ".(isset($data_type_selected[6]) ? $data_type_selected[6] : '')." >".$msg['333']."</option>
					<option value='7' ".(isset($data_type_selected[7]) ? $data_type_selected[7] : '')." >".$msg['indexint_menu']."</option>
					<option value='8' ".(isset($data_type_selected[8]) ? $data_type_selected[8] : '')." >".$msg['titre_uniforme_search']."</option>
					<option value='9' ".(isset($data_type_selected[9]) ? $data_type_selected[9] : '')." >".$msg['skos_view_concepts_concepts']."</option>
					$options_authperso
				</select>
			</td>
		</tr>
		";
		return $content_form;
	}
    
	public function set_parameters_from_form() {
		global $METHOD, $DATA_TYPE;
		
		parent::set_parameters_from_form();
		$this->parameters["METHOD"][0]['value'] = stripslashes($METHOD);
		$this->parameters["DATA_TYPE"][0]['value'] = $DATA_TYPE;
	}
}
?>