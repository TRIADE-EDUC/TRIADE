<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_acquisition_form.class.php,v 1.1 2021/03/23 08:43:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/interface_form.class.php');

class interface_acquisition_form extends interface_form {
	
	protected function get_submit_action() {
		return $this->get_url_base()."&action=update&id=".$this->object_id;
	}
	
	protected function get_delete_action() {
		return $this->get_url_base()."&action=del&id=".$this->object_id;
	}
}