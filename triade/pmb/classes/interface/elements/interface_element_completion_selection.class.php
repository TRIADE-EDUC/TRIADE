<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_element_completion_selection.class.php,v 1.1.2.4 2025/03/20 13:53:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_element_completion_selection extends interface_element {
	
	protected $completion = '';
	
	protected $what = '';
	
	protected $caller = '';
	
	protected $openPopUpUrl;
	
	protected $hidden_name = '';
	
	protected $param1;
	
	protected $repeatable = false;
	
	protected $selector_function = '';
	
	protected $callback = '';
	
	protected function init_openPopUpUrl() {
	    $this->openPopUpUrl = '';
	}
	
	protected function add_node($node=null) {
	    if ($node == null) {
	        $node = [
	            'value' => '',
	            'hidden_value' => 0,
	            'is_force_dialog' => 0,
	            'is_force_popup' => 0
	        ];
	    }
	    if (empty($this->hidden_name)) {
	        $this->hidden_name = $this->name."_id";
	    }
	    if ($this->repeatable) {
	        $indice = count($this->nodes);
	        $name = $this->name.$indice;
	        $hidden_name = $this->hidden_name.$indice;
	    } else {
	        $name = $this->name;
	        $hidden_name = $this->hidden_name;
	    }
	    
	    $openPopUpUrl = $this->get_openPopUpUrl();
	    $openPopUpUrl = str_replace('!!name!!', $name, $openPopUpUrl);
	    $openPopUpUrl = str_replace('!!hidden_name!!', $hidden_name, $openPopUpUrl);
	    $this->add_authority_node($node['value'] ?? '', $this->completion)
	    ->set_id($name)
	    ->set_name($name)
	    ->set_class('saisie-30emr')
	    ->set_hidden_name($hidden_name)
	    ->set_hidden_value($node['hidden_value'] ?? '')
	    ->set_hidden_type($node['hidden_type'] ?? 'integer')
	    ->set_openPopUpUrl($openPopUpUrl)
	    ->set_param1($this->param1)
	    ->set_forceSelectorDialog($node['is_force_dialog'] ?? 0)
	    ->set_forceSelectorPopUp($node['is_force_popup'] ?? 0)
	    ->set_repeatable($this->repeatable)
	    ->set_selector_function($this->selector_function)
	    ->set_callback($this->callback);
	}
	
	public function init_nodes($nodes = []) {
	    if (empty($nodes)) {
	        $number = 1;
	    } else {
	        $number = count($nodes);
	    }
	    for ($i = 0; $i < $number; $i++) {
	        $this->add_node($nodes[$i]);
		}
	}
	
	protected function get_display_node($indice=0) {
	    if (!empty($this->nodes[$indice])) {
    	    return "<div class='row'>
    			".$this->nodes[$indice]->get_display()."
    		</div>";
	    }
	    return "";
	}
	
	public function get_display_nodes() {
		$display = '';
		if(!empty($this->nodes)) {
		    $number = count($this->nodes);
		    for ($i = 0; $i < $number; $i++) {
		        $display .= $this->get_display_node($i);
		    }
		}
		return $display;
	}
	
	protected function get_display_button_add() {
	    if ($this->selector_function) {
	        return "<input id='button_add_".$this->hidden_name."' type='button' class='bouton' value='+' onClick=\"templates.add_completion_selection_field('".$this->name."', '".$this->hidden_name."', '".$this->completion."', '".$this->selector_function."');\"/>";
	    } else {
	        return "<input id='button_add_".$this->hidden_name."' type='button' class='bouton' value='+' onClick=\"templates.add_completion_field('".$this->name."', '".$this->hidden_name."', '".$this->completion."');\"/>";
	    }
	}
	
	public function get_display() {
	    if (empty($this->nodes)) {
	        $this->add_node();
	    }
		$display = "
		<div class='row interface-element-display interface-element-display-".$this->name."'>
			<div class='interface-element-display-label interface-element-display-label-".$this->name."'>
				<label class='etiquette' for='".$this->name."'>".$this->label."</label>";
		if ($this->repeatable) {
		    $display .= $this->get_display_button_add();
		}
		$display .=	"
            </div>
			<div class='row interface-element-display-nodes interface-element-display-nodes-".$this->name."'>";
		if ($this->repeatable) {
		    $display .= "
                <input type='hidden' id='max_".$this->name."' name='max_".$this->name."' value=\"".(empty($this->nodes) ? 1 : count($this->nodes))."\" />
				".$this->get_display_nodes()."
                <div id='add".$this->name."'/>";
		} else {
		    $display .= $this->get_display_node();
		}
		$display .= "
			</div>
		</div>";
		return $display;
	}
	
	public function set_completion($completion) {
	    $this->completion = $completion;
		return $this;
	}

	public function set_what($what) {
	    $this->what = $what;
		return $this;
	}
	
	public function set_caller($caller) {
	    $this->caller = $caller;
	    return $this;
	}
	
	public function get_openPopUpUrl() {
	    if (empty($this->openPopUpUrl)) {
	        $this->init_openPopUpUrl();
	    }
        return $this->openPopUpUrl;
	}
	
	public function set_openPopUpUrl($openPopUpUrl) {
	    $this->openPopUpUrl = $openPopUpUrl;
	    return $this;
	}
	
	public function set_hidden_name($hidden_name) {
	    $this->hidden_name = $hidden_name;
		return $this;
	}
	
	public function set_param1($param1) {
	    $this->param1 = $param1;
	    return $this;
	}
	
	public function set_repeatable($repeatable) {
	    $this->repeatable = $repeatable;
	    return $this;
	}
	
	public function set_selector_function($selector_function) {
	    $this->selector_function = $selector_function;
	    return $this;
	}

	public function set_callback($callback) {
	    $this->callback = $callback;
	    return $this;
	}
}