<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_entity_element.class.php,v 1.1.2.2 2025/02/27 10:04:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_entity_element extends interface_element {
	
	protected $movable = true;
	
	public function add_input_node($type, $value='', $attributes = []) {
	    $node = parent::add_input_node($type, $value, $attributes);
	    switch ($type) {
	        case 'url':
	        case 'text':
	            $node->set_class('saisie-80em');
	            break;
	    }
	    return $node;
	}
	
	public function get_display() {
	    global $charset;
	    
	    $display = '';
		switch ($this->display_type) {
			case 'flat':
				$display .= "
				<div id='".$this->uid."' class='".(!empty($this->class) ? $this->class : 'row')."' ".(!empty($this->movable) ? "movable='yes'" : '')." title=\"".htmlentities($this->label, ENT_QUOTES, $charset)."\">
					".(!empty($this->label) ? "<label class='etiquette' for='".$this->name."'>".$this->label."</label>" : "")."
					".$this->get_display_nodes()."
				</div>";
				break;
			default:
			    $display .= "<div id='".$this->uid."' class='".(!empty($this->class) ? $this->class : 'row')."' ".(!empty($this->movable) ? "movable='yes'" : '')." title=\"".htmlentities($this->label, ENT_QUOTES, $charset)."\">";
				if(!empty($this->label)) {
					$display .= "
					<div class='row'>
						<label class='etiquette' for='".$this->name."'>".$this->label."</label>
					</div>";
				}
				if($this->hidden_nodes) {
					$display .= $this->get_display_nodes();
				} else {
					$display .= "
					<div class='row'>
						".$this->get_display_nodes()."
					</div>";
				}
				$display .= "</div>";
				break;
		}
		return $display;
	}
	
}