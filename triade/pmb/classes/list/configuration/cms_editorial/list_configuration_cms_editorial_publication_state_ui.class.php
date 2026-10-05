<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_cms_editorial_publication_state_ui.class.php,v 1.4 2023/12/18 15:17:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/list/configuration/cms_editorial/list_configuration_cms_editorial_ui.class.php");
require_once($class_path."/cms/cms_editorial_publications_state.class.php");

class list_configuration_cms_editorial_publication_state_ui extends list_configuration_cms_editorial_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM cms_editorial_publications_states';
	}
	
	protected function get_object_instance($row) {
		return new cms_editorial_publications_state($row->id_publication_state);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('label');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'label' => 'editorial_content_publication_state_label',
				'opac_show' => 'editorial_content_publication_state_visible',
				'auth_opac_show' => 'editorial_content_publication_state_visible_abo',
		);
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('opac_show', 'align', 'center');
		$this->set_setting_column('opac_show', 'datatype', 'boolean');
		$this->set_setting_column('auth_opac_show', 'align', 'center');
		$this->set_setting_column('auth_opac_show', 'datatype', 'boolean');
	}
	
	protected function get_cell_content($object, $property) {
		global $charset;
		
		$content = '';
		switch($property) {
			case 'label':
			    $content .= $this->get_cell_img_class_html($object, 'class_html');
				$content .= htmlentities($object->label, ENT_QUOTES, $charset);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&id='.$object->id;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['editorial_content_publication_state_add'];
	}
}