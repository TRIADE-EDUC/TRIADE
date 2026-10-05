<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_element_authorities.class.php,v 1.1.2.2 2024/05/30 10:01:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_element_authorities extends interface_element {
	
	protected $object_id = 0;
	
	public function init_nodes($values = []) {
	    $authorities = authorities_collection::get_authorities_list();
		foreach($authorities as $key => $name){
		    $this->add_input_node('checkbox', $key)
			->set_label($name)
			->set_id($this->name."_".$key)
			->set_name($this->name."[]")
			->set_checked((in_array($key,$values) || $this->object_id == 1) ? true : false)
			->set_disabled($this->object_id == 1 ? true : false);
		}
	}
	
	public function get_display_nodes() {
		$display = '';
		
		if(!empty($this->nodes)) {
			foreach ($this->nodes as $indice=>$node) {
				if($indice != 0 && $indice % 5 == 0){
					$display.= "<br>";
				}
				$display.= "
					<span style='margin-right:5px;'>
						".$node->get_display()."
					</span>";
			}
		}
		return $display;
	}
	
	public function get_display() {
		$display = "
		<div class='row interface-element-display interface-element-display-".$this->name."'>
			<div class='colonne5 interface-element-display-label interface-element-display-label-".$this->name."'>
				<label class='etiquette' for='".$this->name."'>".$this->label."</label>
			</div>
			<div class='colonne_suite interface-element-display-nodes interface-element-display-nodes-".$this->name."'>
				".$this->get_display_nodes()."
			</div>
		</div>";
		return $display;
	}
	
	public function set_object_id($object_id) {
		$this->object_id = intval($object_id);
		return $this;
	}
}