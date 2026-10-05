<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_subtabs_catalog_ui.class.php,v 1.9.6.1 2024/11/29 07:40:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_subtabs_catalog_ui extends list_subtabs_ui {
	
	public function get_title() {
		global $msg;
		
		$title = "";
		switch (static::$categ) {
			case 'search':
				$title .= $msg["357"];
				break;
			case 'serials':
				switch (static::$sub) {
					case 'circ_ask':
						$title .= $msg["serialcirc_asklist_title"];
						break;
					case 'serial_replace':
						$title .= $msg['catal_rep_per_h1'];
						break;
					case 'serial_duplicate':
						$title .= $msg['catal_duplicate_serial'];
						break;
					case 'bulletin_replace':
						$title .= $msg['catal_rep_bul_h1'];
						break;
					default:
						break;
				}
				break;
			case 'last_records':
				$title .= $msg['938'];
				break;
			case 'duplicate':
				$title .= $msg['catal_duplicate_notice'];
				break;
			case 'remplace':
				$title .= $msg['catal_rep_not_h1'];
				break;
			case 'expl_create':
				$title .= $msg['290'];
				break;
			case 'edit_expl':
				$title .= $msg['4008'];
				break;
			case 'dupl_expl':
				$title .= $msg['dupl_expl_titre'];
				break;
			case 'del_expl':
				$title .= $msg['313'];
				break;
			case 'expl_update':
				//TODO
				break;
			case 'avis':
				$title .= $msg['titre_avis'];
				break;
			case 'tags':
				$title .= $msg['titre_tag'];
				break;
			case 'caddie':
				$title .= $msg['caddie_menu'];
				break;
			case 'etagere':
				$title .= $msg['etagere_menu'];
				break;
			case 'sug':
				$title .= $msg['acquisition_sug_do'];
				break;
			case 'contribution_area':
				$title .= $msg['catalog_menu_contribution'];
				break;
		}
		return $title;
	}
	
	protected function is_selected_tab($object) {
		switch (static::$categ) {
			case 'search':
			    if(!empty(static::$sub) && static::$sub != 'launch') {
					return parent::is_selected_tab($object);
				} else {
					if(!empty($object->get_url_extra()) && strpos($object->get_url_extra(), '&mode=7') !== false) {
						return ongletSelect("categ=".static::$categ.'&mode=7');
					} elseif(!empty($object->get_url_extra()) && strpos($object->get_url_extra(), '&mode=8') !== false) {
						return ongletSelect("categ=".static::$categ.'&mode=8');
					} else {
						return ongletSelect("categ=".static::$categ.(!empty($object->get_url_extra()) ? $object->get_url_extra() : ''));
					}
				}
			default:
				return parent::is_selected_tab($object);
		}
	}
	
	public function get_sub_title() {
		global $msg;
		
		$sub_title = "";
		switch (static::$categ) {
			case 'search':
				$selected_subtab = $this->get_selected_subtab();
				if(!empty($selected_subtab)) {
					$sub_title .= $selected_subtab->get_label();
				} else {
					$sub_title .= $msg['354'];
				}
				break;
			case 'caddie':
			    if(empty(static::$sub)) static::$sub = 'gestion';
			    $sub_title .= $msg["caddie_menu_".static::$sub];
				$selected_subtab = $this->get_selected_subtab();
				if(!empty($selected_subtab)) {
					$sub_title .= " > ".$selected_subtab->get_label();
				}
				break;
			case 'etagere':
			    switch (static::$sub) {
					case 'constitution':
						$sub_title .= $msg["etagere_menu_constitution"];
						break;
					case 'classementGen':
						$sub_title .= $msg["etagere_menu_classement"];
						break;
					case 'gestion':
					default:
						$sub_title .= $msg["etagere_menu_gestion"];
						break;
				}
				break;
			case 'contribution_area':
				$sub_title .= $msg["contribution_area_moderation"];
				break;
			default:
				$sub_title .= parent::get_sub_title();
				break;
		}
		return $sub_title;
	}
	
	protected function _init_subtabs() {
		global $class_path;
		global $gestion_acces_active, $pmb_scan_request_activate, $pmb_transferts_actif;
		
		switch (static::$categ) {
			case 'search':
				$this->_init_search_subtabs();
				break;
			case 'avis':
				$this->add_subtab('records', 'avis_menu_records');
				if(defined('SESSrights') && SESSrights & CMS_AUTH) {
					$this->add_subtab('articles', 'avis_menu_articles');
					$this->add_subtab('sections', 'avis_menu_sections');
				}
				break;
			case 'caddie':
			    if(empty(static::$sub)) static::$sub = 'gestion';
			    switch (static::$sub) {
					case 'gestion':
						$this->add_subtab(static::$sub, 'caddie_menu_gestion_panier', '', '&quoi=panier');
						$this->add_subtab(static::$sub, 'caddie_menu_gestion_procs', '', '&quoi=procs');
						$this->add_subtab(static::$sub, 'remote_procedures_catalog_title', '', '&quoi=remote_procs');
						$this->add_subtab(static::$sub, 'classementGen_list_libelle', '', '&quoi=classementGen');
						break;
					case 'collecte':
						$this->add_subtab(static::$sub, 'caddie_menu_collecte_cb', '', '&moyen=douchette');
// 						$this->add_subtab(static::$sub, 'caddie_menu_collecte_import', '', '&moyen=import');
						$this->add_subtab(static::$sub, 'caddie_menu_collecte_selection', '', '&moyen=selection');
						break;
					case 'pointage':
						$this->add_subtab(static::$sub, 'caddie_menu_pointage_cb', '', '&moyen=douchette');
// 						$this->add_subtab(static::$sub, 'caddie_menu_pointage_import', '', '&moyen=import');
// 						$this->add_subtab(static::$sub, 'caddie_menu_pointage_import_unimarc', '', '&moyen=importunimarc');
						$this->add_subtab(static::$sub, 'caddie_menu_pointage_selection', '', '&moyen=selection');
						$this->add_subtab(static::$sub, 'caddie_menu_pointage_panier', '', '&moyen=panier');
						$this->add_subtab(static::$sub, 'caddie_menu_pointage_search_history', '', '&moyen=search_history');
						$this->add_subtab(static::$sub, 'caddie_menu_pointage_raz', '', '&moyen=raz');
						break;
					case 'action':
						$this->add_subtab(static::$sub, 'caddie_menu_action_suppr_panier', '', '&quelle=supprpanier');
						$this->add_subtab(static::$sub, 'caddie_menu_action_transfert', '', '&quelle=transfert');
						$this->add_subtab(static::$sub, 'caddie_menu_action_edition', '', '&quelle=edition');
						$this->add_subtab(static::$sub, 'caddie_menu_action_impr_cote', '', '&quelle=impr_cote');
						$this->add_subtab(static::$sub, 'caddie_menu_action_export', '', '&quelle=export');
						$this->add_subtab(static::$sub, 'caddie_menu_action_exp_docnum', '', '&quelle=docnum');
						$this->add_subtab(static::$sub, 'caddie_menu_action_selection', '', '&quelle=selection');
						// On déclenche un événement sur la supression
						require_once($class_path.'/event/events/event_users_group.class.php');
						$evt_handler = events_handler::get_instance();
						$event = new event_users_group("users_group", "get_autorisation_del_base");
						$evt_handler->send($event);
						if(!$event->get_error_message()){
							$this->add_subtab(static::$sub, 'caddie_menu_action_suppr_base', '', '&quelle=supprbase');
						}
						$this->add_subtab(static::$sub, 'caddie_menu_action_reindex', '', '&quelle=reindex');
						if($gestion_acces_active){
							$this->add_subtab(static::$sub, 'caddie_menu_action_access_rights', '', '&quelle=access_rights');
						}
						if((SESSrights & CIRCULATION_AUTH) && $pmb_scan_request_activate){
							$this->add_subtab(static::$sub, 'scan_request_record_button', '', '&quelle=scan_request');
						}
// 						$this->add_subtab(static::$sub, 'caddie_menu_action_change_bloc', '', '&quelle=changebloc');
						if ($pmb_transferts_actif) {
							$this->add_subtab(static::$sub, 'caddie_menu_action_transfert_to_location', '', '&quelle=transfert_to_location');
						}
						$this->add_subtab(static::$sub, 'caddie_menu_action_print_barcode', '', '&quelle=print_barcode');
						break;
				}
				break;
			default:
				break;
		}
	}
	
	protected function _init_search_subtabs() {
		global $pmb_use_uniform_title, $pmb_map_activate, $pmb_allow_external_search;
		global $option_show_notice_fille, $option_show_expl;
		
		$this->add_subtab('search', '354', '', '&mode=0');
		$this->add_subtab('search', '355', '', '&mode=1');
		$this->add_subtab('search', 'search_by_terms', '', '&mode=5');
		$this->add_subtab('search', '356', '', '&mode=2');
		if ($pmb_use_uniform_title) {
			$this->add_subtab('search', 'search_by_titre_uniforme', '', '&mode=9');
		}
		$authpersos= new authpersos();
		$info_authpersos=$authpersos->get_data();
		foreach($info_authpersos as $authperso){
			if($authperso['gestion_search'] != 2) continue; // pas de boutton
			$this->add_subtab('search', $authperso['name'], '', '&mode='.($authperso['id']+1000));
		}
		$this->add_subtab('search', 'search_by_panier', '', '&mode=3');
		$this->add_subtab('search', 'search_extended', '', '&mode=6');
		$this->add_subtab('search', 'search_exemplaire', '', '&mode=8&option_show_notice_fille='.intval($option_show_notice_fille).'&option_show_expl='.intval($option_show_expl));
		if ($pmb_map_activate) {
			$this->add_subtab('search', 'search_by_map', '', '&mode=11');
		}
		if ($pmb_allow_external_search) {
			$this->add_subtab('search', 'connecteurs_external_search', '', '&mode=7&external_type=simple');
		}
		
		//DG - 18/02/21 - je garde ces 2 menus non visible dans un coin - si utilisation ultérieure
		// $this->add_subtab('search', '413', '', '&mode=4');
		// $this->add_subtab('search', 'search_by_titre_serie', '', '&mode=10');
	}
	
	public function get_display_subtab($object) {
		if($object->get_sub() == 'search') {
			$mode = str_replace('&mode=', '', $object->get_url_extra());
			if(strpos($mode, '&') !== false) {
				$mode = substr($mode, 0, strpos($mode, '&'));
			}
			return "<span".$this->is_selected_tab($object)." id='notice_search_tab_".$mode."'>
				<a title='".$object->get_title()."' href='".$object->get_destination_link()."'>
					".$object->get_label()."
				</a>
			</span>";
		} else {
			return parent::get_display_subtab($object);
		}
	}
	
}