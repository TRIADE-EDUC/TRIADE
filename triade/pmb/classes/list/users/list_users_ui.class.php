<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_users_ui.class.php,v 1.30.2.2.2.1 2025/02/20 15:31:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

use Pmb\Animations\Models\AnimationCalendarModel;
use Pmb\Animations\Orm\AnimationCalendarOrm;

require_once($class_path.'/user.class.php');

class list_users_ui extends list_ui {

	protected function _get_query_base() {
		$query = 'SELECT users.userid as id, users.* FROM users';
		return $query;
	}

	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'groups' => 'users_groups'
				)
		);
		$this->available_filters['custom_fields'] = array();
	}

	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {

		$this->filters = array(
				'groups' => array(),
		);
		parent::init_filters($filters);
	}

	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		global $pmb_contribution_area_activate, $acquisition_active, $demandes_active, $opac_websubscribe_show;
		global $animations_active;

		$query = "DESC users";
		$result = pmb_mysql_query($query);
		while ($row = pmb_mysql_fetch_object($result)) {
			if(empty($this->available_columns['main_fields'][$row->Field])) {
				$this->available_columns['main_fields'][$row->Field] = $row->Field;
			}
		}
		$this->available_columns['main_fields']['userid'] = '1601';
		$this->available_columns['main_fields']['username'] = '91';
		$this->available_columns['main_fields']['nom'] = '67';
		$this->available_columns['main_fields']['prenom'] = '68';
		$this->available_columns['main_fields']['user_lang'] = 'user_langue';
		$this->available_columns['main_fields']['nb_per_page_search'] = '900';
		$this->available_columns['main_fields']['nb_per_page_select'] = '901';
		$this->available_columns['main_fields']['nb_per_page_gestion'] = '902';
		$this->available_columns['main_fields']['user_email'] = 'email';
		$this->available_columns['main_fields']['user_alert_resamail'] = 'alert_resa_user_mail';
		if($pmb_contribution_area_activate) {
			$this->available_columns['main_fields']['user_alert_contribmail'] = 'alert_contrib_user_mail';
		} else {
			unset($this->available_columns['main_fields']['user_alert_contribmail']);
		}
		if($acquisition_active) {
			$this->available_columns['main_fields']['user_alert_suggmail'] = 'alert_sugg_user_mail';
		} else {
			unset($this->available_columns['main_fields']['user_alert_suggmail']);
		}
		if($demandes_active) {
			$this->available_columns['main_fields']['user_alert_demandesmail'] = 'alert_demandes_user_mail';
		} else {
			unset($this->available_columns['main_fields']['user_alert_demandesmail']);
		}
		if($opac_websubscribe_show) {
			$this->available_columns['main_fields']['user_alert_subscribemail'] = 'alert_subscribe_user_mail';
		} else {
			unset($this->available_columns['main_fields']['user_alert_subscribemail']);
		}
		if($animations_active) {
			$this->available_columns['main_fields']['user_alert_animation_mail'] = 'alert_animation_user_mail';
		} else {
			unset($this->available_columns['main_fields']['user_alert_animation_mail']);
		}
		$this->available_columns['main_fields']['user_alert_serialcircmail'] = 'alert_subscribe_serialcirc_mail';
		$this->available_columns['main_fields']['group'] = '919';

		//Retirons les colonnes en trop
		unset($this->available_columns['main_fields']['param_licence']);
		unset($this->available_columns['main_fields']['pwd']);
		unset($this->available_columns['main_fields']['user_digest']);
		unset($this->available_columns['main_fields']['speci_coordonnees_etab']);
		unset($this->available_columns['main_fields']['explr_invisible']);
		unset($this->available_columns['main_fields']['explr_visible_mod']);
		unset($this->available_columns['main_fields']['explr_visible_unmod']);
		unset($this->available_columns['main_fields']['environnement']);
		unset($this->available_columns['main_fields']['grp_num']);
	}

	/**
	 * Initialisation des colonnes éditables disponibles
	 */
	protected function init_available_editable_columns() {
		$this->available_editable_columns = array(
				'nb_per_page_search',
				'nb_per_page_select',
				'nb_per_page_gestion',
				'param_popup_ticket',
				'param_sounds',
				'param_rfid_activate',
				'param_chat_activate',
				'param_licence',
				'deflt_notice_statut',
				'deflt_notice_statut_analysis',
				'deflt_integration_notice_statut',
				'xmlta_indexation_lang',
				'deflt_docs_type',
				'deflt_serials_docs_type',
				'deflt_lenders',
				'deflt_styles',
				'deflt_docs_statut',
				'deflt_docs_codestat',
				'value_deflt_lang',
				'value_deflt_fonction',
				'value_deflt_relation',
				'value_deflt_relation_serial',
				'value_deflt_relation_bulletin',
				'value_deflt_relation_analysis',
				'deflt_docs_location',
				'deflt_collstate_location',
				'deflt_bulletinage_location',
				'deflt_resas_location',
				'deflt_docs_section',
				'value_deflt_module',
				'user_alert_resamail',
				'user_alert_contribmail',
				'user_alert_demandesmail',
				'user_alert_subscribemail',
				'user_alert_serialcircmail',
				'user_alert_animation_mail',
				'deflt2docs_location',
				'deflt_empr_statut',
				'deflt_empr_categ',
				'deflt_empr_codestat',
				'deflt_thesaurus',
				'deflt_concept_scheme',
				'deflt_import_thesaurus',
				'xmlta_doctype',
				'xmlta_doctype_serial',
				'xmlta_doctype_bulletin',
				'xmlta_doctype_analysis',
				'value_deflt_antivol',
				'deflt_arch_statut',
				'deflt_arch_emplacement',
				'deflt_arch_type',
				'deflt_upload_repertoire',
				'deflt_short_loan_activate',
				'deflt_cashdesk',
				'user_alert_suggmail',
				'deflt_explnum_statut',
				'deflt_explnum_location',
				'deflt_explnum_lenders',
				'deflt_notice_replace_keep_categories',
				'deflt_notice_is_new',
				'deflt_agnostic_warehouse',
				'deflt_cms_article_statut',
				'deflt_cms_article_type',
				'deflt_cms_section_type',
				'deflt_scan_request_status',
				'xmlta_doctype_scan_request_folder_record',
				'deflt_camera_empr',
				'deflt_catalog_expanded_caddies',
				'deflt_notice_replace_links',
				'deflt_printer',
				'deflt_opac_visible_bulletinage',
				'deflt_scan_request_explnum_status',
				'deflt_type_abts',
				'deflt_docwatch_watch_filter_deleted',
				'deflt_pclassement',
				'deflt_associated_campaign',
				'deflt_bypass_isbn_page',
				'deflt_animation_calendar',
				'deflt_animation_waiting_list',
				'deflt_animation_automatic_registration',
				'deflt_animation_communication_type',
				'deflt_animation_unique_registration',
		    	'deflt_notice_catalog_categories_auto',
		    	'deflt_import_lenders'
		);
	}

	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('username');
	}

	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}

	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'id':
	            return 'userid';
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}

	protected function get_button_add() {
		global $msg;

		return $this->get_button('add', $msg['85']);
	}

	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('groups');
		parent::set_filters_from_form();
	}

	protected function init_default_columns() {
		$this->add_column('userid');
		$this->add_column('username');
		$this->add_column('nom');
		$this->add_column('prenom');
		$this->add_column('user_lang');
		$this->add_column('deflt_styles');
		$this->add_column('deflt_docs_location');
		$this->add_column('deflt_bulletinage_location');
		$this->add_column('deflt_resas_location');
		$this->add_column('user_email');
		$this->add_column('deflt2docs_location');
		$this->add_column('deflt_explnum_location');
		$this->add_column('deflt_animation_calendar');
		$this->add_column('deflt_animation_waiting_list');
		$this->add_column('deflt_animation_automatic_registration');
		$this->add_column('deflt_animation_communication_type');
		$this->add_column('deflt_animation_unique_registration');
		$this->add_column('deflt_notice_catalog_categories_auto');
		$this->add_column('deflt_import_lenders');
	}

	/**
	 * Initialisation des settings par défaut
	 */
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_display('pager', 'visible', false);
		$this->set_setting_selection_actions('edit', 'visible', false);

		$this->set_setting_column('userid', 'datatype', 'integer');
		$this->set_setting_column('create_dt', 'datatype', 'date');
		$this->set_setting_column('last_updated_dt', 'datatype', 'date');
		$this->set_setting_column('nb_per_page_search', 'datatype', 'integer');
		$this->set_setting_column('nb_per_page_select', 'datatype', 'integer');
		$this->set_setting_column('nb_per_page_gestion', 'datatype', 'integer');
		$this->set_setting_column('param_popup_ticket', 'datatype', 'boolean');
		$this->set_setting_column('param_sounds', 'datatype', 'boolean');
		$this->set_setting_column('param_rfid_activate', 'datatype', 'boolean');
		$this->set_setting_column('param_chat_activate', 'datatype', 'boolean');
		$this->set_setting_column('param_licence', 'datatype', 'boolean');
		$this->set_setting_column('deflt_notice_statut', 'datatype', 'integer');
		$this->set_setting_column('deflt_notice_statut_analysis', 'datatype', 'integer');
		$this->set_setting_column('deflt_integration_notice_statut', 'datatype', 'integer');
		$this->set_setting_column('deflt_docs_type', 'datatype', 'integer');
		$this->set_setting_column('deflt_serials_docs_type', 'datatype', 'integer');
		$this->set_setting_column('deflt_lenders', 'datatype', 'integer');
		$this->set_setting_column('deflt_docs_statut', 'datatype', 'integer');
		$this->set_setting_column('deflt_docs_codestat', 'datatype', 'integer');
		$this->set_setting_column('deflt_docs_location', 'datatype', 'integer');
		$this->set_setting_column('deflt_collstate_location', 'datatype', 'integer');
		$this->set_setting_column('deflt_bulletinage_location', 'datatype', 'integer');
		$this->set_setting_column('deflt_resas_location', 'datatype', 'integer');
		$this->set_setting_column('deflt_docs_section', 'datatype', 'integer');
		$this->set_setting_column('user_alert_resamail', 'datatype', 'boolean');
		$this->set_setting_column('user_alert_contribmail', 'datatype', 'boolean');
		$this->set_setting_column('user_alert_demandesmail', 'datatype', 'boolean');
		$this->set_setting_column('user_alert_subscribemail', 'datatype', 'boolean');
		$this->set_setting_column('user_alert_serialcircmail', 'datatype', 'boolean');
		$this->set_setting_column('user_alert_animation_mail', 'datatype', 'boolean');
		$this->set_setting_column('deflt2docs_location', 'datatype', 'integer');
		$this->set_setting_column('deflt_empr_statut', 'datatype', 'integer');
		$this->set_setting_column('deflt_empr_categ', 'datatype', 'integer');
		$this->set_setting_column('deflt_empr_codestat', 'datatype', 'integer');
		$this->set_setting_column('deflt_thesaurus', 'datatype', 'integer');
		$this->set_setting_column('deflt_concept_scheme', 'datatype', 'integer');
		$this->set_setting_column('deflt_import_thesaurus', 'datatype', 'integer');
		$this->set_setting_column('deflt3bibli', 'datatype', 'integer');
		$this->set_setting_column('deflt3exercice', 'datatype', 'integer');
		$this->set_setting_column('deflt3rubrique', 'datatype', 'integer');
		$this->set_setting_column('deflt3type_produit', 'datatype', 'integer');
		$this->set_setting_column('deflt3dev_statut', 'datatype', 'integer');
		$this->set_setting_column('deflt3cde_statut', 'datatype', 'integer');
		$this->set_setting_column('deflt3liv_statut', 'datatype', 'integer');
		$this->set_setting_column('deflt3fac_statut', 'datatype', 'integer');
		$this->set_setting_column('deflt3sug_statut', 'datatype', 'integer');
		$this->set_setting_column('param_allloc', 'datatype', 'boolean');
		$this->set_setting_column('deflt_arch_statut', 'datatype', 'integer');
		$this->set_setting_column('deflt_arch_emplacement', 'datatype', 'integer');
		$this->set_setting_column('deflt_arch_type', 'datatype', 'integer');
		$this->set_setting_column('deflt_upload_repertoire', 'datatype', 'integer');
		$this->set_setting_column('deflt_short_loan_activate', 'datatype', 'boolean');
		$this->set_setting_column('deflt3lgstatdev', 'datatype', 'integer');
		$this->set_setting_column('deflt3lgstatcde', 'datatype', 'integer');
		$this->set_setting_column('deflt3receptsugstat', 'datatype', 'integer');
		$this->set_setting_column('deflt_cashdesk', 'datatype', 'integer');
		$this->set_setting_column('user_alert_suggmail', 'datatype', 'boolean');
		$this->set_setting_column('deflt_explnum_statut', 'datatype', 'integer');
		$this->set_setting_column('deflt_explnum_location', 'datatype', 'integer');
		$this->set_setting_column('deflt_explnum_lenders', 'datatype', 'integer');
		$this->set_setting_column('deflt_notice_replace_keep_categories', 'datatype', 'boolean');
		$this->set_setting_column('deflt_notice_is_new', 'datatype', 'boolean');
		$this->set_setting_column('deflt_agnostic_warehouse', 'datatype', 'integer');
		$this->set_setting_column('deflt_cms_article_statut', 'datatype', 'integer');
		$this->set_setting_column('deflt_cms_article_type', 'datatype', 'integer');
		$this->set_setting_column('deflt_cms_section_type', 'datatype', 'integer');
		$this->set_setting_column('deflt_scan_request_status', 'datatype', 'integer');
		$this->set_setting_column('deflt_camera_empr', 'datatype', 'boolean');
		$this->set_setting_column('deflt_catalog_expanded_caddies', 'datatype', 'boolean');
		$this->set_setting_column('deflt_notice_replace_links', 'datatype', 'boolean');
		$this->set_setting_column('deflt_printer', 'datatype', 'integer');
		$this->set_setting_column('deflt_opac_visible_bulletinage', 'datatype', 'boolean');
		$this->set_setting_column('deflt_scan_request_explnum_status', 'datatype', 'integer');
		$this->set_setting_column('deflt_type_abts', 'datatype', 'integer');
		$this->set_setting_column('deflt_docwatch_watch_filter_deleted', 'datatype', 'boolean');
		$this->set_setting_column('deflt_pclassement', 'datatype', 'integer');
		$this->set_setting_column('deflt_associated_campaign', 'datatype', 'boolean');
		$this->set_setting_column('deflt_bypass_isbn_page', 'datatype', 'boolean');
		$this->set_setting_column('deflt_animation_calendar', 'datatype', 'interger');
		$this->set_setting_column('deflt_animation_waiting_list', 'datatype', 'boolean');
		$this->set_setting_column('deflt_animation_automatic_registration', 'datatype', 'boolean');
		$this->set_setting_column('deflt_animation_communication_type', 'datatype', 'interger');
		$this->set_setting_column('deflt_animation_unique_registration', 'datatype', 'boolean');
		$this->set_setting_column('deflt_notice_catalog_categories_auto', 'datatype', 'boolean');
		$this->set_setting_column('deflt_import_lenders', 'datatype', 'integer');

		$this->set_setting_column('userid', 'edition_type', 'number');
		$this->set_setting_column('create_dt', 'edition_type', 'date');
		$this->set_setting_column('last_updated_dt', 'edition_type', 'date');
		$this->set_setting_column('nb_per_page_search', 'edition_type', 'number');
		$this->set_setting_column('nb_per_page_select', 'edition_type', 'number');
		$this->set_setting_column('nb_per_page_gestion', 'edition_type', 'number');
		$this->set_setting_column('param_popup_ticket', 'edition_type', 'radio');
		$this->set_setting_column('param_sounds', 'edition_type', 'radio');
		$this->set_setting_column('param_rfid_activate', 'edition_type', 'radio');
		$this->set_setting_column('param_chat_activate', 'edition_type', 'radio');
		$this->set_setting_column('param_licence', 'edition_type', 'radio');
		$this->set_setting_column('deflt_notice_statut', 'edition_type', 'select');
		$this->set_setting_column('deflt_notice_statut_analysis', 'edition_type', 'select');
		$this->set_setting_column('deflt_integration_notice_statut', 'edition_type', 'select');
		$this->set_setting_column('deflt_docs_type', 'edition_type', 'select');
		$this->set_setting_column('deflt_serials_docs_type', 'edition_type', 'select');
		$this->set_setting_column('deflt_lenders', 'edition_type', 'select');
		$this->set_setting_column('deflt_styles', 'edition_type', 'select');
		$this->set_setting_column('deflt_docs_statut', 'edition_type', 'select');
		$this->set_setting_column('deflt_docs_codestat', 'edition_type', 'select');
		$this->set_setting_column('value_deflt_lang', 'edition_type', 'select');
		$this->set_setting_column('value_deflt_fonction', 'edition_type', 'select');
		$this->set_setting_column('value_deflt_relation', 'edition_type', 'select');
		$this->set_setting_column('value_deflt_relation_serial', 'edition_type', 'select');
		$this->set_setting_column('value_deflt_relation_bulletin', 'edition_type', 'select');
		$this->set_setting_column('value_deflt_relation_analysis', 'edition_type', 'select');
		$this->set_setting_column('deflt_docs_location', 'edition_type', 'select');
		$this->set_setting_column('deflt_collstate_location', 'edition_type', 'select');
		$this->set_setting_column('deflt_bulletinage_location', 'edition_type', 'select');
		$this->set_setting_column('deflt_resas_location', 'edition_type', 'select');
		$this->set_setting_column('deflt_docs_section', 'edition_type', 'select');
		$this->set_setting_column('value_deflt_module', 'edition_type', 'select');
		$this->set_setting_column('user_alert_resamail', 'edition_type', 'radio');
		$this->set_setting_column('user_alert_contribmail', 'edition_type', 'radio');
		$this->set_setting_column('user_alert_demandesmail', 'edition_type', 'radio');
		$this->set_setting_column('user_alert_subscribemail', 'edition_type', 'radio');
		$this->set_setting_column('user_alert_serialcircmail', 'edition_type', 'radio');
		$this->set_setting_column('user_alert_animation_mail', 'edition_type', 'radio');
		$this->set_setting_column('deflt2docs_location', 'edition_type', 'select');
		$this->set_setting_column('deflt_empr_statut', 'edition_type', 'select');
		$this->set_setting_column('deflt_empr_categ', 'edition_type', 'select');
		$this->set_setting_column('deflt_empr_codestat', 'edition_type', 'select');
		$this->set_setting_column('deflt_thesaurus', 'edition_type', 'select');
		$this->set_setting_column('deflt_concept_scheme', 'edition_type', 'select');
		$this->set_setting_column('deflt_import_thesaurus', 'edition_type', 'select');
		$this->set_setting_column('deflt3bibli', 'edition_type', 'select');
		$this->set_setting_column('deflt3exercice', 'edition_type', 'select');
		$this->set_setting_column('deflt3rubrique', 'edition_type', 'select');
		$this->set_setting_column('deflt3type_produit', 'edition_type', 'select');
		$this->set_setting_column('deflt3dev_statut', 'edition_type', 'select');
		$this->set_setting_column('deflt3cde_statut', 'edition_type', 'select');
		$this->set_setting_column('deflt3liv_statut', 'edition_type', 'select');
		$this->set_setting_column('deflt3fac_statut', 'edition_type', 'select');
		$this->set_setting_column('deflt3sug_statut', 'edition_type', 'select');
		$this->set_setting_column('param_allloc', 'edition_type', 'radio');
		$this->set_setting_column('deflt_arch_statut', 'edition_type', 'select');
		$this->set_setting_column('deflt_arch_emplacement', 'edition_type', 'select');
		$this->set_setting_column('deflt_arch_type', 'edition_type', 'select');
		$this->set_setting_column('deflt_upload_repertoire', 'edition_type', 'select');
		$this->set_setting_column('deflt_short_loan_activate', 'edition_type', 'radio');
		$this->set_setting_column('deflt3lgstatdev', 'edition_type', 'select');
		$this->set_setting_column('deflt3lgstatcde', 'edition_type', 'select');
		$this->set_setting_column('deflt3receptsugstat', 'edition_type', 'select');
		$this->set_setting_column('deflt_cashdesk', 'edition_type', 'select');
		$this->set_setting_column('user_alert_suggmail', 'edition_type', 'radio');
		$this->set_setting_column('deflt_explnum_statut', 'edition_type', 'select');
		$this->set_setting_column('deflt_explnum_location', 'edition_type', 'select');
		$this->set_setting_column('deflt_explnum_lenders', 'edition_type', 'select');
		$this->set_setting_column('deflt_notice_replace_keep_categories', 'edition_type', 'radio');
		$this->set_setting_column('deflt_notice_is_new', 'edition_type', 'radio');
		$this->set_setting_column('deflt_agnostic_warehouse', 'edition_type', 'select');
		$this->set_setting_column('deflt_cms_article_statut', 'edition_type', 'select');
		$this->set_setting_column('deflt_cms_article_type', 'edition_type', 'select');
		$this->set_setting_column('deflt_cms_section_type', 'edition_type', 'select');
		$this->set_setting_column('deflt_scan_request_status', 'edition_type', 'select');
		$this->set_setting_column('deflt_camera_empr', 'edition_type', 'radio');
		$this->set_setting_column('deflt_catalog_expanded_caddies', 'edition_type', 'radio');
		$this->set_setting_column('deflt_notice_replace_links', 'edition_type', 'radio');
		$this->set_setting_column('deflt_printer', 'edition_type', 'integer');
		$this->set_setting_column('deflt_opac_visible_bulletinage', 'edition_type', 'radio');
		$this->set_setting_column('deflt_scan_request_explnum_status', 'edition_type', 'select');
		$this->set_setting_column('deflt_type_abts', 'edition_type', 'select');
		$this->set_setting_column('deflt_docwatch_watch_filter_deleted', 'edition_type', 'radio');
		$this->set_setting_column('deflt_pclassement', 'edition_type', 'select');
		$this->set_setting_column('deflt_associated_campaign', 'edition_type', 'radio');
		$this->set_setting_column('deflt_bypass_isbn_page', 'edition_type', 'radio');
		$this->set_setting_column('deflt_animation_calendar', 'edition_type', 'select');
		$this->set_setting_column('deflt_animation_waiting_list', 'edition_type', 'radio');
		$this->set_setting_column('deflt_animation_automatic_registration', 'edition_type', 'radio');
		$this->set_setting_column('deflt_animation_communication_type', 'edition_type', 'select');
		$this->set_setting_column('deflt_animation_unique_registration', 'edition_type', 'radio');
		$this->set_setting_column('deflt_notice_catalog_categories_auto', 'edition_type', 'radio');
		$this->set_setting_column('deflt_import_lenders', 'edition_type', 'select');
	}

	protected function get_selection_query_fields($type) {
		switch ($type) {
			case 'notice_statut':
				return array('id' => 'id_notice_statut', 'label' => 'gestion_libelle');
			case 'docs_codestat':
				return array('id' => 'idcode', 'label' => 'codestat_libelle');
			case 'docs_section':
				return array('id' => 'idsection', 'label' => 'section_libelle');
			case 'docs_statut':
				return array('id' => 'idstatut', 'label' => 'statut_libelle');
			case 'docs_type':
				return array('id' => 'idtyp_doc', 'label' => 'tdoc_libelle');
			case 'docs_location':
				return array('id' => 'idlocation', 'label' => 'location_libelle');
			case 'empr_categ':
				return array('id' => 'id_categ_empr', 'label' => 'libelle');
			case 'empr_statut':
				return array('id' => 'idstatut', 'label' => 'statut_libelle');
			case 'empr_codestat':
				return array('id' => 'idcode', 'label' => 'libelle');
			case 'explnum_statut':
				return array('id' => 'id_explnum_statut', 'label' => 'gestion_libelle');
			case 'upload_repertoire':
				return array('id' => 'repertoire_id', 'label' => 'repertoire_nom');
			case 'thesaurus':
				return array('id' => 'id_thesaurus', 'label' => 'libelle_thesaurus');
			case 'cashdesk':
				return array('id' => 'cashdesk_id', 'label' => 'cashdesk_name');
			case 'type_abts':
				return array('id' => 'id_type_abt', 'label' => 'type_abt_libelle');
			case 'scan_request_status':
				return array('id' => 'id_scan_request_status', 'label' => 'scan_request_status_label');
			case 'pclassement':
				return array('id' => 'id_pclass', 'label' => 'name_pclass');
			case 'arch_statut':
				return array('id' => 'archstatut_id', 'label' => 'archstatut_gestion_libelle');
			case 'arch_emplacement':
				return array('id' => 'archempla_id', 'label' => 'archempla_libelle');
			case 'arch_type':
				return array('id' => 'archtype_id', 'label' => 'archtype_libelle');
			case 'exercices':
				return array('id' => 'id_exercice', 'label' => 'libelle');
			case 'types_produits':
				return array('id' => 'id_produit', 'label' => 'libelle');
			case 'lignes_actes_statuts':
				return array('id' => 'id_statut', 'label' => 'libelle');
		}
	}

	protected function get_selection_query($type) {
		$query = '';
		switch ($type) {
			case 'entites_1':
				$query = 'select id_entite as id, raison_sociale as label from entites where type_entite="1" order by label';
				break;
			case 'rubriques':
				$query = 'select id_rubrique as id, concat(budgets.libelle,":", rubriques.libelle) as label from rubriques join budgets on num_budget=id_budget order by label';
				break;
			case 'groups':
				$query = 'select grp_id as id, grp_name as label from users_groups order by label';
				break;
			default:
				$query = parent::get_selection_query($type);
				break;
		}
		return $query;
	}

	protected function get_search_filter_groups() {
		global $msg;

		return $this->get_search_filter_multiple_selection($this->get_selection_query('groups'), 'groups', $msg['users_groups_all']);
	}

	protected function _get_object_property_deflt_notice_statut($object) {
		$notice_statut = new notice_statut($object->deflt_notice_statut);
		return $notice_statut->gestion_libelle;
	}

	protected function _get_object_property_deflt_notice_statut_analysis($object) {
		$notice_statut = new notice_statut($object->deflt_notice_statut_analysis);
		return $notice_statut->gestion_libelle;
	}

	protected function _get_object_property_deflt_integration_notice_statut($object) {
		$notice_statut = new notice_statut($object->deflt_integration_notice_statut);
		return $notice_statut->gestion_libelle;
	}

	protected function _get_object_property_deflt_docs_type($object) {
		$docs_type = new docs_type($object->deflt_docs_type);
		return $docs_type->libelle;
	}

	protected function _get_object_property_deflt_serials_docs_type($object) {
		$docs_type = new docs_type($object->deflt_serials_docs_type);
		return $docs_type->libelle;
	}

	protected function _get_object_property_deflt_lenders($object) {
		$lender = new lender($object->deflt_lenders);
		return $lender->lender_libelle;
	}

	protected function _get_object_property_deflt_docs_statut($object) {
		$docs_statut = new docs_statut($object->deflt_docs_statut);
		return $docs_statut->libelle;
	}

	protected function _get_object_property_deflt_docs_codestat($object) {
		$docs_codestat = new docs_codestat($object->deflt_docs_codestat);
		return $docs_codestat->libelle;
	}

	protected function _get_object_property_value_deflt_lang($object) {
		if(!empty($object->value_deflt_lang)) {
			$marc_list_collection = marc_list_collection::get_instance('lang');
			return $marc_list_collection->table[$object->value_deflt_lang];
		}
		return '';
	}

	protected function _get_object_property_value_deflt_fonction($object) {
		if(!empty($object->value_deflt_fonction)) {
			$marc_list_collection = marc_list_collection::get_instance('function');
			return $marc_list_collection->table[$object->value_deflt_fonction];
		}
		return '';
	}

	protected function _get_object_property_value_deflt_relation($object) {

	}

	protected function _get_object_property_value_deflt_relation_serial($object) {

	}

	protected function _get_object_property_value_deflt_relation_bulletin($object) {

	}

	protected function _get_object_property_value_deflt_relation_analysis($object) {

	}

	protected function _get_object_property_deflt_docs_location($object) {
		$docs_location = new docs_location($object->deflt_docs_location);
		return $docs_location->libelle;
	}

	protected function _get_object_property_deflt_collstate_location($object) {
		$docs_location = new docs_location($object->deflt_collstate_location);
		return $docs_location->libelle;
	}

	protected function _get_object_property_deflt_bulletinage_location($object) {
		$docs_location = new docs_location($object->deflt_bulletinage_location);
		return $docs_location->libelle;
	}

	protected function _get_object_property_deflt_resas_location($object) {
		$docs_location = new docs_location($object->deflt_resas_location);
		return $docs_location->libelle;
	}

	protected function _get_object_property_deflt_docs_section($object) {
		$docs_section = new docs_section($object->deflt_docs_section);
		return $docs_section->libelle;
	}

	protected function _get_object_property_value_deflt_module($object) {

	}

	protected function _get_object_property_deflt2docs_location($object) {
		$docs_location = new docs_location($object->deflt2docs_location);
		return $docs_location->libelle;
	}

	protected function _get_object_property_deflt_empr_statut($object) {
		$empr_statut = new empr_statut($object->deflt_empr_statut);
		return $empr_statut->libelle;
	}

	protected function _get_object_property_deflt_empr_categ($object) {
		$empr_categ = new empr_categ($object->deflt_empr_categ);
		return $empr_categ->libelle;
	}

	protected function _get_object_property_deflt_empr_codestat($object) {
		$empr_codestat = new empr_codestat($object->deflt_empr_codestat);
		return $empr_codestat->libelle;
	}

	protected function _get_object_property_deflt_arch_statut($object) {
		$arch_statut = new arch_statut($object->deflt_arch_statut);
		return $arch_statut->gestion_libelle;
	}

	protected function _get_object_property_deflt_arch_emplacement($object) {
		$arch_emplacement = new arch_emplacement($object->deflt_arch_emplacement);
		return $arch_emplacement->libelle;
	}

	protected function _get_object_property_deflt_arch_type($object) {
		$arch_type = new arch_type($object->deflt_arch_type);
		return $arch_type->libelle;
	}

	protected function _get_object_property_deflt_explnum_statut($object) {
		$explnum_statut = new explnum_statut($object->deflt_explnum_statut);
		return $explnum_statut->gestion_libelle;
	}

	protected function _get_object_property_deflt_explnum_location($object) {
		$docs_location = new docs_location($object->deflt_explnum_location);
		return $docs_location->libelle;
	}

	protected function _get_object_property_deflt_explnum_lenders($object) {
		$lender = new lender($object->deflt_explnum_lenders);
		return $lender->lender_libelle;
	}

	protected function _get_object_property_deflt_scan_request_status($object) {
		$scan_request_status = new scan_request_status($object->deflt_scan_request_status);
		return $scan_request_status->get_label();
	}

	protected function _get_object_property_deflt_scan_request_explnum_status($object) {
		$explnum_statut = new explnum_statut($object->deflt_scan_request_explnum_status);
		return $explnum_statut->gestion_libelle;
	}

	protected function _get_object_property_deflt_type_abts($object) {
		$type_abt = new type_abt($object->deflt_type_abts);
		return $type_abt->libelle;
	}

	protected function _get_object_property_deflt_pclassement($object) {
		$pclassement = new pclassement($object->deflt_pclassement);
		return $pclassement->get_name();
	}

	protected function _get_object_property_group($object) {
		$users_group = new users_group($object->grp_num);
		return $users_group->name;
	}

	protected function _get_object_property_mail_configuration_is_validated($object) {
		$mail_configuration = new mail_configuration($object->user_email);
		return $mail_configuration->is_validated();
	}

	protected function get_display_permission_access($permission_access=0) {
		if($permission_access) {
			return '<img src="'.get_url_icon('coche.gif').'" class="align_top" style="margin:0px 3px" />';
		} else {
			return '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" style="margin:0px 3px" />';
		}
	}

	protected function get_display_ask_alert_mail($name, $alert_mail=0) {
		global $msg;
		global $admin_user_alert_row;

		if($alert_mail) {
			return str_replace("!!user_alert!!", $msg[$name].'<img src="'.get_url_icon('tick.gif').'" class="align_top" hspace=3>', $admin_user_alert_row);
		} else {
			return '';
		}
	}

	protected function get_cell_content($object, $property) {
	    global $msg, $charset;
	    global $PMBuserid, $base_path;

		$content = '';
		switch($property) {
			case 'mail_configuration_is_validated':
				if($object->user_email) {
					if(!$this->_get_object_property_mail_configuration_is_validated($object)) {
						$content .= "<tr>";
						$content .= "<td class='brd' colspan='4'>";
						if($PMBuserid == $object->id) {
							$content .= "<span class='erreur'>".htmlentities($msg['mail_configuration_myself_is_not_validated'], ENT_QUOTES, $charset)."</span>";
						} else {
							$content .= "<span class='erreur'>".htmlentities($msg['mail_configuration_other_is_not_validated'], ENT_QUOTES, $charset)."</span>";
						}
						$content .= "</td>";
						$content .= "</tr>";
					}
				}
				break;
			case 'rights':
				$permissions = list_permissions_user_ui::get_instance()->get_objects();
				$content .= "<tr>";
				$indice = 0;
				foreach ($permissions as $indice=>$permission) {
					if($indice % 4 == 0) {
						$content .= "</tr><tr>";
					}
					$content .= "<td class='brd'>".$this->get_display_permission_access($object->rights & $permission->rights).$permission->label."</td>";
				}
				while($indice % 4 != 3) {
					$content .= "<td class='brd'></td>";
					$indice++;
				}
				$content .= "</tr>";
				break;
			case 'group':
			    $content .= "<a href='".$base_path."/admin.php?categ=users&sub=groups&action=modif&id=".$object->grp_num."'>";
			    $content .= $this->_get_object_property_group($object);
			    $content .= "</a>";
			    break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}

	protected function get_display_content_object_list($object, $indice) {
		global $msg;
		global $admin_user_list;
		global $PMBuserid, $base_path;
		global $display_mode;
		global $pmb_contribution_area_activate, $demandes_active, $opac_websubscribe_show, $acquisition_active;
		global $animations_active;

		if($display_mode == 'flat') {
			return parent::get_display_content_object_list($object, $indice);
		} else {
			$ancre = "";
			if(!empty($this->object_id) && $this->object_id==$object->userid) {
				if(empty($this->ancre)) {
					$this->ancre = $this->objects_type."_object_list_ancre";
				}
				$ancre = "<a name='".$this->ancre."'></a>";
			}

			// réinitialisation des chaînes
			$dummy = $admin_user_list;

			$flag = "<img src='./images/flags/".$object->user_lang.".gif' width='24' height='16' vspace='3'>";

			if($this->at_least_one_action()) {
				$dummy =str_replace('!!user_selection!!', "<input type='checkbox' id='".$this->objects_type."_selection_".$object->userid."' name='".$this->objects_type."_selection[".$object->userid."]' class='".$this->objects_type."_selection' value='".$object->userid."' />", $dummy);
			} else {
				$dummy =str_replace('!!user_selection!!', "", $dummy);
			}
			$user_links = array();
			$user_links[] = $this->get_interface_button($msg[62], ['location' => static::get_controller_url_base()."&action=modif&id=".$object->userid]);
			if($PMBuserid == $object->userid) {
				$passwd_location = $base_path."/account.php?categ=authentication&action=edit&id=".$object->userid;
			} else {
				$passwd_location = static::get_controller_url_base()."&action=pwd&id=".$object->userid;
			}
			$user_links[] = $this->get_interface_button($msg['mot_de_passe'], ['location' => $passwd_location]);
			if($PMBuserid == $object->userid) {
			    $user_links[] = $this->get_interface_button($msg['mail_configuration_edit'], ['location' => $base_path."/admin.php?categ=mails&sub=configuration&action=edit&name=".$object->user_email]);
			}
			$dummy =str_replace('!!user_link!!', implode('&nbsp;', $user_links), $dummy);
			$dummy =str_replace('!!user_name!!', "$object->prenom $object->nom", $dummy);
			$dummy =str_replace('!!user_login!!', $object->username, $dummy);

			$content = $this->get_cell_content($object, 'mail_configuration_is_validated');
			$content .= $this->get_cell_content($object, 'rights');
			$content .= $this->get_display_ask_alert_mail('alert_resa_user_mail', $object->user_alert_resamail);
			if ($pmb_contribution_area_activate) {
				$content .= $this->get_display_ask_alert_mail('alert_contrib_user_mail', $object->user_alert_contribmail);
			}
			if ($demandes_active) {
				$content .= $this->get_display_ask_alert_mail('alert_demandes_user_mail', $object->user_alert_demandesmail);
			}
			if ($opac_websubscribe_show) {
				$content .= $this->get_display_ask_alert_mail('alert_subscribe_user_mail', $object->user_alert_subscribemail);
			}
			if ($acquisition_active) {
				$content .= $this->get_display_ask_alert_mail('alert_sugg_user_mail', $object->user_alert_suggmail);
			}
			if ($animations_active) {
			    $content .= $this->get_display_ask_alert_mail('alert_animation_user_mail', $object->user_alert_animation_mail);
			}
			$dummy = str_replace('!!brd_columns!!', $content, $dummy);

			$dummy = str_replace('!!lang_flag!!', $flag, $dummy);
			$dummy = str_replace('!!nuserlogin!!', $object->username, $dummy);
			$dummy = str_replace('!!nuserid!!', $object->userid, $dummy);

			$dummy = str_replace('!!user_created_date!!', $msg['user_created_date'].format_date($object->create_dt), $dummy);

			return $ancre.$dummy;
		}
	}

	protected function get_display_group_header_list($group_label, $level=1, $uid='') {
		global $display_mode;

		if($display_mode == 'flat') {
			return parent::get_display_group_header_list($group_label, $level, $uid);
		} else {
			$display = "
			<div id='".$uid."_group_header'>
				<div class='list_ui_content_list_group list_ui_content_list_group_level_".$level." ".$this->objects_type."_content_list_group ".$this->objects_type."_content_list_group_level_".$level."'>
					".$this->get_cell_group_label($group_label, ($level-1))."
				</div>
			</div>";
			return $display;
		}
	}

	/**
	 * Affichage de la liste des objets
	 * @return string
	 */
	public function get_display_objects_list() {
		global $display_mode;

		$display = '';
		if($display_mode == 'flat') {
			$display .= parent::get_display_objects_list();
		} else {
			if(count($this->objects)) {
				$display .= $this->get_display_content_list();
				$display .= $this->add_events_on_objects_list();
			}
		}
		return $display;
	}

	/**
	 * Affiche la recherche + la liste
	 */
	public function get_display_list() {
		global $msg;

		$display = "
		<div class='row'>
			".$this->get_button('add', $msg[85])."
		</div>";
		$display .= parent::get_display_list();
		return $display;
	}

	protected function get_display_left_actions() {
		global $msg;

		return $this->get_button('add', $msg[85]);
	}

	protected function get_default_attributes_format_cell($object, $property) {
	    return array(
	        'onclick' => "document.location=\"".static::get_controller_url_base()."&action=modif&id=".$object->id."\""
	    );
	}

	protected function get_link_action($action, $act) {
		return array(
				'href' => static::get_controller_url_base()."&action=".$action,
				'confirm' => ''
		);
	}

	protected function init_default_selection_actions() {
		global $msg;

		parent::init_default_selection_actions();
		//Bouton modifier
		$edit_link = array(
				'showConfiguration' => static::get_controller_url_base()."&action=list_save"
		);
		$this->add_selection_action('edit', $msg['62'], 'b_edit.png', $edit_link);

		//Bouton supprimer
// 		$this->add_selection_action('delete', $msg['63'], 'interdit.gif', $this->get_link_action('list_delete', 'delete'));
	}

	/**
	 * Jointure externes SQL pour les besoins des filtres
	 */
	protected function _get_query_join_filters() {

		$filter_join_query = '';
		if((is_array($this->filters['groups']) && count($this->filters['groups'])) || !empty($this->filters['group'])) {
			$filter_join_query .= " LEFT JOIN users_groups ON users.grp_num=users_groups.grp_id";
		}
		return $filter_join_query;
	}

	protected function _add_query_filters() {
		$this->_add_query_filter_multiple_restriction('groups', 'grp_id', 'integer');
	}

	protected function get_options_editable_column($object, $property) {
		global $msg, $base_path, $pmb_printer_list;

		switch ($property) {
			case 'deflt_notice_statut':
			case 'deflt_notice_statut_analysis':
			case 'deflt_integration_notice_statut':
				return $this->get_options_from_query_selection($this->get_selection_query('notice_statut'));
			case 'deflt_docs_type':
			case 'deflt_serials_docs_type':
				return $this->get_options_from_query_selection($this->get_selection_query('docs_type'));
			case 'deflt_lenders':
			case 'deflt_explnum_lenders':
			case 'deflt_import_lenders':
				return $this->get_options_from_query_selection($this->get_selection_query('lenders'));
// 			case 'deflt_styles':
// 				return
			case 'deflt_docs_statut':
				return $this->get_options_from_query_selection($this->get_selection_query('docs_statut'));
			case 'deflt_docs_codestat':
				return $this->get_options_from_query_selection($this->get_selection_query('docs_codestat'));
// 			case 'deflt_docs_location': //cas particulier
			case 'deflt_collstate_location':
			case 'deflt_bulletinage_location':
			case 'deflt_resas_location':
			case 'deflt2docs_location':
			case 'deflt_explnum_location':
				return $this->get_options_from_query_selection($this->get_selection_query('docs_location')/*, $msg["all_location"]*/);
// 			case 'deflt_docs_section': //cas particulier
// 				return $this->get_options_from_query_selection($this->get_selection_query('docs_section');
			case 'deflt_empr_statut':
				return $this->get_options_from_query_selection($this->get_selection_query('empr_statut'));
			case 'deflt_empr_categ':
				return $this->get_options_from_query_selection($this->get_selection_query('empr_categ'));
			case 'deflt_empr_codestat':
				return $this->get_options_from_query_selection($this->get_selection_query('empr_codestat'));
			case 'deflt_thesaurus':
			case 'deflt_import_thesaurus':
				return $this->get_options_from_query_selection($this->get_selection_query('thesaurus'));
			case 'deflt3bibli':
				return $this->get_options_from_query_selection($this->get_selection_query('entites_1'), $msg['deflt3none']);
			case 'deflt3exercice':
				return $this->get_options_from_query_selection($this->get_selection_query('exercices'), $msg['deflt3none']);
			case 'deflt3rubrique':
				return $this->get_options_from_query_selection($this->get_selection_query('rubriques'), $msg['deflt3none']);
			case 'deflt3type_produit':
				return $this->get_options_from_query_selection($this->get_selection_query('types_produits'), $msg['deflt3none']);
			case 'deflt3dev_statut':
				return $this->get_options_from_simple_selection(actes::getStatelist(TYP_ACT_DEV));
			case 'deflt3cde_statut':
				return $this->get_options_from_simple_selection(actes::getStatelist(TYP_ACT_CDE));
			case 'deflt3liv_statut':
				return $this->get_options_from_simple_selection(actes::getStatelist(TYP_ACT_LIV));
			case 'deflt3fac_statut':
				return $this->get_options_from_simple_selection(actes::getStatelist(TYP_ACT_FAC));
			case 'deflt3sug_statut':
				$m = new suggestions_map();
				return $this->get_options_from_simple_selection($m->getStateList());
			case 'deflt_arch_statut':
				return $this->get_options_from_query_selection($this->get_selection_query('arch_statut'));
			case 'deflt_arch_emplacement':
				return $this->get_options_from_query_selection($this->get_selection_query('arch_emplacement'));
			case 'deflt_arch_type':
				return $this->get_options_from_query_selection($this->get_selection_query('arch_type'));
			case 'deflt_upload_repertoire':
				return $this->get_options_from_query_selection($this->get_selection_query('upload_repertoire'));
			case 'deflt3lgstatdev':
				return $this->get_options_from_query_selection($this->get_selection_query('lignes_actes_statuts'));
			case 'deflt3lgstatcde':
				return $this->get_options_from_query_selection($this->get_selection_query('lignes_actes_statuts'));
			case 'deflt3receptsugstat':
				$m = new suggestions_map();
				return $this->get_options_from_simple_selection($m->getStateList('ORDERED',TRUE));
			case 'deflt_cashdesk':
				return $this->get_options_from_query_selection($this->get_selection_query('cashdesk'), "--");
			case 'deflt_explnum_statut':
			case 'deflt_scan_request_explnum_status':
				return $this->get_options_from_query_selection($this->get_selection_query('explnum_statut'));
			case 'deflt_agnostic_warehouse':
				$options = array(
						array('value' => 0, 'label' => $msg['caddie_save_to_warehouse_none'])
				);
				$conn = new agnostic($base_path.'/admin/connecteurs/in/agnostic');
				$conn->get_sources();
				if (is_array($conn->sources)) {
					foreach ($conn->sources as $key_source => $source) {
						$options[] = array('value' => $key_source, 'label' => $source['NAME']);
					}
				}
				return $options;
			case 'deflt_cms_article_statut':
				$publications_states = new cms_editorial_publications_states();
				return $publications_states->get_publications_states();
			case 'deflt_cms_article_type':
				$options = array();
				$types = new cms_editorial_types('article');
				$types_list = $types->get_types();
				if(!empty($types_list)) {
					foreach ($types_list as $type_list) {
						$options[] = array('value' => $type_list['id'], 'label' => $type_list['label']);
					}
				}
				return $options;
			case 'deflt_cms_section_type':
				$options = array();
				$types = new cms_editorial_types('section');
				$types_list = $types->get_types();
				if(!empty($types_list)) {
					foreach ($types_list as $type_list) {
						$options[] = array('value' => $type_list['id'], 'label' => $type_list['label']);
					}
				}
				return $options;
			case 'deflt_scan_request_status':
				return $this->get_options_from_query_selection($this->get_selection_query('scan_request_status'));
			case 'deflt_printer':
				$options = array(
						array('value' => 0, 'label' => $msg['user_deflt_printer_not_selected'])
				);
				if (trim($pmb_printer_list)) {
					$list_printers = explode(";", $pmb_printer_list);
					foreach ($list_printers as $printer) {
						$printer = trim($printer);
						$out=array();
						if (preg_match('#^ *(\d+) *\_ *(.+?) *(\(([\d\.:]+)\))? *$#',$printer,$out)) {
							$options[] = array('value' => $out[1], 'label' => $out[2]);
						}
					}
				}
				return $options;
			case 'deflt_type_abts':
				return $this->get_options_from_query_selection($this->get_selection_query('type_abts'));
			case 'deflt_pclassement':
				return $this->get_options_from_query_selection($this->get_selection_query('pclassement'));
			default:
				return parent::get_options_editable_column($object, $property);
		}
	}

	protected function _get_query_property_filter($property) {
		switch ($property) {
			case 'groups':
				return "select grp_name from users_groups where grp_id IN (".implode(',', $this->filters[$property]).")";
		}
		return '';
	}

	protected function save_object($object, $property, $value) {
		if (is_object($object)) {
			$query = "UPDATE users SET ".$property."='".addslashes($value)."' WHERE userid=".$object->id;
			pmb_mysql_query($query);
		}
	}

	public static function get_controller_url_base() {
		global $base_path;

		return $base_path.'/admin.php?categ=users&sub=users';
	}
}