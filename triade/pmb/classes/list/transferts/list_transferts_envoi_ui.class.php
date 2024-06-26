<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_transferts_envoi_ui.class.php,v 1.1 2018-12-27 10:05:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/list/transferts/list_transferts_ui.class.php');

class list_transferts_envoi_ui extends list_transferts_ui {
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function get_title() {
		global $msg;
		return "<h1>".$msg['transferts_circ_menu_titre']." > ".$msg['transferts_circ_menu_envoi']."</h1>";
	}
	
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
		$this->add_column('record', '233');
		$this->add_column('cb', '232');
		$this->add_column('empr', 'transferts_circ_empr');
		$this->add_column('destination', 'transferts_circ_destination');
		$this->add_column('expl_owner', '651');
		$this->add_column('formatted_date_creation', 'transferts_circ_date_creation');
		$this->add_column('', 'transferts_circ_date_validation');
		$this->add_column('motif', 'transferts_circ_motif');
		$this->add_column('transfert_ask_user_num', 'transferts_edition_ask_user');
		$this->add_column('transfert_send_user_num', 'transferts_edition_send_user');
		if(($action == '' || $action == 'list') && $transferts_envoi_lot == '1') {
			$this->add_column_sel_button();
		}
	}
	
	protected function get_search_filters() {
		global $msg;
		$search_filters = '';
		$search_filters .= "&nbsp;".$msg['transferts_circ_envoi_filtre_destination'];
		$search_filters .= "<select name='".$this->objects_type."_site_destination'>";
		$search_filters .= $this->get_search_options_locations($this->filters['site_destination']);
		$search_filters .= "</select>";
		$search_filters .= "&nbsp;".$msg["transferts_circ_retour_filtre_etat"]."&nbsp;";
		$search_filters .= $this->get_search_retour_filtre_etat_selector();
		return $search_filters;
	}
	
	protected function get_display_selection_actions() {
		global $msg;
		global $transferts_validation_actif;
		global $transferts_envoi_lot;
		if ($transferts_envoi_lot=="1") {
			if ($transferts_validation_actif=="1") {
				return "<input type='button' class='bouton' name='".$msg["transferts_circ_btEnvoyer"]."' value='".$msg["transferts_circ_btEnvoyer"]."' onclick='verifChk(document.".$this->get_form_name().",\"aff_env\")'>";
			} else {
				return "<input type='button' class='bouton' name='".$msg["transferts_circ_btEnvoyer"]."' value='".$msg["transferts_circ_btEnvoyer"]."' onclick='verifChk(document.".$this->get_form_name().",\"aff_env\")'>
					&nbsp;
					<input type='button' class='bouton' name='".$msg["transferts_circ_btRefuser"]."' value='".$msg["transferts_circ_btRefuser"]."' onclick='verifChk(document.".$this->get_form_name().",\"aff_refus\")'>
					";
			}
		} else {
			return "";
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