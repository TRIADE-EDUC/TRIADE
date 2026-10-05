<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_authorities_series_ui.class.php,v 1.2 2023/03/02 11:02:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_authorities_series_ui extends list_authorities_ui {
	
	protected function _get_query_base_select() {
		return parent::_get_query_base_select().", series.*";
	}
	
	protected function _get_query_base_from() {
		return parent::_get_query_base_from()." JOIN series ON authorities.num_object = series.serie_id AND authorities.type_object = " . AUT_TABLE_SERIES;
	}
	
	protected function get_main_fields() {
		return array(
				'serie_name' => '233'
		);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'serie_name' => '233',
				)
		);
		$this->available_filters['custom_fields'] = array();
		$this->add_custom_fields_available_filters('serie', 'serie_id');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		parent::init_available_columns();
		$this->add_custom_fields_available_columns('serie', 'serie_id');
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('serie_name');
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'serie_name' => '',
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('serie_name');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('serie_name');
		parent::set_filters_from_form();
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('serie_name', 'serie_name');
	}
}