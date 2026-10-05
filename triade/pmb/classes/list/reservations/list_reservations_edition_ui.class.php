<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_reservations_edition_ui.class.php,v 1.14 2021/10/04 13:59:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/list/reservations/list_reservations_edition_ui.tpl.php");

class list_reservations_edition_ui extends list_reservations_ui {
		
	protected function get_form_title() {
		global $msg;
		
		return $msg['edit_resa_menu'];
	}
	
	protected function init_default_selected_filters() {
		global $pmb_transferts_actif, $pmb_location_reservation;
		
		$this->add_selected_filter('montrerquoi');
		if ($pmb_transferts_actif=="1" || $pmb_location_reservation) {
			$this->add_selected_filter('removal_location');
		}
	}
	
	protected function init_default_columns() {
		global $pmb_resa_planning;
		
		if(!$this->filters['id_notice'] && !$this->filters['id_bulletin']) {
			$this->add_column('record');
		}
		$this->add_column('expl_cote');
		if(!$this->filters['id_empr']) {
			$this->add_column('empr');
			$this->add_column('empr_location');
		}
		$this->add_column('rank');
		$this->add_column('resa_date');
		$this->add_column('resa_condition');
		if ($pmb_resa_planning) {
			$this->add_column('resa_date_debut');
		}
		$this->add_column('resa_date_fin');
	}
	
	protected function init_default_settings() {
		global $sub;
		
		parent::init_default_settings();
		switch($sub) {
			case "resa_a_traiter" :
				$this->set_setting_display('search_form', 'options', true);
				break;
			default :
				$this->set_setting_display('query', 'human', false);
				break;
		}
	}
	
	protected function get_display_spreadsheet_title() {
		global $msg;
		$this->spreadsheet->write_string(0,0,$msg[350].": ".$msg['edit_resa_menu']);
	}
	
	protected function get_html_title() {
		global $msg;
		return "<h1>".$msg[350]."&nbsp;&gt;&nbsp;".$msg['edit_resa_menu']."</h1>";
	}
	
	protected function init_default_applied_sort() {
		$this->add_applied_sort('record');
		$this->add_applied_sort('resa_date');
	}
	
	public static function get_ajax_controller_url_base() {
		global $base_path, $current_module, $sub;
		return $base_path.'/ajax.php?module='.$current_module.'&categ=notices&sub='.$sub;
	}
}