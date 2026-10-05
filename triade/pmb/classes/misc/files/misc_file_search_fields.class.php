<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: misc_file_search_fields.class.php,v 1.16 2023/12/19 08:03:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/misc/files/misc_file.class.php");
require_once($class_path."/search.class.php");

class misc_file_search_fields extends misc_file {
	
	protected $search;
	
	protected function analyze() {
// 		search::$ignore_subst_file = true;
		$filename = str_replace(array('.xml', '_subst.xml'), '', $this->filename);
		$this->search = new search(false, $filename, $this->path.'/');
		search::$ignore_subst_file = false;
	}
	
	protected function get_display_header_list() {
		global $msg, $charset;
		$display = "
		<tr>
			<!--<th></th>-->
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
			<!--<td>
				<img onclick=\"expandBase('misc_file_search_fields_operators_list_".$key."', true); return false;\" class='img_plus' src='".get_url_icon('plus.gif')."' name='imEx' id='misc_file_search_fields_operators_list_".$key."Img' />
			</td>-->
			<td>
				".$key."
				".$this->get_informations_hidden($key, $group)."
			</td>
			<td align='left'>".htmlentities($label,ENT_QUOTES,$charset)."</td>
			<td>".$this->get_visible_checkbox($key)."</td>
		</tr>";
	}
	
	protected function get_display_content_list() {
		$display = "";
		$list_criteria = $this->search->get_list_criteria();
		$list_criteria = $this->apply_sort($list_criteria);
		foreach ($list_criteria as $group=>$criteria) {
			$display .= $this->get_display_separator($group);
			foreach ($criteria as $field) {
				$display .= $this->get_display_element_content($group, $field['id'], $field['label']);
//				$display .= "<tr class='center'><td colspan='4'>";
//				$display .= $this->get_display_operators_list($field['id']);
//				$display .= "</td></tr>";
			}
		}
		return $display;
	}
	
	public function get_display_list() {
		$display = "<table id='misc_file_search_fields_list'>";
		$display .= $this->get_display_header_list();
		if($this->search->fixedfields){
			$display .= $this->get_display_content_list();
		}
		$display .= "</table>";
		return $display;
	}
	
	protected function get_informations_operator_hidden($field_id, $for) {
		$informations_hidden = "<input type='hidden' name='subst_file_data[".$field_id."][operators][".$for."]' id='subst_file_data_".$field_id."_operator_".$for."' value='".$for."' />";
		return $informations_hidden;
	}
	
	protected function get_visible_operator_checkbox($field_id, $for) {
		return "<input type='checkbox' name='subst_file_data[".$field_id."][operators][".$for."][visible]' id='subst_file_data_".$field_id."_operator_".$for."_visible' ".(!isset($this->data[$field_id]['operators'][$for]['visible']) || $this->data[$field_id]['operators'][$for]['visible'] ? "checked='checked'" : "")." />";
	}
	
	protected function get_display_operators_content_object_list($field_id, $for, $label='') {
		global $charset;
		
		if(!$label) {
			$label = $this->search->operators[$for];
		}
		return "
		<tr class='center' data-file-element='".$field_id."' data-file-operator='".$for."' >
			<td>
				".$for."
				".$this->get_informations_operator_hidden($field_id, $for)."
			</td>
			<td align='left'>".htmlentities($label,ENT_QUOTES,$charset)."</td>
			<td>".$this->get_visible_operator_checkbox($field_id, $for)."</td>
		</tr>";
	}
	
	protected function get_display_operators_content_list($field_id) {
		global $include_path;
		
		$display = "";
		$s=explode("_",$field_id);
		if ($s[0]=="f") {
			//Champs fixes
			for ($j=0; $j<count($this->search->fixedfields[$s[1]]["QUERIES"]); $j++) {
				$q=$this->search->fixedfields[$s[1]]["QUERIES"][$j];
				$display .= $this->get_display_operators_content_object_list($field_id, $q["OPERATOR"]);
			}
		} elseif (array_key_exists($s[0],$this->search->pp)) {
			$datatype=$this->search->pp[$s[0]]->t_fields[$s[1]]["DATATYPE"];
			$type=$this->search->pp[$s[0]]->t_fields[$s[1]]["TYPE"];
			$df=$this->search->get_id_from_datatype($datatype, $s[0]);
			for ($j=0; $j<count($this->search->dynamicfields[$s[0]]["FIELD"][$df]["QUERIES"]); $j++) {
				$q=$this->search->dynamicfields[$s[0]]["FIELD"][$df]["QUERIES"][$j];
				$as=array_search($type,$q["NOT_ALLOWED_FOR"]);
				if (!(($as!==null)&&($as!==false))) {
					$display .= $this->get_display_operators_content_object_list($field_id, $q["OPERATOR"]);
				}
			}
		} elseif ($s[0]=="authperso") {
			$df=10;
			for ($j=0; $j<count($this->search->dynamicfields["a"]["FIELD"][$df]["QUERIES"]); $j++) {
				$q=$this->search->dynamicfields["a"]["FIELD"][$df]["QUERIES"][$j];
				$as=array_search($type,$q["NOT_ALLOWED_FOR"]);
				if (!(($as!==null)&&($as!==false))) {
					$display .= $this->get_display_operators_content_object_list($field_id,$q["OPERATOR"]);
				}
			}
		} elseif ($s[0]=="s") {
			$type=$this->search->specialfields[$s[1]]["TYPE"];
			for ($is=0; $is<count($this->search->tableau_speciaux["TYPE"]); $is++) {
				if ($this->search->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
					$sf=$this->search->specialfields[$s[1]];
					require_once($include_path."/search_queries/specials/".$this->search->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
					$specialclass= new $this->search->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],0,$sf,$this);
					$q=$specialclass->get_op();
					if (count($q)) {
						foreach ($q as $key => $value) {
							$display .= $this->get_display_operators_content_object_list($field_id, $key, $value);
						}
					}
				}
			}
		}
		return $display;
	}
	
	protected function get_display_operators_header_list() {
		global $msg, $charset;
		$display = "
		<tr>
			<th colspan='3'>".htmlentities($msg['misc_file_operators'], ENT_QUOTES, $charset)."</th>
		</tr>
		<tr>
			<th>".htmlentities($msg['misc_file_code'], ENT_QUOTES, $charset)."</th>
			<th>".htmlentities($msg['misc_file_label'], ENT_QUOTES, $charset)."</th>
			<th>".htmlentities($msg['misc_file_visible'], ENT_QUOTES, $charset)."</th>
		</tr>";
		return $display;
	}
	
	public function get_display_operators_list($field_id) {
		$display = "<table id='misc_file_search_fields_operators_list_".$field_id."Child' class='misc_file_search_fields_operators_list' width='100%' style='margin-bottom:6px;display:none;'>";
		$display .= $this->get_display_operators_header_list();
		$display .= $this->get_display_operators_content_list($field_id);
		$display .= "</table>";
		return $display;
	}
	
	public function set_properties_from_form() {
		global $subst_file_data;
		
		parent::set_properties_from_form();
		if(is_array($subst_file_data) && count($subst_file_data)) {
			foreach ($subst_file_data as $code=>$element) {
				$operators = array();
				if(isset($element['operators'])) {
					foreach ($element['operators'] as $name=>$operator) {
						$operators[$name]['visible'] = (isset($operator['visible']) && $operator['visible'] ? 1 : 0);
					}
				}
				$this->data[$code]['operators'] = $operators;
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
		if (empty($this->data)) {
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
		if (!empty($this->data)) {
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
	
	public function apply_operators_substitution($field_id, $queries) {
		if (!empty($this->data)) {
			$substitution = array();
			foreach ($queries as $query) {
				if(!isset($this->data[$field_id]['operators'][$query['OPERATOR']]['visible']) || $this->data[$field_id]['operators'][$query['OPERATOR']]['visible']) {
					$substitution[] = $query;
				}
			}
			//Ordonnancement
// 			$substitution = $this->apply_operators_sort($substitution);
		} else {
			$substitution = $queries;
		}
		return $substitution;
	}
}
	
