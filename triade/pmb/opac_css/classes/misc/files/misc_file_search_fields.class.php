<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: misc_file_search_fields.class.php,v 1.6 2018-12-07 15:24:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/misc/files/misc_file.class.php");
require_once($class_path."/search.class.php");

class misc_file_search_fields extends misc_file {
	
	protected $search;
	
	protected function analyze() {
		search::$ignore_subst_file = true;
		$filename = str_replace(array('.xml', '_subst.xml'), '', $this->filename);
		$this->search = new search(false, $filename, $this->path.'/');
		search::$ignore_subst_file = false;
	}
	
	protected function get_display_header_list() {
		global $msg, $charset;
		$display = "
		<tr>
			<th>".htmlentities($msg['misc_file_code'], ENT_QUOTES, $charset)."</th>
			<th>".htmlentities($msg['misc_file_label'], ENT_QUOTES, $charset)."</th>
			<th>".htmlentities($msg['misc_file_visible'], ENT_QUOTES, $charset)."</th>
		</tr>";
		return $display;
	}
	
	protected function get_display_separator($label) {
		global $charset;
	
		return "
		<tr class='center misc_file_search_fields_group_label'>
			<td colspan='3'><label>".htmlentities($label,ENT_QUOTES,$charset)."</label></td>
		</tr>";
	}
	
	protected function get_display_element_content($group, $key, $label ) {
		global $charset;
		
		return "
		<tr class='center' data-file-group='".$group."' data-file-element='".$key."'>
			<td>
				".$key."
				".$this->get_informations_hidden($key, $group)."
			</td>
			<td align='left'>".htmlentities($label,ENT_QUOTES,$charset)."</td>
			<td>".$this->get_visible_checkbox($key)."</td>
		</tr>";
	}
	
	protected function get_display_content_list() {
		global $msg, $charset;
		global $include_path;
		
		$display = "";
		$list_criteria = $this->search->get_list_criteria();
		foreach ($list_criteria as $group=>$criteria) {
			$display .= $this->get_display_separator($group);
			foreach ($criteria as $field) {
				$display .= $this->get_display_element_content($group, $field['id'], $field['label']);
			}
		}
		return $display;
	}
	
	public function get_display_list() {
		$display = "<table id='file_search_fields_list'>";
		$display .= $this->get_display_header_list();
		if($this->search->fixedfields){
			$display .= $this->get_display_content_list();
		}
		$display .= "</table>";
		return $display;
	}
	
	public function get_default_template() {
		$is_subst = strpos($this->filename, '_subst.xml');
		if(file_exists($this->path.'/'.$this->filename)) {
			$contents = file_get_contents($this->path.'/'.$this->filename);
			return utf8_encode($contents);
		} elseif($is_subst) {
			$contents = file_get_contents($this->path.'/'.str_replace('_subst.xml', '.xml', $this->filename));
			return utf8_encode($contents);
		}
	}
	
	protected function field_exists($field_id, $substitution_fields) {
		foreach ($substitution_fields as $group_name=>$fields_group) {
			foreach ($fields_group as $key=>$field) {
				if($field['id'] == $field_id) {
					return array('group' => $group_name, 'key' => $key);
				}
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
				$sorted_substitution[$field['group']][$field_id] = $substitution_fields[$field_exists['group']][$field_exists['key']];
				unset($substitution_fields[$field_exists['group']][$field_exists['key']]);
			}
		}
		foreach ($substitution_fields as $group_name=>$group) {
			if(!count($substitution_fields[$group_name])) {
				unset($substitution_fields[$group_name]);
			}
		}
		$sorted_substitution = array_merge_recursive($sorted_substitution, $substitution_fields);
		return $sorted_substitution;
	}
	
	public function apply_substitution($fields) {
		
		if(count($this->data)) {
			$substitution = array();
			foreach ($fields as $group_name=>$fields_group) {
				foreach ($fields_group as $field) {
					if(!isset($this->data[$field['id']]['visible']) || $this->data[$field['id']]['visible']) {
						$substitution[$group_name][$field['id']] = $field;
					}
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
	
