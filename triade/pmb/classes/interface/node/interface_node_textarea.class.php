<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node_textarea.class.php,v 1.4 2023/07/04 09:58:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node_textarea extends interface_node {
	
	protected $cols;
	
	protected $rows;
	
	public function get_display() {
		global $charset;
		
		$display = "
		<textarea id='".$this->id."'
				name='".$this->name."'
				class='".$this->class."'
				".(!empty($this->cols) ? "cols='".$this->cols."'" : "")."
				".(!empty($this->rows) ? "rows='".$this->rows."'" : "")."
				".$this->get_display_attributes().">".htmlentities($this->value, ENT_QUOTES, $charset)."</textarea>";
		return $display;
	}
	
	public function get_cols() {
		return $this->cols;
	}
	
	public function get_rows() {
		return $this->rows;
	}
	
	public function set_cols($cols) {
		$this->cols = $cols;
		return $this;
	}
	
	public function set_rows($rows) {
		$this->rows = $rows;
		return $this;
	}
}