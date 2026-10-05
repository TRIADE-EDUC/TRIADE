<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_authorities_collections_ui.class.php,v 1.2 2023/03/02 11:02:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_authorities_collections_ui extends list_authorities_ui {
	
	protected function _get_query_base_select() {
		return parent::_get_query_base_select().", collections.*";
	}
	
	protected function _get_query_base_from() {
		return parent::_get_query_base_from()." JOIN collections ON authorities.num_object = collections.collection_id AND authorities.type_object = " . AUT_TABLE_COLLECTIONS;
	}
	
	protected function get_main_fields() {
	    return array(
	    		'collection_name' => '714',
	    		'collection_parent' => '164',
	    		'collection_issn' => '165',
	    		'collection_web' => '147',
	    		'collection_comment' => 'collection_comment'
	    );
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'collection_name' => '714',
						'collection_parent' => '164',
						'collection_issn' => '165',
						'collection_comment' => 'collection_comment',
				)
		);
		$this->available_filters['custom_fields'] = array();
		$this->add_custom_fields_available_filters('collection', 'collection_id');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		parent::init_available_columns();
		$this->add_custom_fields_available_columns('collection', 'collection_id');
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('collection_name');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_filter('collection_parent', 'selection_type', 'completion');
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'collection_name' => '',
				'collection_parent' => 0,
				'collection_issn' => '',
				'collection_comment' => '',
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('collection_name');
		$this->add_selected_filter('collection_parent');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('collection_name');
		$this->set_filter_from_form('collection_parent', 'integer');
		$this->set_filter_from_form('collection_issn');
		$this->set_filter_from_form('collection_comment');
		parent::set_filters_from_form();
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('collection_name', 'collection_name');
		$this->_add_query_filter_simple_restriction('collection_parent', 'collection_parent', 'integer');
		$this->_add_query_filter_simple_restriction('collection_issn', 'collection_issn');
		$this->_add_query_filter_simple_restriction('collection_comment', 'collection_comment');
	}
	
	protected function get_search_filter_collection_parent() {
		global $msg;
		return $this->get_search_filter_simple_selection($this->get_selection_query('publishers'), 'collection_parent', $msg["all"]);
	}
	
	protected function get_selection_query($type) {
		$query = '';
		switch ($type) {
			case 'collection_parent':
				$query = 'select distinct ed_id as id, ed_name as label from publishers order by label';
				break;
		}
		return $query;
	}
	
	protected function _get_object_property_collection_parent($object) {
		$publisher = authorities_collection::get_authority(AUT_TABLE_PUBLISHERS, $object->collection_parent);
		return $publisher->get_isbd();
	}
	
	protected function _get_query_human_collection_parent() {
		if($this->filters['collection_parent']) {
			$publisher = authorities_collection::get_authority(AUT_TABLE_PUBLISHERS, $this->filters['collection_parent']);
			return $publisher->get_isbd();
		}
		return '';
	}
}