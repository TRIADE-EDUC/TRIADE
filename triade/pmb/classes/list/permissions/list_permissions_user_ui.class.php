<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_permissions_user_ui.class.php,v 1.1 2023/02/02 08:04:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_permissions_user_ui extends list_ui {
	
	protected function _init_permissions() {
		global $dsi_active, $acquisition_active, $pmb_transferts_actif;
		global $cms_active, $pmb_extension_tab, $demandes_active, $fiches_active;
		global $acquisition_rent_requests_activate, $semantic_active;
		global $thesaurus_concepts_active, $modelling_active;
		global $frbr_active, $animations_active;
		
		$this->add_permission('circ', '5', CIRCULATION_AUTH);
		$this->add_permission('catal', '93', CATALOGAGE_AUTH);
		$this->add_permission('auth', '132', AUTORITES_AUTH);
		$this->add_permission('thesaurus', 'thesaurus_auth', THESAURUS_AUTH);
		
		$this->add_permission('modifcbexpl', 'catal_modif_cb_expl_droit', CATAL_MODIF_CB_EXPL_AUTH);
		$this->add_permission('edit', '1100', EDIT_AUTH);
		if ($dsi_active) {
			$this->add_permission('dsi', 'dsi_droit', DSI_AUTH);
		}
		if ($acquisition_active) {
			$this->add_permission('acquisition', 'acquisition_droit', ACQUISITION_AUTH);
		}
		$this->add_permission('restrictcirc', 'restrictcirc_auth', RESTRICTCIRC_AUTH);
		$this->add_permission('editforcing', 'edit_droit_forcing', EDIT_FORCING_AUTH);
		$this->add_permission('pref', '933', PREF_AUTH);
		if ($pmb_transferts_actif) {
			$this->add_permission('transferts', 'transferts_droit', TRANSFERTS_AUTH);
		}
		$this->add_permission('admin', '7', ADMINISTRATION_AUTH);
		$this->add_permission('sauv', '28', SAUV_AUTH);
		if ($cms_active) {
			$this->add_permission('cms', 'cms_onglet_title', CMS_AUTH);
			$this->add_permission('cms_build', 'cms_build_tab', CMS_BUILD_AUTH);
		}
		if ($pmb_extension_tab) {
			$this->add_permission('extensions', 'extensions_droit', EXTENSIONS_AUTH);
		}
		if ($demandes_active) {
			$this->add_permission('demandes', 'demandes_droit', DEMANDES_AUTH);
		}
		if ($fiches_active) {
			$this->add_permission('fiches', 'onglet_fichier', FICHES_AUTH);
		}
		if ($acquisition_active && $acquisition_rent_requests_activate) {
			$this->add_permission('acquisition_account_invoice', 'acquisition_account_invoice_flg', ACQUISITION_ACCOUNT_INVOICE_AUTH);
		}
		if ($semantic_active){
			$this->add_permission('semantic', 'semantic_flg', SEMANTIC_AUTH);
		}
		if($thesaurus_concepts_active){
			$this->add_permission('concepts', 'ontology_skos_menu', CONCEPTS_AUTH);
		}
		if($modelling_active){
			$this->add_permission('modelling', 'modelling', MODELLING_AUTH);
		}
		if($frbr_active){
			$this->add_permission('frbr', 'frbr', FRBR_AUTH);
		}
		if($animations_active){
			$this->add_permission('animations', 'animations', ANIMATION_AUTH);
		}
	}
	
	protected function fetch_data() {
		$this->objects = array();
		$this->_init_permissions();
		$this->pager['nb_results'] = count($this->objects);
		$this->messages = "";
	}
	
// 	protected function init_default_applied_sort() {
// 		$this->add_applied_sort('label');
// 	}
	
	public function add_permission($name, $label_code, $rights) {
		global $msg;
		
		$permission = array('name' => $name, 'label' => $msg[$label_code], 'rights' => $rights);
		$this->add_object((object) $permission);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters['main_fields'] = array();
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'label' => '103',
						'access' => '',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('label');
		$this->add_column('access');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('label', 'align', 'left');
	}
}