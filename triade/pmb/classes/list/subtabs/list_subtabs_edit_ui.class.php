<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_subtabs_edit_ui.class.php,v 1.5.2.1 2024/11/29 07:40:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_subtabs_edit_ui extends list_subtabs_ui {
	
	public function get_title() {
		global $msg;
		
		$title = "";
		switch (static::$categ) {
			case 'procs':
				$title .= $msg['1130'];
				break;
			case 'state':
				$title .= $msg['edition_state_label'];
				break;
			case 'expl':
				$title .= $msg['1110'];
				break;
			case 'pnb':
			    $title .= $msg['edit_menu_pnb'];
			    break;
			case 'notices':
				$title .= $msg['350'];
				break;
			case 'empr':
				$title .= $msg['1120'];
				break;
			case 'serials':
				$title .= $msg['1150'];
				break;
			case 'cbgen':
			case 'barcodes_sheets':
				$title .= $msg['1140'];
				break;
			case 'transferts':
				$title .= $msg['transferts_edition_titre'];
				break;
			case 'transferts_demandes':
				$title .= $msg['transferts_demandes'];
				break;
			case 'sticks_sheet':
				$title .= $msg['sticks_sheet'];
				break;
			case 'tpl':
				$title .= $msg['edit_tpl_menu'];
				break;
			case 'stat_opac':
				$title .= $msg['opac_admin_menu'];
				break;
			case 'opac':
				$title .= $msg['opac_admin_menu'];
				break;
		}
		return $title;
	}
	
	public function get_sub_title() {
		global $msg;
		
		$sub_title = "";
		switch (static::$categ) {
			case 'notices':
				switch(static::$sub) {
					case 'resa_a_traiter':
						$sub_title .= $msg['edit_resa_menu_a_traiter'];
						break;
					case 'resa_planning':
						$sub_title .= $msg['edit_resa_planning_menu'];
						break;
					default:
						$sub_title .= $msg['edit_resa_menu'];
						break;
				}
				break;
			case 'empr':
				switch(static::$sub) {
					case 'limite':
						$sub_title .= $msg['edit_titre_empr_abo_limite'];
						break;
					case 'depasse':
						$sub_title .= $msg['edit_titre_empr_abo_depasse'];
						break;
					case 'cashdesk':
						$sub_title .= $msg['cashdesk_edition_menu'];
						break;
					case 'categ_change':
						$sub_title .= $msg['edit_titre_empr_categ_change'];
						break;
					case 'encours':
					default :
						$sub_title .= $msg['1121'];
						break;
				}
				break;
			case 'serials':
				switch(static::$sub) {
// 					case 'manquant':
// 						$sub_title .= $msg['1154'];
// 						break;
					case 'circ_state':
						$sub_title .= $msg['serial_circ_state_edit'];
						break;
					case 'simple_circ':
						$sub_title .= $msg['serial_simple_circ_edit'];
						break;
					case 'collect':
					default:
						$sub_title .= $msg['1151'];
						break;
				}
				break;
			case 'procs':
				$sub_title .= $msg['1131'];
				break;
			case 'pnb':
			    $sub_title .= $msg['edit_menu_pnb_orders'];
			    break;
			case 'cbgen':
				switch(static::$sub) {
					case 'libre':
					default :
						$sub_title .= $msg['1141'];
						break;
				}
				break;
			case 'transferts':
				$sub_title .= $msg["transferts_edition_".static::$sub];
				break;
			case 'transferts_demandes':
				break;
			case 'sticks_sheet' :
				$sub_title .= $msg['sticks_sheet_models'];
				break;
			case 'barcodes_sheets' :
				$sub_title .= $msg['barcodes_sheet_models'];
				break;
			case 'tpl':
				switch(static::$sub) {
					case 'serialcirc':
						$sub_title .= $msg['edit_serialcirc_tpl_menu'];
						break;
					case 'bannette':
						$sub_title .= $msg['edit_bannette_tpl_menu'];
						break;
					case 'print_cart_tpl':
						$sub_title .= $msg['admin_print_cart_tpl_title'];
						break;
					case 'notice':
					default :
						$sub_title .= $msg['edit_notice_tpl_menu'];
						break;
				}
				break;
			case 'stat_opac':
				$sub_title .= $msg['stat_opac_menu'];
				break;
			case 'opac':
				switch(static::$sub) {
					case 'campaigns':
						$sub_title .= $msg['campaigns'];
						break;
					case 'visits_statistics':
						$sub_title .= $msg['dashboard_visits_statistics'];
						break;
					default :
						break;
				}
				break;
			case 'state':
				break;
			case 'serialcirc_diff':
				break;
			case 'pnb':
				break;
			case 'contribution_area':
				break;
			case 'plugin':
				break;
			case 'expl':
			default:
				switch(static::$sub) {
					case 'ppargroupe':
						$sub_title .= $msg['1114'];
						break;
					case 'rpargroupe':
						$sub_title .= $msg['menu_retards_groupe'];
						break;
					case 'retard':
						$sub_title .= $msg['1112'];
						break;
					case 'retard_par_date':
						$sub_title .= $msg['edit_expl_retard_par_date'];
						break;
					case 'owner':
						$sub_title .= $msg['1113'];
						break;
					case 'short_loans':
						$sub_title .= $msg['current_short_loans'];
						break;
					case 'unreturned_short_loans':
						$sub_title .= $msg['unreturned_short_loans'];
						break;
					case 'overdue_short_loans':
						$sub_title .= $msg['overdue_short_loans'];
						break;
					case 'archives':
						$sub_title .= $msg['loans_archives'];
						break;
					case 'encours':
					default :
						$sub_title .= $msg['1111'];
						break;
				}
				break;
		}
		return $sub_title;
	}
	
	protected function _init_subtabs() {
		
	}
}