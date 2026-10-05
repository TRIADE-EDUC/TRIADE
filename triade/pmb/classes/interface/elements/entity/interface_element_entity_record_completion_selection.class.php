<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_element_entity_record_completion_selection.class.php,v 1.1.2.4 2025/03/20 13:53:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_element_entity_record_completion_selection extends interface_element_entity_completion_selection {
	
    protected $caller = 'notice';
    
    protected $movable = true;
    
    public function add_authority_node($value='', $completion='') {
        $node = parent::add_authority_node($value, $completion);
        $node->set_openPopUpFunction('openPopUpSelector');
        return $node;
    }
    
    public function get_display() {
        global $charset;
        
        if (empty($this->nodes)) {
            $this->add_node();
        }
        $display = "<div id='".$this->uid."' class='".(!empty($this->class) ? $this->class : 'row')."' ".(!empty($this->movable) ? "movable='yes'" : '')." title=\"".htmlentities($this->label, ENT_QUOTES, $charset)."\">
			<div id='".$this->uid."a' class='row'>
				<label class='etiquette' for='".$this->name."'>".$this->label."</label>";
        if ($this->repeatable) {
            $display .= $this->get_display_button_add();
        }
        $display .= "
			</div>";
        if ($this->repeatable) {
            $display .= "
                <input type='hidden' id='max_".$this->name."' name='max_".$this->name."' value=\"".(empty($this->nodes) ? 1 : count($this->nodes))."\" />
                ".$this->get_display_nodes()."
                <div id='add".$this->name."'></div>";
        } else {
            $display .= "
				<div id='".$this->uid."b' class='row'>
					".$this->get_display_node()."
				</div>";
        }
        $display .= "
        </div>";
        return $display;
    }
}