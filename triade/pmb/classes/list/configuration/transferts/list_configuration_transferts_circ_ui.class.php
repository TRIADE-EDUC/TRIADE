<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_transferts_circ_ui.class.php,v 1.5 2022/09/30 12:56:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_transferts_circ_ui extends list_configuration_transferts_ui {
	
	protected function fetch_data() {
		global $msg;
		
		$this->objects = array();
		//Nb de lignes par page
		$this->add_parameter('transferts', 'tableau_nb_lignes', 'admin_transferts_lib_nb_lignes');
		//Nombre de jours avant que l'alerte ne s'affiche en retour
		$this->add_parameter('transferts', 'nb_jours_alerte', 'admin_transferts_lib_nb_jours_alerte');
		//Autorise le traitement par lot en envoi
		$this->add_selector_parameter('transferts', 'envoi_lot', 'admin_transferts_lib_envoi_lot');
		//Autorise le traitement par lot en reception
		$this->add_selector_parameter('transferts', 'reception_lot', 'admin_transferts_lib_reception_lot');
		//Autorise le traitement par lot pour les retours
		$this->add_selector_parameter('transferts', 'retour_lot', 'admin_transferts_lib_retour_lot');
		
		$this->add_separator_parameter('admin_transferts_sep_pret_exemplaire');
		
		//Action par défaut si le document est en transfert
		$values = array (
				array ("value" => "0", "label" => $msg ["admin_transferts_lib_pret_statut_transfert_non"] ),
				array ("value" => "1", "label" => $msg ["admin_transferts_lib_pret_statut_transfert_oui"] )
		);
		$this->add_selector_parameter('transferts', 'pret_statut_transfert', 'admin_transferts_lib_pret_statut_transfert', $values);
		
		$this->add_separator_parameter('admin_transferts_sep_retour_exemplaire');
		
		//Empêcher le retour de l'exemplaire sur un site autre que le sien
		$this->add_selector_parameter('transferts', 'retour_origine', 'admin_transferts_lib_force_retour_origine');
		//Autoriser à forcer le retour de l'exemplaire sur un site autre que le sien
		$this->add_selector_parameter('transferts', 'retour_origine_force', 'admin_transferts_lib_force_retour_origine_autorise');
		
		//Action par défaut lors d'un retour sur un autre site
		$values = array (
				array ("value" => "0", "label" => $msg ["admin_transferts_lib_retour_action_plus_tard"] ),
				array ("value" => "1", "label" => $msg ["admin_transferts_lib_retour_action_loc"] ),
				array ("value" => "2", "label" => $msg ["admin_transferts_lib_retour_action_trans"] )
		);
		$this->add_selector_parameter('transferts', 'retour_action_defaut', 'admin_transferts_lib_retour_action_defaut', $values);
		//Autorise une autre action que celle par défaut
		$this->add_selector_parameter('transferts', 'retour_action_autorise_autre', 'admin_transferts_lib_retour_autorise_autre');
		
		//Sauvegarder l'ancienne localisation en cas de changement de localisation au retour
		$values = array (
				array ("value" => "0", "label" => $msg ["admin_transferts_lib_retour_loc_pas_sauv"] ),
				array ("value" => "1", "label" => $msg ["admin_transferts_lib_retour_loc_sauv"] )
		);
		$this->add_selector_parameter('transferts', 'retour_change_localisation', 'admin_transferts_lib_retour_loc', $values);
		//Etat du transfert généré
		$values = array (
				array ("value" => "0", "label" => $msg ["admin_transferts_lib_retour_trans_creer"] ),
				array ("value" => "1", "label" => $msg ["admin_transferts_lib_retour_trans_envoi"] )
		);
		$this->add_selector_parameter('transferts', 'retour_etat_transfert', 'admin_transferts_lib_retour_trans', $values);
		//Motif du transfert généré automatiquement
		$this->add_parameter('transferts', 'retour_motif_transfert', 'admin_transferts_lib_motif_transfert');
		
		//Génère un transfert pour répondre à une réservation
		$this->add_selector_parameter('transferts', 'retour_action_resa', 'admin_transferts_lib_retour_action_resa');
	}
	
	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'section');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('grouped_objects', 'sort', false);
		$this->set_setting_column('valeur_param', 'display_mode', 'edition');
		$this->settings['objects']['default']['display_mode'] = 'form_table';
	}
	
	protected function get_message_not_grouped() {
		return '';
	}
	
	protected function get_display_group_header_list($group_label, $level=1, $uid='') {
		if($group_label == $this->get_message_not_grouped()) {
			return '';
		}
		$display = "
		<tr id='".$uid."_group_header'>
			<th colspan='".count($this->columns)."'>
				".$this->get_cell_group_label($group_label, ($level-1))."
			</th>
		</tr>";
		return $display;
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
					case 'tableau_nb_lignes':
					case 'nb_jours_alerte':
						$content .= $this->get_cell_edition_format_content($object, $property, 'number');
						break;
					case 'envoi_lot':
					case 'reception_lot':
					case 'retour_lot':
					case 'pret_statut_transfert':
					case 'retour_origine':
					case 'retour_origine_force':
					case 'retour_action_defaut':
					case 'retour_action_autorise_autre':
					case 'retour_change_localisation':
					case 'retour_etat_transfert':
					case 'retour_action_resa':
						$content .= $this->get_cell_edition_format_content($object, $property, 'select');
						break;
					case 'retour_motif_transfert':
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