<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_admin_acquisition_form.class.php,v 1.3.8.3 2025/05/28 06:18:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/admin/interface_admin_form.class.php');

class interface_admin_acquisition_form extends interface_admin_form {
	
	protected $id_entity;
	
	protected $id_budget;
	
	protected $id_parent;
	
	protected $statut;
	
	protected $confirm_cloture_msg;
	
	protected function get_action_delete_label() {
		global $sub, $msg;
		
		switch ($sub) {
			case 'compta':
			case 'budget':
				return $msg['supprimer'];
			case 'pricing_systems':
				return $msg['pricing_system_delete'];
			default:
				return parent::get_action_delete_label();
		}
	}
	
	protected function get_action_duplicate_label() {
		global $sub, $msg;
		
		switch ($sub) {
			case 'budget':
				return $msg['acquisition_bud_bt_dup'];
			case 'pricing_systems':
				return $msg['pricing_system_duplicate'];
			default:
				return parent::get_action_duplicate_label();
		}
	}
	
	protected function get_submit_action() {
		global $sub, $action;
		
		switch ($sub) {
			case 'budget':
				switch ($action) {
					case 'add_rub':
					case 'modif_rub':
						return $this->get_url_base()."&id_bibli=".$this->id_entity."&action=update_rub&id_bud=".$this->id_budget."&id_rub=".$this->object_id."&id_parent=".$this->id_parent;
					default:
						return $this->get_url_base()."&id_bibli=".$this->id_entity."&action=update&id_bud=".$this->object_id;
				}
			case 'compta':
			default:
				return $this->get_url_base()."&id_entity=".$this->id_entity."&action=save&id=".$this->object_id;
		}
		
	}
	
	protected function get_duplicate_action() {
		global $sub;
		
		switch ($sub) {
			case 'budget':
				return $this->get_url_base()."&id_bibli=".$this->id_entity."&action=dup&id=".$this->object_id;
			case 'compta':
			default:
				return $this->get_url_base()."&id_entity=".$this->id_entity."&action=duplicate&id=".$this->object_id;
		}
	}
	
	protected function get_delete_action() {
		global $sub, $action;
		
		switch ($sub) {
			case 'budget':
				switch ($action) {
					case 'modif_rub':
						return $this->get_url_base()."&id_bibli=".$this->id_entity."&action=del_rub&id_bud=".$this->id_budget."&id_parent=".$this->id_parent."&id_rub=".$this->object_id;
					default:
						return $this->get_url_base()."&id_bibli=".$this->id_entity."&action=del&id_bud=".$this->object_id;
				}
			case 'compta':
			default:
				return $this->get_url_base()."&id_entity=".$this->id_entity."&action=delete&id=".$this->object_id;
		}
	}
	
	protected function get_cancel_action() {
		global $sub, $action;
		
		switch ($sub) {
			case 'budget':
				switch ($action) {
					case 'add_rub':
					case 'modif_rub':
						if(!$this->id_parent) {
							$undo = "modif";
						} else {
							$undo = "modif_rub";
						}
						return $this->get_url_base()."&action=".$undo."&id_bibli=".$this->id_entity."&id_bud=".$this->id_budget."&id_rub=".$this->id_parent;
					default:
						return $this->get_url_base()."&action=list&id_bibli=".$this->id_entity;
				}
			case 'compta':
				return $this->get_url_base()."&action=list&id_entity=".$this->id_entity;
			default:
				return $this->get_url_base()."&id_entity=".$this->id_entity;
		}
	}
	
	protected function get_cloture_action() {
		return $this->get_url_base()."&id_entity=".$this->id_entity."&action=cloture&id=".$this->object_id;
	}
	
	protected function get_action_cloture_label() {
		global $sub;
		global $msg;
		
		switch ($sub) {
			case 'compta':
				return $msg['acquisition_compta_clot'];
			case 'budget':
				return $msg['acquisition_budg_clot'];
		}
	}
	
	protected function get_display_cloture_action() {
		global $sub;
		
		switch ($sub) {
			case 'compta':
				if($this->statut != STA_EXE_CLO) {
					return $this->get_display_action('cloture_button', $this->get_action_cloture_label(), ['confirm_msg' => $this->confirm_cloture_msg, 'location' => $this->get_cloture_action()]);
				}
				break;
			case 'budget':
				if($this->statut == STA_BUD_VAL) {
				    return $this->get_display_action('cloture_button', $this->get_action_cloture_label(), ['confirm_msg' => $this->confirm_cloture_msg, 'location' => $this->get_cloture_action()]);
				}
				break;
		}
		return '';
	}
	
	protected function get_activation_action() {
		return $this->get_url_base()."&id_entity=".$this->id_entity."&action=activation&id=".$this->object_id;
	}
	
	protected function get_action_activation_label() {
		global $msg;
		
		return $msg['acquisition_budg_act'];
	}
	
	protected function get_display_activation_action() {
		if($this->statut != STA_BUD_VAL && $this->statut != STA_BUD_CLO) {
		    return $this->get_display_action('activation_button', $this->get_action_activation_label(), ['location' => $this->get_activation_action()]);
		}
		return '';
	}
	
	protected function get_display_actions() {
		global $msg, $sub, $action;
		
		switch ($sub) {
			case 'compta':
				$display = "
					<div class='left'>
						".$this->get_display_cancel_action()."
						".$this->get_display_submit_action()."
						".($this->object_id && !empty($this->duplicable) ? $this->get_display_duplicate_action() : "")."
						".($this->object_id && !empty($this->actions_extension) ? $this->get_display_actions_extension() : "")."
					</div>
					<div class='right'>
						".($this->object_id ? $this->get_display_cloture_action() : "")."
						".($this->object_id ? $this->get_display_delete_action() : "")."
					</div>";
				break;
			case 'budget':
				switch ($action) {
					case 'add_rub':
					case 'modif_rub':
						$display = parent::get_display_actions();
						break;
					default:
						$display = "
							<div class='left'>
								".$this->get_display_cancel_action()."
								".$this->get_display_submit_action()."
								".($this->object_id && !empty($this->duplicable) ? $this->get_display_duplicate_action() : "")."
								".($this->object_id && !empty($this->actions_extension) ? $this->get_display_actions_extension() : "")."
							</div>
							<div class='right'>
								".($this->object_id ? $this->get_display_activation_action() : "")."
								".($this->object_id ? $this->get_display_cloture_action() : "")."
								".($this->object_id ? $this->get_display_delete_action() : "")."
							</div>";
						break;
				}
				break;
			case 'pricing_systems':
			    switch ($action) {
			        case 'grid_edit':
			            $display = "
                            <div class='left'>
                                ".$this->get_display_action('cancel_button', $this->get_action_cancel_label(), ['function' => 'history.go(-1);'])."&nbsp;
                        		<input class='bouton' type='submit' value=' ".$this->get_action_save_label()." ' onClick=\"return pricing_system_grid_check_form(this.form);\"  />
                        	</div>
                        	<div class='right'>
                                ".$this->get_display_action('reset_button', $msg['pricing_system_grid_reset'], ['function' => 'pricing_system_grid_confirm_reset();return false;'])."
                        	</div>";
			            break;
			        default:
			            $display = parent::get_display_actions();
			            break;
			    }
			    break;
			default:
				$display = parent::get_display_actions();
		}
		return $display;
	}
	
	public function set_id_entity($id_entity) {
		$this->id_entity = intval($id_entity);
		return $this;
	}
	
	public function set_id_budget($id_budget) {
		$this->id_budget = intval($id_budget);
		return $this;
	}
	
	public function set_id_parent($id_parent) {
		$this->id_parent = intval($id_parent);
		return $this;
	}
	
	public function set_statut($statut) {
		$this->statut = $statut;
		return $this;
	}
	
	public function set_confirm_cloture_msg($confirm_cloture_msg) {
		$this->confirm_cloture_msg = $confirm_cloture_msg;
		return $this;
	}
}