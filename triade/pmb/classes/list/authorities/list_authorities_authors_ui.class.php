<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_authorities_authors_ui.class.php,v 1.2 2023/03/02 11:02:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_authorities_authors_ui extends list_authorities_ui {
	
	protected function _get_query_base_select() {
		return parent::_get_query_base_select().", authors.*";
	}
	
	protected function _get_query_base_from() {
		return parent::_get_query_base_from()." JOIN authors ON authorities.num_object = authors.author_id AND authorities.type_object = " . AUT_TABLE_AUTHORS;
	}
	
	protected function get_main_fields() {
		return array(
				'author_type' => '205',
				'author_name' => '201',
				'author_rejete' => '202',
				'author_date' => '713',
				'author_web' => '147',
				'author_comment' => 'author_comment',
				'author_lieu' => 'congres_lieu_libelle',
				'author_ville' => 'congres_ville_libelle',
				'author_pays' => 'congres_pays_libelle',
				'author_subdivision' => 'congres_subdivision_libelle',
				'author_isni' => 'author_isni'
		);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'author_type' => '205',
						'author_name' => '201',
						'author_rejete' => '202',
						'author_date' => '713',
						'author_comment' => 'author_comment',
						'author_lieu' => 'congres_lieu_libelle',
						'author_ville' => 'congres_ville_libelle',
						'author_pays' => 'congres_pays_libelle',
						'author_subdivision' => 'congres_subdivision_libelle',
						'author_isni' => 'author_isni',
				)
		);
		$this->available_filters['custom_fields'] = array();
		$this->add_custom_fields_available_filters('author', 'author_id');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		parent::init_available_columns();
		$this->add_custom_fields_available_columns('author', 'author_id');
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('author_name');
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'author_type' => '',
				'author_name' => '',
				'author_rejete' => '',
				'author_date' => '',
				'author_comment' => '',
				'author_lieu' => '',
				'author_ville' => '',
				'author_pays' => '',
				'author_subdivision' => '',
				'author_isni' => '',
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('author_type');
		$this->add_selected_filter('author_name');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('author_type');
		$this->set_filter_from_form('author_name');
		$this->set_filter_from_form('author_rejete');
		$this->set_filter_from_form('author_date');
		$this->set_filter_from_form('author_comment');
		$this->set_filter_from_form('author_lieu');
		$this->set_filter_from_form('author_ville');
		$this->set_filter_from_form('author_pays');
		$this->set_filter_from_form('author_subdivision');
		$this->set_filter_from_form('author_isni');
		parent::set_filters_from_form();
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('author_type', 'author_type');
		$this->_add_query_filter_simple_restriction('author_name', 'author_name');
		$this->_add_query_filter_simple_restriction('author_rejete', 'author_rejete');
		$this->_add_query_filter_simple_restriction('author_date', 'author_date');
		$this->_add_query_filter_simple_restriction('author_comment', 'author_comment');
		$this->_add_query_filter_simple_restriction('author_lieu', 'author_lieu');
		$this->_add_query_filter_simple_restriction('author_ville', 'author_ville');
		$this->_add_query_filter_simple_restriction('author_pays', 'author_pays');
		$this->_add_query_filter_simple_restriction('author_subdivision', 'author_subdivision');
		$this->_add_query_filter_simple_restriction('author_isni', 'author_isni');
	}
	
	protected function get_search_filter_author_type() {
		global $msg;
		
		$options = array(
				'70' => $msg[203],
				'71' => $msg[204],
				'72' => $msg["congres_libelle"]
		);
		return $this->get_search_filter_simple_selection('', 'author_type', $msg['all'], $options);
	}
	
	protected function _get_object_property_author_type($object) {
		global $msg;
		
		switch ($object->author_type) {
			case '70':
				return $msg[203];
			case '71':
				return $msg[204];
			case '72':
				return $msg["congres_libelle"];
		}
	}

	protected function _get_query_human_author_type() {
		global $msg;
		
		if($this->filters['author_type']) {
			switch ($this->filters['author_type']) {
				case '70':
					return $msg[203];
				case '71':
					return $msg[204];
				case '72':
					return $msg["congres_libelle"];
			}
		}
		return '';
	}
}