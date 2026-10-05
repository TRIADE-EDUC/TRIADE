<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_demandes_form.class.php,v 1.4.2.1 2025/01/10 15:26:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/interface_form.class.php');

class interface_demandes_form extends interface_form {
	
	protected $num_demande;
	
	protected $num_action;
	
	protected function get_cancel_action() {
		switch ($this->table_name) {
			case 'demandes':
				if($this->object_id){
				    return "./demandes.php?categ=gestion&act=see_dmde&iddemande=".$this->object_id;
				} else {
				    return "./demandes.php?categ=list";
				}
				break;
			case 'demandes_actions':
				if($this->object_id){
					return $this->get_url_base()."&act=see&iddemande=".$this->num_demande."&idaction=".$this->object_id;
				} else {
					return "./demandes.php?categ=gestion&act=see_dmde&iddemande=".$this->num_demande;
				}
				break;
			case 'demandes_notes':
				return "./demandes.php?categ=action&act=see&idaction=".$this->num_action."#fin";
				break;
			case 'explnum_doc':
				return "./demandes.php?categ=action&act=see&idaction=".$this->num_action;
				break;
			default:
				return parent::get_cancel_action();
				break;
		}
	}
	
	protected function get_submit_action() {
		switch ($this->table_name) {
			case 'demandes':
				if($this->object_id){
				    return "./demandes.php?categ=gestion&act=save&iddemande=".$this->object_id;
				} else {
				    return "./demandes.php?categ=list&act=save";
				}
			case 'demandes_actions':
				return $this->get_url_base()."&act=save_action&iddemande=".$this->num_demande."&idaction=".$this->object_id;
			case 'demandes_notes':
				return $this->get_url_base()."&act=save_note&idnote=".$this->object_id."#fin";
			case 'explnum_doc':
				return $this->get_url_base()."&act=save_docnum&iddocnum=".$this->object_id."&idaction=".$this->num_action;
			default:
				return parent::get_submit_action();
		}
	}
	
	protected function get_delete_action() {
		switch ($this->table_name) {
			case 'demandes':
				return $this->get_url_base()."&act=suppr&iddemande=".$this->object_id;
			case 'demandes_actions':
				return $this->get_url_base()."&act=suppr_action&idaction=".$this->object_id;
			case 'demandes_notes':
				return $this->get_url_base()."&act=suppr_note&idnote=".$this->object_id."#fin";
			case 'explnum_doc':
			    return $this->get_url_base()."&act=suppr_docnum&iddocnum=".$this->object_id."&idaction=".$this->num_action;
			default:
				return parent::get_delete_action();
		}
	}
	
	public function set_num_demande($num_demande) {
		$this->num_demande = intval($num_demande);
		return $this;
	}
	
	public function set_num_action($num_action) {
		$this->num_action = intval($num_action);
		return $this;
	}
}