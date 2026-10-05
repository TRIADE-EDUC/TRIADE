<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_subtabs_circ_ui.class.php,v 1.7.8.1.2.1 2025/05/30 12:00:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_subtabs_circ_ui extends list_subtabs_ui {
	
	public function get_title() {
		global $msg;
		
		$title = "";
		switch (static::$categ) {
			case 'groups':
				$title .= $msg['907'];
				break;
			case 'groupexpl':
				$title .= $msg['groupexpl_submenu_list_title'];
				break;
			case 'caddie':
				$title .= $msg['empr_caddie_menu'];
				break;
			case 'rfid_prog':
				$title .= $msg['rfid_programmation_etiquette_titre'];
				break;
			case 'rfid_del':
				$title .= $msg['rfid_effacement_etiquette_titre'];
				break;
			case 'rfid_read':
				$title .= $msg['rfid_lecture_etiquette_titre'];
				break;
			case 'listeresa':
				$title .= $msg['resa_menu'];
				break;
				break;
			case 'resa_planning':
				$title .= $msg['resa_menu'];
				break;
			case 'relance':
				$title .= $msg['relance_menu'];
				break;
			case 'sug':
				$title .= $msg['acquisition_sug_do'];
				break;
			case 'trans':
				$title .= $msg['transferts_circ_menu_titre'];
				break;
			case 'scan_request':
				$title .= $msg['scan_request_list'];
				break;
			case 'note_ex':
			    $title .= $msg['377'];
			    break;
			case 'express':
			    $title .= $msg['pret_express'];
			    break;
		}
		return $title;
	}
	
	public function get_sub_title() {
		global $msg, $quoi;
		
		$sub_title = "";
		switch (static::$categ) {
			case 'caddie':
				switch (static::$sub) {
					case 'gestion':
						switch ($quoi) {
							case 'panier':
							case 'procs':
							case 'remote_procs':
							case 'classementGen':
								$tab_name = 'gestion';
								break;
							case 'barcode':
							case 'selection':
								$tab_name = 'collecte';
								break;
							case 'pointagebarcode':
							case 'pointage':
							case 'pointagepanier':
							case 'razpointage':
								$tab_name = 'pointage';
								break;
						}
						break;
					case 'action':
						$tab_name = 'action';
						break;
				}
				$sub_title .= $msg["empr_caddie_menu_".$tab_name];
				$selected_subtab = $this->get_selected_subtab();
				if(!empty($selected_subtab)) {
					$sub_title .= " > ".$selected_subtab->get_label();
				}
				break;
			case 'listeresa':
				$sub_title .= $msg["resa_menu_liste_".static::$sub];
				break;
			case 'resa_planning':
				$sub_title .= $msg["resa_menu_planning"];
				break;
			case 'relance':
				switch (static::$sub) {
					case 'recouvr':
						$sub_title .= $msg['relance_recouvrement'];
						break;
					default:
						$sub_title .= $msg['relance_to_do']."&nbsp;<span id='nb_relance_to_do'>&nbsp;</span>";
						break;
				}
				break;
			case 'trans':
				switch (static::$sub) {
					case 'recep':
						$sub_title .= $msg['transferts_circ_menu_reception'];
						break;
					case 'refus':
						$sub_title .= $msg['transferts_circ_menu_refuse'];
						break;
					case 'valid':
					    $sub_title .= $msg['transferts_circ_menu_validation'];
					    break;
					default:
						$sub_title .= $msg['transferts_circ_menu_'.static::$sub];
						break;
				}
				break;
			default:
				$sub_title .= parent::get_sub_title();
				break;
		}
		return $sub_title;
	}
	
	protected function _init_subtabs() {
		global $quoi, $resa_action, $id_empr, $groupID, $mode;
		
		switch (static::$categ) {
			case 'caddie':
				switch (static::$sub) {
					case 'gestion':
						switch ($quoi) {
							case 'panier':
							case 'procs':
							case 'remote_procs':
							case 'classementGen':
								$this->add_subtab(static::$sub, 'empr_caddie_menu_gestion_panier', '', '&quoi=panier');
								$this->add_subtab(static::$sub, 'empr_caddie_menu_gestion_procs', '', '&quoi=procs');
								$this->add_subtab(static::$sub, 'remote_procedures_circ_title', '', '&quoi=remote_procs');
								$this->add_subtab(static::$sub, 'classementGen_list_libelle', '', '&quoi=classementGen');
								break;
							case 'barcode':
							case 'selection':
								$this->add_subtab(static::$sub, 'empr_caddie_menu_collecte_barcode', '', '&quoi=barcode');
								$this->add_subtab(static::$sub, 'empr_caddie_menu_collecte_selection', '', '&quoi=selection');
								break;
							case 'pointagebarcode':
							case 'pointage':
							case 'pointagepanier':
							case 'razpointage':
								$this->add_subtab(static::$sub, 'empr_caddie_menu_pointage_barcode', '', '&quoi=pointagebarcode');
								$this->add_subtab(static::$sub, 'empr_caddie_menu_pointage_selection', '', '&quoi=pointage');
								$this->add_subtab(static::$sub, 'empr_caddie_menu_pointage_panier', '', '&quoi=pointagepanier');
								$this->add_subtab(static::$sub, 'empr_caddie_menu_pointage_raz', '', '&quoi=razpointage');
								break;
						}
						
						break;
					case 'action':
						$this->add_subtab(static::$sub, 'empr_caddie_menu_action_suppr_panier', '', '&quelle=supprpanier');
						$this->add_subtab(static::$sub, 'empr_caddie_menu_action_transfert', '', '&quelle=transfert');
						$this->add_subtab(static::$sub, 'empr_caddie_menu_action_edition', '', '&quelle=edition');
						$this->add_subtab(static::$sub, 'empr_caddie_menu_action_mailing', '', '&quelle=mailing');
						$this->add_subtab(static::$sub, 'empr_caddie_menu_action_carte', '', '&quelle=carte');
						$this->add_subtab(static::$sub, 'empr_caddie_menu_action_selection', '', '&quelle=selection');
						$this->add_subtab(static::$sub, 'empr_caddie_menu_action_suppr_base', '', '&quelle=supprbase');
						break;
				}
				break;
			case 'resa_planning':
				if($resa_action == "search_resa" && $mode != "view_serial") {
					$url_extra = "&resa_action=search_resa&id_empr=".$id_empr."&groupID=".$groupID;
					$this->add_subtab('', '354', '', $url_extra."&mode=0");
					$this->add_subtab('', '355', '', $url_extra."&mode=1");
					$this->add_subtab('', 'search_by_terms', '', $url_extra."&mode=5");
					$this->add_subtab('', '356', '', $url_extra."&mode=2");
					$this->add_subtab('', 'search_by_panier', '', $url_extra."&mode=3");
					$this->add_subtab('', 'search_extended', '', $url_extra."&mode=6");
				}
				break;
		}
	}
}