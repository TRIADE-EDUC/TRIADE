<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_visits_statistics_ui.class.php,v 1.1.2.4 2024/11/07 07:57:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/visit_statistics.class.php');

class list_visits_statistics_ui extends list_ui {
	
	protected function _get_query_base() {
		$query = 'select visits_statistics_id as id, visits_statistics_date as date, visits_statistics_location as location, visits_statistics_type as type, 1 as visits_number from visits_statistics';
		return $query;
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'types' => 'visits_statistics_types',
						'locations' => 'visits_statistics_locations',
						'date' => 'visits_statistics_dates'
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'types' => array(),
				'locations' => array(),
				'date_start' => '',
				'date_end' => '',
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('types');
		$this->add_selected_filter('locations');
		$this->add_empty_selected_filter();
		$this->add_selected_filter('date');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
					'type' => 'visit_statistics_type',
					'date' => 'visit_statistics_date',
					'location' => 'visit_statistics_location',
                    'visits_number' => 'visit_statistics_visits_number',
                    'actions' => 'visit_statistics_actions',
			)
		);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('date', 'desc');
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'id':
	            return 'visits_statistics_id';
	        case 'type' :
	        case 'location' :
	        case 'date':
	            return 'visits_statistics_'.$sort_by;
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('types');
		$this->set_filter_from_form('locations');
		$this->set_filter_from_form('date_start');
		$this->set_filter_from_form('date_end');
		parent::set_filters_from_form();
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('type');
		$this->add_column('location');
		$this->add_column('date');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('date', 'datatype', 'datetime');
		$this->set_setting_column('visits_number', 'datatype', 'integer');
	}
	
	protected function init_default_selection_actions() {
	    global $msg;
	    
	    parent::init_default_selection_actions();
	    if(static::class == 'list_visits_statistics_ui') {
    	    $delete_link = array(
    	        'href' => static::get_controller_url_base()."&action=list_delete",
    	        'confirm' => $msg['confirm_suppr']
    	    );
    	    $this->add_selection_action('delete', $msg['63'], 'interdit.gif', $delete_link);
	    }
	}
	
	protected function get_search_filter_types() {
		global $msg;
	
		$options = array();
		$query = "SELECT DISTINCT visits_statistics_type FROM visits_statistics ORDER BY visits_statistics_type";
		$result = pmb_mysql_query($query);
		while ($row = pmb_mysql_fetch_object($result)) {
			$label = visits_statistics::get_label_from_type($row->visits_statistics_type);
			$options[$row->visits_statistics_type] = ($label ? $label : $row->visits_statistics_type);
		}
		return $this->get_search_filter_multiple_selection('', 'types', $msg['all'], $options);
	}
	
	protected function get_search_filter_locations() {
		global $msg;
	
		$query = "SELECT idlocation AS id, location_libelle AS label FROM docs_location ORDER BY label";
		return $this->get_search_filter_multiple_selection($query, 'locations', $msg['all_location']);
	}
	
	protected function get_search_filter_date() {
		return $this->get_search_filter_interval_date('date');
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_multiple_restriction('types', 'visits_statistics_type');
		$this->_add_query_filter_multiple_restriction('locations', 'visits_statistics_location');
		$this->_add_query_filter_interval_restriction('date', 'visits_statistics_date', 'datetime');
		if($this->filters['ids']) {
			$this->query_filters [] = 'visits_statistics_id IN ('.$this->filters['ids'].')';
		}
	}
	
	protected function _get_object_property_type($object) {
		global $msg;
		
		$label = visits_statistics::get_label_from_type($object->type);
		if($label) {
			return $label;
		} elseif(isset($msg['dashboard_visits_statistics_'.$object->type])) {
			return $msg['dashboard_visits_statistics_'.$object->type];
		} else {
			return $object->type;
		}
	}
	
	protected function _get_object_property_location($object) {
		$docs_location = new docs_location($object->location);
		return $docs_location->libelle;
	}
	
	protected function _get_query_human_types() {
		if(!empty($this->filters['types'])) {
			$labels = array();
			foreach ($this->filters['types'] as $type) {
				$labels[] = visits_statistics::get_label_from_type($type);
			}
			return implode(', ', $labels);
		}
	}
	
	protected function _get_query_human_locations() {
		if(!empty($this->filters['locations'])) {
			$labels = array();
			foreach ($this->filters['locations'] as $location) {
				$docs_location = new docs_location($location);
				$labels[] = $docs_location->libelle;
			}
			return implode(', ', $labels);
		}
		return '';
	}
	
	protected function _get_query_human_date() {
		return $this->_get_query_human_interval_date('date');
	}
	
	public static function delete_object($id) {
	    $id = intval($id);
	    visit_statistics::delete($id);
	}
	
	public static function get_controller_url_base() {
	    global $visits_statistics_ui_date;
	    
	    return parent::get_controller_url_base().(!empty($visits_statistics_ui_date) ? "&visits_statistics_ui_date=".$visits_statistics_ui_date : "");
	}
}