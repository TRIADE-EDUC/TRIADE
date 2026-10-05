<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_content_form.class.php,v 1.6.4.2 2025/05/16 13:30:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/translation.class.php');

class interface_content_form {
	
	protected $name;
	
	protected $zones = [];
	
	protected $elements = [];
	
	protected $grid_model = 'default';
	
	protected $grid_elements = [];
	
	protected $separator_zones = '';
	
	protected $grid_editables = false;
	
	protected $current_element = 0;
	
	public function __construct($name = ''){
		$this->name = $name;
	}
			
	public function add_zone($name, $label_code='', $elements = []) {
		$interface_zone = new interface_zone($name);
		$interface_zone->set_label_code($label_code);
		if(!empty($elements)) {
			foreach ($elements as $element_name) {
				$interface_zone->add_element($this->elements[$element_name]);
// 				unset($this->elements[$element_name]);
			}
		}
		$this->zones[$name] = $interface_zone;
		return $interface_zone;
	}
			
	public function add_element($name, $label_code='', $display_type='row') {
		$interface_element = new interface_element($name);
		$interface_element->set_label_code($label_code)
		->set_display_type($display_type);
		$this->elements[$name] = $interface_element;
		$this->add_grid_element($name);
		return $interface_element;
	}
	
	public function add_inherited_element($inherited_name, $name, $label_code='') {
		$classname = 'interface_element_'.$inherited_name;
		$interface_element = new $classname($name);
		$interface_element->set_label_code($label_code);
		$this->elements[$name] = $interface_element;
		$this->add_grid_element($name);
		return $interface_element;
	}
	
	public function get_element($name) {
		return $this->elements[$name];
	}
	
	public function set_grid_model($grid_model) {
		$this->grid_model = $grid_model;
	}
	
	public function add_grid_element($name) {
		$this->grid_elements[$name] = array(
			'display' => 'row'	
		);
	}
	
	public function set_display_grid_element($name, $display) {
		$this->grid_elements[$name]['display'] = $display;
	}
	
	public function set_separator_zones($separator_zones) {
	    $this->separator_zones = $separator_zones;
	    return $this;
	}
	
	public function set_grid_editables($grid_editables) {
	    $this->grid_editables = $grid_editables;
	    return $this;
	}
	
	protected function get_display_column_element($element, $number) {
	    $number = intval($number);
	    $this->current_element++;
	    $display = '';
	    if ($this->current_element % $number == 1) {
	        $display .= "<div class='row'>";
	    }
	    $display .= $element->get_display();
	    if ($this->current_element % $number == 0 || $this->current_element == count($this->elements)) {
	        $display .= "</div>";
	    }
	    return $display;
	}
	
	protected function get_display_element($element) {
	    switch($this->grid_model) {
	        case 'flat_column_25':
	            return $element->get_display_flat('25');
	        case 'flat_column_3':
	            return $element->get_display_flat('3');
	        case 'flat_column_2_right':
	            return $element->get_display_flat('2', 'right');
	        case 'flat_column_25_right':
	            return $element->get_display_flat('25', 'right');
	        case 'flat_column_4_right':
	            return $element->get_display_flat('4', 'right');
	        case 'column_3':
	            return $this->get_display_column_element($element, 3);
	        default:
	            return $element->get_display();
	    }
	}
	public function get_display_elements() {
	    global $charset;
	    
		$display = '';
		foreach ($this->elements as $indice=>$element) {
		    if ($this->grid_editables) {
		        $display .= "<div id='el0Child_".$indice."' class='row' movable='yes' title=\"".htmlentities($element->get_label(), ENT_QUOTES, $charset)."\">";
		    }
		    $display .= $this->get_display_element($element);
		    if ($this->grid_editables) {
		        $display .= "</div>";
		    }
		}
		return $display;
	}
	
	public function get_display_zones() {
		$display = '';
		if (!empty($this->zones)) {
		    $indice_zone = 1;
		    $nb_zones = count($this->zones);
    		foreach ($this->zones as $zone) {
    		    $display .= $zone->get_display();
    		    if (!empty($this->separator_zones) && $indice_zone < $nb_zones) {
    			    $display .= $this->separator_zones;
    			}
    			$indice_zone++;
    		}
		}
		return $display;
	}
	
	public function get_display() {
		$display = '';
		if(!empty($this->zones)) {
			$display .= $this->get_display_zones();
		} else {
			$display .= $this->get_display_elements();
		}
		return $display;
	}
}