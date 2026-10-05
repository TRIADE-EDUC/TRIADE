<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_authperso_ui.class.php,v 1.1 2023/11/16 13:13:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/authperso.class.php');

class list_authperso_ui extends list_ui {
	
	protected function _get_query_base() {
		$query = "SELECT id_authperso FROM authperso";
		return $query;
	}
	
	protected function get_object_instance($row) {
	    return new authperso($row->id_authperso);
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'opac_search' => '',
		);
		parent::init_filters($filters);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
			    'name' => 'authperso_name'
			)
		);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('name');
	}
	
	protected function init_default_columns() {
		$this->add_column('name');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('opac_search', 'datatype', 'boolean');
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('opac_search', 'authperso_opac_search');
	}
}