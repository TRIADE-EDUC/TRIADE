<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_admin_connecteurs_form.class.php,v 1.1 2022/05/20 07:27:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/admin/interface_admin_form.class.php');

class interface_admin_connecteurs_form extends interface_admin_form {
	
	protected $connector_id;
	
	protected function get_submit_action() {
		return $this->get_url_base()."&act=update_source&id=".$this->connector_id."&source_id=".$this->object_id;
	}
	
	protected function get_delete_action() {
		return $this->get_url_base()."&act=delete_source&id=".$this->connector_id."&source_id=".$this->object_id;
	}

	public function set_connector_id($connector_id) {
		$this->connector_id = $connector_id;
		return $this;
	}
}