<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_subtabs_admin_ui.class.php,v 1.35.2.2.2.1 2025/05/20 14:00:09 qvarin Exp $

use Pmb\Thumbnail\Models\ThumbnailSourcesHandler;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_subtabs_admin_ui extends list_subtabs_ui {

	public function get_title() {
		global $msg;
		
		$title = "";
		switch (static::$categ) {
			case 'docs':
				$title .= $msg['admin_menu_exemplaires'];
				break;
			case 'notices':
			case 'authorities':
			case 'collstate':
			case 'abonnements':
			case 'loans':
			case 'pnb':
			case 'composed_vedettes':
			case 'search_universes':
			case 'transferts':
			case 'connecteurs':
				$title .= $msg['admin_menu_'.static::$categ];
				break;
			case 'docnum':
				$title .= $msg['admin_menu_upload_docnum'];
				break;
			case 'empr':
				$title .= $msg['22'];
				break;
			case 'users':
				$title .= $msg['25'];
				break;
			case 'cms_editorial':
				$title .= $msg['editorial_content'];
				break;
			case 'infopages':
				$title .= $msg['admin_menu_opac'];
				break;
			case 'gestion':
			    switch (static::$sub) {
			        case 'facettes':
			        case 'facettes_authorities':
			        case 'facettes_external':
			        case 'facettes_comparateur':
			            $title .= $msg['admin_menu_gestion_facette'];
			            break;
			        default:
			            $title .= $msg['admin_menu_gestion'];
			            break;
			    }
			    break;
			case 'opac':
				switch (static::$sub) {
					case 'facettes':
					case 'facettes_authorities':
					case 'facettes_external':
					case 'facettes_comparateur':
						$title .= $msg['admin_menu_opac_facette'];
						break;
					default:
						$title .= $msg['admin_menu_opac'];
						break;
				}
				break;
			case 'visionneuse':
				$title .= $msg['visionneuse_admin_menu'];
				break;
			case 'contact_forms':
				$title .= $msg['admin_opac_contact_forms'];
				break;
			case 'proc':
				$title .= $msg['admin_menu_act'];
				break;
			case 'family':
			case 'formation':
			case 'voice':
			case 'instrument':
			case 'material':
				$title .= $msg['admin_nomenclature'];
				break;
			case 'finance':
				$title .= $msg['admin_gestion_financiere'];
				break;
			case 'import':
				$title .= $msg['519'];
				break;
			case 'convert':
				$title .= $msg['admin_conversion'];
				break;
			case 'z3950':
				$title .= 'Z39.50';
				break;
			case 'planificateur':
				$title .= $msg['planificateur_admin_menu'];
				break;
			case 'external_services':
				$title .= $msg['es_admin_menu'];
				break;
			case 'selfservice':
				$title .= $msg['selfservice_admin_menu'];
				break;
			case 'sauvegarde':
				$title .= $msg['28'];
				break;
			case 'acquisition':
				$title .= $msg['acquisition_menu'];
				break;
			case 'harvest':
			case 'demandes':
			case 'faq':
			case 'mailtpl':
			case 'scan_request':
			case 'quotas':
			case 'calendrier':
				$title .= $msg['admin_'.static::$categ];
				break;
			case 'misc':
			case 'netbase':
			case 'chklnk':
			case 'log':
			case 'param':
			case 'alter':
				$title .= $msg['27'];
				break;
			case 'html_editor':
				$title .= $msg['admin_html_editor'];
				break;
			case 'vignette':
			case 'thumbnail':
				$title .= $msg['admin_vignette_menu'];
				break;
			case 'animations':
				$title .= $msg['animation_base_title'];
				break;
			case 'ark':
			    $title .= $msg['ark_base_title'];
			    break;
			case 'digital_signature':
				$title .= $msg['digital_signature_base_title'];
				break;
			case 'mails':
				$title .= $msg['mails'];
				break;
			case 'interface':
				$title .= $msg['interface'];
				break;
			case 'supervision':
				$title .= $msg['supervision'];
				break;
			case 'acces':
				$title .= $msg['admin_menu_acces'];
				break;
			case 'mfa':
			    $title .= $msg['admin_menu_mfa'];
			    break;
			case 'auth':
			    $title .= $msg['admin_menu_auth'];
			    break;
			case 'ai':
			    $title .= $msg['admin_menu_ai'];
			    break;
		}
		return $title;
	}

	protected function is_selected_tab($object) {
	    switch (static::$categ) {
	        case 'gestion':
	        case 'opac':
	            if(!empty(static::$sub) && static::$sub == 'facettes_authorities') {
	                return ongletSelect("categ=".static::$categ."&sub=".$object->get_sub());
	            }
	        default:
	            return parent::is_selected_tab($object);
	    }
	}

	public function get_sub_title() {
		global $msg, $quoi, $elements, $id;
		
		$sub_title = "";
		switch (static::$categ) {
			case 'caddie':
				$sub_title .= $msg["caddie_menu_".static::$sub];
				$selected_subtab = $this->get_selected_subtab();
				if(!empty($selected_subtab)) {
					$sub_title .= " > ".$selected_subtab->get_label();
				}
				break;
			case 'infopages':
				$sub_title .= $msg['infopages_admin_menu'];
				break;
			case 'opac':
				switch(static::$sub) {
					case 'search_persopac':
						$sub_title .= $msg['admin_menu_search_persopac'];
						break;
					case 'maintenance':
						$sub_title .= $msg['admin_menu_opac_'.static::$sub];
						break;
					case 'navigopac':
						$sub_title .= $msg['exemplaire_admin_navigopac'];
						break;
					case 'stat':
						$sub_title .= $msg['stat_opac_menu'];
						break;
					case 'analytics_services':
						$sub_title .= $msg['analytics_services'];
						break;
					default :
						$selected_subtab = $this->get_selected_subtab();
						if(!empty($selected_subtab)) {
							$sub_title .= $selected_subtab->get_label();
						}
						break;
				}
				break;
			case 'contact_forms':
				switch(static::$sub) {
					case 'objects':
					case 'recipients':
					case 'parameters':
						$sub_title .= $msg['admin_opac_contact_form_'.static::$sub];
						break;
					default :
						break;
				}
				break;
			case 'proc':
				switch(static::$sub) {
					case 'clas':
						$sub_title .= $msg['admin_menu_act_perso_clas'];
						break;
					case 'req':
						$sub_title .= $msg['admin_menu_req'];
						break;
					case 'proc':
					default :
						$sub_title .= $msg['admin_menu_act_perso'];
						break;
				}
				break;
			case 'quotas':
				$sub_title .= parent::get_sub_title();
				if(!empty($elements)) {
					$qt=quota::get_instance(static::$sub);
					$sub_title .= " > ".$qt->get_title_by_elements_id($elements);
				}
				break;
			case 'calendrier':
				switch(static::$sub) {
					case 'edition':
						$sub_title .= $msg['calendrier_edition'];
						break;
					case 'consulter':
					default :
						$sub_title .= $msg['calendrier_consulter'];
						break;
				}
				break;
			case 'z3950':
				if(static::$sub == 'zattr') {
					$sub_title .= $msg['769'];
				} else {
					$sub_title .= $msg['768'];
				}
				break;
			case 'visionneuse':
			    $sub_title .= parent::get_sub_title();
			    if(!empty($quoi)) {
			        $sub_title .= " > $quoi";
			    }
				break;
			case 'interface':
				switch (static::$sub) {
					case 'tabs':
						$sub_title .= $msg['tabs']." > ";
						break;
					default:
						break;
				}
				$sub_title .= parent::get_sub_title();
				break;
			case 'acces':
				$ac = acces::get_instance();
				$t_cat= $ac->getCatalog();
				foreach($t_cat as $k=>$v) {
					if ($id==$k) {
						$sub_title .= $v['comment'];
						$ac = acces::get_instance();
						$dom = $ac->setDomain($id);
						switch (static::$sub) {
							case 'user_prf':
								$sub_title .= ' > '.$dom->getComment('user_prf_lib');
								break;
							case 'res_prf':
								$sub_title .= ' > '.$dom->getComment('res_prf_lib');
								break;
						}
					}
				}
				break;
			default:
				$sub_title .= parent::get_sub_title();
				break;
		}
		return $sub_title;
	}
	
	protected function _init_subtabs() {
		global $pmb_sur_location_activate, $pmb_map_activate, $ldap_accessible;
		global $pmb_pret_restriction_prolongation, $pmb_short_loan_management;
		global $pmb_gestion_financiere, $pmb_gestion_abonnement, $pmb_gestion_tarif_prets, $pmb_gestion_amende, $pmb_gestion_financiere_caisses;
		global $acquisition_gestion_tva, $acquisition_sugg_categ;
		global $pmb_opac_view_activate, $PMBuserid;
		
		switch (static::$categ) {
			case 'docs':
				//Exemplaires
				$this->add_subtab('typdoc', 'admin_menu_docs_type', '724');
				$this->add_subtab('location', '21', '728');
				if($pmb_sur_location_activate) {
					$this->add_subtab('sur_location', 'sur_location_admin_menu', 'sur_location_admin_menu_title');
				}
				$this->add_subtab('section', '19', '726');
				$this->add_subtab('statut', '20', '727');
				$this->add_subtab('codstat', '24', '725');
				$this->add_subtab('lenders', '554', '732');
				$this->add_subtab('perso', 'admin_menu_docs_perso');
				break;
			case 'notices':
				//Notices
				$this->add_subtab('orinot', 'orinot_origine_short', 'orinot_origine');
				$this->add_subtab('statut', 'admin_menu_noti_statut');
				if($pmb_map_activate) {
					$this->add_subtab('map_echelle', 'admin_menu_noti_map_echelle');
					$this->add_subtab('map_projection', 'admin_menu_noti_map_projection');
					$this->add_subtab('map_ref', 'admin_menu_noti_map_ref');
				}
				$this->add_subtab('perso', 'admin_menu_noti_perso');
				$this->add_subtab('onglet', 'admin_menu_noti_onglet', 'admin_menu_noti_onglet_title');
				$this->add_subtab('notice_usage', 'admin_menu_notice_usage');
				break;
			case 'authorities':
				//Autorités
				$this->add_subtab('origins', 'origins');
				$this->add_subtab('statuts', '20');
				$perso_subtabs = array('author', 'categ', 'publisher', 'collection', 'subcollection', 'serie', 'tu', 'indexint', 'skos');
				for($i=0; $i<count($perso_subtabs); $i++) {
					$this->add_subtab('perso', 'admin_menu_docs_perso_'.$perso_subtabs[$i], '', '&type_field='.$perso_subtabs[$i]);
				}
				$this->add_subtab('authperso', 'admin_menu_authperso');
				$this->add_subtab('templates', 'admin_menu_authperso_template');
				break;
			case 'docnum':
				//upload des documents numériques
				$this->add_subtab('rep', 'upload_repertoire');
				$this->add_subtab('storages', 'storage_menu');
				$this->add_subtab('statut', 'admin_menu_docnum_statut');
				$this->add_subtab('perso', 'admin_menu_noti_perso');
				$this->add_subtab('licence', 'admin_menu_noti_licence');
				break;
			case 'collstate':
				//Etats des collections
				$this->add_subtab('emplacement', 'admin_menu_collstate_emplacement');
				$this->add_subtab('support', 'admin_menu_collstate_support');
				$this->add_subtab('statut', 'admin_menu_collstate_statut');
				$this->add_subtab('perso', 'admin_collstate_collstate_perso');
				break;
			case 'abonnements':
				//Abonnements
				$this->add_subtab('periodicite', 'admin_menu_abonnements_periodicite', 'admin_menu_abonnements_periodicite');
				$this->add_subtab('status', 'admin_menu_abonnements_status', 'admin_menu_abonnements_status');
				break;
			case 'empr':
				//Lecteurs
				$this->add_subtab('categ', 'lecteurs_categories', '729');
				$this->add_subtab('statut', 'empr_statut_menu', 'empr_statut_menu');
				$this->add_subtab('codstat', '24', '730');
				$this->add_subtab('implec', 'import_lec_lien', 'import_lec_alt');
				if ($ldap_accessible) {
					$this->add_subtab('ldap', 'import_ldap', 'import_ldap');
					$this->add_subtab('exldap', 'menu_suppr_exldap', 'menu_suppr_exldap');
				}
				$this->add_subtab('parperso', 'parametres_perso_lec_lien', 'parametres_perso_lec_alt');
				$this->add_subtab('empr_account', 'empr_account', 'empr_account');
				$this->add_subtab('password_rules', 'admin_empr_password_rules_sub_menu', 'admin_empr_password_rules_sub_menu_title');
				break;
			case 'users':
				//Utilisateurs
				$this->add_subtab('users', '26', '731');
				$this->add_subtab('groups', 'admin_usr_grp_ges', '731');
				$this->add_subtab('roles', 'roles_gestion', '731');
				break;
			case 'cms_editorial':
				//Contenu éditorial
				$this->add_subtab('type', 'editorial_content_type_section', '', '&elem=section');
				$this->add_subtab('type', 'editorial_content_type_article', '', '&elem=article');
				$this->add_subtab('publication_state', 'editorial_content_publication_state');
				break;
			case 'loans':
				//Prets
				$this->add_subtab('perso', 'admin_menu_loans_perso');
				break;
			case 'composed_vedettes':
				//Vedettes composées
				$this->add_subtab('grammars', 'composed_vedettes_grammars');
				$this->add_subtab('schemes', 'ontology_skos_conceptscheme');
				break;
			case 'pnb':
				//PNB
				$this->add_subtab('param', 'admin_menu_pnb_param');
				$this->add_subtab('check', 'admin_pnb_check_title');
				break;
			case 'visionneuse':
				//Visionneuse
				$this->add_subtab('class', 'visionneuse_admin_class');
				$this->add_subtab('mimetype', 'visionneuse_admin_mimetype');
				break;
			case 'gestion':
			    switch (static::$sub) {
			        case 'facettes':
			        case 'facettes_authorities':
			        case 'facettes_external':
			        case 'facettes_comparateur':
			            //Facettes
			            $this->add_subtab('facettes', 'facettes_records');
			            $this->add_subtab('facettes_authorities', 'facettes_authorities', '', '&type=authors');
// 			            $this->add_subtab('facettes_external', 'facettes_external_records');
// 			            $this->add_subtab('facettes_comparateur', 'facettes_admin_menu_compare');
			            break;
			    }
			    break;
			case 'opac':
				switch (static::$sub) {
					case 'facettes':
					case 'facettes_authorities':
					case 'facettes_external':
					case 'facettes_comparateur':
						//Facettes
						$this->add_subtab('facettes', 'facettes_records');
						$this->add_subtab('facettes_authorities', 'facettes_authorities', '', '&type=authors');
						$this->add_subtab('facettes_external', 'facettes_external_records');
						$this->add_subtab('facettes_comparateur', 'facettes_admin_menu_compare');
						break;
					case 'opac_view':
						//Vues OPAC
						$this->add_subtab(static::$sub, 'opac_view_admin_menu_list', '', '&section=list');
						if($pmb_opac_view_activate == 2){
							$this->add_subtab(static::$sub, 'opac_view_admin_menu_affect', '', '&section=affect');
						}
						break;
				}
				break;
			case 'search_universes':
				//Univers de recherche
				$this->add_subtab('universe', 'admin_search_universe');
				break;
			case 'family':
			case 'formation':
			case 'voice':
			case 'instrument':
			case 'material':
				//Nomenclatures
				$this->add_subtab(static::$categ, 'admin_nomenclature_'.static::$categ);
				break;
			case 'quotas':
				global $class_path;
				require_once($class_path."/pnb/dilicom.class.php");
				
				$qt=quota::get_instance(static::$sub);
				$_quotas_types_ = quota::$_quotas_[$qt->descriptor]['_types_'];
				for ($i=0; $i<count($_quotas_types_); $i++) {
					if($pmb_pret_restriction_prolongation!=2 && $_quotas_types_[$i]['NAME']=='PROLONG_NMBR_QUOTA' ) continue;
					if($pmb_pret_restriction_prolongation!=2 && $_quotas_types_[$i]['NAME']=='PROLONG_TIME_QUOTA') continue;
					if(!$pmb_short_loan_management && (($_quotas_types_[$i]['NAME']=='SHORT_LOAN_TIME_QUOTA')||($_quotas_types_[$i]['NAME']=='SHORT_LOAN_NMBR_QUOTA'))) continue;
					if(!dilicom::is_pnb_active() && (($_quotas_types_[$i]['NAME']=='PNB_LOAN_NMBR_QUOTA')||($_quotas_types_[$i]['NAME']=='PNB_LOAN_TIME_QUOTA')||($_quotas_types_[$i]['NAME']=='PNB_LOAN_PROLONG_NMBR_QUOTA')||($_quotas_types_[$i]['NAME']=='PNB_LOAN_PROLONG_TIME_QUOTA'))) continue;
					$this->add_subtab($_quotas_types_[$i]["ID"], $_quotas_types_[$i]["SHORT_COMMENT"]);
				}
				break;
			case 'finance':
				//Gestion financiere
				if (($pmb_gestion_financiere)&&($pmb_gestion_abonnement==2)) {
					$this->add_subtab('abts', 'finance_abts');
				}
				if (($pmb_gestion_financiere)&&($pmb_gestion_tarif_prets==2)) {
					$this->add_subtab('prets', 'finance_prets');
				}
				if (($pmb_gestion_financiere)&&($pmb_gestion_amende)) {
					$this->add_subtab('amendes', 'finance_amendes');
					$this->add_subtab('amendes_relance', 'finance_amendes_relances');
				}
				$this->add_subtab('blocage', 'finance_blocage');
				if (($pmb_gestion_financiere)) {
					$this->add_subtab('transactype', 'transaction_admin');
					$this->add_subtab('transaction_payment_method', 'transaction_payment_method_admin');
				}
				if (($pmb_gestion_financiere)&&($pmb_gestion_financiere_caisses)) {
					$this->add_subtab('cashdesk', 'cashdesk_admin');
				}
				if (($pmb_gestion_financiere)&&($pmb_gestion_financiere_caisses)) {
					$this->add_subtab('organization_account', 'organization_account_admin');
				}
				break;
			case 'import':
				//Import
				$this->add_subtab('import', '500', '733');
				$this->add_subtab('import_expl', '520', '734');
				$this->add_subtab('pointage_expl', '569');
				$this->add_subtab('import_skos', 'ontology_skos_admin_import');
				break;
			case 'convert':
				//Outils de Conversion/Export de formats
				$this->add_subtab('import', 'admin_convExterne');
				$this->add_subtab('export', 'admin_ExportPMB');
				$this->add_subtab('paramgestion', 'admin_param_export_gestion');
				$this->add_subtab('paramopac', 'admin_param_export_opac');
				break;
			case 'harvest':
				//Récolteur
				$this->add_subtab('profil', 'admin_harvest_build_menu');
				$this->add_subtab('profil_import', 'admin_harvest_profil_title');
				break;
			case 'z3950':
				//Z39.50
				$this->add_subtab('zbib', 'z3950_serveurs', 'z3950_menu_admin_title');
				break;
			case 'planificateur':
				//Gestionnaire de tâches
				$this->add_subtab('manager', 'planificateur_admin_manager');
				$this->add_subtab('reporting', 'planificateur_admin_reporting');
				break;
			case 'external_services':
				//Services externes
				$this->add_subtab('general', 'es_admin_general');
				$this->add_subtab('peruser', 'es_admin_peruser');
				$this->add_subtab('esusers', 'es_admin_esusers');
				$this->add_subtab('esusergroups', 'es_admin_esusergroups');
// 				$this->add_subtab('es_tests', 'Tests');
				break;
			case 'connecteurs':
				//Connecteurs
				$this->add_subtab('in', 'admin_connecteurs_in');
				$this->add_subtab('categ', 'admin_connecteurs_categ');
				$this->add_subtab('out', 'admin_connecteurs_out');
				$this->add_subtab('out_auth', 'admin_connecteurs_outauth');
				$this->add_subtab('out_sets', 'admin_connecteurs_sets');
				$this->add_subtab('categout_sets', 'admin_connecteurs_categsets');
				$this->add_subtab('enrichment', 'admin_connecteurs_enrichment');
				break;
			case 'selfservice':
				//Borne de prêt
				$this->add_subtab('pret', 'selfservice_admin_pret');
				$this->add_subtab('retour', 'selfservice_admin_retour');
				break;
			case 'sauvegarde':
				//Sauvegarde
				$this->add_subtab('lieux', 'sauv_menu_lieux', 'sauv_menu_lieux_c');
				$this->add_subtab('tables', 'sauv_menu_tables', 'sauv_menu_tables_c');
				$this->add_subtab('gestsauv', 'sauv_menu_jeux', 'sauv_menu_jeux_c');
				$this->add_subtab('launch', 'sauv_menu_launch', 'sauv_menu_launch_c');
				$this->add_subtab('list', 'sauv_menu_liste', 'sauv_menu_liste_c');
				break;
			case 'acquisition':
				//Acquisition
				$this->add_subtab('entite', 'acquisition_menu_ref_entite');
				$this->add_subtab('compta', 'acquisition_menu_ref_compta');
				if ($acquisition_gestion_tva) {
					$this->add_subtab('tva', 'acquisition_menu_ref_tva');
				}
				$this->add_subtab('type', 'acquisition_menu_ref_type');
				$this->add_subtab('frais', 'acquisition_menu_ref_frais');
				$this->add_subtab('mode', 'acquisition_menu_ref_mode');
				$this->add_subtab('budget', 'acquisition_menu_ref_budget');
				if($acquisition_sugg_categ=='1') {
					$this->add_subtab('categ', 'acquisition_menu_ref_categ');
				}
				$this->add_subtab('src', 'acquisition_menu_ref_src');
				$this->add_subtab('lgstat', 'acquisition_menu_ref_lgstat');
				$this->add_subtab('pricing_systems', 'acquisition_menu_pricing_systems');
				$this->add_subtab('account_types', 'acquisition_menu_account_types');
				$this->add_subtab('thresholds', 'acquisition_menu_thresholds');
				break;
			case 'transferts':
				//Transferts
				$this->add_subtab('general', 'admin_tranferts_general');
				$this->add_subtab('circ', 'admin_tranferts_circ');
				$this->add_subtab('opac', 'admin_tranferts_opac');
				$this->add_subtab('ordreloc', 'admin_tranferts_ordre_localisation');
				$this->add_subtab('statutsdef', 'admin_tranferts_statuts_defaut');
				$this->add_subtab('purge', 'admin_tranferts_purge');
				break;
			case 'demandes':
				//Demandes
				$this->add_subtab('theme', 'demandes_theme');
				$this->add_subtab('type', 'demandes_type');
				$this->add_subtab('perso', 'admin_menu_demandes_perso');
				break;
			case 'faq':
				//FAQ
				$this->add_subtab('theme', 'faq_theme');
				$this->add_subtab('type', 'faq_type');
				break;
			case 'mailtpl':
				//Templates de mail
				$this->add_subtab('build', 'admin_mailtpl_menu');
				$this->add_subtab('img', 'admin_mailtpl_img_menu');
				$this->add_subtab('attachments', 'admin_mailtpl_attachments_menu');
				break;
			case 'scan_request':
				//Demandes de numérisation
				$this->add_subtab('status', 'admin_scan_request_status');
				$this->add_subtab('workflow', 'admin_scan_request_workflow');
				$this->add_subtab('priorities', 'admin_scan_request_priorities');
				$this->add_subtab('upload_folder', 'upload_folder_storage');
				break;
			case 'misc':
			case 'netbase':
			case 'chklnk':
			case 'log':
			case 'param':
			case 'alter':
				//Outils
				$this->add_misc_subtab('netbase', 'netbase', '329', '735');
				$this->add_misc_subtab('chklnk', 'chklnk', 'chklnk_titre');
				$this->add_misc_subtab('alter', 'alter', '1801', '740');
				$this->add_misc_subtab('misc', 'tables', '31', '740');
				$this->add_misc_subtab('misc', 'mysql', '32', '741');
				if($PMBuserid == 1) {
					$this->add_misc_subtab('misc', 'files', 'files');
				}
				$this->add_misc_subtab('param', 'param', '1600');
				break;
			case 'vignette':
				//Vignette
				$vign_subtabs = array('record');
				//La suite avait apparemment été commencé..
// 				$vign_subtabs = array('record', 'author', 'categ', 'publisher', 'collection', 'subcollection', 'serie', 'tu', 'indexint');
				for($i=0; $i<count($vign_subtabs); $i++) {
					$this->add_subtab($vign_subtabs[$i], 'admin_vignette_menu_'.$vign_subtabs[$i]);
				}
				break;
			case 'animations':
				//Animations
				$this->add_subtab('status', 'animation_manage_status', '', '&action=list');
				$this->add_subtab('types', 'animation_manage_types', '', '&action=list');
				$this->add_subtab('calendar', 'animation_manage_calendar', '', '&action=list');
				$this->add_subtab('priceTypes', 'animation_manage_type');
				$this->add_subtab('perso', 'admin_menu_animations_perso_categ', '', '&type_field=anim_animation');
				$this->add_subtab('mailing', 'animation_manage_mailing');
				break;
			case 'ark':
			    // Ark
			    $this->add_subtab('naan', 'ark_manage_naan', '', '&action=edit');
			    $this->add_subtab('generate', 'ark_manage_generate', '', '');
			    break;
			case 'digital_signature':
				// Signature electronique
				$this->add_subtab('certificate', 'digital_signature_manage_certificat', '', '');
				$this->add_subtab('signature', 'digital_signature_manage_signature', '', '');
				break;
			case 'mails':
				$this->add_subtab('configuration', 'mails_configuration');
				$this->add_subtab('settings', 'mails_settings');
				break;
			case 'interface':
				switch (static::$sub) {
					case 'tabs':
						$this->add_subtab('', '&lt;&lt;', 'bt_retour');
						$list_modules_ui = new list_modules_ui();
						$objects = $list_modules_ui->get_objects();
						foreach ($objects as $object) {
							$this->add_subtab('tabs', $object->get_label(), '', '&tab_module='.$object->get_name());
						}
						break;
					default:
						$this->add_subtab('modules', 'admin_menu_modules');
						//$this->add_subtab('tabs', 'tabs');
						//$this->add_subtab('lists', 'lists');
						$this->add_subtab('selectors', 'selectors');
						break;
				}
				break;
			case 'supervision':
				$this->add_subtab('mails', 'supervision_mails');
				$this->add_subtab('logs', 'supervision_logs');
				break;
			case 'thumbnail':
				$this->add_subtab('sources', 'admin_thumbnail_sources');
				$thumbnailSourcesHandler = new ThumbnailSourcesHandler();
				foreach ($thumbnailSourcesHandler->getEntitiesList() as $entity) {
				    $this->add_subtab('pivots', $entity['label_code'], '', '&type=' . $entity['type']);
				}
				$this->add_subtab('cache', 'admin_thumbnail_cache');
				break;
			case 'acces':
				$ac = acces::get_instance();
				$t_cat= $ac->getCatalog();
				foreach($t_cat as $k=>$v) {
					$this->add_subtab('domain', $v['comment'], '', '&action=view&id='.$k);
				}
				break;
			case 'auth':
			    $this->add_subtab('services', 'admin_auth_services');
			    $this->add_subtab('models', 'admin_auth_models');
			    $this->add_subtab('config_opac', 'admin_auth_config_opac');
			    $this->add_subtab('config_gestion', 'admin_auth_config_gestion');

			    $this->add_subtab('whitelist', 'admin_auth_whitelist');
			    $this->add_subtab('blacklist', 'admin_auth_blacklist');
			    break;
			case 'mfa':
			    $this->add_subtab('services', 'mfa_services');
			    $this->add_subtab('mail', 'mfa_mail');
			    $this->add_subtab('sms', 'mfa_sms');
			    $this->add_subtab('otp', 'mfa_otp');
			    break;
			case 'ai':
				$this->add_subtab('semantic_search', 'admin_ai_semantic_search');
				$this->add_subtab('shared_lists', 'admin_ai_shared_lists');
			    break;
		}
	}

	public function add_misc_subtab($categ, $sub, $label_code, $title_code='', $url_extra='') {
		global $msg;
		global $base_path;

		if(!$title_code) $title_code = $label_code;
		$subtab = new subtab();
		$subtab->set_sub($sub)
		->set_label_code($label_code)
		->set_label(isset($msg[$label_code]) ? $msg[$label_code] : $label_code)
		->set_title_code($title_code)
		->set_title(isset($msg[$title_code]) ? $msg[$title_code] : $title_code)
		->set_url_extra($url_extra)
		->set_destination_link($base_path."/".static::$module_name.".php?categ=".$categ.($sub ? "&sub=".$sub : '').$url_extra);
		$this->add_object($subtab);
	}
}