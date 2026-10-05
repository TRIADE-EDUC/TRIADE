<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_authorities_titres_uniformes_ui.class.php,v 1.2 2023/03/02 11:02:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_authorities_titres_uniformes_ui extends list_authorities_ui {
	
	protected function _get_query_base_select() {
		return parent::_get_query_base_select().", titres_uniformes.*";
	}
	
	protected function _get_query_base_from() {
		return parent::_get_query_base_from()." JOIN titres_uniformes ON authorities.num_object = titres_uniformes.tu_id AND authorities.type_object = " . AUT_TABLE_TITRES_UNIFORMES;
	}
	
	protected function get_main_fields() {
		return array(
				'tu_name' => 'aut_titre_uniforme_form_nom',
				'tu_comment' => 'aut_titre_uniforme_commentaire',
				'tu_sujet' => 'aut_oeuvre_form_sujet',
				'tu_lieu' => 'aut_oeuvre_form_lieu',
				'tu_histoire' => 'aut_oeuvre_form_histoire',
				'tu_caracteristique' => 'aut_oeuvre_form_caracteristique',
				'tu_public' => 'aut_oeuvre_form_public',
				'tu_coordonnees' => 'aut_oeuvre_form_coordonnees',
				'tu_equinoxe' => 'aut_oeuvre_form_equinoxe',
				'tu_tonalite' => 'aut_titre_uniforme_form_tonalite',
				'tu_tonalite_marclist' => 'aut_titre_uniforme_form_tonalite_list',
				'tu_forme' => 'aut_oeuvre_form_forme',
				'tu_forme_marclist' => 'aut_oeuvre_form_forme_list',
				'tu_oeuvre_nature' => 'aut_oeuvre_form_oeuvre_nature',
				'tu_oeuvre_type' => 'aut_oeuvre_form_oeuvre_type'
		);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'tu_name' => 'aut_titre_uniforme_form_nom',
						'tu_comment' => 'aut_titre_uniforme_commentaire',
// 						'tu_num_author' => 'tu_num_author',
						'tu_date_date' => 'aut_oeuvre_form_date',
						'tu_sujet' => 'aut_oeuvre_form_sujet',
						'tu_lieu' => 'aut_oeuvre_form_lieu',
						'tu_histoire' => 'aut_oeuvre_form_histoire',
						'tu_caracteristique' => 'aut_oeuvre_form_caracteristique',
						'tu_public' => 'aut_oeuvre_form_public',
						'tu_coordonnees' => 'aut_oeuvre_form_coordonnees',
						'tu_equinoxe' => 'aut_oeuvre_form_equinoxe',
						'tu_tonalite' => 'aut_titre_uniforme_form_tonalite',
						'tu_tonalite_marclist' => 'aut_titre_uniforme_form_tonalite_list',
						'tu_forme' => 'aut_oeuvre_form_forme',
						'tu_forme_marclist' => 'aut_oeuvre_form_forme_list',
						'tu_oeuvre_nature' => 'aut_oeuvre_form_oeuvre_nature',
						'tu_oeuvre_type' => 'aut_oeuvre_form_oeuvre_type',
				)
		);
		$this->available_filters['custom_fields'] = array();
		$this->add_custom_fields_available_filters('tu', 'tu_id');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		parent::init_available_columns();
		$this->add_custom_fields_available_columns('tu', 'tu_id');
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('tu_name');
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'tu_name' => '',
				'tu_comment' => '',
				'tu_num_author' => 0,
				'tu_date_date' => '',
				'tu_sujet' => '',
				'tu_lieu' => '',
				'tu_histoire' => '',
				'tu_caracteristique' => '',
				'tu_public' => '',
				'tu_coordonnees' => '',
				'tu_equinoxe' => '',
				'tu_tonalite' => '',
				'tu_tonalite_marclist' => '',
				'tu_forme' => '',
				'tu_forme_marclist' => '',
				'tu_oeuvre_nature' => '',
				'tu_oeuvre_type' => '',
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('tu_name');
		$this->add_selected_filter('tu_num_author');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('tu_name');
		$this->set_filter_from_form('tu_num_author', 'integer');
		$this->set_filter_from_form('tu_comment');
		$this->set_filter_from_form('tu_date_date');
		$this->set_filter_from_form('tu_sujet');
		$this->set_filter_from_form('tu_lieu');
		$this->set_filter_from_form('tu_histoire');
		$this->set_filter_from_form('tu_caracteristique');
		$this->set_filter_from_form('tu_public');
		$this->set_filter_from_form('tu_coordonnees');
		$this->set_filter_from_form('tu_equinoxe');
		$this->set_filter_from_form('tu_tonalite');
		$this->set_filter_from_form('tu_tonalite_marclist');
		$this->set_filter_from_form('tu_forme');
		$this->set_filter_from_form('tu_forme_marclist');
		$this->set_filter_from_form('tu_oeuvre_nature');
		$this->set_filter_from_form('tu_oeuvre_type');
		parent::set_filters_from_form();
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('tu_name', 'tu_name');
		$this->_add_query_filter_simple_restriction('tu_num_author', 'tu_num_author', 'integer');
		$this->_add_query_filter_simple_restriction('tu_comment', 'tu_comment');
		$this->_add_query_filter_simple_restriction('tu_date_date', 'tu_date_date');
		$this->_add_query_filter_simple_restriction('tu_sujet', 'tu_sujet');
		$this->_add_query_filter_simple_restriction('tu_lieu', 'tu_lieu');
		$this->_add_query_filter_simple_restriction('tu_histoire', 'tu_histoire');
		$this->_add_query_filter_simple_restriction('tu_caracteristique', 'tu_caracteristique');
		$this->_add_query_filter_simple_restriction('tu_public', 'tu_public');
		$this->_add_query_filter_simple_restriction('tu_coordonnees', 'tu_coordonnees');
		$this->_add_query_filter_simple_restriction('tu_equinoxe', 'tu_equinoxe');
		$this->_add_query_filter_simple_restriction('tu_tonalite', 'tu_tonalite');
		$this->_add_query_filter_simple_restriction('tu_tonalite_marclist', 'tu_tonalite_marclist');
		$this->_add_query_filter_simple_restriction('tu_forme', 'tu_forme');
		$this->_add_query_filter_simple_restriction('tu_forme_marclist', 'tu_forme_marclist');
		$this->_add_query_filter_simple_restriction('tu_oeuvre_nature', 'tu_oeuvre_nature');
		$this->_add_query_filter_simple_restriction('tu_oeuvre_type', 'tu_oeuvre_type');
	}
	
	protected function get_search_filter_tu_num_author() {
		
	}
	
	protected function get_search_filter_tu_tonalite_marclist() {
		return $this->get_search_filter_marclist_simple_selection('music_key', 'tu_tonalite_marclist');
	}
	
	protected function get_search_filter_tu_forme_marclist() {
		return $this->get_search_filter_marclist_simple_selection('music_form', 'tu_forme_marclist');
	}
	
	protected function get_search_filter_tu_oeuvre_nature() {
		return $this->get_search_filter_marclist_simple_selection('oeuvre_nature', 'tu_oeuvre_nature');
	}
	
	protected function get_search_filter_tu_oeuvre_type() {
		return $this->get_search_filter_marclist_simple_selection('oeuvre_type', 'tu_oeuvre_type');
	}
	
	protected function _get_object_property_tu_tonalite_marclist($object) {
		if(!empty($object->tu_tonalite_marclist)) {
			$marc_list_collection = marc_list_collection::get_instance('music_key');
			return $marc_list_collection->table[$object->tu_tonalite_marclist] ?? "";
		}
		return '';
	}
	
	protected function _get_object_property_tu_forme_marclist($object) {
		if(!empty($object->tu_forme_marclist)) {
			$marc_list_collection = marc_list_collection::get_instance('music_form');
			return $marc_list_collection->table[$object->tu_forme_marclist] ?? "";
		}
		return '';
	}
	
	protected function _get_object_property_tu_oeuvre_nature($object) {
		if(!empty($object->tu_oeuvre_nature)) {
			$marc_list_collection = marc_list_collection::get_instance('oeuvre_nature');
			return $marc_list_collection->table[$object->tu_oeuvre_nature] ?? "";
		}
		return '';
	}
	
	protected function _get_object_property_tu_oeuvre_type($object) {
		if(!empty($object->tu_oeuvre_type)) {
			$marc_list_collection = marc_list_collection::get_instance('oeuvre_type');
			return $marc_list_collection->table[$object->tu_oeuvre_type] ?? "";
		}
		return '';
	}
	
	protected function _get_query_human_tu_tonalite_marclist() {
		if($this->filters['tu_tonalite_marclist']) {
			$marc_list_collection = marc_list_collection::get_instance('music_key');
			return $marc_list_collection->table[$this->filters['tu_tonalite_marclist']] ?? "";
		}
		return '';
	}
	
	protected function _get_query_human_tu_forme_marclist() {
		if($this->filters['tu_forme_marclist']) {
			$marc_list_collection = marc_list_collection::get_instance('music_form');
			return $marc_list_collection->table[$this->filters['tu_forme_marclist']] ?? "";
		}
		return '';
	}
	
	protected function _get_query_human_tu_oeuvre_nature() {
		if($this->filters['tu_oeuvre_nature']) {
			$marc_list_collection = marc_list_collection::get_instance('oeuvre_nature');
			return $marc_list_collection->table[$this->filters['tu_oeuvre_nature']] ?? "";
		}
		return '';
	}
	
	protected function _get_query_human_tu_oeuvre_type() {
		if($this->filters['tu_oeuvre_type']) {
			$marc_list_collection = marc_list_collection::get_instance('oeuvre_type');
			return $marc_list_collection->table[$this->filters['tu_oeuvre_type']] ?? "";
		}
		return '';
	}
}