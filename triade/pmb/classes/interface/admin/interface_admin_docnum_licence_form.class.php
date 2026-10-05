<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_admin_docnum_licence_form.class.php,v 1.1 2022/03/31 14:17:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/admin/interface_admin_form.class.php');

class interface_admin_docnum_licence_form extends interface_admin_form {
	
	protected $num_explnum_licence;
	
	protected $what;
	
	protected function get_submit_action() {
		switch ($this->what) {
			case 'profiles':
				return $this->get_url_base()."&action=settings&id=".$this->num_explnum_licence."&what=".$this->what."&profileaction=save&profileid=".$this->object_id;
			case 'rights':
				return $this->get_url_base()."&action=settings&id=".$this->num_explnum_licence."&what=".$this->what."&rightaction=save&rightid=".$this->object_id;
		}
		
	}
	
	protected function get_delete_action() {
		switch ($this->what) {
			case 'profiles':
				return $this->get_url_base()."&action=settings&id=".$this->num_explnum_licence."&what=".$this->what."&profileaction=delete&profileid=".$this->object_id;
			case 'rights':
				return $this->get_url_base()."&action=settings&id=".$this->num_explnum_licence."&what=".$this->what."&rightaction=delete&rightid=".$this->object_id;
		}
	}
	
	protected function get_cancel_action() {
		switch ($this->what) {
			case 'profiles':
				return $this->get_url_base()."&action=settings&id=".$this->num_explnum_licence;
			case 'rights':
				return $this->get_url_base()."&action=settings&id=".$this->num_explnum_licence;
		}
	}
	
	public function set_num_explnum_licence($num_explnum_licence) {
		$this->num_explnum_licence = $num_explnum_licence;
		return $this;
	}
	
	public function set_what($what) {
		$this->what = $what;
		return $this;
	}
}