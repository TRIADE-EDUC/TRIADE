<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_date_box.class.php,v 1.1 2021/05/10 07:03:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/options/options.class.php");

class options_date_box extends options {
    
	public function init_default_parameters() {
		parent::init_default_parameters();
		$this->parameters["DEFAULT_TODAY"][0]["value"] = '';
		$this->parameters['REPEATABLE'][0]['value'] = '';
	}
	
	public function get_content_form() {
		global $msg;
		$content_form = $this->get_line_content_form($msg["parperso_default_today"], 'DEFAULT_TODAY', 'checkbox', 'yes');
		$content_form .= $this->get_line_content_form($msg['persofield_textrepeat'], 'REPEATABLE', 'checkbox');
		return $content_form;
	}
	
	public function set_parameters_from_form() {
    	global $DEFAULT_TODAY, $REPEATABLE;
    	
		parent::set_parameters_from_form();
		if ($DEFAULT_TODAY) $this->parameters["DEFAULT_TODAY"][0]["value"]="yes";
		else $this->parameters["DEFAULT_TODAY"][0]["value"]="";
		$this->parameters['REPEATABLE'][0]['value'] = $REPEATABLE ? 1 : 0;
	}
}
?>