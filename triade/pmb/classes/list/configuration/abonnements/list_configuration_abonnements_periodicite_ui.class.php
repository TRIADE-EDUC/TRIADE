<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_abonnements_periodicite_ui.class.php,v 1.5 2021/04/19 07:10:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_abonnements_periodicite_ui extends list_configuration_abonnements_ui {
	
	protected function _get_query_base() {
		return 'SELECT periodicite_id, libelle, duree, unite, seuil_periodicite, retard_periodicite,consultation_duration FROM abts_periodicites';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('libelle');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('duree', 'data_type', 'integer');
		$this->set_setting_column('seuil_periodicite', 'datatype', 'integer');
		$this->set_setting_column('retard_periodicite', 'datatype', 'integer');
		$this->set_setting_column('consultation_duration', 'datatype', 'integer');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'libelle' => 'abonnements_periodicite_libelle',
				'duree' => 'abonnements_periodicite_duree',
				'unite' => 'abonnements_periodicite_unite',
				'seuil_periodicite' => 'seuil_periodicite',
				'retard_periodicite' => 'retard_periodicite',
				'consultation_duration' => 'serialcirc_consultation_duration',
				
		);
	}
	
	protected function _get_object_property_unite($object) {
		global $msg;
		
		switch($object->unite) {
			case '0':return $msg['abonnements_periodicite_unite_jour'];
			case '1':return $msg['abonnements_periodicite_unite_mois'];
			case '2':return $msg['abonnements_periodicite_unite_annee'];
		}
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->periodicite_id;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['abonnements_ajouter_une_periodicite'];
	}
}