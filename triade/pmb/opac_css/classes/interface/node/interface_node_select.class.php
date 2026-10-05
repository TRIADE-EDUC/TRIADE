<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node_select.class.php,v 1.1.6.1 2025/02/27 10:04:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node_select extends interface_node {
	
	protected $query;
	
	protected $options = [];
	
	protected $selected = '';
	
	protected $multiple = 0;

	protected $onchange = '';
	
	protected $empty_option = [];
	
	protected $first_option = [];
	
	public function get_display() {
		global $charset;
		
		if(empty($this->options) && !empty($this->query)) {
			$result=pmb_mysql_query($this->query);
			if(pmb_mysql_num_rows($result)) {
				while($row = pmb_mysql_fetch_array($result)) {
					$this->options[$row[0]] = $row[1];
				}
			}
		}
		$display = "<select name=\"".$this->name."\" id=\"".$this->name."\" ".(!empty($this->onchange) ? "onChange=\"".$this->onchange."\"" : "")." ";
		if ($this->multiple) {
		    $display .= "multiple ";
		}
		$attr = $this->get_display_attributes();
		if ($attr) {
		    $display .= "$attr ";
		}
		$display.=">\n";
		if(count($this->options)) {
			if(!empty($this->first_option)) {
		        $display .= "<option value=\"".htmlentities($this->first_option['value'], ENT_QUOTES, $charset)."\" ";
		        if((is_array($this->selected) && in_array($this->first_option['value'], $this->selected)) || $this->selected==$this->first_option['value']) {
	                $display .= "selected=\"selected\"";
		        }
		        $display .= ">".htmlentities($this->first_option['label'], ENT_QUOTES, $charset)."</option>\n";
		    }
		    foreach ($this->options as $value => $label) {
		        $display .= "<option value=\"".$value."\" ";
		        if ((is_array($this->selected) && in_array($value, $this->selected)) || $this->selected == $value) {
		            $display.="selected=\"selected\"";
		        }
		        $display.=">".htmlentities($label,ENT_QUOTES, $charset)."</option>\n";
		        
		    }
		} else {
			if(!empty($this->empty_option)) {
				$display .= "<option value=\"".htmlentities($this->empty_option['value'], ENT_QUOTES, $charset)."\">".htmlentities($this->empty_option['label'], ENT_QUOTES, $charset)."</option>\n";
			}
		}
		$display .= "</select>\n";
		return $display;
	}
	
	public function get_query() {
		return $this->query;
	}
	
	public function get_options() {
	    return $this->options;
	}
	
	public function get_selected() {
		return $this->selected;
	}
	
	public function set_query($query) {
		$this->query = $query;
		return $this;
	}
	
	public function set_options($options) {
	    $this->options = $options;
	    return $this;
	}
	
	public function set_selected($selected) {
	    $this->selected = $selected;
		return $this;
	}
	
	public function set_multiple($multiple) {
	    $this->multiple = $multiple;
	    return $this;
	}
	
	public function set_onchange($onchange) {
	    $this->onchange = $onchange;
	    return $this;
	}
	
	public function set_empty_option($value, $label) {
		$this->empty_option = array('value' => $value, 'label' => $label);
		return $this;
	}
	
	public function set_first_option($value, $label) {
		$this->first_option = array('value' => $value, 'label' => $label);
		return $this;
	}
}