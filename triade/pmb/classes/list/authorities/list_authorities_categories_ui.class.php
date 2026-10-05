<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_authorities_categories_ui.class.php,v 1.2 2023/03/02 11:02:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_authorities_categories_ui extends list_authorities_ui {
	
	protected function _get_query_base_select() {
		return parent::_get_query_base_select().", categories.*";
	}
	
	protected function _get_query_base_from() {
		return parent::_get_query_base_from()." JOIN categories ON authorities.num_object = categories.num_noeud AND authorities.type_object = " . AUT_TABLE_CATEG;
	}
	
	protected function get_main_fields() {
		return array(
				'libelle_categorie' => '103',
				'note_application' => 'categ_na',
				'comment_public' => 'categ_commentaire'
		);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'libelle_categorie' => '103',
						'note_application' => 'categ_na',
						'comment_public' => 'categ_commentaire',
				)
		);
		$this->available_filters['custom_fields'] = array();
		$this->add_custom_fields_available_filters('categ', 'num_noeud');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		parent::init_available_columns();
		$this->add_custom_fields_available_columns('categ', 'num_noeud');
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('libelle_categorie');
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'libelle_categorie' => '',
				'note_application' => '',
				'comment_public' => '',
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('libelle_categorie');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('libelle_categorie');
		$this->set_filter_from_form('note_application');
		$this->set_filter_from_form('comment_public');
		parent::set_filters_from_form();
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('libelle_categorie', 'libelle_categorie');
		$this->_add_query_filter_simple_restriction('note_application', 'note_application');
		$this->_add_query_filter_simple_restriction('comment_public', 'comment_public');
	}
}