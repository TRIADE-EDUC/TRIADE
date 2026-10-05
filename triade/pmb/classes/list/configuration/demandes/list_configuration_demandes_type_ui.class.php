<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_demandes_type_ui.class.php,v 1.3 2023/12/22 13:19:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once($class_path."/workflow.class.php");

class list_configuration_demandes_type_ui extends list_configuration_demandes_ui {
	
	protected $allowed_actions;
	
	protected function _get_query_base() {
		return 'SELECT * FROM demandes_type';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('libelle_type');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('libelle_type', 'text', array('italic' => true));
		foreach($this->get_allowed_actions() as $action){
			$this->set_setting_column('allowed_action_'.$action['id'], 'align', 'center');
		}
	}
	
	protected function get_main_fields_from_sub() {
		$main_fields = array();
		$main_fields['libelle_type'] = '103';
		foreach($this->get_allowed_actions() as $action){
			$main_fields['allowed_action_'.$action['id']] = $action['comment'];
		}
		return $main_fields;
	}
	
	protected function get_allowed_actions() {
		if(!isset($this->allowed_actions)) {
			$workflow = new workflow('ACTIONS');
			$this->allowed_actions = $workflow->getTypeList();
		}
		return $this->allowed_actions;
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		if(strpos($property, 'allowed_action') !== false) {
			$id = str_replace('allowed_action_', '', $property);
			$allowed_actions = unserialize($object->allowed_actions);
			if(is_array($allowed_actions) && count($allowed_actions)) {
				foreach ($allowed_actions as $action) {
					if($action['id'] == $id) {
						if($action['active']) {
							if($action['default']) {
								$content .= "<b>X</b>";
							} else {
								$content .= "X";
							}
						}
					}
				}
			}
		} else {
			$content .= parent::get_cell_content($object, $property);
		}
		return $content;
	}
	
	public function get_display_header_list() {
		global $msg;
		
		$display = "
		<tr>
			<th scope='colgroup'></th>
			<th colspan='".count($this->get_allowed_actions())."' scope='colgroup'>".$msg["demande_type_allowed_actions"]."</th>
		</tr>";
		$display .= parent::get_display_header_list();
		return $display;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->id_type;
	}
	
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg["demandes_no_type_available"], ENT_QUOTES, $charset);
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['demandes_add_type'];
	}
}