<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_tabs_admin_ui.class.php,v 1.15 2024/02/16 15:06:29 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_tabs_admin_ui extends list_tabs_ui {
	
	protected function _init_tabs() {
		global $animations_active;
		global $pmb_logs_activate, $opac_visionneuse_allow, $pmb_opac_view_activate, $opac_search_universes_activate;
		global $pmb_nomenclature_activate;
		global $pmb_quotas_avances, $pmb_utiliser_calendrier;
		global $pmb_gestion_financiere, $pmb_gestion_abonnement, $pmb_gestion_tarif_prets, $pmb_gestion_amende;
		global $pmb_planificateur_allow, $pmb_selfservice_allow, $acquisition_active;
		global $pmb_transferts_actif, $gestion_acces_active, $pmb_javascript_office_editor;
		global $demandes_active, $faq_active, $pmb_scan_request_activate, $pmb_ark_activate;
		global $pmb_digital_signature_activate;
		global $security_mfa_active, $ai_active;
		
		//Administration
		$this->add_tab('7', 'docs', 'admin_menu_exemplaires');
		$this->add_tab('7', 'notices', 'admin_menu_notices');
		$this->add_tab('7', 'authorities', 'admin_menu_authorities');
		$this->add_tab('7', 'docnum', 'admin_menu_upload_docnum');
		$this->add_tab('7', 'collstate', 'admin_etats_collections');
		$this->add_tab('7', 'abonnements', 'admin_menu_abonnements');
		$this->add_tab('7', 'empr', '22');
		$this->add_tab('7', 'users', '25');
		$this->add_tab('7', 'cms_editorial', 'editorial_content');
		$this->add_tab('7', 'loans', 'admin_menu_loans');
		$this->add_tab('7', 'pnb', 'admin_menu_pnb', 'param');
		$this->add_tab('7', 'composed_vedettes', 'admin_menu_composed_vedettes', 'grammars');
		if($animations_active){
			$this->add_tab('7', 'animations', 'animations', '');
		}
		if($pmb_digital_signature_activate){
			$this->add_tab('7', 'digital_signature', 'digital_signature', 'admin_digital_signature');
		}
		$this->add_tab('7', 'thumbnail', 'admin_vignette_menu', 'admin_thumbnail');
		
		//Gestion
		$this->add_tab('admin_menu_gestion', 'gestion', 'gestion_facette', 'facettes');
		
		//OPAC
		$this->add_tab('opac_admin_menu', 'infopages', 'infopages_admin_menu');
		$this->add_tab('opac_admin_menu', 'opac', 'search_persopac_list_title', 'search_persopac', '&section=liste');
		$this->add_tab('opac_admin_menu', 'opac', 'exemplaire_admin_navigopac', 'navigopac');
		$this->add_tab('opac_admin_menu', 'opac', 'opac_facette', 'facettes');
		if($pmb_logs_activate) {
			$this->add_tab('opac_admin_menu', 'opac', 'stat_opac_menu', 'stat', '&section=view_list');
		}
		if($opac_visionneuse_allow) {
			$this->add_tab('opac_admin_menu', 'visionneuse', 'visionneuse_admin_menu');
		}
		if($pmb_opac_view_activate) {
			$this->add_tab('opac_admin_menu', 'opac', 'opac_view_admin_menu', 'opac_view', '&section=list');
		}
		$this->add_tab('opac_admin_menu', 'contact_forms', 'admin_opac_contact_forms');
		$this->add_tab('opac_admin_menu', 'opac', 'admin_opac_maintenance', 'maintenance');
		if($opac_search_universes_activate){
			$this->add_tab('opac_admin_menu', 'search_universes', 'admin_menu_search_universes');
		}
		$this->add_tab('opac_admin_menu', 'opac', 'analytics_services', 'analytics_services');
		
		//Actions
		$this->add_tab('admin_menu_act', 'proc', 'admin_menu_act_perso', 'proc');
		$this->add_tab('admin_menu_act', 'proc', 'admin_menu_act_perso_clas', 'clas');
		
		//Nomenclatures
		if($pmb_nomenclature_activate) {
			$this->add_tab('admin_menu_nomenclature', 'family', 'admin_menu_nomenclature_tutti', 'family');
			$this->add_tab('admin_menu_nomenclature', 'formation', 'admin_menu_nomenclature_formations', 'formation');
			$this->add_tab('admin_menu_nomenclature', 'voice', 'admin_menu_nomenclature_voice', 'voice');
			$this->add_tab('admin_menu_nomenclature', 'instrument', 'admin_menu_nomenclature_instruments', 'instrument');
			$this->add_tab('admin_menu_nomenclature', 'material', 'admin_menu_nomenclature_material', 'material');
		}
		
		//Modules
		if($pmb_quotas_avances) {
			$this->add_tab('admin_menu_modules', 'quotas', 'admin_quotas');
		}
		if($pmb_utiliser_calendrier) {
			$this->add_tab('admin_menu_modules', 'calendrier', 'admin_calendrier');
		}
		if(($pmb_gestion_financiere)&&(($pmb_gestion_abonnement==2)||($pmb_gestion_tarif_prets==2)||($pmb_gestion_amende))) {
			$this->add_tab('admin_menu_modules', 'finance', 'admin_gestion_financiere');
		}
		$this->add_tab('admin_menu_modules', 'import', '519');
		$this->add_tab('admin_menu_modules', 'convert', 'admin_conversion');
		$this->add_tab('admin_menu_modules', 'harvest', 'admin_harvest');
		$this->add_tab('admin_menu_modules', 'misc', '27');
		$this->add_tab('admin_menu_modules', 'z3950', 'Z39.50');
		if($pmb_planificateur_allow) {
			$this->add_tab('admin_menu_modules', 'planificateur', 'planificateur_admin_menu');
		}
		$this->add_tab('admin_menu_modules', 'external_services', 'es_admin_menu');
		$this->add_tab('admin_menu_modules', 'connecteurs', 'admin_connecteurs_menu');
		if($pmb_selfservice_allow) {
			$this->add_tab('admin_menu_modules', 'selfservice', 'selfservice_admin_menu');
		}
		$this->add_tab('admin_menu_modules', 'sauvegarde', '28');
		if($acquisition_active) {
			$this->add_tab('admin_menu_modules', 'acquisition', 'admin_acquisition');
		}
		if($pmb_transferts_actif) {
			$this->add_tab('admin_menu_modules', 'transferts', 'admin_menu_transferts');
		}
		if($gestion_acces_active==1) {
			$this->add_tab('admin_menu_modules', 'acces', 'admin_menu_acces');
		}
		if($pmb_javascript_office_editor) {
			$this->add_tab('admin_menu_modules', 'html_editor', 'admin_html_editor');
		}
		if($demandes_active) {
			$this->add_tab('admin_menu_modules', 'demandes', 'admin_demandes');
		}
		if($faq_active) {
			$this->add_tab('admin_menu_modules', 'faq', 'admin_faq');
		}
		$this->add_tab('admin_menu_modules', 'mailtpl', 'admin_mailtpl');
		if($pmb_scan_request_activate) {
			$this->add_tab('admin_menu_modules', 'scan_request', 'admin_menu_scan_request');
		}
		if($pmb_ark_activate) {
			$this->add_tab('admin_menu_modules', 'ark', 'admin_menu_ark');
		}
		$this->add_tab('admin_menu_modules', 'interface', 'interface', 'modules');
		$this->add_tab('admin_menu_modules', 'mails', 'mails', 'configuration');
		$this->add_tab('admin_menu_modules', 'supervision', 'supervision', 'mails');

		$this->add_tab('admin_menu_security', 'auth', 'admin_menu_auth');

		if($security_mfa_active) {
    		$this->add_tab('admin_menu_security', 'mfa', 'admin_menu_mfa', 'services');
		}

		if($ai_active){
			$this->add_tab('admin_menu_modules', 'ai', 'admin_menu_ai');
		}

	}
	
	protected function is_active_tab($label_code, $categ, $sub='') {
		$active = false;
		switch ($label_code) {
			case 'opac_facette':
				if(($this->is_equal_var_get('categ', 'opac') && $this->is_equal_var_get('sub', array("facettes", "facettes_authorities", "facettes_external", "facettes_comparateur")))) {
					$active = true;
				}
				break;
			case 'visionneuse_admin_menu':
				if(($this->is_equal_var_get('categ', 'visionneuse') && (empty($_GET['sub']) || $this->is_equal_var_get('sub', array("class", "mimetype"))))) {
					$active = true;
				}
				break;
			case '27':
				if($this->is_equal_var_get('categ', array("misc", "netbase", "chklnk", "alter", "param"))) {
					$active = true;
				}
				break;
			default:
				$active = parent::is_active_tab($label_code, $categ, $sub);
				break;
		}
		return $active;
	}
}