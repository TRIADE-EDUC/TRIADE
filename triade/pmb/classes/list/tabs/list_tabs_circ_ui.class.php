<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_tabs_circ_ui.class.php,v 1.4 2021/06/17 07:00:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_tabs_circ_ui extends list_tabs_ui {
	
	protected function _init_tabs() {
		global $pmb_serialcirc_active, $pmb_pret_groupement, $empr_show_caddie;
		global $pmb_rfid_activate, $pmb_rfid_serveur_url;
		global $pmb_resa_planning, $pmb_gestion_financiere, $pmb_gestion_amende;
		global $acquisition_active, $pmb_transferts_actif, $pmb_scan_request_activate;
		global $transferts_regroupement_depart, $transferts_validation_actif;
		
		//Circulation
		$this->add_tab('5', '', '13');
		$this->add_tab('5', 'retour', '14');
		$this->add_tab('5', 'ret_todo', 'circ_doc_a_traiter');
		$this->add_tab('5', 'groups', '903');
		$this->add_tab('5', 'empr_create', '15');
		if($pmb_serialcirc_active) {
			$this->add_tab('5', 'serialcirc', 'serialcirc_circ_menu');
		}
		if($pmb_pret_groupement) {
			$this->add_tab('5', 'groupexpl', 'groupexpl_submenu_list_title');
		}
		$this->add_tab('5', 'search_perso', 'search_perso_menu');
		
		//Paniers
		if($empr_show_caddie) {
			$this->add_tab('empr_caddie_menu', 'caddie', 'empr_caddie_menu_gestion', 'gestion', '&quoi=panier');
			$this->add_tab('empr_caddie_menu', 'caddie', 'empr_caddie_menu_collecte', 'gestion', '&quoi=barcode');
			$this->add_tab('empr_caddie_menu', 'caddie', 'empr_caddie_menu_pointage', 'gestion', '&quoi=pointagebarcode');
			$this->add_tab('empr_caddie_menu', 'caddie', 'empr_caddie_menu_action', 'action');
		}
		
		//Visualiser
		$this->add_tab('show', 'visu_ex', 'voir_exemplaire');
		$this->add_tab('show', 'visu_rech', 'voir_document');
		
		//RFID
		if($pmb_rfid_activate==1 && $pmb_rfid_serveur_url) {
			$this->add_tab('circ_menu_rfid', 'rfid_prog', 'circ_menu_rfid_programmer');
			$this->add_tab('circ_menu_rfid', 'rfid_del', 'circ_menu_rfid_effacer');
			$this->add_tab('circ_menu_rfid', 'rfid_read', 'circ_menu_rfid_lire');
		}
		
		//Reservations
		$this->add_tab('resa_menu', 'listeresa', 'resa_menu_liste_encours', 'encours');
		$this->add_tab('resa_menu', 'listeresa', 'resa_menu_liste_depassee', 'depassee');
		$this->add_tab('resa_menu', 'listeresa', 'resa_menu_liste_docranger', 'docranger');
		if($pmb_resa_planning) {
			$this->add_tab('resa_menu', 'resa_planning', 'resa_menu_planning');
		}
		
		//Relances
		if(($pmb_gestion_financiere)&&($pmb_gestion_amende)) {
			$this->add_tab('relance_menu', 'relance', 'relance_to_do', 'todo');
			$this->add_tab('relance_menu', 'relance', 'relance_recouvrement', 'recouvr');
		}
		
		//Suggestions
		if($acquisition_active) {
			$this->add_tab('acquisition_menu_sug', 'sug', 'acquisition_sug_do', '', '&action=modif&id_bibli=0');
		}
		
		//Transferts
		if($pmb_transferts_actif && (SESSrights & TRANSFERTS_AUTH)) {
			if($transferts_regroupement_depart){
				$this->add_tab('transferts_circ_menu_titre', 'trans', 'transferts_circ_menu_reception', 'recep');
				$this->add_tab('transferts_circ_menu_titre', 'trans', 'transferts_circ_menu_departs', 'departs');
				$this->add_tab('transferts_circ_menu_titre', 'trans', 'transferts_circ_menu_refuse', 'refus');
				$this->add_tab('transferts_circ_menu_titre', 'trans', 'transferts_circ_menu_reset', 'reset');
			} else {
				if($transferts_validation_actif=="1") {
					$this->add_tab('transferts_circ_menu_titre', 'trans', 'transferts_circ_menu_validation', 'valid');
				}
				$this->add_tab('transferts_circ_menu_titre', 'trans', 'transferts_circ_menu_envoi', 'envoi');
				$this->add_tab('transferts_circ_menu_titre', 'trans', 'transferts_circ_menu_reception', 'recep');
				$this->add_tab('transferts_circ_menu_titre', 'trans', 'transferts_circ_menu_retour', 'retour');
				$this->add_tab('transferts_circ_menu_titre', 'trans', 'transferts_circ_menu_refuse', 'refus');
				$this->add_tab('transferts_circ_menu_titre', 'trans', 'transferts_circ_menu_reset', 'reset');
			}
			
		}
		if($pmb_scan_request_activate){
			$this->add_tab('admin_menu_scan_request', 'scan_request', 'circ_scan_request_create_label', 'request', '&action=edit');
			$this->add_tab('admin_menu_scan_request', 'scan_request', 'circ_scan_request_see_label', 'list');
		}
	}
	
	protected function is_active_tab($label_code, $categ, $sub='') {
		$active = false;
		switch ($label_code) {
			case 'empr_caddie_menu_gestion':
				if(($this->is_equal_var_get('categ', 'caddie') && $this->is_equal_var_get('sub', 'gestion') && $this->is_equal_var_get('quoi', array("panier", "procs", "remote_procs", "classementGen")))) {
					$active = true;
				}
				break;
			case 'empr_caddie_menu_collecte':
				if(($this->is_equal_var_get('categ', 'caddie') && $this->is_equal_var_get('sub', 'gestion') && $this->is_equal_var_get('quoi', array("barcode", "selection")))) {
					$active = true;
				}
				break;
			case 'empr_caddie_menu_pointage':
				if(($this->is_equal_var_get('categ', 'caddie') && $this->is_equal_var_get('sub', 'gestion') && $this->is_equal_var_get('quoi', array("pointagebarcode", "pointage", "pointagepanier", "razpointage")))) {
					$active = true;
				}
				break;
			default:
				$active = parent::is_active_tab($label_code, $categ, $sub);
				break;
		}
		return $active;
	}
}