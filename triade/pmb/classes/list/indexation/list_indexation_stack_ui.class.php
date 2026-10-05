<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_indexation_stack_ui.class.php,v 1.5.2.1 2024/10/17 11:59:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/indexation_stack.class.php');

class list_indexation_stack_ui extends list_ui {
	
	protected function _get_query_base() {
		$query = "SELECT 
			concat(indexation_stack_entity_type,'_', indexation_stack_entity_id,'_', indexation_stack_datatype) as id,
			indexation_stack_entity_id as entity_id,
			indexation_stack_entity_type as entity_type,
		 	indexation_stack_datatype as datatype,
			indexation_stack_timestamp as timestamp,
			indexation_stack_informations as informations
			FROM indexation_stack";
		return $query;
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'entity_types' => 'indexation_stack_entity_types',
						'informations' => 'indexation_stack_informations',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'entity_types' => array(),
				'informations' => array(),
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('entity_types');
		$this->add_selected_filter('informations');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
					'entity_id' => 'indexation_stack_entity_id',
					'entity_isbd' => 'indexation_stack_entity_isbd',
					'entity_type' => 'indexation_stack_entity_type',
					'datatype' => 'indexation_stack_datatype',
					'timestamp' => 'indexation_stack_timestamp',
					'informations' => 'indexation_stack_informations',
			)
		);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('timestamp');
	}
	
	protected function get_title() {
	    global $msg, $charset;
	    return "<h3>".htmlentities($msg["supervision_indexation_stack"], ENT_QUOTES, $charset)."</h3>";
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'datatype' :
	        case 'timestamp' :
	            return $sort_by.', indexation_stack_timestamp, indexation_stack_entity_id, indexation_stack_entity_type, indexation_stack_datatype';
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('entity_types');
		$this->set_filter_from_form('informations');
		parent::set_filters_from_form();
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('entity_id');
		$this->add_column('entity_isbd');
		$this->add_column('datatype');
		$this->add_column('timestamp');
		$this->add_column('informations');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('default', 'align', 'left');
	}
	
	protected function get_search_filter_entity_types() {
		global $msg;
	
		$options = array();
		$query = "SELECT DISTINCT indexation_stack_entity_type FROM indexation_stack";
		$result = pmb_mysql_query($query);
		while ($row = pmb_mysql_fetch_object($result)) {
			$options[$row->indexation_stack_entity_type] = indexation_stack::get_label_from_type($row->indexation_stack_entity_type);
		}
		return $this->get_search_filter_multiple_selection('', 'entity_types', $msg['all'], $options);
	}
	
	protected function get_search_filter_informations() {
		global $msg;
		
		$options = array();
		$query = "SELECT DISTINCT indexation_stack_informations FROM indexation_stack WHERE indexation_stack_informations <> ''";
		$result = pmb_mysql_query($query);
		while ($row = pmb_mysql_fetch_object($result)) {
			$options[$row->indexation_stack_informations] = $row->indexation_stack_informations;
		}
		return $this->get_search_filter_multiple_selection('', 'informations', $msg['all'], $options);
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_multiple_restriction('entity_types', 'indexation_stack_entity_type');
		$this->_add_query_filter_interval_restriction('informations', 'indexation_stack_informations');
	}
	
	protected function _get_object_property_entity_type($object) {
		return indexation_stack::get_label_from_type($object->entity_type);
	}
	
	protected function _get_query_human_entity_types() {
		if(!empty($this->filters['entity_types'])) {
			$labels = array();
			foreach ($this->filters['entity_types'] as $type) {
				$labels[] = indexation_stack::get_label_from_type($type);
			}
			return implode(', ', $labels);
		}
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		
		parent::init_default_selection_actions();
		$manual_indexation_link = array(
				'href' => static::get_controller_url_base()."&action=list_manual_indexation",
				'confirm' => ''
		);
		$this->add_selection_action('manual_indexation', $msg['manual_indexation'], '', $manual_indexation_link);
	}
	
	protected function _get_object_property_entity_isbd($object) {
		return indexation_stack::get_entity_isbd($object->entity_type, $object->entity_id);
	}
	
	protected function _get_object_property_datatype($object) {
	    global $msg;
	    return $msg[$object->datatype] ?? $object->datatype;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		
		$content = '';
		switch($property) {
			case 'timestamp':
				$dt = new \DateTime();
				$dt->setTimestamp($object->timestamp);
				$content .= $dt->format($msg['1005']." H:i:s");
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
			
	public function get_display_list() {
	    if (empty($this->objects)) {
	        return '';
	    }
	    return parent::get_display_list();
	}
	
	public static function run_action_list($action='') {
		$selected_objects = static::get_selected_objects();
		if(is_array($selected_objects) && count($selected_objects)) {
			foreach ($selected_objects as $selected_object) {
				$exploded_object = explode('_', $selected_object);
				$entity_type = intval($exploded_object[0]);
				$entity_id = intval($exploded_object[1]);
				$datatype = $exploded_object[2];
				switch ($action) {
					case 'manual_indexation':
					    // Réinitialiser la pile d'indexation en amont
					    pmb_mysql_query("UPDATE parametres SET valeur_param = 0 WHERE type_param ='pmb' AND sstype_param = 'indexation_in_progress'");
					    pmb_mysql_query("UPDATE parametres SET valeur_param = 1 WHERE type_param ='pmb' AND sstype_param = 'indexation_needed'");
					    
						indexation_stack::index_entity($entity_id, $entity_type, $datatype, '');
						break;
				}
			}
		}
	}
}