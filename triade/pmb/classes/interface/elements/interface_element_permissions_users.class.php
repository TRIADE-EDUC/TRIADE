<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_element_permissions_users.class.php,v 1.2 2023/07/07 14:29:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_element_permissions_users extends interface_element {
	
	protected $autorisations = [];
	
	protected $on_create = 0;
	
	public function get_display_nodes() {
		return users::get_form_autorisations($this->autorisations, $this->on_create);
	}
	
	public function get_display() {
		global $msg;
		
		$display = "
		<div class='row interface-element-display-label interface-element-display-label-".$this->name."'>
			<label class='etiquette' for='".$this->name."'>".$this->label."</label>
			<input type='button' class='bouton_small align_middle' value='".$msg['tout_cocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,1);'>
			<input type='button' class='bouton_small align_middle' value='".$msg['tout_decocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,0);'>
		</div>
		<div class='row interface-element-display-nodes interface-element-display-nodes-".$this->name."'>
			".$this->get_display_nodes()."
		</div>";
		return $display;
	}
	
	public function set_autorisations($autorisations) {
		$this->autorisations = $autorisations;
		return $this;
	}
	
	public function set_on_create($on_create) {
		$this->on_create = $on_create;
		return $this;
	}
}