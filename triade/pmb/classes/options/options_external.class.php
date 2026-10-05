<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_external.class.php,v 1.1 2021/05/11 06:46:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/options/options.class.php");

class options_external extends options {
	    
	public function init_default_parameters() {
		parent::init_default_parameters();
		$this->parameters["URL"][0]['value'] = '';
		$this->parameters["HIDE"][0]['value'] = '';
		$this->parameters["DELETE"][0]['value'] = '';
		$this->parameters["BUTTONTEXT"][0]['value'] = '';
		$this->parameters["WIDTH"][0]['value'] = '';
		$this->parameters["HEIGHT"][0]['value'] = '';
		$this->parameters["SIZE"][0]['value'] = '';
		$this->parameters["MAXSIZE"][0]['value'] = '';
		$this->parameters["QUERY"][0]['value'] = '';
	}
	
	public function get_content_form() {
		global $msg;
		$content_form = $this->get_line_content_form($msg["parperso_options_external_url"], 'URL', 'url');
        
		$content_form .= "
		<tr>
			<td>".$msg["parperso_options_external_hide"]."</td>
			<td>
				<select name='HIDE'>
					<option value='0' ".(!$this->parameters["HIDE"][0]['value'] ? "selected='selected'" : "").">".$msg["parperso_external_no"]."</option>
					<option value='1' ".($this->parameters["HIDE"][0]['value'] ? "selected='selected'" : "").">".$msg["parperso_external_yes"]."</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>".$msg["parperso_options_external_del"]."</td>
			<td>
				<select name='DELETE'>
					<option value='0' ".(!$this->parameters["DELETE"][0]['value'] ? "selected='selected'" : "").">Non</option>
					<option value='1' ".($this->parameters["DELETE"][0]['value'] ? "selected='selected'" : "").">Oui</option>
				</select>
			</td>
		</tr>";

		$content_form .= $this->get_line_content_form($msg["parperso_options_external_button"], 'BUTTONTEXT', 'text');
		$content_form .= $this->get_line_content_form($msg["parperso_options_external_width"], 'WIDTH', 'number');
		$content_form .= $this->get_line_content_form($msg["parperso_options_external_height"], 'HEIGHT', 'number');
		$content_form .= $this->get_line_content_form($msg["procs_options_text_taille"], 'SIZE', 'number');
		$content_form .= $this->get_line_content_form($msg["procs_options_text_max"], 'MAXSIZE', 'number');
		$content_form .= $this->get_line_content_form($msg["parperso_options_external_query"], 'QUERY', 'textarea');
		return $content_form;
	}
    
	public function set_parameters_from_form() {
    	global $URL, $HIDE, $MAXSIZE, $DELETE, $BUTTONTEXT;
    	global $WIDTH, $HEIGHT, $SIZE, $MAXSIZE, $QUERY;
    	
    	parent::set_parameters_from_form();
		$this->parameters["URL"][0]['value'] = stripslashes($URL);
		$this->parameters["HIDE"][0]['value'] = stripslashes($HIDE);
		$this->parameters["DELETE"][0]['value'] = stripslashes($DELETE);
		$this->parameters["BUTTONTEXT"][0]['value'] = stripslashes($BUTTONTEXT);
		$this->parameters["WIDTH"][0]['value'] = intval($WIDTH);
		$this->parameters["HEIGHT"][0]['value'] = intval($HEIGHT);
		$this->parameters["SIZE"][0]['value'] = intval($SIZE);
		$this->parameters["MAXSIZE"][0]['value'] = intval($MAXSIZE);
		$this->parameters["QUERY"][0]['value']=stripslashes($QUERY);
    }
}
?>