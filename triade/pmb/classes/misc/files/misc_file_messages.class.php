<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: misc_file_messages.class.php,v 1.3 2020/08/17 11:57:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/misc/files/misc_file.class.php");
require_once($class_path."/XMLlist.class.php");

class misc_file_messages extends misc_file {
	
	protected $type;
	
	protected $messages;
	
	public function __construct($path, $filename) {
		parent::__construct($path, $filename);
	}
	
	protected function analyze() {
		if(file_exists($this->path.'/'.$this->filename)) {
			XMLlist::$ignore_subst_file = true;
			$messages = new XMLlist($this->path.'/'.$this->filename);
			$messages->analyser();
			$this->messages = array();
			if(is_array($messages->table)){
				$this->messages['source'] = $messages->table;
			}
			XMLlist::$ignore_subst_file = false;
		}
	}
	
	protected function get_display_header_list() {
		global $msg, $charset;
		$display = "
		<tr>
			<th>".htmlentities($msg['misc_file_code'], ENT_QUOTES, $charset)."</th>
			<th>".htmlentities($msg['misc_file_label'], ENT_QUOTES, $charset)."</th>
			<th></th>
		</tr>";
		return $display;
	}
	
	protected function get_substitution_label($code) {
		if(isset($this->messages['substitution'][$code])) {
			return $this->messages['substitution'][$code];
		}
		return '';
	}
	
	protected function get_display_content_list() {
		$display = "";
		foreach ($this->messages['source'] as $code=> $message) {
			$display .= "
			<tr>
				<td>
					".$code."
					".$this->get_informations_hidden($code)."
				</td>
				<td>".$message."</td>
				<td>".$this->get_substituted_icon($code)."</td>
			</tr>";
		}
		return $display;
	}
	
	public function get_display_list() {
		$display = "<table id='misc_file_messages_list'>";
		$display .= $this->get_display_header_list();
		if(count($this->messages)) {
			$display .= $this->get_display_content_list();
		}
		$display .= "</table>";
		
		return $display;
	}
	
	public function get_default_template() {
		return '<?xml version="1.0" encoding="iso-8859-1"?>
<!DOCTYPE XMLlist SYSTEM "../XMLlist.dtd" [<!ENTITY nbsp "&amp;nbsp;">]>

<!-- messages localisés
****************************************************************************************
'.$this->get_sign_template().'
****************************************************************************************
'.$this->get_versionning_template().' $ -->

<XMLlist>

</XMLlist>';
	}
}
	
