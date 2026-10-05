<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_finance_abts_ui.class.php,v 1.3 2021/04/19 07:10:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_finance_abts_ui extends list_configuration_finance_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM type_abts';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('type_abt_libelle');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'type_abt_libelle' => '103',
// 				'prepay' => 'type_abts_prepay',
// 				'prepay_deflt_mnt' => 'type_abts_prepay_dflt',
				'tarif' => 'type_abts_tarif',
				'caution' => 'type_abts_caution',
		);
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('prepay', 'align', 'center');
		$this->set_setting_column('prepay', 'datatype', 'boolean');
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->id_type_abt;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['type_abts_add'];
	}
}