<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_transferts_opac_ui.class.php,v 1.2 2021/02/25 13:27:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_transferts_opac_ui extends list_configuration_transferts_ui {
	
	protected function fetch_data() {
		global $msg;
		
		$this->objects = array();
		//Choix pour la réservation
		$values = array (
				array ("value" => "0", "label" => $msg ["admin_transferts_opac_site_util"] ),
				array ("value" => "1", "label" => $msg ["admin_transferts_opac_site_choix"] ),
				array ("value" => "2", "label" => $msg ["admin_transferts_opac_site_precise"] ),
				array ("value" => "3", "label" => $msg ["admin_transferts_opac_site_ex"] )
		);
		$this->add_selector_parameter('transferts','choix_lieu_opac', 'admin_transferts_lib_choix_opac', $values);
		//Site si lieu fixe précisé
		$values = array (
				array (
						"query" => "SELECT idlocation,location_libelle FROM docs_location",
						"affichage" => "SELECT location_libelle FROM docs_location WHERE idlocation=!!id!!"
				)
		);
		$this->add_selector_parameter('transferts','site_fixe', 'admin_transferts_lib_site_fixe', $values);
		
		//Motif du transfert généré automatiquement
		$this->add_parameter('transferts','resa_motif_transfert', 'admin_transferts_lib_motif_transfert_resa');
		
		//Type du transfert généré
		$values = array (
				array ("value" => "0", "label" => $msg ["admin_transferts_lib_retour_trans_creer"] ),
				array ("value" => "1", "label" => $msg ["admin_transferts_lib_retour_trans_envoi"] )
		);
		$this->add_selector_parameter('transferts','resa_etat_transfert', 'admin_transferts_lib_etat_trans_resa', $values);
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('valeur_param', 'display_mode', 'edition');
		$this->settings['objects']['default']['display_mode'] = 'form_table';
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'label' => 'admin_tranferts_titre_tableau_param',
				'valeur_param' => 'admin_tranferts_titre_tableau_valeur',
		);
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'label', 'valeur_param'
		);
	}
	
	protected function get_cell_edition_content($object, $property) {
		$content = '';
		switch($property) {
			default :
				switch ($object->sstype_param) {
					case 'choix_lieu_opac':
					case 'site_fixe':
					case 'resa_etat_transfert':
						$content .= $this->get_cell_edition_format_content($object, $property, 'select');
						break;
					case 'resa_motif_transfert':
						$content .= $this->get_cell_edition_format_content($object, $property, 'text');
						break;
				}
				break;
		}
		return $content;
	}
	
	protected function get_button_add() {
		return '';
	}
}