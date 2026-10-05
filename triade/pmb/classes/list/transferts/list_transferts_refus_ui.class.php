<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_transferts_refus_ui.class.php,v 1.13 2023/12/15 14:56:53 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_transferts_refus_ui extends list_transferts_ui {
	
	protected function get_form_title() {
		global $msg;
		return "<h3>".$msg["transferts_circ_retours_lot"]."</h3>";
	}
	
	protected function init_default_columns() {
		global $action;
		$this->add_column('record');
		$this->add_column('cb');
		$this->add_column('empr');
		$this->add_column('source');
		$this->add_column('expl_owner');
		$this->add_column('formatted_date_creation');
		$this->add_column('formatted_date_refus');
		$this->add_column('motif_refus', 'transferts_circ_motif');
		$this->add_column('transfert_ask_user_num');
		$this->add_column('transfert_bt_relancer');
		if(($action == '' || $action == 'list')) {
			$this->add_column_selection();
		}
	}
	
	protected function get_edition_link() {
		return '';
	}
	
	public function init_filters($filters=array()) {
		global $deflt_docs_location;
		
		$this->filters = array(
				'site_origine' => 0,
				'site_destination' => $deflt_docs_location,
		);
		//Surcharge si les filtres ne sont pas affiches dans ce contexte
		if(empty($this->selected_filters['site_origine'])) {
			$filters['site_origine'] = 0;
		}
		if(empty($this->selected_filters['site_destination'])) {
			$filters['site_destination'] = $deflt_docs_location;
		}
		parent::init_filters($filters);
	}
	
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
				    'site_origine' => 'transferts_circ_reception_filtre_source',
				    'site_destination' => 'transferts_circ_validation_filtre_destination',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	protected function init_default_selected_filters() {
	    $this->add_selected_filter('site_origine');
	    $this->add_selected_filter('site_destination');
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		global $action;
		
		parent::init_default_selection_actions();
		if(($action == '' || $action == 'list')) {
			$this->add_selection_action('supp', $msg['transferts_circ_btSupprimer'], '');
		}
	}
	
	protected function get_display_no_results() {
		global $msg;
		global $list_transferts_ui_no_results;
		$display = $list_transferts_ui_no_results;
		$display = str_replace('!!message!!', $msg["transferts_refuse_liste_vide"], $display);
		return $display;
	}
	
	protected function get_valid_form_title() {
	    global $msg, $action;
		
		switch ($action) {
		    case 'aff_supp':
		        return "<h3>".$msg["transferts_circ_refus_valide_liste"]."</h3>";
		    case 'aff_refus':
		    default:
		        return "<h3>".$msg["transferts_circ_validation_refus"]."</h3>";
		}
	}
}