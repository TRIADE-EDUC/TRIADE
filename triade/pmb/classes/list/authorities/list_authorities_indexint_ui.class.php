<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_authorities_indexint_ui.class.php,v 1.2 2023/03/02 11:02:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_authorities_indexint_ui extends list_authorities_ui {
	
	protected function _get_query_base_select() {
		return parent::_get_query_base_select().", indexint.*";
	}
	
	protected function _get_query_base_from() {
		return parent::_get_query_base_from()." JOIN indexint ON authorities.num_object = indexint.indexint_id AND authorities.type_object = " . AUT_TABLE_INDEXINT;
	}
	
	protected function get_main_fields() {
		return array(
				'indexint_name' => 'indexint_nom',
				'indexint_comment' => 'indexint_comment',
				'pclassement' => 'menu_pclassement'
		);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'indexint_name' => 'indexint_nom',
						'indexint_comment' => 'indexint_comment',
						'pclassement' => 'menu_pclassement',
				)
		);
		$this->available_filters['custom_fields'] = array();
		$this->add_custom_fields_available_filters('indexint', 'indexint_id');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		parent::init_available_columns();
		$this->add_custom_fields_available_columns('indexint', 'indexint_id');
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('indexint_name');
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'indexint_name' => '',
				'indexint_comment' => '',
				'pclassement' => 0,
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('indexint_name');
		$this->add_selected_filter('pclassement');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('indexint_name');
		$this->set_filter_from_form('indexint_comment');
		$this->set_filter_from_form('pclassement', 'integer');
		parent::set_filters_from_form();
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('indexint_name', 'indexint_name');
		$this->_add_query_filter_simple_restriction('indexint_comment', 'indexint_comment');
		$this->_add_query_filter_simple_restriction('pclassement', 'num_pclass', 'integer');
	}
	
	protected function get_search_filter_pclassement() {
		global $msg;
		return $this->get_search_filter_simple_selection($this->get_selection_query('pclassement'), 'pclassement', $msg["all"]);
	}
	
	protected function get_selection_query_fields($type) {
		switch ($type) {
			case 'pclassement':
				return array('id' => 'id_pclass', 'label' => 'name_pclass');
		}
	}
	
	protected function _get_object_property_pclassement($object) {
		$pclassement = new pclassement($object->num_pclass);
		return $pclassement->get_name();
	}
	
	protected function _get_query_human_pclassement() {
		if($this->filters['pclassement']) {
			$pclassement = new pclassement($this->filters['pclassement']);
			return $pclassement->get_name();
		}
		return '';
	}
}