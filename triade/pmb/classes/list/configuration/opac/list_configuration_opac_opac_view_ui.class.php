<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_opac_opac_view_ui.class.php,v 1.4 2023/03/24 07:44:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/opac_view.class.php");

class list_configuration_opac_opac_view_ui extends list_configuration_opac_ui {
	
	protected function _get_query_base() {
		return "SELECT * FROM opac_views";
	}
	
	protected function get_object_instance($row) {
		return new opac_view($row->opac_view_id);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('name');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'id' => 'opac_view_list_id',
				'name' => 'opac_view_list_name',
				'comment' => 'opac_view_list_comment',
				'link' => 'opac_view_list_link'
		);
	}
	
	protected function get_cell_content($object, $property) {
		global $charset, $pmb_opac_url;
		
		$content = '';
		switch($property) {
			case 'link':
				$content .= "<a href=\"$pmb_opac_url?opac_view=".$object->id."\" alt='".htmlentities($object->name, ENT_QUOTES, $charset)."' title='".htmlentities($object->name, ENT_QUOTES, $charset)."' target='_blank'>$pmb_opac_url?opac_view=".$object->id."</a>";
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_button_gen() {
		global $msg;
		
		return $this->get_button('gen', $msg["opac_view_gen"]);
	}
	
	protected function get_display_left_actions() {
		$display = parent::get_display_left_actions();
		$display .= $this->get_button_gen();
		return $display;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&opac_view_id='.$object->id;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		switch($property) {
			case 'link':
				return array();
			default :
				return array(
						'onclick' => "document.location=\"".$this->get_edition_link($object)."\""
				);
		}
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['opac_view_add'];
	}
	
	public static function get_controller_url_base() {
		return parent::get_controller_url_base()."&section=list";
	}
	
}