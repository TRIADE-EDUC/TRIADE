<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_explnum_ui.class.php,v 1.3 2023/09/28 10:36:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_explnum_ui extends list_ui {
		
	protected function _get_query_base() {
		$query = "SELECT * FROM explnum
			JOIN explnum_statut ON explnum_statut.id_explnum_statut = explnum.explnum_docnum_statut";
		return $query;
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'explnum_nom' => 'explnum_nom',
						'explnum_mimetype' => 'extexplnum_minetype',
						'explnum_statut' => 'extexplnum_statut',
						'explnum_create_date' => 'exp_cre_date',
						'explnum_update_date' => 'exp_upd_date'
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'explnum_id' => 0,
				'explnum_notice' => 0,
				'explnum_bulletin' => 0,
				'explnum_nom' => '',
				'explnum_mimetype' => '',
				'explnum_statut' => '',
				'explnum_create_date_start' => '',
				'explnum_create_date_end' => '',
				'explnum_update_date_start' => '',
				'explnum_update_date_end' => '',
		);
		parent::init_filters($filters);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('explnum_nom');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'explnum_nom' => 'explnum_nom',
						'explnum_mimetype' => 'extexplnum_minetype',
						'explnum_statut' => 'extexplnum_statut',
						'explnum_create_date' => 'exp_cre_date',
						'explnum_update_date' => 'exp_upd_date'
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column('explnum_nom');
		$this->add_column('explnum_mimetype');
		$this->add_column('explnum_statut');
		$this->add_column('explnum_create_date');
		$this->add_column('explnum_update_date');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('explnum_nom', 'align', 'left');
		$this->set_setting_column('explnum_create_date', 'datatype', 'date');
		$this->set_setting_column('explnum_update_date', 'datatype', 'date');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('explnum_nom');
		$this->set_filter_from_form('explnum_mimetype');
		$this->set_filter_from_form('explnum_statut', 'integer');
		$this->set_filter_from_form('explnum_create_date_start');
		$this->set_filter_from_form('explnum_create_date_end');
		$this->set_filter_from_form('explnum_update_date_start');
		$this->set_filter_from_form('explnum_update_date_end');
		parent::set_filters_from_form();
	}
	
	protected function get_selection_query($type) {
		$query = '';
		switch ($type) {
			case 'explnum_mimetype':
				$query = 'select distinct explnum_mimetype as id, explnum_mimetype as label from explnum order by label';
				break;
			case 'explnum_statut':
				$query = 'select id_explnum_statut as id, gestion_libelle as label from explnum_statut order by label';
				break;
		}
		return $query;
	}
	
	protected function get_search_filter_explnum_mimetype() {
		global $msg;
		
		return $this->get_search_filter_simple_selection($this->get_selection_query('explnum_mimetype'), 'explnum_mimetype', $msg['all']);
	}
	
	protected function get_search_filter_explnum_statut() {
		global $msg;
		
		return $this->get_search_filter_simple_selection($this->get_selection_query('explnum_statut'), 'explnum_statut', $msg['all']);
	}
	
	protected function get_search_filter_explnum_create_date() {
		return $this->get_search_filter_interval_date('explnum_create_date');
	}
	
	protected function get_search_filter_explnum_update_date() {
		return $this->get_search_filter_interval_date('explnum_update_date');
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('explnum_id', 'explnum_id', 'integer');
		$this->_add_query_filter_simple_restriction('explnum_notice', 'explnum_notice', 'integer');
		$this->_add_query_filter_simple_restriction('explnum_bulletin', 'explnum_bulletin', 'integer');
		$this->_add_query_filter_simple_restriction('explnum_nom', 'explnum_nom', 'boolean_search');
		$this->_add_query_filter_simple_restriction('explnum_mimetype', 'explnum_mimetype');
		$this->_add_query_filter_simple_restriction('explnum_statut', 'explnum_docnum_statut', 'integer');
		$this->_add_query_filter_interval_restriction('explnum_create_date', 'explnum_create_date', 'datetime');
		$this->_add_query_filter_interval_restriction('explnum_update_date', 'explnum_update_date', 'datetime');
	}
	
	protected function _get_object_property_explnum_statut($object) {
		$explnum_statut = new explnum_statut($object->explnum_docnum_statut);
		return $explnum_statut->gestion_libelle;
	}
	
	protected function _get_query_property_filter($property) {
		switch ($property) {
			case 'explnum_statut':
				return "select gestion_libelle from explnum_statut where id_explnum_statut = ".$this->filters[$property];
		}
		return '';
	}
}