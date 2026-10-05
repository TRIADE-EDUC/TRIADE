<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_authorities_subcollections_ui.class.php,v 1.2 2023/03/02 11:02:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_authorities_subcollections_ui extends list_authorities_ui {
	
	protected function _get_query_base_select() {
		return parent::_get_query_base_select().", sub_collections.*";
	}
	
	protected function _get_query_base_from() {
		return parent::_get_query_base_from()." JOIN sub_collections ON authorities.num_object = sub_collections.sub_coll_id AND authorities.type_object = " . AUT_TABLE_SUB_COLLECTIONS;
	}
	
	protected function get_main_fields() {
		return array(
				'sub_coll_name' => '67',
				'sub_coll_parent' => '179',
				'sub_coll_issn' => '165',
				'subcollection_web' => '147',
				'subcollection_comment' => 'subcollection_comment'
		);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'sub_coll_name' => '67',
						'sub_coll_parent' => '179',
						'sub_coll_issn' => '165',
						'subcollection_comment' => 'subcollection_comment',
				)
		);
		$this->available_filters['custom_fields'] = array();
		$this->add_custom_fields_available_filters('subcollection', 'sub_coll_id');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		parent::init_available_columns();
		$this->add_custom_fields_available_columns('subcollection', 'sub_coll_id');
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('sub_coll_name');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_filter('sub_coll_parent', 'selection_type', 'completion');
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'sub_coll_name' => '',
				'sub_coll_parent' => 0,
				'sub_coll_issn' => '',
				'subcollection_comment' => '',
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('sub_coll_name');
		$this->add_selected_filter('sub_coll_parent');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('sub_coll_name');
		$this->set_filter_from_form('sub_coll_parent', 'integer');
		$this->set_filter_from_form('sub_coll_issn');
		$this->set_filter_from_form('subcollection_comment');
		parent::set_filters_from_form();
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('sub_coll_name', 'sub_coll_name');
		$this->_add_query_filter_simple_restriction('sub_coll_parent', 'sub_coll_parent', 'integer');
		$this->_add_query_filter_simple_restriction('sub_coll_issn', 'sub_coll_issn');
		$this->_add_query_filter_simple_restriction('subcollection_comment', 'subcollection_comment');
	}
	
	protected function get_search_filter_sub_coll_parent() {
		global $msg;
		return $this->get_search_filter_simple_selection($this->get_selection_query('collections'), 'sub_coll_parent', $msg["all"]);
	}
	
	protected function get_selection_query($type) {
		$query = '';
		switch ($type) {
			case 'sub_coll_parent':
				$query = 'select distinct collection_id as id, collection_name as label from collections order by label';
				break;
		}
		return $query;
	}
	
	protected function _get_object_property_sub_coll_parent($object) {
		$collection = authorities_collection::get_authority(AUT_TABLE_COLLECTIONS, $object->sub_coll_parent);
		return $collection->get_isbd();
	}
	
	protected function _get_query_human_sub_coll_parent() {
		if($this->filters['sub_coll_parent']) {
			$collection = authorities_collection::get_authority(AUT_TABLE_COLLECTIONS, $this->filters['sub_coll_parent']);
			return $collection->get_isbd();
		}
		return '';
	}
}