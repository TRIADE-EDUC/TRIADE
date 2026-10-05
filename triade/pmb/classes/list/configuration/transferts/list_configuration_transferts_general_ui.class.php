<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_transferts_general_ui.class.php,v 1.2 2021/02/25 13:27:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_transferts_general_ui extends list_configuration_transferts_ui {
	
	protected function fetch_data() {
		$this->objects = array();
		//Activation de l'étape de validation
		$this->add_selector_parameter('transferts', 'validation_actif', 'admin_transferts_lib_active_validation');
		//Regrouper la gestion des départs: Validation, envoi, retour
		$this->add_selector_parameter('transferts', 'regroupement_depart', 'admin_transferts_regroupement_depart');
		//Nb de jours de dépôt par défaut
		$this->add_parameter('transferts','nb_jours_pret_defaut', 'admin_transferts_lib_nb_jours_pret');
		//Statut de l'exemplaire après validation
		$this->add_selector_status_parameter('transferts', 'statut_validation', 'admin_transferts_lib_statut_validation', 'admin_transferts_lib_statut_validation_pas_chg');
		//Appliquer ce statut avant la validation
		$this->add_selector_parameter('transferts', 'pret_demande_statut', 'admin_transferts_pret_demande_statut');
		//Statut de l'exemplaire pendant le transfert
		$this->add_selector_status_parameter('transferts', 'statut_transferts', 'admin_transferts_lib_statut_transfert');
		//Activer les exemplaires fantômes
		$this->add_selector_parameter('transferts', 'ghost_expl_enable', 'admin_transferts_lib_activate_ghost_expl');
		//Choix du script de génération des codes barres des exemplaires fantômes
		$this->add_parameter('transferts','ghost_expl_gen_script', 'admin_transferts_lib_ghost_expl_gen_script');
		//Statut de l'exemplaire fantôme
		$this->add_selector_status_parameter('transferts', 'ghost_statut_expl_transferts', 'admin_transferts_lib_statut_ghost_transfert');
		//Afficher la source et la destination en édition
		$this->add_selector_parameter('transferts', 'edition_show_all_colls', 'admin_transferts_lib_edition_show_all_colls');
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
					case 'nb_jours_pret_defaut':
						$content .= $this->get_cell_edition_format_content($object, $property, 'number');
						break;
					case 'validation_actif':
					case 'regroupement_depart':
					case 'statut_validation':
					case 'pret_demande_statut':
					case 'statut_transferts':
					case 'ghost_expl_enable':
					case 'ghost_statut_expl_transferts':
					case 'edition_show_all_colls':
						$content .= $this->get_cell_edition_format_content($object, $property, 'select');
						break;
					case 'ghost_expl_gen_script':
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