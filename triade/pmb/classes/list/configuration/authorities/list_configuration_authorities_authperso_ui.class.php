<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_authorities_authperso_ui.class.php,v 1.4.4.1 2025/02/21 13:34:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/authperso_admin.class.php");

class list_configuration_authorities_authperso_ui extends list_configuration_authorities_ui {
	
	protected function get_title() {
		global $msg, $charset;
		return "<h1>".htmlentities($msg["admin_authperso"], ENT_QUOTES, $charset)."</h1>";
	}
	
	protected function _get_query_base() {
		return 'SELECT * FROM authperso';
	}
	
	protected function get_object_instance($row) {
		return new authperso_admin($row->id_authperso);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('id');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'name' => 'admin_authperso_name',
				'onglet_name' => 'admin_authperso_notice_onglet',
				'opac_search' => 'admin_authperso_opac_search_simple_list_title',
				'opac_multi_search' => 'admin_authperso_opac_search_multi_list_title',
				'gestion_search' => 'admin_authperso_gestion_search_simple_list_title',
				'gestion_multi_search' => 'admin_authperso_gestion_search_multi_list_title',
				'oeuvre_event' => 'aut_oeuvre_form_oeuvre_event',
				'responsability_authperso' => 'aut_responsability_form_responsability_authperso'
				
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
		
		$html_properties = array(
				'value' => $msg['admin_authperso_edition'],
				'link' => static::get_controller_url_base().'&auth_action=edition&id_authperso=!!id!!'
		);
		$this->add_column_simple_action('action', $msg['admin_authperso_action'], $html_properties);
	}
	
	protected function get_display_content_object_list($object, $indice) {
		$this->is_editable_object_list = false;
		return parent::get_display_content_object_list($object, $indice);
	}
	
	protected function _compare_objects($a, $b, $index=0) {
		$sort_by = $this->applied_sort[$index]['by'];
		switch($sort_by) {
			case 'name':
			case 'onglet_name':
			case 'opac_search':
			case 'gestion_search':
			case 'opac_multi_search':
			case 'gestion_multi_search':
			case 'oeuvre_event':
			case 'responsability_authperso':
				return strcmp(strtolower(convert_diacrit($a->info[$sort_by])), strtolower(convert_diacrit($b->info[$sort_by])));
				break;
			default :
				return parent::_compare_objects($a, $b, $index);
				break;
		}
	}
	
	protected function _get_object_property_name($object) {
		return $object->info['name'];
	}
	
	protected function _get_object_property_onglet_name($object) {
		return $object->info['onglet_name'];
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
	
		$content = '';
		switch($property) {
			case 'opac_search':
			case 'gestion_search':
				if($object->info[$property]==1) {
					$content .= "x";
				}
				if($object->info[$property]==2) {
					$content .= htmlentities($msg['admin_authperso_'.$property.'_simple_list_valid'], ENT_QUOTES, $charset);
				}
				break;
			case 'opac_multi_search':
			case 'gestion_multi_search':
			case 'oeuvre_event':
			case 'responsability_authperso':
				if($object->info[$property]) {
					$content .= "x";
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&auth_action=form&id_authperso='.$object->id;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		return array(
				'onclick' => "document.location=\"".$this->get_edition_link($object)."\""
		);
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['admin_authperso_add'];
	}
	
	protected function get_button_add() {
		return $this->get_interface_button($this->get_label_button_add(), ['location' => static::get_controller_url_base()."&auth_action=form"]);
	}
}