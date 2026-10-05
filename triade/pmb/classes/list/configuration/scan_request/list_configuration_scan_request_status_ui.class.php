<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_scan_request_status_ui.class.php,v 1.4 2023/12/18 15:17:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/list/configuration/scan_request/list_configuration_scan_request_ui.class.php");

class list_configuration_scan_request_status_ui extends list_configuration_scan_request_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM scan_request_status';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('scan_request_status_label');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'scan_request_status_label' => 'scan_request_status_label',
				'scan_request_status_opac_show' => 'scan_request_status_visible',
				'scan_request_status_cancelable' => 'scan_request_cancelable',
				'scan_request_status_infos_editable' => 'scan_request_infos_editable',
				'scan_request_status_is_closed' => 'scan_request_is_closed',
		);
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'unfolded_filters', true);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_column('default', 'align', 'center');
		$this->set_setting_column('scan_request_status_label', 'align', 'left');
		$this->set_setting_column('default', 'text', array('italic' => true));
		$this->set_setting_column('print_mail', 'text', array('italic' => false));
		$this->set_setting_column('scan_request_status_opac_show', 'datatype', 'boolean');
		$this->set_setting_column('scan_request_status_cancelable', 'datatype', 'boolean');
		$this->set_setting_column('scan_request_status_infos_editable', 'datatype', 'boolean');
		$this->set_setting_column('scan_request_status_is_closed', 'datatype', 'boolean');
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'scan_request_status_label':
			    $content .= $this->get_cell_img_class_html($object, 'scan_request_status_class_html');
				break;
			default :
				break;
		}
		$content .= parent::get_cell_content($object, $property);
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&id='.$object->id_scan_request_status;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['editorial_content_publication_state_add'];
	}
}