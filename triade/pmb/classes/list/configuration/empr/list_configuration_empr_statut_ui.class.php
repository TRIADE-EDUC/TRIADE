<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_empr_statut_ui.class.php,v 1.5 2023/03/24 07:44:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_empr_statut_ui extends list_configuration_empr_ui {
	
	protected function _get_query_base() {
		return 'SELECT idstatut, statut_libelle, allow_loan, allow_loan_hist, allow_book, allow_opac, allow_dsi, allow_dsi_priv, allow_sugg, allow_dema, allow_prol, allow_avis, allow_tag , allow_pwd, allow_liste_lecture, allow_self_checkout, allow_self_checkin, allow_serialcirc, allow_scan_request, allow_contribution FROM empr_statut';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('statut_libelle');
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
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('default', 'align', 'center');
		$this->set_setting_column('statut_libelle', 'align', 'left');
		$this->set_setting_column('allow_loan', 'datatype', 'boolean');
		$this->set_setting_column('allow_loan_hist', 'datatype', 'boolean');
		$this->set_setting_column('allow_book', 'datatype', 'boolean');
		$this->set_setting_column('allow_opac', 'datatype', 'boolean');
		$this->set_setting_column('allow_dsi', 'datatype', 'boolean');
		$this->set_setting_column('allow_dsi_priv', 'datatype', 'boolean');
		$this->set_setting_column('allow_sugg', 'datatype', 'boolean');
		$this->set_setting_column('allow_liste_lecture', 'datatype', 'boolean');
		$this->set_setting_column('allow_dema', 'datatype', 'boolean');
		$this->set_setting_column('allow_prol', 'datatype', 'boolean');
		$this->set_setting_column('allow_avis', 'datatype', 'boolean');
		$this->set_setting_column('allow_tag', 'datatype', 'boolean');
		$this->set_setting_column('allow_pwd', 'datatype', 'boolean');
		$this->set_setting_column('allow_self_checkout', 'datatype', 'boolean');
		$this->set_setting_column('allow_self_checkin', 'datatype', 'boolean');
		$this->set_setting_column('allow_serialcirc', 'datatype', 'boolean');
		$this->set_setting_column('allow_scan_request', 'datatype', 'boolean');
		$this->set_setting_column('allow_contribution', 'datatype', 'boolean');
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		switch($property) {
			case 'statut_libelle':
				if ($object->idstatut<=2) {
					return array(
							'style' => 'font-weight:bold;'
					);
				}
		}
		return array();
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->idstatut;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['empr_statut_create_bt'];
	}
}