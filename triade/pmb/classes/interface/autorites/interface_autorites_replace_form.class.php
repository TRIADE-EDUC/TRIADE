<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_autorites_replace_form.class.php,v 1.1 2021/04/29 12:22:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/interface_replace_form.class.php');

class interface_autorites_replace_form extends interface_replace_form {
	
	protected $num_parent; // pour les categories
	
	protected $id_pclass; // pour les indexations decimales
	
	protected function get_submit_action() {
		switch ($this->table_name) {
			case 'authors':
			case 'publishers':
			case 'collections':
			case 'sub_collections':
			case 'series':
			case 'authperso':
				return $this->get_url_base()."&sub=replace&id=".$this->object_id;
			case 'noeuds':
				return $this->get_url_base()."&sub=categ_replace&id=".$this->object_id."&parent=".$this->num_parent;
			case 'indexint':
				return $this->get_url_base()."&sub=replace&id=".$this->object_id."&id_pclass=".$this->id_pclass;
			default:
				return parent::get_submit_action();
		}
	}
	
	public function set_num_parent($num_parent) {
		$this->num_parent = $num_parent;
		return $this;
	}
	
	public function set_id_pclass($id_pclass) {
		$this->id_pclass = $id_pclass;
		return $this;
	}
}