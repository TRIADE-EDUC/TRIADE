<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_opac_opac_view_quota_ui.class.php,v 1.2 2023/03/24 07:44:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_opac_opac_view_quota_ui extends list_configuration_opac_opac_view_ui {
	
	protected static $quota_prefix;
	
	protected static $quota_allowed;
	
	protected static $quota_default_selected;
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		if(empty($this->objects_type)) {
			$this->objects_type = str_replace('list_', '', get_class($this)).'_'.static::$quota_prefix;
		}
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('pager', 'visible', false);
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'allowed', 'name', 'default_selected'
		);
	}

	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'allowed' => 'opac_view_allowed',
				'name' => 'opac_view_list_name',
				'default_selected' => 'opac_view_default',
		);
	}
	
	protected function get_js_sort_script_sort() {
		return "";
	}
	
	protected function _get_cell_header($name, $label = '') {
		global $msg, $charset;
		
		switch ($name) {
			case 'allowed':
				return "<th class='center' style='vertical-align:middle'>
							".$this->_get_label_cell_header($label)."
							&nbsp;
							<i class='fa fa-plus-square' id='".$this->objects_type."_list_cell_header_square_plus' onclick='".$this->objects_type."_allowed_selection_all(this);' style='cursor:pointer;' title='".htmlentities($msg['tout_cocher_checkbox'], ENT_QUOTES, $charset)."'></i>
							&nbsp;
							<i class='fa fa-minus-square' id='".$this->objects_type."_list_cell_header_square_minus' onclick='".$this->objects_type."_allowed_unselection_all(this);' style='cursor:pointer;' title='".htmlentities($msg['tout_decocher_checkbox'], ENT_QUOTES, $charset)."'></i>
						</th>";
			default:
				return parent::_get_cell_header($name, $label);
		}
	}
	
	protected function get_cell_content($object, $property) {
		global $charset;
		
		$content = '';
		switch($property) {
			case 'allowed':
				$content .= "<input type='checkbox' ".(in_array($object->id,static::$quota_allowed) ? "checked='checked' " : "")."name='".static::$quota_prefix."[allowed][]' value='".htmlentities($object->id,ENT_QUOTES,$charset)."' class='".$this->objects_type."_allowed_selection'/>";
				break;
			case 'default_selected':
				$content .= "<input type='radio' ".($object->id == static::$quota_default_selected ? "checked='checked' " : "")."name='".static::$quota_prefix."[default]' value='".htmlentities($object->id,ENT_QUOTES,$charset)."'/>";
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	public function get_display_search_form() {
		return "";
	}
	
	public function get_display_content_list() {
		global $msg;
		
		$display = "<tr>
				<td>
					<input type='checkbox' ".(in_array(0,static::$quota_allowed) ? "checked='checked' " : "")."name='".static::$quota_prefix."[allowed][]' value='0' class='".$this->objects_type."_allowed_selection'/>
				</td>
				<td>".$msg['opac_view_classic_opac']."</td>
				<td>
					<input type='radio' ".(0 == static::$quota_default_selected ? "checked='checked' " : "")."name='".static::$quota_prefix."[default]' value='0'/>
				</td>
			</tr>";
		$display .= parent::get_display_content_list();
		return $display;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		return array();
	}
	
	protected function get_display_left_actions() {
		return '';
	}
	
	protected function get_display_others_actions() {
		$display = "
		<script type='text/javascript'>
			function ".$this->objects_type."_allowed_selection_all(domNode) {
				var selection_in_group = domNode.closest('table');
				if(selection_in_group && selection_in_group.id) {
					dojo.query('#'+selection_in_group.id+' .".$this->objects_type."_allowed_selection').forEach(function(node) {
						node.setAttribute('checked', 'checked');
					});
				} else {
					dojo.query('.".$this->objects_type."_allowed_selection').forEach(function(node) {
						node.setAttribute('checked', 'checked');
					});
				}
			}
			function ".$this->objects_type."_allowed_unselection_all(domNode) {
				var selection_in_group = domNode.closest('table');
				if(selection_in_group && selection_in_group.id) {
					dojo.query('#'+selection_in_group.id+' .".$this->objects_type."_allowed_selection').forEach(function(node) {
						node.removeAttribute('checked');
					});
				} else {
					dojo.query('.".$this->objects_type."_allowed_selection').forEach(function(node) {
						node.removeAttribute('checked');
					});
				}
			}
		</script>";
		return $display;
	}
	
	public static function set_quota_prefix($quota_prefix) {
		static::$quota_prefix = $quota_prefix;
	}
	
	public static function set_quota_allowed($quota_allowed) {
		static::$quota_allowed = $quota_allowed;
	}
	
	public static function set_quota_default_selected($quota_default_selected) {
		static::$quota_default_selected = intval($quota_default_selected);
	}
}