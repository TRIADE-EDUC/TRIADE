<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_authorities_publishers_ui.class.php,v 1.2 2023/03/02 11:02:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_authorities_publishers_ui extends list_authorities_ui {
	
	protected function _get_query_base_select() {
		return parent::_get_query_base_select().", publishers.*";
	}
	
	protected function _get_query_base_from() {
		return parent::_get_query_base_from()." JOIN publishers ON authorities.num_object = publishers.ed_id AND authorities.type_object = " . AUT_TABLE_PUBLISHERS;
	}
	
	protected function get_main_fields() {
		return array(
				'ed_name' => 'editeur_nom',
				'ed_adr1' => 'editeur_adr1',
				'ed_adr2' => 'editeur_adr2',
				'ed_cp' => 'editeur_cp',
				'ed_ville' => 'editeur_ville',
				'ed_pays' => '146',
				'ed_web' => 'editeur_web',
				'ed_comment' => 'ed_comment'
		);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'ed_name' => 'editeur_nom',
						'ed_adr1' => 'editeur_adr1',
						'ed_adr2' => 'editeur_adr2',
						'ed_cp' => 'editeur_cp',
						'ed_ville' => 'editeur_ville',
						'ed_pays' => '146',
						'ed_web' => 'editeur_web',
						'ed_comment' => 'ed_comment',
				)
		);
		$this->available_filters['custom_fields'] = array();
		$this->add_custom_fields_available_filters('publisher', 'ed_id');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		parent::init_available_columns();
		$this->add_custom_fields_available_columns('publisher', 'ed_id');
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('ed_name');
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'ed_name' => '',
				'ed_adr1' => '',
				'ed_adr2' => '',
				'ed_cp' => '',
				'ed_ville' => '',
				'ed_pays' => '',
				'ed_web' => '',
				'ed_comment' => '',
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('ed_name');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('ed_name');
		$this->set_filter_from_form('ed_adr1');
		$this->set_filter_from_form('ed_adr2');
		$this->set_filter_from_form('ed_cp');
		$this->set_filter_from_form('ed_ville');
		$this->set_filter_from_form('ed_pays');
		$this->set_filter_from_form('ed_web');
		$this->set_filter_from_form('ed_comment');
		parent::set_filters_from_form();
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('ed_name', 'ed_name');
		$this->_add_query_filter_simple_restriction('ed_adr1', 'ed_adr1');
		$this->_add_query_filter_simple_restriction('ed_adr2', 'ed_adr2');
		$this->_add_query_filter_simple_restriction('ed_cp', 'ed_cp');
		$this->_add_query_filter_simple_restriction('ed_ville', 'ed_ville');
		$this->_add_query_filter_simple_restriction('ed_pays', 'ed_pays');
		$this->_add_query_filter_simple_restriction('ed_web', 'ed_web');
		$this->_add_query_filter_simple_restriction('ed_comment', 'ed_comment');
	}
}