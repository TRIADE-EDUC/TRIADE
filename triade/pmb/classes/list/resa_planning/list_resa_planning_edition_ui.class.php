<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_resa_planning_edition_ui.class.php,v 1.8 2023/03/29 12:31:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_resa_planning_edition_ui extends list_resa_planning_ui {
	
	protected function get_form_title() {
		global $msg;
		
		return $msg['edit_resa_planning_menu'];
	}
	
	protected function init_default_selected_filters() {
	    global $pmb_lecteurs_localises, $pmb_location_resa_planning;
	    
	    $this->add_selected_filter('montrerquoi');
	    $this->add_empty_selected_filter();
	    $this->add_empty_selected_filter();
	    if($pmb_lecteurs_localises) {
	        $this->add_selected_filter('empr_location');
	    }
	    if($pmb_location_resa_planning) {
	        $this->add_selected_filter('resa_loc_retrait');
	    }
	}
	
	protected function init_default_columns() {
	    global $pmb_lecteurs_localises;
		
		$this->add_column('record');
		$this->add_column('empr');
		if($pmb_lecteurs_localises) {
		    $this->add_column('empr_location');
		}
		$this->add_column('resa_date');
		$this->add_column('resa_date_debut');
		$this->add_column('resa_date_fin');
		$this->add_column('resa_qty');
		$this->add_column('resa_validee');
		$this->add_column('resa_confirmee');
		if ($this->get_locations_number() > 1) {
		    $this->add_column('resa_loc_retrait');
		}
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('empr');
	    $this->add_applied_sort('record');
	    $this->add_applied_sort('resa_date');
	}
	
	public static function get_ajax_controller_url_base() {
		global $base_path, $current_module;
		return $base_path.'/ajax.php?module='.$current_module.'&categ=notices&sub=resa_planning';
	}
}