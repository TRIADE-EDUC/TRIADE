<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_marclist.class.php,v 1.1 2021/05/11 06:46:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/options/options.class.php");

class options_marclist extends options {
	    
	public function init_default_parameters() {
		parent::init_default_parameters();
		$this->parameters['MULTIPLE'][0]['value'] = '';
		$this->parameters['AUTORITE'][0]['value'] = '';
		$this->parameters['UNSELECT_ITEM'][0]['VALUE'] = '';
		$this->parameters['UNSELECT_ITEM'][0]['value'] = '';
		$this->parameters['DEFAULT_VALUE'][0]['value'] = '';
		$this->parameters["METHOD"]["0"]["value"] = '';
		$this->parameters["METHOD_SORT_VALUE"]["0"]["value"] = '';
		$this->parameters["METHOD_SORT_ASC"]["0"]["value"] = '';
		$this->parameters["DATA_TYPE"]["0"]["value"] = '';
	}
	
	public function get_content_form() {
		global $msg, $charset;
		
		$method_checked=array();
		if($this->parameters["METHOD"]["0"]["value"])$method_checked[$this->parameters["METHOD"]["0"]["value"]]="checked";
		else $method_checked[1]="checked";
		$data_type_selected=array();
		$data_type_selected[$this->parameters["DATA_TYPE"]["0"]["value"]]="selected";
		$method_sort_value_checked=array();
		if($this->parameters["METHOD_SORT_VALUE"]["0"]["value"])$method_sort_value_checked[$this->parameters["METHOD_SORT_VALUE"]["0"]["value"]]="checked";
		else $method_sort_value_checked[2]="checked";
		$method_sort_asc_checked=array();
		if($this->parameters["METHOD_SORT_ASC"]["0"]["value"])$method_sort_asc_checked[$this->parameters["METHOD_SORT_ASC"]["0"]["value"]]="checked";
		else $method_sort_asc_checked[1]="checked";
		
		$content_form = "
		<tr>
			<td>".$msg['parperso_include_option_methode']."</td>
			<td>
				<table style='width:100%;vertical-align:center'>
					<tr>
						<td class='center'>".ucfirst($msg['parperso_include_option_selectors_code'])."
							<br />
							<input type='radio' name='METHOD' value='1' ".(isset($method_checked[1]) ? $method_checked[1] : '').">
						</td>
						<td class='center'>".ucfirst($msg['parperso_include_option_selectors_label'])."
							<br />
							<input type='radio' name='METHOD' value='2' ".(isset($method_checked[2]) ? $method_checked[2] : '').">
						</td>
					</tr>
				</table>
			</td>
		</tr>
								
		<tr>
			<td>".$msg['include_option_type_donnees']."</td>
			<td>
				<select name='DATA_TYPE'>
					<option value='country' ".(isset($data_type_selected["country"]) ? $data_type_selected["country"] : '')." >".$msg['parperso_marclist_option_country']."</option>
					<option value='lang' ".(isset($data_type_selected["lang"]) ? $data_type_selected["lang"] : '')." >".$msg['parperso_marclist_option_lang']."</option>
					<option value='doctype' ".(isset($data_type_selected["doctype"]) ? $data_type_selected["doctype"] : '')." >".$msg['parperso_marclist_option_doctype']."</option>
					<option value='function' ".(isset($data_type_selected["function"]) ? $data_type_selected["function"] : '')." >".$msg['parperso_marclist_option_function']."</option>
					<option value='section_995' ".(isset($data_type_selected["section_995"]) ? $data_type_selected["section_995"] : '')." >".$msg['parperso_marclist_option_section_995']."</option>
					<option value='typdoc_995' ".(isset($data_type_selected["typdoc_995"]) ? $data_type_selected["typdoc_995"] : '')." >".$msg['parperso_marclist_option_typdoc_995']."</option>
					<option value='codstatdoc_995' ".(isset($data_type_selected["codstatdoc_995"]) ? $data_type_selected["codstatdoc_995"] : '')." >".$msg['parperso_marclist_option_codstatdoc_995']."</option>
					<option value='nivbiblio' ".(isset($data_type_selected["nivbiblio"]) ? $data_type_selected["nivbiblio"] : '')." >".$msg['parperso_marclist_option_nivbiblio']."</option>
					<option value='music_form' ".(isset($data_type_selected["music_form"]) ? $data_type_selected["music_form"] : '')." >".$msg['parperso_marclist_option_music_form']."</option>
					<option value='music_key' ".(isset($data_type_selected["music_key"]) ? $data_type_selected["music_key"] : '')." >".$msg['parperso_marclist_option_music_key']."</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>".$msg['parperso_include_option_sort_methode']."</td>
			<td>
				<table style='width:100%;vertical-align:center'>
					<tr>
						<td class='center'>".ucfirst($msg['parperso_include_option_sort_selectors_code'])."
							<br />
							<input type='radio' name='METHOD_SORT_VALUE' value='1' ".(isset($method_sort_value_checked[1]) ? $method_sort_value_checked[1] : '').">
						</td>
						<td class='center'>".ucfirst($msg['parperso_include_option_sort_selectors_label'])."
							<br />
							<input type='radio' name='METHOD_SORT_VALUE' value='2' ".(isset($method_sort_value_checked[2]) ? $method_sort_value_checked[2] : '').">
						</td>
						<td>".ucfirst($msg['parperso_options_list_order'])."
							<br />
							<input type='radio' name='METHOD_SORT_VALUE' value='3' ".(isset($method_sort_value_checked[3]) ? $method_sort_value_checked[3] : '').">
						</td>
					</tr>
					<tr>
						<td class='center'>".ucfirst($msg['parperso_include_option_sort_selectors_asc'])."
							<br />
							<input type='radio' name='METHOD_SORT_ASC' value='1' ".(isset($method_sort_asc_checked[1]) ? $method_sort_asc_checked[1] : '').">
						</td>
						<td class='center'>".ucfirst($msg['parperso_include_option_sort_selectors_desc'])."
							<br />
							<input type='radio' name='METHOD_SORT_ASC' value='2' ".(isset($method_sort_asc_checked[2]) ? $method_sort_asc_checked[2] : '').">
						</td>
						<td></td>
					</tr>
				</table>
			</td>
		</tr>
		".$this->get_line_content_form($msg["procs_options_liste_multi"], 'MULTIPLE', 'checkbox', 'yes')."
		".$this->get_line_content_form($msg["pprocs_options_liste_authorities"], 'AUTORITE', 'checkbox', 'yes')."
		<tr>
			<td>".$msg['procs_options_choix_vide']."</td>
			<td>".$msg['procs_options_value']." : <input type='text' size='5' name='UNSELECT_ITEM_VALUE' value='".htmlentities($this->parameters['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset)."'>&nbsp;".$msg['procs_options_label']." : <input type='text' name='UNSELECT_ITEM_LIB' value='".htmlentities($this->parameters['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset)."'></td>
		</tr>";
		return $content_form;
	}
    
	public function set_parameters_from_form() {
		global $METHOD, $DATA_TYPE, $METHOD_SORT_VALUE, $METHOD_SORT_ASC, $MULTIPLE, $AUTORITE;
		global $UNSELECT_ITEM_VALUE, $UNSELECT_ITEM_LIB, $DEFAULT_VAULE;
		
		parent::set_parameters_from_form();
		$this->parameters["METHOD"][0]['value'] = stripslashes($METHOD);
		$this->parameters["DATA_TYPE"][0]['value'] = $DATA_TYPE;
		$this->parameters["METHOD_SORT_VALUE"][0]['value'] = stripslashes($METHOD_SORT_VALUE);
		$this->parameters["METHOD_SORT_ASC"][0]['value'] = stripslashes($METHOD_SORT_ASC);
		$this->parameters['MULTIPLE'][0]['value']="no";
		if ($MULTIPLE == "yes") {
			$this->parameters['MULTIPLE'][0]['value'] = "yes";
		}
		$this->parameters['AUTORITE'][0]['value'] = "no";
		if ($AUTORITE == "yes") {
			$this->parameters['AUTORITE'][0]['value']="yes";
		}
		$this->parameters['UNSELECT_ITEM'][0]['VALUE']=stripslashes($UNSELECT_ITEM_VALUE);
		$this->parameters['UNSELECT_ITEM'][0]['value']="<![CDATA[".stripslashes($UNSELECT_ITEM_LIB)."]]>";
		$this->parameters["DEFAULT_VALUE"][0]['value'] = stripslashes($DEFAULT_VAULE);
	}
}
?>