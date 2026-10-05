<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: misc_file_list.class.php,v 1.3 2018/11/26 09:20:57 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/misc/files/misc_file.class.php");
require_once($class_path."/marc_table.class.php");

class misc_file_list extends misc_file {
	
	protected $type;
	
	protected $options;
	
	public function __construct($path, $filename) {
		$this->set_type($filename);
		parent::__construct($path, $filename);
	}
	
	protected function analyze() {
		XMLlist::$ignore_subst_file = true;
		$this->options=new marc_list($this->type);
// 		$tmp=array();
// 		$tmp = $this->options->table;
// 		$tmp=array_map("convert_diacrit",$tmp);//On enlève les accents
// 		$tmp=array_map("strtoupper",$tmp);//On met en majuscule
// 		asort($tmp);//Tri sur les valeurs en majuscule sans accent
// 		foreach ( $tmp as $key => $value ) {
// 			$tmp[$key]=$this->options->table[$key];//On reprend les bons couples clé / libellé
// 		}
// 		$this->options->table=$tmp;
		reset($this->options->table);
		XMLlist::$ignore_subst_file = false;
		$this->options->table = $this->apply_sort($this->options->table);
	}
	
	protected function get_display_header_list() {
		global $msg, $charset;
		$display = "
		<tr>
			<th>".htmlentities($msg['misc_file_code'], ENT_QUOTES, $charset)."</th>
			<th>".htmlentities($msg['misc_file_label'], ENT_QUOTES, $charset)."</th>
			<th>".htmlentities($msg['misc_file_visible'], ENT_QUOTES, $charset)."</th>
			<th></th>
		</tr>";
		return $display;
	}
	
	protected function get_display_content_list() {
		$display = "";
		foreach ($this->options->table as $key=> $value) {
			$display .= "
			<tr class='center' data-file-element='".$key."'>
				<td>
					".$key."
					".$this->get_informations_hidden($key)."			
				</td>
				<td>".$value."</td>
				<td>".$this->get_visible_checkbox($key)."</td>
				<td>".$this->get_substituted_icon($key)."</td>
			</tr>";
		}
		return $display;
	}
	
	public function get_display_list() {
		$display = "<table id='misc_file_list'>";
		$display .= $this->get_display_header_list();
		if(count($this->options->table)) {
			$display .= $this->get_display_content_list();
		}
		$display .= "</table>";
		
		return $display;
	}
	
	public function set_type($type) {
		$type = str_replace(array('.xml', '_subst.xml'), '', $type);
		$this->type = $type;
	}
	
	public function get_default_template() {
		return '<?xml version="1.0" encoding="iso-8859-1"?>

<!DOCTYPE XMLlist SYSTEM "../../XMLlist.dtd">

<!-- '.$this->get_versionning_template().' $ -->

<XMLlist>

</XMLlist>';
	}
	
	protected function field_exists($field_id, $substitution_fields) {
		foreach ($substitution_fields as $code=>$value) {
			if($code == $field_id) {
				return $code;
			}
		}
		return false;
	}
	
	protected function apply_sort($substitution_fields) {
		if(!count($this->data)) {
			return $substitution_fields;
		}
		$sorted_substitution = array();
		foreach ($this->data as $field_id=>$field) {
			$field_exists = $this->field_exists($field_id, $substitution_fields);
			if($field_exists) {
				$sorted_substitution[$field_id] = $substitution_fields[$field_exists];
				unset($substitution_fields[$field_exists]);
			}
		}
		$sorted_substitution = array_merge($sorted_substitution, $substitution_fields);
		return $sorted_substitution;
	}
	
	public function apply_substitution($fields) {
		if(count($this->data)) {
			$substitution = array();
			foreach ($fields as $code=>$value) {
				if(!isset($this->data[$code]['visible']) || $this->data[$code]['visible']) {
					$substitution[$code] = $value;
				}
			}
			//Ordonnancement
			$substitution = $this->apply_sort($substitution);
		} else {
			$substitution = $fields;
		}
		return $substitution;
	}
}
	
