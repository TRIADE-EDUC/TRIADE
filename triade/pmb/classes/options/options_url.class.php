<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_url.class.php,v 1.1 2021/05/10 07:03:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/options/options.class.php");

class options_url extends options {
    
	public function init_default_parameters() {
		parent::init_default_parameters();
		$this->parameters['MAXSIZE'][0]['value'] = '255';
		$this->parameters['TIMEOUT'][0]['value'] = '';
		$this->parameters['REPEATABLE'][0]['value'] = '';
		$this->parameters['LINKTARGET'][0]['value'] = '';
	}
	
	public function get_content_form() {
		global $msg;
		$content_form = $this->get_line_content_form($msg["procs_options_url_max"], 'MAXSIZE', 'number');
		$content_form .= $this->get_line_content_form($msg["procs_options_url_timeout"], 'TIMEOUT', 'number');
		$content_form .= $this->get_line_content_form($msg["persofield_urlrepeat"], 'REPEATABLE', 'checkbox');
		$content_form .= $this->get_line_content_form($msg['persofield_url_linktarget']." (".$msg['persofield_url_linktarget_default_checked'].")", 'LINKTARGET', 'checkbox');
		return $content_form;
	}
	
	public function set_parameters_from_form() {
		global $SIZE, $MAXSIZE, $TIMEOUT, $REPEATABLE, $LINKTARGET;
		
		parent::set_parameters_from_form();
		$this->parameters['SIZE'][0]['value'] = intval($SIZE);
		$this->parameters['MAXSIZE'][0]['value'] = intval($MAXSIZE);
		$this->parameters['TIMEOUT'][0]['value'] = intval($TIMEOUT);
		$this->parameters['REPEATABLE'][0]['value'] = $REPEATABLE ? 1 : 0;
		$this->parameters['LINKTARGET'][0]['value'] = $LINKTARGET ? 1 : 0;
	}
}
?>