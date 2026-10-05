<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_html.class.php,v 1.1 2021/05/10 07:03:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/options/options.class.php");

class options_html extends options {
    
	public function init_default_parameters() {
		parent::init_default_parameters();
		$this->parameters['HEIGHT'][0]['value'] = '150';
		$this->parameters['WIDTH'][0]['value'] = '800';
		$this->parameters['REPEATABLE'][0]['value'] = '';
		$this->parameters['HTMLEDITOR'][0]['value'] = '';
	}
	
	public function get_content_form() {
		global $msg;
		$content_form = $this->get_line_content_form($msg["persofield_htmlheight"], 'HEIGHT', 'number');
		$content_form .= $this->get_line_content_form($msg["persofield_htmlwidth"], 'WIDTH', 'number');
		$content_form .= $this->get_line_content_form($msg["persofield_textrepeat"], 'REPEATABLE', 'checkbox');
		$content_form .= $this->get_line_content_form($msg["persofield_usehtmleditor"], 'HTMLEDITOR', 'checkbox');
		return $content_form;
	}
    
    public function set_parameters_from_form() {
    	global $HEIGHT, $WIDTH, $REPEATABLE, $HTMLEDITOR;
    	
    	parent::set_parameters_from_form();
		$this->parameters['HEIGHT'][0]['value'] = intval($HEIGHT);
		$this->parameters['WIDTH'][0]['value'] = intval($WIDTH);
		$this->parameters['REPEATABLE'][0]['value'] = $REPEATABLE ? 1 : 0;
		$this->parameters['HTMLEDITOR'][0]['value'] = $HTMLEDITOR ? 1 : 0;
    }
}
?>