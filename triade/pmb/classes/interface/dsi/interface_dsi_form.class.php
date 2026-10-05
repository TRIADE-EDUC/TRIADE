<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_dsi_form.class.php,v 1.2.8.1 2025/03/06 08:40:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/interface_form.class.php');

class interface_dsi_form extends interface_form {
	
	protected $bannette_type;
	
	protected $id_empr;
	
	protected function get_cancel_action() {
		switch ($this->table_name) {
			case 'bannettes':
				if($this->bannette_type == 'abo') {
					return $this->get_url_base()."&suite=acces&id_empr=".$this->id_empr;
				} else {
					if($this->object_id) {
						return $this->get_url_base()."&suite=search";
					}
				}
			default:
				return parent::get_cancel_action();
		}
	}
	
	protected function get_display_cancel_action() {
		switch ($this->table_name) {
			case 'bannettes':
				if($this->bannette_type == 'abo') {
					return parent::get_display_cancel_action();
				} else {
					if($this->object_id) {
						return parent::get_display_cancel_action();
					} else {
						return $this->get_display_action('cancel_button', $this->get_action_cancel_label(), ['function' => 'history.go(-1);']);
					}
				}
				break;
			default:
				return parent::get_display_cancel_action();
				break;
		}
	}
	
	protected function get_submit_action() {
		switch ($this->table_name) {
			case 'bannettes':
				return $this->get_url_base()."&suite=update&id_bannette=".$this->object_id;
			case 'equations':
				return $this->get_url_base()."&suite=update&id_equation=".$this->object_id;
			case 'classements':
				return $this->get_url_base()."&suite=update&id_classement=".$this->object_id;
			case 'rss_flux':
				return $this->get_url_base()."&suite=update&id_rss_flux=".$this->object_id;
			default:
				return $this->get_url_base()."&suite=update&id=".$this->object_id;
		}
	}
	
	protected function get_duplicate_action() {
		switch ($this->table_name) {
			case 'bannettes':
				return $this->get_url_base()."&suite=duplicate&id_bannette=".$this->object_id;
			case 'equations':
				return $this->get_url_base()."&suite=duplicate&id_equation=".$this->object_id;
			case 'classements':
				return $this->get_url_base()."&suite=duplicate&id_classement=".$this->object_id;
			case 'rss_flux':
				return $this->get_url_base()."&suite=duplicate&id_rss_flux=".$this->object_id;
			default:
				return $this->get_url_base()."&suite=duplicate&id=".$this->object_id;
		}
	}
	
	protected function get_delete_action() {
		switch ($this->table_name) {
			case 'bannettes':
				return $this->get_url_base()."&suite=delete&id_bannette=".$this->object_id;
			case 'equations':
				return $this->get_url_base()."&suite=delete&id_equation=".$this->object_id;
			case 'classements':
				return $this->get_url_base()."&suite=delete&id_classement=".$this->object_id;
			case 'rss_flux':
				return $this->get_url_base()."&suite=delete&id_rss_flux=".$this->object_id;
			default:
				return $this->get_url_base()."&suite=delete&id=".$this->object_id;
		}
	}
	
	public function set_bannette_type($bannette_type) {
		$this->bannette_type = $bannette_type;
		return $this;
	}
	
	public function set_id_empr($id_empr) {
		$this->id_empr = intval($id_empr);
		return $this;
	}
}