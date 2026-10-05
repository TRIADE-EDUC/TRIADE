<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_frbr_pages_ui.class.php,v 1.11.4.1 2025/02/20 09:18:48 dgoron Exp $
if (stristr ( $_SERVER ['REQUEST_URI'], ".class.php" )) die ( "no access" );

class list_frbr_pages_ui extends list_ui {
	
	protected $managed_entities;
	
	protected function _get_query_base() {
		$query = 'select id_page
				from frbr_pages';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new frbr_page($row->id_page);
	}
	
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'order':
	            return 'page_order, page_name';
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	protected function init_default_applied_sort() {
		$this->add_applied_sort('order');
		$this->add_applied_sort('name');
	}

	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'entity');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = array (
				'main_fields' => array (
						'order' => 'frbr_page_order',
						'name' => 'frbr_page_name',
						'records_list' => 'frbr_page_parameter_records_list',
						'facettes_list' => 'frbr_page_parameter_facettes_list',
						'isbd' => 'frbr_page_parameter_isbd',
						'template_directory' => 'frbr_page_parameter_template_directory',
						'record_template_directory' => 'frbr_page_parameter_record_template_directory'
				)
		);
	}

	/**
	 * Initialisation des colonnes par défaut
	 */
	protected function init_default_columns() {
		$this->add_column('order');
		$this->add_column('name');
		$this->add_column('records_list');
		$this->add_column('facettes_list');
		$this->add_column('isbd');
		$this->add_column('template_directory');
		$this->add_column('record_template_directory');
		$this->add_column_build();
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('records_list', 'datatype', 'boolean');
		$this->set_setting_column('facettes_list', 'datatype', 'boolean');
		$this->set_setting_column('isbd', 'datatype', 'boolean');
		$this->settings['objects']['default']['display_mode'] = 'expandable_table';
		$this->settings['grouped_objects']['level_1']['display_mode'] = 'expandable_table';
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array (
				'order', 'name', 'records_list',
				'facettes_list', 'isbd', 'template_directory',
				'record_template_directory'
		);
	}

	protected function add_column_build() {
		global $msg;
		
		$html_properties = array(
				'value' => $msg['frbr_page_tree_build'],
				'link' => static::get_controller_url_base().'&sub=build&num_page=!!id!!&num_parent=0'
		);
		$this->add_column_simple_action('', '', $html_properties);
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters = array()) {
		$this->filters = array();
		parent::init_filters($filters);
	}

	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters = array (
				'main_fields' => array ()
		);
		$this->available_filters ['custom_fields'] = array ();
	}
	
	protected function _get_object_property_entity($object) {
		$managed_entities = $this->get_managed_entities();
		return $managed_entities[$object->get_entity()]['name'];
	}
	
	protected function _get_object_property_records_list($object) {
		return $object->get_parameter_value('records_list');
	}
	
	protected function _get_object_property_facettes_list($object) {
		return $object->get_parameter_value('facettes_list');
	}
	
	protected function _get_object_property_isbd($object) {
		return $object->get_parameter_value('isbd');
	}
	
	protected function _get_object_property_template_directory($object) {
		return $object->get_parameter_value('template_directory');
	}
	
	protected function _get_object_property_record_template_directory($object) {
		return $object->get_parameter_value('record_template_directory');
	}
		
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		
		$content = '';
		switch ($property) {
			case 'order':
				$content .= "
					<img src='".get_url_icon('bottom-arrow.png')."' title='".htmlentities($msg['move_bottom_arrow'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['move_bottom_arrow'], ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&sub=list&action=down&id=".$object->get_id()."'\" style='cursor:pointer;'/>
					<img src='".get_url_icon('top-arrow.png')."' title='".htmlentities($msg['move_top_arrow'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['move_top_arrow'], ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&sub=list&action=up&id=".$object->get_id()."'\" style='cursor:pointer;'/>
				";
				break;
			case 'records_list':
			case 'facettes_list':
			case 'isbd':
				if ($object->get_parameter_value($property)) {
					$content .= "X";
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	public function get_managed_entities() {
		if(!isset($this->managed_entities)) {
			$entities_parser = new frbr_entities_parser();
			$this->managed_entities = $entities_parser->get_managed_entities();
		}
		return $this->managed_entities;
	}
	
	protected function get_button_add() {
		global $msg;
		
		return $this->get_interface_button($msg["frbr_page_add"], ['location' => static::get_controller_url_base()."&sub=edit"], ['name' => 'frbr_page_add']);
	}
	
	public function get_js_sort_expandable_list() {
		return "";	
	}
	
	protected function gen_plus($id, $titre, $contenu, $maximise=0) {
		return "
			<div id=\"el" . $id . "Parent\" class='parent' width=\"100%\">
				<span class='heada'>
					<h3>".$titre."</h3>
				</span>
				<br />
			</div>
			<div id=\"el" . $id . "Child\" class=\"child\" style=\"margin-bottom:6px;\">
				".$contenu."
			</div>";
	}
	
	protected function get_display_left_actions() {
		return $this->get_button_add();
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		$attributes = array();
		switch ($property) {
			case 'order':
				break;
			case 'name':
				$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&sub=edit&id=".$object->get_id()."\"";
				$attributes['width'] = "20%";
				break;
			default:
				$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&sub=edit&id=".$object->get_id()."\"";
				break;
		}
		return $attributes;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		//On considere la liste des pages comme la page par défaut du module, sinon cela pose probleme au paginateur
		return $base_path.'/cms.php?categ=frbr_pages&sub=list';
	}
}