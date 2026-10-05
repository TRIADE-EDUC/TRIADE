<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_suggestions_empr_ui.class.php,v 1.7 2023/09/29 09:54:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_suggestions_empr_ui extends list_ui {
		
	protected $suggestions_map;
	
	protected function get_form_title() {
		global $msg, $charset;
		
		return htmlentities($msg['acquisition_sugg_list_lecteur'], ENT_QUOTES, $charset);
	}
	
	protected function _get_query_base() {
		$query = "select count(id_suggestion) as nb, concat(empr_nom,' ',empr_prenom) as name, id_empr as id, empr_location from suggestions 
				JOIN suggestions_origine ON suggestions.id_suggestion=suggestions_origine.num_suggestion
				JOIN empr ON suggestions_origine.origine=empr.id_empr AND suggestions_origine.type_origine=1";
		return $query;
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'state' => 'acquisition_sugg_filtre_by_etat',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'state' => -1,
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('state');
	}
	
	protected function get_search_filter_state() {
		return $this->get_suggestions_map()->getStateSelector($this->filters['state']);
	}
	
	protected function _add_query_filters() {
		if($this->filters['state'] && $this->filters['state'] != '-1') {
			$this->query_filters [] = "statut='".$this->filters['state']."'";
		}
	}
			
	/**
	 * Objet de la liste
	 */
	protected function get_display_content_object_list($object, $indice) {
		if(!isset($this->is_editable_object_list)) {
			$this->is_editable_object_list = true;
		}
		return parent::get_display_content_object_list($object, $indice);
	}
	
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg['acquisition_sugg_no_state_lecteur'], ENT_QUOTES, $charset);
	}
	
	protected function _get_query_human_state() {
		if($this->filters['state'] && $this->filters['state'] != '-1') {
			$states = $this->get_suggestions_map()->getStateList();
			return $states[$this->filters['state']];
		}
		return '';
	}
	
	protected function _get_query_human() {
		$humans = $this->_get_query_human_main_fields();
		return $this->get_display_query_human($humans);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'name' => 'acquisition_sugg_lecteur',
						'nb' => 'acquisition_sugg_nb',
				)
		);
	}
	
	protected function init_default_columns() {
		$this->add_column('name');
		$this->add_column('nb');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('search_form', 'unfolded_filters', true);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('name');
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    return $sort_by;
	}
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
	    return " group by name ".parent::_get_query_order();
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$state = 'statut';
		global ${$state};
		if(isset(${$state})) {
			$this->filters['state'] = ${$state};
		}
		parent::set_filters_from_form();
	}
	
	protected function get_edition_link($object) {
		global $base_path;
		
		return $base_path.'/acquisition.php?categ=sug&action=list&user_id[]='.$object->id.'&user_statut[]=1&sugg_location_id='.$object->empr_location;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
	
		return $base_path.'/acquisition.php?categ=sug&sub=empr_sug';
	}
	
	public static function get_ajax_controller_url_base() {
		global $base_path, $current_module;
		return $base_path.'/ajax.php?module='.$current_module.'&categ=sugg';
	}
	
	public function get_suggestions_map() {
		if(!isset($this->suggestions_map)) {
			$this->suggestions_map = new suggestions_map();
		}
		return $this->suggestions_map;
	}
}