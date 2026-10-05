<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_groups_ui.class.php,v 1.15.4.1 2025/02/20 15:31:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/group.class.php");

class list_groups_ui extends list_ui {
	
	protected function _get_query_base() {
		$query = 'SELECT DISTINCT id_groupe FROM groupe';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new group($row->id_groupe);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		global $empr_groupes_localises;
		
		$this->available_filters =
			array('main_fields' =>
					array(
							'name' => '908',
					)
			);
		if ($empr_groupes_localises){
			$this->available_filters['main_fields']['locations'] = 'editions_filter_empr_location';
		}
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
                'name' => '*',
				'locations' => array()
		);
		parent::init_filters($filters);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
	    $this->available_columns =
	    array('main_fields' =>
	    		array(
	    				'libelle' => '904',
	    				'libelle_resp' => '913',
	    				'nb_members' => 'circ_group_emprunteur',
	    				'nb_loans' => '349',
	    				'nb_resas' => 'reserv_en_cours',
	    				'nb_loans_late' => 'nb_loans_late',
	    				'nb_loans_including_late' => 'nb_loans_including_late'
	    		)
	    );
	}
	
	protected function init_default_columns() {
		$this->add_column('libelle');
		$this->add_column('libelle_resp');
		$this->add_column('nb_members');
		$this->add_column('nb_loans');
		$this->add_column('nb_resas');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_column('nb_members', 'datatype', 'integer');
		$this->set_setting_column('nb_loans', 'datatype', 'integer');
		$this->set_setting_column('nb_loans_late', 'datatype', 'integer');
		$this->set_setting_column('nb_resas', 'datatype', 'integer');
	}
	
	protected function init_default_selected_filters() {
		global $empr_groupes_localises;
		
		$this->add_selected_filter('name');
		if($empr_groupes_localises) {
			$this->add_selected_filter('locations');
		}
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('libelle');
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
		global $nb_per_page_author;
		
		parent::init_default_pager();
		if ($nb_per_page_author != "") {
			$this->pager['nb_per_page'] = $nb_per_page_author;
		}
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'id':
	            return 'id_groupe';
	        case 'libelle' :
	            return 'libelle_groupe';
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		global $group_location_id;
		
		$this->set_filter_from_form('name');
		if(isset($group_location_id)) {
			$this->filters['locations'] = $group_location_id;
		}
		parent::set_filters_from_form();
	}
	
	protected function get_search_filter_name() {
		return $this->get_search_filter_simple_text('name');
	}
	
	protected function get_search_filter_locations() {
		return group::gen_combo_box_grp($this->filters['locations'],1);
	}
	
	protected function get_button_add() {
		global $msg;
		
		return $this->get_interface_button($msg['909'], ['location' => './circ.php?categ=groups&action=create']);
	}
	
	/**
	 * Jointure externes SQL pour les besoins des filtres
	 */
	protected function _get_query_join_filters() {
		$filter_join_query = '';
		if(is_array($this->filters['locations']) && count($this->filters['locations'])) {
			$filter_join_query .= " LEFT JOIN empr ON groupe.resp_groupe=empr.id_empr";
		}
		return $filter_join_query;
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		global $empr_groupes_localises;
		
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if($this->filters['name']) {
			$filters [] = 'libelle_groupe like "%'.str_replace("*", "%", addslashes($this->filters['name'])).'%"';
		}
		if($empr_groupes_localises && array_key_exists('locations', $this->filters) && is_array($this->filters['locations']) && count($this->filters['locations'])) {
			//Toutes les localisations sélectionnées
			if (!in_array('-1',$this->filters['locations'])) {
				//Aucune localisation
				if (in_array('-2',$this->filters['locations'])) {
					$filters [] = '(empr_location IN ('.implode(',', $this->filters['locations']).') OR empr_location IS NULL)';
				} else {
					$filters [] = 'empr_location IN ('.implode(',', $this->filters['locations']).')';
				}
			}
		}
		
		if(count($filters)) {
			$filter_query .= $this->_get_query_join_filters();
			$filter_query .= $this->_get_query_join_custom_fields_filters('empr', 'id_empr');
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		global $base_path;
		
		$attributes = array();
		$attributes['onclick'] = "window.location=\"".$base_path."/circ.php?categ=groups&action=showgroup&groupID=".$object->id."\"";
		return $attributes;
	}
	
	public function get_error_message_empty_list() {
		global $msg, $charset;
		
		return str_replace('!!group_cle!!', htmlentities(stripslashes($this->filters['name']),ENT_QUOTES, $charset), $msg[918]);
	}
	
	protected function _get_query_property_filter($property) {
		switch ($property) {
			case 'locations':
				return "select location_libelle from docs_location where idlocation IN (".implode(',', $this->filters[$property]).")";
		}
		return '';
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		global $categ, $action;
		
		return $base_path.'/circ.php?categ='.$categ.'&action='.$action;
	}
}