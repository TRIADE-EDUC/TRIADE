<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_rss_ui.class.php,v 1.2 2019-05-17 10:59:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/list/list_ui.class.php');
require_once($class_path.'/rss_flux.class.php');

class list_rss_ui extends list_ui {
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function _get_query_base() {
		$query = 'SELECT id_rss_flux FROM rss_flux';
		return $query;
	}
	
	protected function add_object($row) {
		$this->objects[] = new rss_flux($row->id_rss_flux);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		global $pmb_lecteurs_localises;
	
		$this->available_filters =
		array('main_fields' =>
				array(
						'nom_rss_flux' => 'dsi_flux_search_nom',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'nom_rss_flux' => '',
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('nom_rss_flux');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
					'nom_rss_flux' => 'dsi_flux_form_nom',
					'nb_paniers' => 'dsi_flux_nb_paniers',
					'nb_bannettes' => 'dsi_flux_nb_bannettes',
					'permalink' => 'dsi_flux_link'
			)
		);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'nom_rss_flux',
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
				case 'id':
					$order .= 'id_rss_flux';
					break;
				case 'name' :
					$order .= $sort_by;
					break;
				default :
					$order .= parent::_get_query_order();
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
	
	protected function get_form_title() {
		global $msg, $charset;
		return htmlentities($msg['dsi_flux_search'], ENT_QUOTES, $charset);
	}
	
	public function get_export_icons() {
		return "
		";
	}
	
	protected function get_button_add() {
		global $msg;
		
		return "<input type='button' class='bouton' value='".$msg['ajouter']."' onClick=\"document.location='".static::get_controller_url_base().'&suite=add'."';\" />";
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		$search_form = parent::get_search_form();
		$search_form = str_replace('!!action!!', static::get_controller_url_base(), $search_form);
		return $search_form;
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$nom_rss_flux = $this->objects_type.'_nom_rss_flux';
		global ${$nom_rss_flux};
		if(isset(${$nom_rss_flux})) {
			$this->filters['nom_rss_flux'] = stripslashes(${$nom_rss_flux});
		}
		parent::set_filters_from_form();
	}
	
	protected function init_default_columns() {
		$this->add_column('nom_rss_flux');
		$this->add_column('nb_paniers');
		$this->add_column('nb_bannettes');
		$this->add_column('permalink');
	}
	
	protected function get_search_filter_nom_rss_flux() {
		global $msg, $charset;
	
		return "<input class='saisie-30em' id='".$this->objects_type."_name' type='text' name='".$this->objects_type."_nom_rss_flux' value=\"".htmlentities($this->filters['nom_rss_flux'], ENT_QUOTES, $charset)."\" title='".$msg['3000']."' />";
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if($this->filters['nom_rss_flux']) {
			$filters [] = 'nom_rss_flux like "%'.str_replace("*", "%", $this->filters['nom_rss_flux']).'%"';
		}
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
	
	protected function _get_query_human() {
		global $msg, $charset;
	
		$humans = array();
		if($this->filters['nom_rss_flux']) {
			$humans[] = $this->_get_label_query_human($msg['dsi_flux_search_nom'], $this->filters['nom_rss_flux']);
		}
		if(!count($humans)) {
			$humans[] = "<b>".htmlentities($msg['list_ui_no_filter'], ENT_QUOTES, $charset)."</b>";
		}
		return $this->get_display_query_human($humans);;
	}
	
	protected function get_js_sort_script_sort() {
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'fluxrss', $display);
		$display = str_replace('!!sub!!', '', $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	protected function get_cell_content($object, $property) {
		global $opac_url_base;
		
		$content = '';
		switch($property) {
			case 'nom_rss_flux':
				$content .= "<strong>".parent::get_cell_content($object, $property)."</strong>";
				break;
			case 'permalink':
				$content .= "<a href='".$opac_url_base."rss.php?id=".$object->id_rss_flux."' target='_blank'>".$opac_url_base."rss.php?id=".$object->id_rss_flux."</a>";
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_display_cell($object, $property) {
		switch($property) {
			case 'permalink':
				$display = "<td>".$this->get_cell_content($object, $property)."</td>";
				break;
			default:
				$display = "<td class='center' onclick=\"window.location='".static::get_controller_url_base()."&action=view&suite=acces&id_rss_flux=".$object->id_rss_flux."'\" style='cursor:pointer;'>".$this->get_cell_content($object, $property)."</td>";
				break;
		}
		return $display;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/dsi.php?categ=fluxrss&sub=definition';
	}
}