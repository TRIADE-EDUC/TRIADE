<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_entity_record_element.class.php,v 1.1.2.2 2025/03/12 15:39:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_entity_record_element extends interface_entity_element {
	
	public function add_authority_node($value='', $completion='') {
	    $node = parent::add_authority_node($value, $completion);
	    $node->set_openPopUpFunction('openPopUpSelector');
	    return $node;
	}
	
	public function get_display() {
	    global $charset;
	    
	    $display = '';
		switch ($this->display_type) {
			case 'flat':
				$display .= "
				<div id='".$this->uid."' ".(!empty($this->class) ? "class='".$this->class."'" : "")." ".(!empty($this->movable) ? "movable='yes'" : '')." title=\"".htmlentities($this->label, ENT_QUOTES, $charset)."\">
					".(!empty($this->label) ? "<label class='etiquette' for='".$this->name."'>".$this->label."</label>" : "")."
					".$this->get_display_nodes()."
				</div>";
				break;
			default:
			    $display .= "<div id='".$this->uid."' ".(!empty($this->class) ? "class='".$this->class."'" : "")." ".(!empty($this->movable) ? "movable='yes'" : '')." title=\"".htmlentities($this->label, ENT_QUOTES, $charset)."\">";
				if(!empty($this->label)) {
					$display .= "
					<div id='".$this->uid."a' class='row'>
						<label class='etiquette' for='".$this->name."'>".$this->label."</label>
					</div>";
				}
				if($this->hidden_nodes) {
					$display .= $this->get_display_nodes();
				} else {
					$display .= "
					<div id='".$this->uid."b' class='row'>
						".$this->get_display_nodes()."
					</div>";
				}
				$display .= "</div>";
				break;
		}
		return $display;
	}
	
}