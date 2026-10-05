<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_readers_bannettes_ui.class.php,v 1.10 2023/03/24 07:44:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_readers_bannettes_ui extends list_readers_ui {
	
	protected function _get_query_base() {
		$query = parent::_get_query_base();
		$query .= ' JOIN bannette_abon on id_empr=num_empr
			JOIN bannettes on num_bannette=id_bannette AND  proprio_bannette=id_empr';
		return $query;
	}
	
	protected function get_form_title() {
		global $msg, $charset;
		
		return htmlentities($msg['circ_tit_form_cb_empr'], ENT_QUOTES, $charset);
	}
	
	public function init_filters($filters=array()) {
		global $param_allloc;
		parent::init_filters($filters);
		if($param_allloc) {
			$this->filters['empr_location_id'] = 0;
		}
	}
	
	protected function init_default_selected_filters() {
		global $pmb_lecteurs_localises;
		
		$this->add_selected_filter('simple_search');
		if($pmb_lecteurs_localises) {
			$this->add_selected_filter('location');
		}
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		return array(
				'onclick' => "document.location=\"".static::get_controller_url_base()."&id_empr=".$object->id."&suite=acces\";"
		);
	}
	
	protected function init_default_columns() {
	
		$this->add_column('cb');
		$this->add_column('empr_name');
		$this->add_column('categ_libelle');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_column('default', 'align', 'left');
	}
	
	protected function _get_query_order() {
		$this->applied_sort_type = 'SQL';
		return " group by id_empr ".parent::_get_query_order();
	}
	
	public function get_error_message_empty_list() {
		global $msg;
		return $msg['dsi_lect_aucun_trouve'];
	}
	
	protected function get_selection_actions() {
		if(!isset($this->selection_actions)) {
			$this->selection_actions = array();
		}
		return $this->selection_actions;
	}
}