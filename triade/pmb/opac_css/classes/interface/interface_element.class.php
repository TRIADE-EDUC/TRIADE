<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_element.class.php,v 1.1.6.5 2025/05/23 07:42:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/translation.class.php');

class interface_element {
	
    protected $uid;
    
	protected $name;
	
	protected $label;
	
	protected $label_code;
	
	protected $class;
	
	protected $nodes;
	
	protected $display_type = 'row';
	
	protected $hidden_nodes = false;
	
	protected $display_nodes_separator = '<br />';
	
	public function __construct($name = ''){
		$this->name = $name;
		$this->nodes = array();
	}
	
	public function init_nodes($values = []) {
		$this->nodes = array();
	}
	
	public function add_input_node($type, $value='', $attributes = []) {
		switch ($type) {
			case 'integer':
				$node = new interface_node_input_integer($this->name);
				break;
			case 'float':
				$node = new interface_node_input_float($this->name);
				break;
			case 'boolean':
				$node = new interface_node_input_boolean($this->name);
				$node->set_checked($value);
				$value = 1;
				break;
			case 'radio':
				$node = new interface_node_input_radio($this->name);
				break;
			case 'button':
				$node = new interface_node_input_button($this->name);
				break;
			case 'checkbox':
				$node = new interface_node_input_checkbox($this->name);
				break;
			case 'password':
				$node = new interface_node_input_password($this->name);
				break;
			case 'hidden':
				$node = new interface_node_input_hidden($this->name);
				$this->hidden_nodes = true;
				break;
			case 'char':
				$node = new interface_node_input_char($this->name);
				break;
			case 'number':
				$node = new interface_node_input_number($this->name);
				break;
			case 'date':
			    $node = new interface_node_input_date($this->name);
			    break;
			case 'file':
			    $node = new interface_node_input_file($this->name);
			    break;
			case 'text':
			default:
				$node = new interface_node_input_text($this->name);
				break;
		}
		$node->set_value($value);
		$node->set_attributes($attributes);
		$this->nodes[] = $node;
		return $node;
	}
	
	public function add_query_node($type, $query, $selected=0, $multiple=false) {
		switch ($type) {
			case 'select':
			default:
				$node = new interface_node_select($this->name);
				break;
		}
		$node->set_query($query);
		$node->set_selected($selected);
		$node->set_multiple($multiple);
		$this->nodes[] = $node;
		return $node;
	}
		
	public function add_select_node($options, $selected=0, $multiple=false) {
		$node = new interface_node_select($this->name);
		$node->set_options($options)
				->set_selected($selected)
				->set_multiple($multiple);
		$this->nodes[] = $node;
		return $node;
	}

	public function add_html_node($content = '') {
		$node = new interface_node_html($this->name);
		$node->set_content($content);
		$this->nodes[] = $node;
		return $node;
	}

	public function add_p_node($value = '') {
		$node = new interface_node_p($this->name);
		$node->set_value($value);
		$this->nodes[] = $node;
		return $node;
	}
	
	public function add_textarea_node($value='', $cols=0, $rows=0) {
		$node = new interface_node_textarea($this->name);
		$node->set_value($value)
		->set_cols($cols)
		->set_rows($rows);
		$this->nodes[] = $node;
		return $node;
	}

	public function add_img_node($src='', $alt='') {
		$node = new interface_node_img($this->name);
		$node->set_src($src)
		->set_alt($alt);
		$this->nodes[] = $node;
		return $node;
	}
	
	public function add_datalist_node($options) {
	    $node = new interface_node_datalist($this->name);
	    $node->set_options($options);
	    $this->nodes[] = $node;
	    return $node;
	}
	
	public function add_button_node($value='') {
	    $node = new interface_node_button($this->name);
	    $node->set_value($value);
	    $this->nodes[] = $node;
	    return $node;
	}
	
	public function add_text_node($value='') {
	    $node = new interface_node_text($this->name);
	    $node->set_value($value);
	    $this->nodes[] = $node;
	    return $node;
	}
	
	protected function add_node($node=null) {
		
	}
	
	protected function has_display_attribute_for() {
	    if (empty($this->nodes) || (is_countable($this->nodes) && count($this->nodes) == 1 && get_class($this->nodes[0]) == 'interface_node_text')) {
	        return false;
	    }
	    return true;
	}
	
	public function get_display_nodes() {
		$display = '';
		
		if(!empty($this->nodes)) {
			foreach ($this->nodes as $indice=>$node) {
				if($indice) {
					switch ($this->display_type) {
						case 'flat':
							break;
						default: 
							$display .= $this->display_nodes_separator;
					}
				}
				$display.= $node->get_display();
			}
		}
		return $display;
	}
	
	public function get_display() {
		$display = '';
		switch ($this->display_type) {
			case 'flat':
				$display .= "
				<div class='".(!empty($this->class) ? $this->class : 'row')."'>";
				if (!empty($this->label)) {
				    if ($this->has_display_attribute_for()) {
				        $display .= "<label class='etiquette' for='".$this->name."'>".$this->label."</label>";
				    } else {
				        $display .= "<label class='etiquette'>".$this->label."</label>";
				    }
				}
				$display .= $this->get_display_nodes()."
				</div>";
				break;
			default:
				if(!empty($this->class)) {
					$display .= "<div class='".$this->class."'>";
				}
				if(!empty($this->label)) {
					$display .= "
					<div class='row interface-element-display-label'>";
					if ($this->has_display_attribute_for()) {
					    $display .= "<label class='etiquette' for='".$this->name."'>".$this->label."</label>";
					} else {
					    $display .= "<label class='etiquette'>".$this->label."</label>";
					}
					$display .= "</div>";
				}
				if($this->hidden_nodes) {
					$display .= $this->get_display_nodes();
				} else {
					$display .= "
					<div class='row interface-element-display-nodes'>
						".$this->get_display_nodes()."
					</div>";
				}
				if(!empty($this->class)) {
					$display .= "</div>";
				}
				break;
		}
		return $display;
	}
	
	public function get_display_flat($column='', $align='left') {
	    $display = '';
	    if(!empty($this->class)) {
	        $display .= "<div class='".$this->class."'>";
	    }
		$display .= "
		<div class='row interface-element-display-flat'>
			<div class='colonne".$column." ".($align == 'right' ? 'align_right' : '')."'>
				<label class='etiquette' for='".$this->name."'>".$this->label."</label>
			</div>
			<div class='colonne_suite'>
				".$this->get_display_nodes()."
			</div>
		</div>";
		if(!empty($this->class)) {
		    $display .= "</div>";
		}
		return $display;
	}
	
	public function get_display_tr($column='', $align='left') {
	    $display = "
		<tr class='row interface-element-display-tr'>
			<td ".($align == 'right' ? "class='align_right'" : '')."'>
				<label class='etiquette' for='".$this->name."'>".$this->label."</label>
			</td>
			<td>
				".$this->get_display_nodes()."
			</td>
		</tr>";
	    return $display;
	}
	
	public function set_uid($uid) {
	    $this->uid = $uid;
	    return $this;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_label() {
		return $this->label;
	}
	
	public function set_label($label) {
		$this->label = $label;
		return $this;
	}
	
	public function set_label_code($label_code) {
		global $msg;
		
		$this->label_code = $label_code;
		$this->label = $msg[$label_code] ?? '';
		return $this;
	}
	
	public function set_class($class) {
		$this->class = $class;
		return $this;
	}
	
	public function add_class($class) {
	    if (!isset($this->class)) {
	        $this->class = '';
	    }
	    if (!empty($this->class)) {
	        $this->class .= ' ';
	    }
	    $this->class .= $class;
	    return $this;
	}
	
	public function set_display_type($display_type) {
		$this->display_type = $display_type;
		return $this;
	}
	
	public function set_display_nodes_separator($display_nodes_separator) {
	    $this->display_nodes_separator = $display_nodes_separator;
	    return $this;
	}
	
	public static function get_instance($uid, $name, $label_code='', $display_type='row') {
	    $classname = static::class;
	    $interface_element = new $classname($name);
	    $interface_element->set_uid($uid)
	    ->set_label_code($label_code)
	    ->set_display_type($display_type);
	    return $interface_element;
	}
}