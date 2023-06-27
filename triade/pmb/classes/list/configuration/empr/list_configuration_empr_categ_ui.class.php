<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_empr_categ_ui.class.php,v 1.1 2018-10-12 11:59:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/configuration/empr/list_configuration_empr_ui.class.php");

class list_configuration_empr_categ_ui extends list_configuration_empr_ui {
	
	protected function _get_query_base() {
		return 'SELECT id_categ_empr, libelle, duree_adhesion, tarif_abt, age_min, age_max FROM empr_categ';
	}
	
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'libelle',
				'asc_desc' => 'asc'
		);
	}
	
	protected function get_main_fields_from_sub() {
		global $pmb_gestion_financiere, $pmb_gestion_abonnement;
		if ($pmb_gestion_financiere) {
			$gestion_abts=$pmb_gestion_abonnement; 
		} else $gestion_abts=0;
		
		$main_fields = array(
				'libelle' => '103',
				'duree_adhesion' => '1400'
		);
		if ($gestion_abts) {
			$main_fields['tarif_abt'] = 'empr_categ_tarif';
		}
		$main_fields['age_min'] = 'empr_categ_age_min';
		$main_fields['age_max'] = 'empr_categ_age_max';
		return $main_fields;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		global $pmb_gestion_financiere, $pmb_gestion_abonnement;
		if ($pmb_gestion_financiere) {
			$gestion_abts=$pmb_gestion_abonnement;
		} else $gestion_abts=0;
		
		$content = '';
		switch($property) {
			case 'tarif_abt':
				if ($gestion_abts==1) {
					$content .= $object->tarif_abt;
				} else if ($gestion_abts==2) {
					$content .= $msg["finance_see_finance"];
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->id_categ_empr;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['524'];
	}
}