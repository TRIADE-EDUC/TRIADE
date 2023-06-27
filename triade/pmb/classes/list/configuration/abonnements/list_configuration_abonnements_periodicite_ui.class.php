<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_abonnements_periodicite_ui.class.php,v 1.1 2018-10-12 11:59:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/configuration/abonnements/list_configuration_abonnements_ui.class.php");

class list_configuration_abonnements_periodicite_ui extends list_configuration_abonnements_ui {
	
	protected function _get_query_base() {
		return 'SELECT periodicite_id, libelle, duree, unite, seuil_periodicite, retard_periodicite,consultation_duration FROM abts_periodicites';
	}
	
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'libelle',
				'asc_desc' => 'asc'
		);
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
	
	protected function get_cell_content($object, $property) {
		global $msg;
	
		$content = '';
		switch($property) {
			case 'unite':
				switch($object->unite) {
					case '0':$content .= $msg['abonnements_periodicite_unite_jour'];break;
					case '1':$content .= $msg['abonnements_periodicite_unite_mois'];break;
					case '2':$content .= $msg['abonnements_periodicite_unite_annee'];break;
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->periodicite_id;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['abonnements_ajouter_une_periodicite'];
	}
}