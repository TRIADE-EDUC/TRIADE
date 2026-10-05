<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_acquisition_mode_ui.class.php,v 1.1 2021/01/15 10:57:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_acquisition_mode_ui extends list_configuration_acquisition_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM paiements';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('libelle');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('libelle', 'text', array('italic' => true));
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'libelle' => '103',
		);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->id_paiement;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['acquisition_ajout_mode'];
	}
}