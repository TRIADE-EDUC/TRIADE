<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_bannettes_ui.class.php,v 1.1 2023/11/28 10:27:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_opac_bannettes_ui extends list_opac_ui {
	
	protected function _get_query_base() {
		$query = 'select id_bannette, IF(comment_public <> "", comment_public, nom_bannette) AS label FROM bannettes ';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new bannette($row->id_bannette);
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'name':
	            return 'label';
	        case 'comment_public':
	            return 'comment_public';
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('name');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'name' => 'dsi_bannette_gerer_nom_liste',
				        'send_last_date' => 'dsi_bannette_gerer_date',
						'number_records' => 'dsi_bannette_gerer_nb_notices',
						'periodicity' => 'dsi_bannette_gerer_periodicite',
						'date_used_to_calc' => 'dsi_bannette_gerer_date_used_to_calc',
				        'nom_classement_opac' => 'dsi_clas_type_class_BAN'
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
	    global $opac_private_bannette_date_used_to_calc;
	    
	    $this->add_column_selection();
		$this->add_column('name');
		$this->add_column('send_last_date');
		$this->add_column('number_records');
		$this->add_column('periodicity');
		if($opac_private_bannette_date_used_to_calc == 2) {
		    $this->add_column('date_used_to_calc');
		}
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_column('name', 'align', 'left');
		$this->set_setting_column('send_last_date', 'datatype', 'datetime');
		$this->set_setting_column('number_records', 'datatype', 'integer');
		$this->set_setting_column('periodicity', 'datatype', 'integer');
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'name' => 'dsi_bannette_gerer_nom_liste',
				        'id_classement' => 'dsi_classement',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'auto' => '',
                'id_classement' => '',
				'name' => '',
				'proprio_bannette' => '',
				'type' => '',
		        'num_empr' => ''
		);
		parent::init_filters($filters);
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('name');
		$this->set_filter_from_form('id_classement');
		parent::set_filters_from_form();
	}
	
	protected function get_search_filter_name() {
		return $this->get_search_filter_simple_text('name');
	}
	
	protected function _add_query_filters() {
		if($this->filters['auto'] !== '') {
			$this->query_filters [] = 'bannette_auto = "'.$this->filters['auto'].'"';
		}
		if($this->filters['name']) {
			$this->query_filters [] = 'nom_bannette like "%'.str_replace("*", "%", addslashes($this->filters['name'])).'%"';
		}
		if($this->filters['num_empr'] != '') {
			$this->query_filters [] = 'num_empr = "'.$this->filters['num_empr'].'"';
		}
		if($this->filters['proprio_bannette'] !== '') {
			$this->query_filters [] = 'proprio_bannette = "'.$this->filters['proprio_bannette'].'"';
		}
	}
	
	protected function _get_object_property_number_records($object) {
		return $object->nb_notices;
	}
	
	protected function _get_object_property_periodicity($object) {
	    return $object->periodicite;
	}
	
	protected function _get_object_property_send_last_date($object) {
		return $object->aff_date_last_envoi;
	}
	
	protected function _get_object_property_date_used_to_calc($object) {
	    global $msg;
	    return $msg['dsi_ban_update_type_'.strtolower($object->update_type)];
	}
	
	protected function _get_object_property_nom_classement_opac($object) {
	    $classement = new classement($object->num_classement);
	    return $classement->nom_classement_opac;
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'name':
			    //TODO : Lien autour du label
			    
// 				$content .= "";
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
}