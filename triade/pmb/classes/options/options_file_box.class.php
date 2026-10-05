<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_file_box.class.php,v 1.1 2021/05/10 07:54:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/options/options.class.php");

class options_file_box extends options {
	    
	public function init_default_parameters() {
		parent::init_default_parameters();
		$this->parameters["METHOD"][0]['value'] = 1;
		$this->parameters["TEMP_TABLE_NAME"][0]['value'] = '';
		$this->parameters["DATA_TYPE"][0]['value'] = 1;
	}
	
	public function get_content_form() {
		global $msg, $charset;
		
		$content_form = "
		<tr>
			<td>".$msg['include_option_methode']."</td>
			<td>
				<table style='width:100%;vertical-align:center'>
					<tr>
						<td class='center'>Liste <br />
							<input type='radio' name='METHOD' value='1' ".($this->parameters["METHOD"]["0"]["value"]==1 ? "checked" : "")." /></td>
						<td>".$msg['include_option_table']."<br />
							<input type='radio' name='METHOD' value='2' ".($this->parameters["METHOD"]["0"]["value"]==2 ? "checked" : "")." /></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>".$msg['include_option_nom_table']."</td>
			<td>
				<input type='text' class='saisie-10em' name='TEMP_TABLE_NAME' value='".htmlentities($this->parameters["TEMP_TABLE_NAME"][0]['value'],ENT_QUOTES,$charset)."' />
			</td>
		</tr>
		<tr>
			<td>".$msg['include_option_type_donnees']."</td>
			<td>
				<select name='DATA_TYPE'>
					<option value='1' ".($this->parameters["DATA_TYPE"][0]['value']==1 ? "selected" : "").">".$msg['include_option_chaine']."</option>
					<option value='2' ".($this->parameters["DATA_TYPE"][0]['value']==2 ? "selected" : "").">".$msg['include_option_entier']."</option>
				</select>
			</td>
		</tr>";
		return $content_form;
	}
    
	public function set_parameters_from_form() {
		global $METHOD, $TEMP_TABLE_NAME, $DATA_TYPE;
		
		parent::set_parameters_from_form();
		$this->parameters["METHOD"][0]['value'] = stripslashes($METHOD);
		$this->parameters["TEMP_TABLE_NAME"][0]['value'] = stripslashes($TEMP_TABLE_NAME);
		$this->parameters["DATA_TYPE"][0]['value'] = $DATA_TYPE;
	}
}
?>