<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_scheduler_planning_ui.class.php,v 1.3 2024/03/08 07:36:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/scheduler/scheduler_planning.class.php");

class list_scheduler_planning_ui extends list_ui {
	
    public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
        if(empty($this->objects_type) && !empty($filters['type'])) {
            $this->objects_type = str_replace('list_', '', get_class($this)).'_'.$filters['type'];
        }
        parent::__construct($filters, $pager, $applied_sort);
    }
    
	protected function _get_query_base() {
		$query = "SELECT id_planificateur, num_type_tache FROM planificateur";
		return $query;
	}
		
	protected function get_object_instance($row) {
		$name = scheduler_tasks::get_catalog_element($row->num_type_tache, 'NAME');
		$classname = $name.'_planning';
		if(class_exists($classname)) {
			$scheduler_planning =  new $classname($row->id_planificateur);
		} else {
			$scheduler_planning =  new scheduler_planning($row->id_planificateur);
		}
		$scheduler_planning->get_property_task_bdd();
		return $scheduler_planning;
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'type' => 'planificateur_type',
						'libelle_tache' => 'planificateur_task_name',
						'desc_tache' => 'planificateur_task_desc',
						'status' => 'planificateur_task_active',
						'users' => 'planificateur_task_users'
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'type' => '',
				'libelle_tache' => '',
				'desc_tache' => '',
				'status' => -1,
				'users' => array()
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('type');
		$this->add_selected_filter('libelle_tache');
		$this->add_selected_filter('status');
		$this->add_selected_filter('users');
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
		
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'libelle_tache':
	            return "libelle_tache";
	        case 'desc_tache':
	            return "desc_tache";
	    }
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('type', 'integer');
		$this->set_filter_from_form('libelle_tache');
		$this->set_filter_from_form('desc_tache');
		$this->set_filter_from_form('users', 'integer');
		parent::set_filters_from_form();
	}
	
	protected function get_selection_query($type) {
		$query = '';
		switch ($type) {
			case 'users':
				$query = 'select userid as id, concat(prenom, " ", nom) as label from users order by label';
				break;
		}
		return $query;
	}
	
	protected function get_search_filter_type() {
		
	}
	
	protected function get_search_filter_libelle_tache() {
		return $this->get_search_filter_simple_text('libelle_tache');
	}
	
	protected function get_search_filter_desc_tache() {
		return $this->get_search_filter_simple_text('desc_tache');
	}
	
	protected function get_search_filter_status() {
		// Activé Tous / Oui / Non
	}
	
	protected function get_search_filter_users() {
		global $msg;
	
		return $this->get_search_filter_multiple_selection($this->get_selection_query('users'), 'users', $msg['all']);
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('type', 'num_type_tache');
		$this->_add_query_filter_simple_restriction('libelle_tache', 'libelle_tache');
		$this->_add_query_filter_simple_restriction('desc_tache', 'desc_tache');
		if($this->filters['status'] != -1) {
			$this->_add_query_filter_simple_restriction('status', 'statut');
		}
		$this->_add_query_filter_multiple_restriction('users', 'num_user');
	}
	
	protected function _get_object_property_statut($object) {
		global $msg;
		
		if($object->get_statut() == 1) {
			return $msg['planificateur_task_statut_active'];
		} else {
			return $msg['planificateur_task_statut_inactive'];
		}
	}
	
	protected function format_periodicity($field, $value) {
		global $msg;
		
		$formatted_values = array();
		$exploded_values = explode(',', $value);
		switch ($field) {
			case 'perio_jour_mois':
				if($exploded_values[0] != '*') {
					for($i=1; $i<=31; $i++) {
						if(in_array($i, $exploded_values)) {
							$formatted_values[] = '<strong>'.$i.'</strong>';
						} else {
							$formatted_values[] = $i;
						}
					}
				} else {
					$formatted_values[] = '01 - 31';
				}
				
				break;
			case 'perio_jour':
				for($i=1; $i<=7; $i++) {
					if($exploded_values[0] == '*' || in_array($i, $exploded_values)) {
						$formatted_values[] = '<strong>'.$msg[(1017 + $i)].'</strong>';
					} else {
						$formatted_values[] = $msg[(1017 + $i)];
					}
				}
				break;
			case 'perio_mois':
				for($i=1; $i<=12; $i++) {
					if($exploded_values[0] == '*' || in_array($i, $exploded_values)) {
						$formatted_values[] = '<strong>'.pmb_substr(ucfirst($msg[(1005 + $i)]),0,3).'</strong>';
					} else {
						$formatted_values[] = pmb_substr(ucfirst($msg[(1005 + $i)]),0,3);
					}
				}
				break;
		}
		return implode(' ', $formatted_values);
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'periodicity':
				$content .= $object->get_perio_heure()." h ".$object->get_perio_minute();
				$content .= "<br />".$this->format_periodicity('perio_jour_mois', $object->get_perio_jour_mois());
				$content .= "<br />".$this->format_periodicity('perio_jour', $object->get_perio_jour());
				$content .= "<br />".$this->format_periodicity('perio_mois', $object->get_perio_mois());
				break;
			case 'settings':
				$formatted_settings = $object->get_formatted_settings();
				if(is_countable($formatted_settings) && count($formatted_settings)) {
					$content .= "<ul>";
					foreach ($formatted_settings as $formatted_setting) {
						$content .= "<li>".$formatted_setting['label']." : ".$formatted_setting['value']."</li>";
					}
					$content .= "</ul>";
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		$attributes = array();
		$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&action=edit&type_id=".$this->filters['type']."&id=".$object->get_id()."\"";
		return $attributes;
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'libelle_tache' => 'planificateur_task_name',
						'desc_tache' => 'planificateur_task_desc',
						'statut' => 'planificateur_task_active',
						'next_execution' => 'planificateur_next_exec',
						'periodicity' => 'periodicity',
						'settings' => '33'
				)
		);
	}
	
	protected function init_default_columns() {
		$this->add_column('libelle_tache');
		$this->add_column('desc_tache');
		$this->add_column('statut');
		$this->add_column('next_execution');
//		$this->add_column('periodicity');
//		$this->add_column('settings');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_display('pager', 'visible', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('statut', 'align', 'center');
		$this->set_setting_column('next_exec', 'align', 'center');
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('libelle_tache');
	}
	
	public function get_display_header_list() {
		return '';	
	}
	
	protected function get_js_sort_script_sort() {
		return '';	
	}
	
	public static function get_controller_url_base() {
		global $base_path;
	
		return $base_path.'/admin.php?categ=planificateur&sub=manager';
	}
}