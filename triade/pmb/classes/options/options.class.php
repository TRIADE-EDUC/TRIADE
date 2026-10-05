<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options.class.php,v 1.6 2024/01/19 11:11:02 dgoron Exp $
if (stristr ( $_SERVER ['REQUEST_URI'], ".class.php" )) die ( "no access" );

global $include_path;
require_once ("$include_path/parser.inc.php");
require_once ("$include_path/fields_empr.inc.php"); //display_type = 'custom_field'
require_once ("$include_path/fields.inc.php"); //display_type = 'custom_action'

class options {
	protected $name;
	protected $type;
	protected $parameters;
	protected $additional_parameters;
	protected $idchamp;
	protected $display_type; //Type d'affichage : custom_field / custom_action
	
	public function __construct() {
		$this->name = '';
		$this->type = str_replace('options_', '', static::class);
		$this->init_default_parameters();
	}
	
	public function init_default_parameters() {
		$this->parameters = array ();
		$this->parameters["FOR"] = $this->type;
	}
	
	public function init_additional_parameters() {
	    $this->additional_parameters = array ();
	}
	
	public function get_title() {
		global $msg;
		return "<h3>" . $msg['procs_options_param'] . $this->name . "</h3>";
	}
	
	protected function get_label_cell_content_form($label, $name) {
		global $msg;
		switch ($name) {
			case 'MAXSIZE' :
			    return "<label for='" . $name . "' style='all:unset'>".$label."</label><br /><span style='font-size: 0.8em'>".$msg['procs_options_text_max_help']."</span>";
			default :
				return "<label for='" . $name . "' style='all:unset'>".$label."</label>";
		}
	}
	
	protected function get_value_cell_content_form($label, $name, $field_type, $default_value = '') {
		global $charset;
		switch ($field_type) {
			case 'checkbox' :
			    if (empty($default_value)) {
					$default_value = '1';
			    }

			    $checked = "";
			    if (isset($this->parameters[$name]) && $this->parameters[$name][0]['value'] == $default_value) {
    			    $checked = "checked='checked'";
			    }
			    
			    return "<input type='checkbox' id='" . $name . "' name='" . $name . "' " . $checked . " value='" . $default_value . "'/>";
			case 'number' :
				return "<input class='saisie-10em' type='text' id='" . $name . "' name='" . $name . "' value='" . htmlentities($this->parameters[$name][0]['value'], ENT_QUOTES, $charset ) . "' />";
			case 'url' :
				return "<input class='saisie-40em' type='text' id='" . $name . "' name='" . $name . "' value='" . htmlentities($this->parameters[$name][0]['value'], ENT_QUOTES, $charset ) . "' />";
			case 'textarea' :
				return "<textarea cols=50 rows=5 wrap='virtual' id='" . $name . "' name='" . $name . "'>" . htmlentities($this->parameters[$name][0]['value'], ENT_QUOTES, $charset ) . "</textarea>";
			case 'text' :
			default :
				return "<input class='saisie-40em' type='text' id='" . $name . "' name='" . $name . "' value='" . htmlentities($this->parameters[$name][0]['value'], ENT_QUOTES, $charset ) . "' />";
		}
	}
	
	protected function get_line_content_form($label, $name, $field_type, $default_value = '') {
		$line_form = "
        <tr>
            <td>" . $this->get_label_cell_content_form($label, $name)."</td>
            <td>" . $this->get_value_cell_content_form($label, $name, $field_type, $default_value)."</td>
		</tr>";
		return $line_form;
	}
	
	protected function get_hidden_fields_form() {
		global $charset;
		return "<input type='hidden' name='first' value='1' />
               <input type='hidden' name='name' value='".htmlentities($this->name, ENT_QUOTES, $charset )."' />";
	}
	
	protected function get_form_title() {
		global $type_list, $type_list_empr;
		
		if($this->display_type == 'custom_action') {
			return $type_list[$this->type];
		} else {
			return $type_list_empr [$this->type];
		}
	}
	public function get_form() {
		global $current_module;

		$form = "<form class='form-".$current_module."' id='formulaire' name='formulaire' action='".static::get_controller_url_base()."' method='post'>
			<h3>".$this->get_form_title()."</h3>
			<div class='form-contenu'>
				".$this->get_hidden_fields_form()."
				<table class='table-no-border' width='100%' role='presentation'>
					".$this->get_content_form()."
				</table>
				".$this->get_additional_content_form()."
			</div>
			".$this->get_buttons_form()."
	   </form>";
		return $form;
	}
	
	protected function get_additional_content_form() {
		return '';
	}
	
	protected function get_buttons_form() {
		global $msg;
		return "<input class='bouton' type='submit' value='" . $msg[77] . "' />";
	}
	
	public function set_parameters_from_form() {
		$this->parameters["FOR"] = $this->type;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_type() {
		return $this->type;
	}
	
	public function get_parameters() {
		return $this->parameters;
	}
	
	public function get_additional_parameters() {
	    return $this->additional_parameters;
	}
	
	public function get_idchamp() {
		return $this->idchamp;
	}
	
	public function set_name($name) {
		$this->name = $name;
	}
	
	public function set_parameters($parameters) {
		$this->parameters = $parameters;
	}
	
	public function set_additional_parameters($additional_parameters) {
	    $this->additional_parameters = $additional_parameters;
	}
	
	public function set_idchamp($idchamp) {
		$this->idchamp = intval($idchamp);
	}
	
	public function set_display_type($display_type) {
		$this->display_type = $display_type;
	}
	
	public function get_controller_url_base() {
		return "options_" . $this->type . ".php";
	}
}
?>