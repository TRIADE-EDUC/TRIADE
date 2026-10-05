<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_fiches_ui.class.php,v 1.9 2023/03/24 07:44:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/fiche.class.php");

class list_fiches_ui extends list_ui {
	
	protected $i_search;
	
	protected function _get_query_base() {
		$query = 'select id_fiche from fiche ';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new fiche($row->id_fiche);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('id_fiche', 'desc');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'id_fiche' => '',
				)
		);
		$this->available_columns['custom_fields'] = array();
		$this->add_custom_fields_available_columns('gestfic0', 'id_fiche');
	}
	
	protected function add_custom_fields_available_columns($type, $property_id) {
		foreach ($this->get_custom_parameters_instance($type)->t_fields as $field) {
			//Uniquement multiple=1 (visible OPAC)
			if(!empty($field['OPAC_SHOW'])) {
				$this->available_columns['custom_fields'][$field['NAME']] = $field['TITRE'];
				$this->custom_fields_available_columns[$field['NAME']] = array(
						'type' => $type,
						'property_id' => $property_id
				);
			}
		}
	}
	
	protected function get_form_title() {
		global $msg, $charset;
		return htmlentities($msg['fichier_search_list'], ENT_QUOTES, $charset);
	}
	
	protected function init_default_columns() {
		if(count($this->available_columns['custom_fields'])) {
			foreach ($this->available_columns['custom_fields'] as $property=>$label) {
				$this->add_column($property, $label);
			}
		}
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'global_search' => 'fichier_saisie_label',
				)
		);
		$this->available_filters['custom_fields'] = array();
// 		$this->add_custom_fields_available_filters('gestfic0', 'id_fiche');
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'global_search' => '',
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('global_search');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$global_search = $this->objects_type.'_global_search';
		global ${$global_search};
		if(isset(${$global_search}) && ${$global_search} != '') {
			$this->filters['global_search'] = ${$global_search};
		}
		parent::set_filters_from_form();
	}
	
	protected function get_search_filter_global_search() {
		global $charset;
		
		return "<input type='text' name='".$this->objects_type."_global_search' class='saisie-50em' value='".htmlentities($this->filters['global_search'], ENT_QUOTES, $charset)."' />";
	}
	
	protected function _add_query_filters() {
		if($this->filters['global_search']) {
			$global_search = str_replace("*", "%",$this->filters['global_search']);
			$this->query_filters [] = 'infos_global like "%'.$global_search.'%" or index_infos_global like "%'.$global_search.'%"';
		}
	}
			
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities(sprintf($msg["fichier_no_result_found"], $this->filters['global_search']), ENT_QUOTES, $charset);
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		if(empty($this->i_search)) {
			$this->i_search = (($this->pager['page']-1)*$this->pager['nb_per_page']);
		}
		$attributes = array();
		$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&sub=view&idfiche=".$object->id_fiche."&i_search=".($this->i_search++)."\"";
		return $attributes;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/fichier.php?categ=consult&mode=search';
	}
}