<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: misc_file_indexation.class.php,v 1.5.2.1 2024/09/06 10:40:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/misc/files/misc_file.class.php");

class misc_file_indexation extends misc_file {
	
	protected $type;
	
	protected static $xml_indexation;
	
	public function __construct($path, $filename) {
		parent::__construct($path, $filename);
		$this->set_type($path);
	}
	
	protected function analyze() {
		if(file_exists($this->path.'/'.$this->filename)) {
			$xml = file_get_contents($this->path.'/'.$this->filename);
			static::$xml_indexation = _parser_text_no_function_($xml,"INDEXATION");
			static::$xml_indexation['FIELD'] = $this->apply_sort(static::$xml_indexation['FIELD']);
		}
	}
	
	protected function get_display_header_list() {
		global $msg, $charset;
		$display = "
		<tr>
			<th>".htmlentities($msg['misc_file_code'], ENT_QUOTES, $charset)."</th>
			<th>".htmlentities($msg['misc_file_label'], ENT_QUOTES, $charset)."</th>
			<th>".htmlentities($msg['misc_file_visible'], ENT_QUOTES, $charset)."</th>
			<th>".htmlentities($msg['misc_file_pond'], ENT_QUOTES, $charset)."</th>
		</tr>";
		return $display;
	}
	
	protected function get_display_pond($code, $label, $pond='') {
// 		global $msg, $charset;
		
		$display = "<span id='subst_file_data_".$code."_pond_span'>";
		if(isset($this->data[$code]['pond']) && $this->data[$code]['pond'] != $pond) {
			$display .= "<strong>".$this->data[$code]['pond']."</strong>";
// 			$display .= "<input type='hidden' name='subst_file_data[".$code."][pond]' id='subst_file_data_".$code."_pond' value='".intval($this->data[$code]['pond'])."' />";
// 			$display .= " <img data-file-code='".$code."' data-file-label='".htmlentities($label, ENT_QUOTES, $charset)."' data-file-pond='".intval($this->data[$code]['pond'])."' data-file-action='edit_pond' ".(!empty($this->type) ? "data-file-type='".$this->type."'" : "")." src='".get_url_icon('b_edit.png')."' alt='".$msg['62']."' title='".$msg['62']."' style='cursor:pointer;'/>";
			
		} elseif($pond !== '') {
			$display .= $pond;
// 			$display .= " <img data-file-code='".$code."' data-file-label='".htmlentities($label, ENT_QUOTES, $charset)."' data-file-pond='".intval($pond)."' data-file-action='edit_pond' ".(!empty($this->type) ? "data-file-type='".$this->type."'" : "")." src='".get_url_icon('b_edit.png')."' alt='".$msg['62']."' title='".$msg['62']."' style='cursor:pointer;'/>";
		}
		
		$display .= "</span>";
		return $display;

		return '';
	}
	
	protected function get_display_content_element_list($code_champ, $code_ss_champ, $name, $pond, $has_group=false) {
	    global $msg, $charset;
	    
	    $display_code = $code_champ.($code_ss_champ ? " - ".$code_ss_champ : '');
	    $code = $code_champ.($code_ss_champ ? "_".$code_ss_champ : '');
	    return "
        <tr class='center' data-file-group='".$code_champ."' data-file-element='".$code."' ".($has_group ? "style='font-weight:bold'" : "").">
			<td>
				".$display_code."
				".$this->get_informations_hidden($code)."
			</td>
			<td>".(isset($msg[$name]) ? htmlentities($msg[$name], ENT_QUOTES, $charset) : '')."</td>
			<td>".$this->get_visible_checkbox($code)."</td>
			<td>".$this->get_display_pond($code, $msg[$name] ?? '', $pond ?? '')."</td>
		</tr>";
	}
	
	protected function get_display_content_list() {
		$display = "";
		foreach (static::$xml_indexation['FIELD'] as $field) {
			if (!empty($field['TABLE'][0]['TABLEFIELD']) && count($field['TABLE'][0]['TABLEFIELD']) > 1) {
			    $display .= $this->get_display_content_element_list($field['ID'], 0, $field['NAME'], $field['POND'] ?? '', true);	    
			    foreach ($field['TABLE'][0]['TABLEFIELD'] as $tablefield) {
			        $display .= $this->get_display_content_element_list($field['ID'], $tablefield['ID'], $tablefield['NAME'], $tablefield['POND'] ?? '');
			    }
			} else {
			    $display .= $this->get_display_content_element_list($field['ID'], 0, $field['NAME'], $field['POND'] ?? '');
			}
		}
		return $display;
	}
	
	public function get_display_list() {
		$display = "<table id='misc_file_indexation_list'>";
		$display .= $this->get_display_header_list();
		if(count(static::$xml_indexation['FIELD'])) {
			$display .= $this->get_display_content_list();
		}
		$display .= "</table>";
		
		return $display;
	}
	
	public function set_type($type) {
		$this->type = substr($type, strrpos($type, '/')+1);
	}
	
	public function set_properties_from_form() {
		global $subst_file_data;
		
		parent::set_properties_from_form();
		if(is_array($subst_file_data) && count($subst_file_data)) {
			foreach ($subst_file_data as $code=>$element) {
				if(isset($element['pond'])) {
					$this->data[$code]['pond'] = $element['pond'];
				}
			}
		}
	}
	
	public function get_default_template() {
		$is_subst = strpos($this->filename, '_subst.xml');
		if(file_exists($this->path.'/'.$this->filename)) {
			$contents = file_get_contents($this->path.'/'.$this->filename);
			return encoding_normalize::utf8_normalize($contents);
		} elseif($is_subst) {
			$contents = file_get_contents($this->path.'/'.str_replace('_subst.xml', '.xml', $this->filename));
			return encoding_normalize::utf8_normalize($contents);
		}
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
		if(!count($this->data)) {
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
	
	protected function apply_substitution_tablefield($field) {
	    if (!empty($field['TABLE']) && count($field['TABLE'])) {
	        foreach ($field['TABLE'] as $index=>$table) {
	            if (!empty($table['TABLEFIELD']) && count($table['TABLEFIELD']) > 1) {
	                //reorganisation des tablefield (code sous champ) si certains ont ete rendu invisibles
	                $field_tablefield = array();
	                foreach ($table['TABLEFIELD'] as $tablefield) {
	                    if(!isset($this->data[$field['ID']."_".$tablefield['ID']]['visible']) || $this->data[$field['ID']."_".$tablefield['ID']]['visible']) {
	                        $field_tablefield[] = $tablefield;
	                    }
	                }
	                $field['TABLE'][$index]['TABLEFIELD'] = $field_tablefield;
	            }
	        }
	    }
	    return $field;
	}
	
	public function apply_substitution($fields) {
		if(count($this->data)) {
			$substitution = array();
			foreach ($fields as $field) {
				if(!isset($this->data[$field['ID']]['visible']) || $this->data[$field['ID']]['visible']) {
					if(isset($this->data[$field['ID']]['pond'])) {
						$field['POND'] = $this->data[$field['ID']]['pond'];
					}
					$field = $this->apply_substitution_tablefield($field);
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
	
