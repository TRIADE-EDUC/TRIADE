<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: misc_file_catalog.class.php,v 1.10 2022/06/28 06:14:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/misc/files/misc_file.class.php");
require_once($class_path."/marc_table.class.php");

class misc_file_catalog extends misc_file {
	
	protected $type;
	
	protected static $xml_catalog;
	
	public function __construct($path, $filename) {
		parent::__construct($path, $filename);
		$this->set_type($filename);
	}
	
	protected function analyze() {
		if(file_exists($this->path.'/'.$this->filename)) {
			$xml = file_get_contents($this->path.'/'.$this->filename);
			static::$xml_catalog = _parser_text_no_function_($xml,"CATALOG");
			if(isset(static::$xml_catalog['ACTION'])) {
				static::$xml_catalog['ACTION'] = $this->apply_sort(static::$xml_catalog['ACTION']);
			}elseif(isset(static::$xml_catalog['ITEM'])) {
				static::$xml_catalog['ITEM'] = $this->apply_sort(static::$xml_catalog['ITEM']);
			}
		}
	}
	
	protected function get_display_header_list() {
		global $msg, $charset;
		$display = "
		<tr>
			<th>".htmlentities($msg['misc_file_code'], ENT_QUOTES, $charset)."</th>
			<th>".htmlentities($msg['misc_file_label'], ENT_QUOTES, $charset)."</th>
			<th>".htmlentities($msg['misc_file_comment'], ENT_QUOTES, $charset)."</th>
			<th>".htmlentities($msg['misc_file_visible'], ENT_QUOTES, $charset)."</th>
			<!--<th></th>-->
		</tr>";
		return $display;
	}
	
	protected function get_display_content_list() {
		$display = "";
		foreach (static::$xml_catalog as $elements) {
			foreach ($elements as $element) {
				$display .= "
				<tr class='center' data-file-element='".$element['ID']."'>
					<td>
						".$element['ID']."
						".$this->get_informations_hidden($element['ID'])."
					</td>
					<td>".$element['NAME']."</td>
					<td>".(isset($element['COMMENT']) ? get_msg_to_display($element['COMMENT']) : '')."</td>
					<td>".$this->get_visible_checkbox($element['ID'])."</td>
					<!--<td>".$this->get_substituted_icon($element['ID'])."</td>-->
				</tr>";
			}
		}
		return $display;
	}
	
	public function get_display_list() {
		$display = "<table id='misc_file_catalog_list'>";
		$display .= $this->get_display_header_list();
		if(count(static::$xml_catalog)) {
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

<!-- catalogue
****************************************************************************************
'.$this->get_sign_template().'
****************************************************************************************
'.$this->get_versionning_template().' $ -->

<catalog>
	
</catalog>';
	}
	
	protected function field_exists($field_id, $substitution_fields) {
		foreach ($substitution_fields as $key=>$field) {
			if($field['ID'] == $field_id) {
				return $key;
			}
		}
		return false;
	}
	
	protected function apply_sort($substitution_fields) {
		if (empty($this->data)) {
			return $substitution_fields;
		}
		$sorted_substitution = array();
		foreach ($this->data as $field_id=>$field) {
			$field_exists = $this->field_exists($field_id, $substitution_fields);
			if($field_exists !== false) {
				$sorted_substitution[] = $substitution_fields[$field_exists];
				unset($substitution_fields[$field_exists]);
			}
		}
		$sorted_substitution = array_merge($sorted_substitution, $substitution_fields);
		return $sorted_substitution;
	}
	
	public function apply_substitution($fields) {
		if (!empty($this->data)) {
			$substitution = array();
			foreach ($fields as $field) {
				if(!isset($this->data[$field['ID']]['visible']) || $this->data[$field['ID']]['visible']) {
					$substitution[] = $field;
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
	
