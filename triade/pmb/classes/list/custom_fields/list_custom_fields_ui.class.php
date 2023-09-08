<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_custom_fields_ui.class.php,v 1.10 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/list_ui.class.php");
require_once($include_path.'/templates/list/custom_fields/list_custom_fields_ui.tpl.php');

class list_custom_fields_ui extends list_ui {
		
	protected static $prefix;
	
	protected static $option_visibilite;
	
	public static function set_prefix($prefix) {
		static::$prefix = $prefix;
	}
	
	public static function set_option_visibilite($option_visibilite) {
		static::$option_visibilite = $option_visibilite;
	}
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	public function get_form_title() {
		return '';
	}
	
	protected function _get_query_base() {
		$query = "select idchamp as id, name, titre, type, datatype, multiple, obligatoire, ordre ,search, export,exclusion_obligatoire, opac_sort, comment, custom_classement from
				".static::$prefix."_custom";
		return $query;
	}
	
	protected function add_object($row) {
		$this->objects[] = $row;
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'input_type' => 'parperso_input_type',
						'data_type' => 'parperso_data_type',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $pmb_lecteurs_localises, $deflt2docs_location;
		
		$this->filters = array(
			'input_type' => array(),
			'data_type' => array()
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('input_type');
		$this->add_selected_filter('data_type');
	}
	
	/**
	 * Initialisation du groupement appliqué à la recherche
	 */
	public function init_applied_group($applied_group=array()) {
		if(!isset($this->applied_group)) {
			$this->applied_group = array(0 => 'custom_classement');
		}
		parent::init_applied_group($applied_group);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'name' => 'parperso_field_name',
						'titre' => 'parperso_field_title',
						'type' => 'parperso_input_type',
						'datatype' => 'parperso_data_type',
						'custom_classement' => 'parperso_field_classement'
				)
		);
		if(isset(static::$option_visibilite["multiple"]) && static::$option_visibilite["multiple"] == "block") {
			$this->available_columns['main_fields']['multiple'] = 'parperso_opac_visibility';
		}
		if(isset(static::$option_visibilite["opac_sort"]) && static::$option_visibilite["opac_sort"] == "block") {
			$this->available_columns['main_fields']['opac_sort'] = 'parperso_opac_sort';
		}
		if(isset(static::$option_visibilite["obligatoire"]) && static::$option_visibilite["obligatoire"] == "block") {
			$this->available_columns['main_fields']['obligatoire'] = 'parperso_mandatory';
		}
		if(isset(static::$option_visibilite["filters"]) && static::$option_visibilite["filters"] == "block") {
			$this->available_columns['main_fields']['filters'] = 'parperso_opac_filters';
		}
		if(isset(static::$option_visibilite["search"]) && static::$option_visibilite["search"] == "block") {
			$this->available_columns['main_fields']['search'] = 'parperso_field_search_tableau';
		}
		if(isset(static::$option_visibilite["export"]) && static::$option_visibilite["export"] == "block") {
			$this->available_columns['main_fields']['export'] = 'parperso_exportable';
		}
		if(isset(static::$option_visibilite["exclusion"]) && static::$option_visibilite["exclusion"] == "block") {
			$this->available_columns['main_fields']['exclusion_obligatoire'] = 'parperso_exclusion_entete';
		}
	}
	
	protected function add_column_dnd() {
		global $msg;
		
		$this->columns[] = array(
				'property' => 'ordre',
				'label' => $msg['parperso_options_list_order'],
				'html' => "<input type='button' class='bouton_small' value='-' onClick='document.location=\"".static::get_controller_url_base()."&action=up&id=!!id!!\"' />
					<input type='button' class='bouton_small' value='+' onClick='document.location=\"".static::get_controller_url_base()."&action=down&id=!!id!!\"' />"
		);
	}
	
	protected function init_default_columns() {
		global $pmb_gestion_amende, $pmb_gestion_financiere;
		global $sub;
	
		$this->add_column_dnd();
		$this->add_column('name');
		$this->add_column('titre');
		$this->add_column('type');
		$this->add_column('datatype');
		if(isset(static::$option_visibilite["multiple"]) && static::$option_visibilite["multiple"] == "block") {
			$this->add_column('multiple');
		}
		if(isset(static::$option_visibilite["opac_sort"]) && static::$option_visibilite["opac_sort"] == "block") {
			$this->add_column('opac_sort');
		}
		if(isset(static::$option_visibilite["obligatoire"]) && static::$option_visibilite["obligatoire"] == "block") {
			$this->add_column('obligatoire');
		}
		if(isset(static::$option_visibilite["filters"]) && static::$option_visibilite["filters"] == "block") {
			$this->add_column('filters');
		}
		if(isset(static::$option_visibilite["search"]) && static::$option_visibilite["search"] == "block") {
			$this->add_column('search');
		}
		if(isset(static::$option_visibilite["export"]) && static::$option_visibilite["export"] == "block") {
			$this->add_column('export');
		}
		if(isset(static::$option_visibilite["exclusion"]) && static::$option_visibilite["exclusion"] == "block") {
			$this->add_column('exclusion_obligatoire');
		}
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
		global $nb_per_page_empr;
		$this->pager = array(
				'page' => 1,
				'nb_per_page' => 100,
				'nb_results' => 0,
				'nb_page' => 1
		);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'ordre',
				'asc_desc' => 'asc'
		);
	}
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
	
		if($this->applied_sort['by']) {
			$order = '';
			$sort_by = $this->applied_sort['by'];
			switch($sort_by) {
				default :
					$order .= $sort_by;
					break;
			}
			if($order) {
				$this->applied_sort_type = 'SQL';
				return " order by ".$order." ".$this->applied_sort['asc_desc'];
			} else {
				return "";
			}
		}
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {

		$input_type = $this->objects_type.'_input_type';
		global ${$input_type};
		if(isset(${$input_type})) {
			$this->filters['input_type'] = array();
			if(${$input_type}[0] != '') {
				$this->filters['input_type'] = stripslashes_array(${$input_type});
			}
		}
		$data_type = $this->objects_type.'_data_type';
		global ${$data_type};
		if(isset(${$data_type})) {
			$this->filters['data_type'] = array();
			if(${$data_type}[0] != '') {
				$this->filters['data_type'] = stripslashes_array(${$data_type});
			}
		}
		parent::set_filters_from_form();
	}
	
	/**
	 * Liste des types
	 */
	protected function get_search_filter_input_type() {
		global $msg, $charset;
		global $type_list_empr;
		
		$selector = "<select name='".$this->objects_type."_input_type[]' multiple='3'>";
		reset($type_list_empr);
		foreach ($type_list_empr as $key => $val) {
			$selector .= "<option value='".$key."' ".(in_array($key, $this->filters['input_type']) ? "selected='selected'" : "").">".htmlentities($val,ENT_QUOTES, $charset)."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}
	
	/**
	 * Liste des types de données
	 */
	protected function get_search_filter_data_type() {
		global $msg, $charset;
		global $datatype_list;
	
		$selector = "<select name='".$this->objects_type."_data_type[]' multiple='3'>";
		reset($datatype_list);
		foreach ($datatype_list as $key => $val) {
			$selector .= "<option value='".$key."' ".(in_array($key, $this->filters['data_type']) ? "selected='selected'" : "").">".htmlentities($val,ENT_QUOTES, $charset)."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}
	
	public function get_export_icons() {
		return "";
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		$this->is_displayed_options_block = true;
		$search_form = parent::get_search_form();
		$search_form = str_replace('!!action!!', static::get_controller_url_base(), $search_form);
		return $search_form;
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
		
	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'custom_fields', $display);
		$display = str_replace('!!sub!!', "&prefix=".static::$prefix."&option_visibilite=".encoding_normalize::json_encode(static::$option_visibilite), $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		
		$content = '';
		switch($property) {
			case 'name':
				$content .= "<b>".$object->{$property}."</b>";
				break;
			case 'type':
				global $type_list_empr;
				$content .= $type_list_empr[$object->type];
				break;
			case 'datatype':
				global $datatype_list;
				$content .= $datatype_list[$object->datatype];
				break;
			case 'multiple':
			case 'opac_sort':
			case 'obligatoire':
			case 'search':
			case 'export':
			case 'exclusion_obligatoire':
			case 'filters':
				if ($object->{$property}==1) {
					$content .= $msg["40"];
				} else {
					$content .= $msg["39"];
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function _get_query_human() {
		global $msg, $charset;
		
		$humans = array();
		return $this->get_display_query_human($humans);
	}
	
	protected function get_display_cell($object, $property) {
		$display = "<td onclick=\"window.location='".static::get_controller_url_base()."&action=edit&id=".$object->id."'\" style='cursor:pointer;'>".$this->get_cell_content($object, $property)."</td>";
		return $display;
	}
	
	/**
	 * Affiche la recherche + la liste
	 */
	public function get_display_list() {
		global $msg;
		
		$display = parent::get_display_list();
		$display .= "<div><input type='button' class='bouton' value='".$msg['parperso_new_field']."' onClick='document.location=\"".static::get_controller_url_base()."&action=nouv\"'/></div>";
		return $display;
	}
	
	public static function get_controller_url_base() {
		global $base_path, $base_auth;
		global $categ, $sub;
		global $type_field;
		
		if($base_auth == 'FICHES_AUTH') {
			return $base_path.'/fichier.php?categ=gerer&mode=champs';
		}
		return $base_path.'/admin.php?categ='.$categ.'&sub='.$sub.($type_field ? '&type_field='.$type_field : '');
	}
}