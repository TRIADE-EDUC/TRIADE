<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_resa_planning_record_ui.class.php,v 1.1 2023/08/04 12:25:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_opac_resa_planning_record_ui extends list_opac_resa_planning_ui {
	
    protected function init_default_selected_filters() {
        $this->selected_filters = array();
    }
    
	protected function init_default_columns() {
		$this->add_column('resa_dates');
		$this->add_column('resa_qty');
		if ($this->get_locations_number() > 1) {
		    $this->add_column('resa_loc_retrait');
		}
	}
	
	protected function init_default_pager() {
	    parent::init_default_pager();
	    $this->pager['all_on_page'] = true;
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('resa_date_debut');
	    $this->add_applied_sort('resa_date_fin');
	}
	
	protected function init_default_settings() {
	    parent::init_default_settings();
	    $this->set_setting_display('pager', 'visible', false);
	}
}