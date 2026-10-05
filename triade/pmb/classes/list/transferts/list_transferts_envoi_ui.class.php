<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_transferts_envoi_ui.class.php,v 1.10 2022/10/04 09:20:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_transferts_envoi_ui extends list_transferts_ui {
	
	protected function get_form_title() {
		global $msg;
		global $transferts_envoi_lot;
		if ($transferts_envoi_lot=="1") {
			return "<h3>".$msg["transferts_circ_envoi_lot"]."</h3>";
		} else {
			return "<h3>".$msg["transferts_circ_envoi_list"]."</h3>";
		}
	}
	
	protected function init_default_columns() {
		global $action, $transferts_envoi_lot;
		$this->add_column('record');
		$this->add_column('cb');
		$this->add_column('empr');
		$this->add_column('destination');
		$this->add_column('expl_owner');
		$this->add_column('formatted_date_creation');
		$this->add_column('formatted_date_acceptee');
		$this->add_column('motif');
		$this->add_column('transfert_ask_user_num');
		$this->add_column('transfert_send_user_num');
		if(($action == '' || $action == 'list') && $transferts_envoi_lot == '1') {
			$this->add_column_selection();
		}
	}
	
	public function init_filters($filters=array()) {
		global $deflt_docs_location;

		$this->filters = array(
				'site_origine' => $deflt_docs_location,
				'site_destination' => 0,
		);
		//Surcharge si les filtres ne sont pas affiches dans ce contexte
		if(empty($this->selected_filters['site_origine'])) {
			$filters['site_origine'] = $deflt_docs_location;
		}
		if(empty($this->selected_filters['site_destination'])) {
			$filters['site_destination'] = 0;
		}
		parent::init_filters($filters);
	}
	
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
				        'site_origine' => 'transferts_circ_envoi_filtre_origine',
						'site_destination' => 'transferts_circ_envoi_filtre_destination',
						'f_etat_date' => 'transferts_circ_retour_filtre_etat',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	protected function init_default_selected_filters() {
	    $this->add_selected_filter('site_origine');
		$this->add_selected_filter('site_destination');
		$this->add_selected_filter('f_etat_date');
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		global $transferts_validation_actif;
		global $action, $transferts_envoi_lot;
		
		parent::init_default_selection_actions();
		if (($action == '' || $action == 'list') && $transferts_envoi_lot=="1") {
			$this->add_selection_action('env', $msg['transferts_circ_btEnvoyer'], '');
			if ($transferts_validation_actif=="1") {
				// Pas de bouton de refus ici lorsque le paramètre transferts_validation_actif est à 1
				
			} else {
				$this->add_selection_action('refus', $msg['transferts_circ_btRefuser'], '');
			}
		}
	}
	
	protected function get_display_no_results() {
		global $msg;
		global $list_transferts_ui_no_results;
		$display = $list_transferts_ui_no_results;
		$display = str_replace('!!message!!', $msg["transferts_envoi_liste_vide"], $display);
		return $display;
	}
	
	protected function get_valid_form_title() {
		global $msg;
		return "<h3>".$msg["transferts_circ_envoi_valide_liste"]."</h3>";
	}
}