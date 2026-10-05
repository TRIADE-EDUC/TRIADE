<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_empr_categ_ui.class.php,v 1.5 2021/04/19 07:10:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_empr_categ_ui extends list_configuration_empr_ui {
	
	protected function _get_query_base() {
		return 'SELECT id_categ_empr, libelle, duree_adhesion, tarif_abt, age_min, age_max FROM empr_categ';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('libelle');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('duree_adhesion', 'datatype', 'integer');
		$this->set_setting_column('age_min', 'datatype', 'integer');
		$this->set_setting_column('age_max', 'datatype', 'integer');
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
	
	protected function _get_object_property_tarif_abt($object) {
		global $msg;
		global $pmb_gestion_financiere, $pmb_gestion_abonnement;
		
		if ($pmb_gestion_financiere) {
			if ($pmb_gestion_abonnement==1) {
				return $object->tarif_abt;
			} else if ($pmb_gestion_abonnement==2) {
				return $msg["finance_see_finance"];
			}
		}
		return '';
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->id_categ_empr;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['524'];
	}
}