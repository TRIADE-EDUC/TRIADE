<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_element_display_colors.class.php,v 1.3 2024/03/08 08:33:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_element_display_colors extends interface_element {
	
	public function init_nodes($values = []) {
		for ($i=1;$i<=20; $i++) {
			$checked = (isset($values[0]) && $values[0] == 'statutnot'.$i ? true : false);
			$this->add_input_node('radio', 'statutnot'.$i)
			->set_checked($checked);
		}
	}
	
	public function get_display_nodes() {
		$display = '';
		
		if(!empty($this->nodes)) {
			foreach ($this->nodes as $indice=>$node) {
				$i = $indice+1;
				$display .= "
					<span for='statutnot".$i."' class='statutnot".$i."' style='margin: 7px;'>
						<img src='".get_url_icon('spacer.gif')."' style='width:10px; height:10px' alt='' />
						".$node->get_display()."
					</span>";
				if ($i == 10) $display .= "<br />";
				elseif ($i != 20) $display .= "<b>|</b>";
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
}