<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_empr_statut_ui.class.php,v 1.1 2018-10-12 12:18:37 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/configuration/empr/list_configuration_empr_ui.class.php");

class list_configuration_empr_statut_ui extends list_configuration_empr_ui {
	
	protected function _get_query_base() {
		return 'SELECT idstatut, statut_libelle, allow_loan, allow_loan_hist, allow_book, allow_opac, allow_dsi, allow_dsi_priv, allow_sugg, allow_dema, allow_prol, allow_avis, allow_tag , allow_pwd, allow_liste_lecture, allow_self_checkout, allow_self_checkin, allow_serialcirc, allow_scan_request, allow_contribution FROM empr_statut';
	}
	
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'statut_libelle',
				'asc_desc' => 'asc'
		);
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'statut_libelle' => '103',
				'allow_loan' => 'empr_short_no_allow_loan',
				'allow_loan_hist' => 'empr_short_no_allow_loan_hist',
				'allow_book' => 'empr_short_no_allow_book',
				'allow_opac' => 'empr_short_no_allow_opac',
				'allow_dsi' => 'empr_short_no_allow_dsi',
				'allow_dsi_priv' => 'empr_short_no_allow_dsi_priv',
				'allow_sugg' => 'empr_short_no_allow_sugg',
				'allow_liste_lecture' => 'empr_short_no_allow_liste_lecture',
				'allow_dema' => 'empr_short_no_allow_dema',
				'allow_prol' => 'empr_short_no_allow_prol',
				'allow_avis' => 'empr_short_no_allow_avis',
				'allow_tag' => 'empr_short_no_allow_tag',
				'allow_pwd' => 'empr_short_no_allow_pwd',
				'allow_self_checkout' => 'empr_short_no_allow_self_checkout',
				'allow_self_checkin' => 'empr_short_no_allow_self_checkin',
				'allow_serialcirc' => 'empr_short_no_allow_serialcirc',
				'allow_scan_request' => 'empr_short_no_allow_scan_request',
				'allow_contribution' => 'empr_short_no_allow_contribution',
		);
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
	
		$content = '';
		switch($property) {
			case 'statut_libelle':
				if ($object->idstatut>2) {
					$content .= $object->statut_libelle;
				}
				else {
					$content .= "<strong>".$object->statut_libelle."</strong>";
				}
				break;
			case 'allow_loan':
			case 'allow_loan_hist':
			case 'allow_book':
			case 'allow_opac':
			case 'allow_dsi':
			case 'allow_dsi_priv':
			case 'allow_sugg':
			case 'allow_liste_lecture':
			case 'allow_dema':
			case 'allow_prol':
			case 'allow_avis':
			case 'allow_tag':
			case 'allow_pwd':
			case 'allow_self_checkout':
			case 'allow_self_checkin':
			case 'allow_serialcirc':
			case 'allow_scan_request':
			case 'allow_contribution':
				$content .= $this->get_cell_visible_flag($object, $property);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->idstatut;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['empr_statut_create_bt'];
	}
}