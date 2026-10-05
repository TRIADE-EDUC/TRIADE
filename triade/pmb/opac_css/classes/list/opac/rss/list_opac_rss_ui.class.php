<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_rss_ui.class.php,v 1.3 2023/09/29 08:31:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/rss_flux.class.php');

class list_opac_rss_ui extends list_opac_ui {

	protected function _get_query_base() {
		$query = 'SELECT id_rss_flux FROM rss_flux';
		return $query;
	}

	protected function get_object_instance($row) {
		return new rss_flux($row->id_rss_flux);
	}

	protected function add_object($row) {
	    global $opac_view_filter_class;

	    if(empty($opac_view_filter_class) || ($opac_view_filter_class && $opac_view_filter_class->is_selected("flux_rss", $row->id_rss_flux))){
            parent::add_object($row);
	    }
	}

	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
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
                'id' => 0,
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
			        'img_url_rss_flux' => '',
					'nom_rss_flux' => 'dsi_flux_form_nom',
					'permalink' => 'dsi_flux_link'
			)
		);
	}

	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('nom_rss_flux');
	}

	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'id':
	            return 'id_rss_flux';
	        case 'name' :
	            return $sort_by;
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}

	protected function get_form_title() {
		global $msg, $charset;
		return htmlentities($msg['dsi_flux_search'], ENT_QUOTES, $charset);
	}

	public function get_display_header_list() {
	    return '';
	}

	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('nom_rss_flux');
		parent::set_filters_from_form();
	}

	protected function init_default_columns() {
	    $this->add_column('img_url_rss_flux');
	    $this->add_column('nom_rss_flux');
		$this->add_column('permalink');
	}

	protected function init_default_pager() {
	    parent::init_default_pager();
	    $this->pager['all_on_page'] = true;
	}

	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_column('nom_rss_flux', 'align', 'left');
		$this->set_setting_column('permalink', 'align', 'left');
		$this->set_setting_column('nom_rss_flux', 'text', array('strong' => true));
		$this->set_setting_display('pager', 'visible', false);
	}

	protected function get_search_filter_nom_rss_flux() {
		return $this->get_search_filter_simple_text('nom_rss_flux');
	}

	protected function _add_query_filters() {
	    $this->_add_query_filter_simple_restriction('id', 'id_rss_flux', 'integer');
		if($this->filters['nom_rss_flux']) {
			$this->query_filters [] = 'nom_rss_flux like "%'.str_replace("*", "%", $this->filters['nom_rss_flux']).'%"';
		}
	}

	protected function get_cell_content($object, $property) {
		global $msg, $charset, $opac_url_base;

		$content = '';
		switch($property) {
		    case 'img_url_rss_flux':
		        if ($object->img_url_rss_flux) {
		            $content .= "<a href=\"index.php?lvl=rss_see&id=".$object->id_rss_flux."\"><img src='".$object->img_url_rss_flux."' /></a>";
		        }
		        break;
		    case 'nom_rss_flux':
		        $content .= "<a href=\"index.php?lvl=rss_see&id=".$object->id_rss_flux."\">".htmlentities($object->nom_rss_flux,ENT_QUOTES, $charset)."</a>";
		        break;
			case 'permalink':
			    $content .= "<a href=\"".$opac_url_base."rss.php?id=".$object->id_rss_flux."\" title=\"".$msg['abonne_rss_dispo']."\"><img id=\"rss_logo\" alt='rss' src='".get_url_icon('rss.png', 1)."' /></a>";
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}

	protected function get_default_attributes_format_cell($object, $property) {
	    $attributes = array();
	    switch($property) {
	        case 'img_url_rss_flux':
	            $attributes['style'] = 'width:10%;';
	            break;
	        default:
	            break;
	    }
	    return $attributes;
	}

	protected function get_class_objects_list() {
	    return "rss_list_table ".parent::get_class_objects_list();
	}
}