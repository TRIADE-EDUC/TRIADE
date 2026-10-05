<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_tpl_notice_ui.class.php,v 1.4 2023/03/24 07:44:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_tpl_notice_ui extends list_configuration_tpl_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM notice_tpl';
	}
	
	protected function get_object_instance($row) {
		return new notice_tpl($row->notpl_id);
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'id' => 'notice_tpl_id',
				'name' => 'notice_tpl_name',
				'comment' => 'notice_tpl_description',
				'show_opac' => 'notice_tpl_show_opac'
		);
	}
	
	protected function init_default_columns() {
		parent::init_default_columns();
		$this->add_column_action();
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'action',
		);
	}
	
	protected function add_column_action() {
		global $msg;
		$this->columns[] = array(
				'property' => 'action',
				'label' => $msg['admin_authperso_action'],
				'html' => '<input class="bouton" value="'.$msg["notice_tpl_evaluer"].'" onclick=\'document.location="'.static::get_controller_url_base().'&action=eval&id=!!id!!"\' type="button" />
						<input class="bouton" value="'.$msg["edit_tpl_export_button"].'" onclick=\'document.location="./export.php?quoi=notice_tpl&id=!!id!!"\' type="button" />',
				'exportable' => false
		);
	}
	
	protected function get_display_content_object_list($object, $indice) {
		$this->is_editable_object_list = false;
		return parent::get_display_content_object_list($object, $indice);
	}
	
	protected function _get_object_property_show_opac($object) {
		global $msg;
		
		if($object->show_opac) {
			return $msg["notice_tpl_show_opac_yes"];
		} else {
			return $msg["notice_tpl_show_opac_no"];
		}
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		return array(
				'onclick' => "document.location=\"".$this->get_edition_link($object)."\""
		);
	}
	
	protected function get_display_others_actions() {
		global $base_path, $msg;
		return "
			<div id='list_ui_others_actions' class='list_ui_others_actions ".$this->objects_type."_others_actions'>
				<span class='right list_ui_other_action_tpl_link ".$this->objects_type."_other_action_tpl_link'>
					<a href='".$base_path."/includes/interpreter/doc?group=notice_tpl' target='_blank'>".$msg['interpreter_doc_notice_tpl_link']."</a>
				</span>
			</div>";
	}
	
	protected function get_button_add() {
		global $msg;
		
		$buttons = parent::get_button_add();
		return $buttons.$this->get_button('import', $msg["edit_tpl_import_button"]);
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['notice_tpl_ajouter'];
	}
}