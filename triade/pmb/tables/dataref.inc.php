<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dataref.inc.php,v 1.16 2024/03/26 11:03:40 dbellamy Exp $

// References des index sur les tables

// prevents direct script access
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// PMB version : 7.4
// PMB database version : v5.34
// PMB database subversion : 0

// Generated from DATABASE bibli on 2021-04-29 09:10:56

global $tabindexref;



//  ###################### abo_liste_lecture
$tabindexref["abo_liste_lecture"]["PRIMARY"][]="num_empr";
$tabindexref["abo_liste_lecture"]["PRIMARY"][]="num_liste";


//  ###################### abts_abts
$tabindexref["abts_abts"]["PRIMARY"][]="abt_id";
$tabindexref["abts_abts"]["index_num_notice"][]="num_notice";
$tabindexref["abts_abts"]["i_date_fin"][]="date_fin";


//  ###################### abts_abts_modeles
$tabindexref["abts_abts_modeles"]["PRIMARY"][]="modele_id";
$tabindexref["abts_abts_modeles"]["PRIMARY"][]="abt_id";


//  ###################### abts_grille_abt
$tabindexref["abts_grille_abt"]["PRIMARY"][]="id_bull";
$tabindexref["abts_grille_abt"]["num_abt"][]="num_abt";


//  ###################### abts_grille_modele
$tabindexref["abts_grille_modele"]["PRIMARY"][]="num_modele";
$tabindexref["abts_grille_modele"]["PRIMARY"][]="date_parution";
$tabindexref["abts_grille_modele"]["PRIMARY"][]="type_serie";
$tabindexref["abts_grille_modele"]["num_modele"][]="num_modele";


//  ###################### abts_modeles
$tabindexref["abts_modeles"]["PRIMARY"][]="modele_id";
$tabindexref["abts_modeles"]["num_notice"][]="num_notice";
$tabindexref["abts_modeles"]["num_periodicite"][]="num_periodicite";


//  ###################### abts_periodicites
$tabindexref["abts_periodicites"]["PRIMARY"][]="periodicite_id";


//  ###################### abts_status
$tabindexref["abts_status"]["PRIMARY"][]="abts_status_id";


//  ###################### acces_profiles
$tabindexref["acces_profiles"]["PRIMARY"][]="prf_id";
$tabindexref["acces_profiles"]["prf_type"][]="prf_type";
$tabindexref["acces_profiles"]["prf_name"][]="prf_name";
$tabindexref["acces_profiles"]["dom_num"][]="dom_num";


//  ###################### acces_rights
$tabindexref["acces_rights"]["PRIMARY"][]="dom_num";
$tabindexref["acces_rights"]["PRIMARY"][]="usr_prf_num";
$tabindexref["acces_rights"]["PRIMARY"][]="res_prf_num";
$tabindexref["acces_rights"]["dom_num"][]="dom_num";
$tabindexref["acces_rights"]["usr_prf_num"][]="usr_prf_num";
$tabindexref["acces_rights"]["res_prf_num"][]="res_prf_num";


//  ###################### actes
$tabindexref["actes"]["PRIMARY"][]="id_acte";
$tabindexref["actes"]["num_fournisseur"][]="num_fournisseur";
$tabindexref["actes"]["date"][]="date_acte";
$tabindexref["actes"]["num_entite"][]="num_entite";
$tabindexref["actes"]["numero"][]="numero";


//  ###################### admin_session
$tabindexref["admin_session"]["PRIMARY"][]="userid";


//  ###################### analysis
$tabindexref["analysis"]["PRIMARY"][]="analysis_bulletin";
$tabindexref["analysis"]["PRIMARY"][]="analysis_notice";
$tabindexref["analysis"]["analysis_notice"][]="analysis_notice";


//  ###################### anim_animation_categories
$tabindexref["anim_animation_categories"]["PRIMARY"][]="num_animation";
$tabindexref["anim_animation_categories"]["PRIMARY"][]="num_noeud";


//  ###################### anim_animation_custom
$tabindexref["anim_animation_custom"]["PRIMARY"][]="idchamp";


//  ###################### anim_animation_custom_dates
$tabindexref["anim_animation_custom_dates"]["PRIMARY"][]="anim_animation_custom_champ";
$tabindexref["anim_animation_custom_dates"]["PRIMARY"][]="anim_animation_custom_origine";
$tabindexref["anim_animation_custom_dates"]["PRIMARY"][]="anim_animation_custom_order";
$tabindexref["anim_animation_custom_dates"]["anim_animation_custom_champ"][]="anim_animation_custom_champ";
$tabindexref["anim_animation_custom_dates"]["anim_animation_custom_origine"][]="anim_animation_custom_origine";


//  ###################### anim_animation_custom_lists
$tabindexref["anim_animation_custom_lists"]["editorial_custom_champ"][]="anim_animation_custom_champ";
$tabindexref["anim_animation_custom_lists"]["editorial_champ_list_value"][]="anim_animation_custom_champ";
$tabindexref["anim_animation_custom_lists"]["editorial_champ_list_value"][]="anim_animation_custom_list_value";


//  ###################### anim_animation_custom_values
$tabindexref["anim_animation_custom_values"]["anim_animation_custom_champ"][]="anim_animation_custom_champ";
$tabindexref["anim_animation_custom_values"]["anim_animation_custom_origine"][]="anim_animation_custom_origine";
$tabindexref["anim_animation_custom_values"]["i_encv_st"][]="anim_animation_custom_small_text";
$tabindexref["anim_animation_custom_values"]["i_encv_t"][]="anim_animation_custom_text";
$tabindexref["anim_animation_custom_values"]["i_encv_i"][]="anim_animation_custom_integer";
$tabindexref["anim_animation_custom_values"]["i_encv_d"][]="anim_animation_custom_date";
$tabindexref["anim_animation_custom_values"]["i_encv_f"][]="anim_animation_custom_float";


//  ###################### anim_animation_locations
$tabindexref["anim_animation_locations"]["PRIMARY"][]="num_animation";
$tabindexref["anim_animation_locations"]["PRIMARY"][]="num_location";


//  ###################### anim_animations
$tabindexref["anim_animations"]["PRIMARY"][]="id_animation";


//  ###################### anim_events
$tabindexref["anim_events"]["PRIMARY"][]="id_event";


//  ###################### anim_mailing_list
$tabindexref["anim_mailing_list"]["PRIMARY"][]="id_mailing_list";


//  ###################### anim_mailing_types
$tabindexref["anim_mailing_types"]["PRIMARY"][]="id_mailing_type";


//  ###################### anim_mailings
$tabindexref["anim_mailings"]["PRIMARY"][]="id_mailing";


//  ###################### anim_price_type_categories
$tabindexref["anim_price_type_categories"]["PRIMARY"][]="num_price_type";
$tabindexref["anim_price_type_categories"]["PRIMARY"][]="num_noeud";


//  ###################### anim_price_type_custom
$tabindexref["anim_price_type_custom"]["PRIMARY"][]="idchamp";


//  ###################### anim_price_type_custom_dates
$tabindexref["anim_price_type_custom_dates"]["PRIMARY"][]="anim_price_type_custom_champ";
$tabindexref["anim_price_type_custom_dates"]["PRIMARY"][]="anim_price_type_custom_origine";
$tabindexref["anim_price_type_custom_dates"]["PRIMARY"][]="anim_price_type_custom_order";
$tabindexref["anim_price_type_custom_dates"]["anim_price_type_custom_champ"][]="anim_price_type_custom_champ";
$tabindexref["anim_price_type_custom_dates"]["anim_price_type_custom_origine"][]="anim_price_type_custom_origine";


//  ###################### anim_price_type_custom_lists
$tabindexref["anim_price_type_custom_lists"]["editorial_custom_champ"][]="anim_price_type_custom_champ";
$tabindexref["anim_price_type_custom_lists"]["editorial_champ_list_value"][]="anim_price_type_custom_champ";
$tabindexref["anim_price_type_custom_lists"]["editorial_champ_list_value"][]="anim_price_type_custom_list_value";


//  ###################### anim_price_type_custom_values
$tabindexref["anim_price_type_custom_values"]["anim_price_type_custom_champ"][]="anim_price_type_custom_champ";
$tabindexref["anim_price_type_custom_values"]["anim_price_type_custom_origine"][]="anim_price_type_custom_origine";
$tabindexref["anim_price_type_custom_values"]["i_encv_st"][]="anim_price_type_custom_small_text";
$tabindexref["anim_price_type_custom_values"]["i_encv_t"][]="anim_price_type_custom_text";
$tabindexref["anim_price_type_custom_values"]["i_encv_i"][]="anim_price_type_custom_integer";
$tabindexref["anim_price_type_custom_values"]["i_encv_d"][]="anim_price_type_custom_date";
$tabindexref["anim_price_type_custom_values"]["i_encv_f"][]="anim_price_type_custom_float";


//  ###################### anim_price_types
$tabindexref["anim_price_types"]["PRIMARY"][]="id_price_type";


//  ###################### anim_prices
$tabindexref["anim_prices"]["PRIMARY"][]="id_price";


//  ###################### anim_registration_origins
$tabindexref["anim_registration_origins"]["PRIMARY"][]="id_registration_origin";


//  ###################### anim_registration_status
$tabindexref["anim_registration_status"]["PRIMARY"][]="id_registration_status";


//  ###################### anim_registrations
$tabindexref["anim_registrations"]["PRIMARY"][]="id_registration";


//  ###################### anim_registred_persons
$tabindexref["anim_registred_persons"]["PRIMARY"][]="id_person";


//  ###################### anim_status
$tabindexref["anim_status"]["PRIMARY"][]="id_status";


//  ###################### arch_emplacement
$tabindexref["arch_emplacement"]["PRIMARY"][]="archempla_id";


//  ###################### arch_statut
$tabindexref["arch_statut"]["PRIMARY"][]="archstatut_id";


//  ###################### arch_type
$tabindexref["arch_type"]["PRIMARY"][]="archtype_id";


//  ###################### audit
$tabindexref["audit"]["type_obj"][]="type_obj";
$tabindexref["audit"]["object_id"][]="object_id";
$tabindexref["audit"]["user_id"][]="user_id";
$tabindexref["audit"]["type_modif"][]="type_modif";


//  ###################### aut_link
$tabindexref["aut_link"]["PRIMARY"][]="id_aut_link";
$tabindexref["aut_link"]["i_from"][]="aut_link_from";
$tabindexref["aut_link"]["i_from"][]="aut_link_from_num";
$tabindexref["aut_link"]["i_to"][]="aut_link_to";
$tabindexref["aut_link"]["i_to"][]="aut_link_to_num";
$tabindexref["aut_link"]["aut_link_from"][]="aut_link_from";
$tabindexref["aut_link"]["aut_link_from"][]="aut_link_from_num";
$tabindexref["aut_link"]["aut_link_from"][]="aut_link_to";
$tabindexref["aut_link"]["aut_link_from"][]="aut_link_to_num";
$tabindexref["aut_link"]["aut_link_from"][]="aut_link_type";


//  ###################### author_custom
$tabindexref["author_custom"]["PRIMARY"][]="idchamp";


//  ###################### author_custom_dates
$tabindexref["author_custom_dates"]["PRIMARY"][]="author_custom_champ";
$tabindexref["author_custom_dates"]["PRIMARY"][]="author_custom_origine";
$tabindexref["author_custom_dates"]["PRIMARY"][]="author_custom_order";
$tabindexref["author_custom_dates"]["author_custom_champ"][]="author_custom_champ";
$tabindexref["author_custom_dates"]["author_custom_origine"][]="author_custom_origine";


//  ###################### author_custom_lists
$tabindexref["author_custom_lists"]["editorial_custom_champ"][]="author_custom_champ";
$tabindexref["author_custom_lists"]["editorial_champ_list_value"][]="author_custom_champ";
$tabindexref["author_custom_lists"]["editorial_champ_list_value"][]="author_custom_list_value";


//  ###################### author_custom_values
$tabindexref["author_custom_values"]["editorial_custom_champ"][]="author_custom_champ";
$tabindexref["author_custom_values"]["editorial_custom_origine"][]="author_custom_origine";
$tabindexref["author_custom_values"]["i_acv_st"][]="author_custom_small_text";
$tabindexref["author_custom_values"]["i_acv_t"][]="author_custom_text";
$tabindexref["author_custom_values"]["i_acv_i"][]="author_custom_integer";
$tabindexref["author_custom_values"]["i_acv_d"][]="author_custom_date";
$tabindexref["author_custom_values"]["i_acv_f"][]="author_custom_float";


//  ###################### authorities
$tabindexref["authorities"]["PRIMARY"][]="id_authority";
$tabindexref["authorities"]["i_a_num_object_type_object"][]="num_object";
$tabindexref["authorities"]["i_a_num_object_type_object"][]="type_object";
$tabindexref["authorities"]["i_a_num_statut"][]="num_statut";


//  ###################### authorities_caddie
$tabindexref["authorities_caddie"]["PRIMARY"][]="idcaddie";
$tabindexref["authorities_caddie"]["caddie_type"][]="type";


//  ###################### authorities_caddie_content
$tabindexref["authorities_caddie_content"]["PRIMARY"][]="caddie_id";
$tabindexref["authorities_caddie_content"]["PRIMARY"][]="object_id";
$tabindexref["authorities_caddie_content"]["object_id"][]="object_id";


//  ###################### authorities_caddie_procs
$tabindexref["authorities_caddie_procs"]["PRIMARY"][]="idproc";


//  ###################### authorities_fields_global_index
$tabindexref["authorities_fields_global_index"]["PRIMARY"][]="id_authority";
$tabindexref["authorities_fields_global_index"]["PRIMARY"][]="code_champ";
$tabindexref["authorities_fields_global_index"]["PRIMARY"][]="code_ss_champ";
$tabindexref["authorities_fields_global_index"]["PRIMARY"][]="ordre";
$tabindexref["authorities_fields_global_index"]["PRIMARY"][]="lang";
$tabindexref["authorities_fields_global_index"]["i_value"][]="value";
$tabindexref["authorities_fields_global_index"]["i_id_value"][]="id_authority";
$tabindexref["authorities_fields_global_index"]["i_id_value"][]="value";
$tabindexref["authorities_fields_global_index"]["i_code_champ_code_ss_champ"][]="code_champ";
$tabindexref["authorities_fields_global_index"]["i_code_champ_code_ss_champ"][]="code_ss_champ";
$tabindexref["authorities_fields_global_index"]["i_id_authority"][]="id_authority";


//  ###################### authorities_sources
$tabindexref["authorities_sources"]["PRIMARY"][]="id_authority_source";
$tabindexref["authorities_sources"]["i_num_authority_authority_type"][]="num_authority";
$tabindexref["authorities_sources"]["i_num_authority_authority_type"][]="authority_type";


//  ###################### authorities_statuts
$tabindexref["authorities_statuts"]["PRIMARY"][]="id_authorities_statut";


//  ###################### authorities_words_global_index
$tabindexref["authorities_words_global_index"]["PRIMARY"][]="id_authority";
$tabindexref["authorities_words_global_index"]["PRIMARY"][]="code_champ";
$tabindexref["authorities_words_global_index"]["PRIMARY"][]="code_ss_champ";
$tabindexref["authorities_words_global_index"]["PRIMARY"][]="num_word";
$tabindexref["authorities_words_global_index"]["PRIMARY"][]="position";
$tabindexref["authorities_words_global_index"]["PRIMARY"][]="field_position";
$tabindexref["authorities_words_global_index"]["code_champ"][]="code_champ";
$tabindexref["authorities_words_global_index"]["i_id_mot"][]="num_word";
$tabindexref["authorities_words_global_index"]["i_id_mot"][]="id_authority";
$tabindexref["authorities_words_global_index"]["i_code_champ_code_ss_champ_num_word"][]="code_champ";
$tabindexref["authorities_words_global_index"]["i_code_champ_code_ss_champ_num_word"][]="code_ss_champ";
$tabindexref["authorities_words_global_index"]["i_code_champ_code_ss_champ_num_word"][]="num_word";
$tabindexref["authorities_words_global_index"]["i_num_word"][]="num_word";
$tabindexref["authorities_words_global_index"]["i_id_authority"][]="id_authority";


//  ###################### authors
$tabindexref["authors"]["PRIMARY"][]="author_id";
$tabindexref["authors"]["author_see"][]="author_see";
$tabindexref["authors"]["author_name"][]="author_name";
$tabindexref["authors"]["author_rejete"][]="author_rejete";
$tabindexref["authors"]["i_author_type"][]="author_type";
$tabindexref["authors"]["i_index_author_author_type"][]="index_author";
$tabindexref["authors"]["i_index_author_author_type"][]="author_type";


//  ###################### authperso
$tabindexref["authperso"]["PRIMARY"][]="id_authperso";


//  ###################### authperso_authorities
$tabindexref["authperso_authorities"]["PRIMARY"][]="id_authperso_authority";
$tabindexref["authperso_authorities"]["i_authperso_authority_authperso_num"][]="authperso_authority_authperso_num";


//  ###################### authperso_custom
$tabindexref["authperso_custom"]["PRIMARY"][]="idchamp";


//  ###################### authperso_custom_dates
$tabindexref["authperso_custom_dates"]["PRIMARY"][]="authperso_custom_champ";
$tabindexref["authperso_custom_dates"]["PRIMARY"][]="authperso_custom_origine";
$tabindexref["authperso_custom_dates"]["PRIMARY"][]="authperso_custom_order";
$tabindexref["authperso_custom_dates"]["authperso_custom_champ"][]="authperso_custom_champ";
$tabindexref["authperso_custom_dates"]["authperso_custom_origine"][]="authperso_custom_origine";


//  ###################### authperso_custom_lists
$tabindexref["authperso_custom_lists"]["editorial_custom_champ"][]="authperso_custom_champ";
$tabindexref["authperso_custom_lists"]["editorial_champ_list_value"][]="authperso_custom_champ";
$tabindexref["authperso_custom_lists"]["editorial_champ_list_value"][]="authperso_custom_list_value";


//  ###################### authperso_custom_values
$tabindexref["authperso_custom_values"]["editorial_custom_champ"][]="authperso_custom_champ";
$tabindexref["authperso_custom_values"]["editorial_custom_origine"][]="authperso_custom_origine";
$tabindexref["authperso_custom_values"]["i_acv_st"][]="authperso_custom_small_text";
$tabindexref["authperso_custom_values"]["i_acv_t"][]="authperso_custom_text";
$tabindexref["authperso_custom_values"]["i_acv_i"][]="authperso_custom_integer";
$tabindexref["authperso_custom_values"]["i_acv_d"][]="authperso_custom_date";
$tabindexref["authperso_custom_values"]["i_acv_f"][]="authperso_custom_float";


//  ###################### avis
$tabindexref["avis"]["PRIMARY"][]="id_avis";
$tabindexref["avis"]["avis_num_notice"][]="num_notice";
$tabindexref["avis"]["avis_num_empr"][]="num_empr";
$tabindexref["avis"]["avis_note"][]="note";


//  ###################### bannette_abon
$tabindexref["bannette_abon"]["PRIMARY"][]="num_bannette";
$tabindexref["bannette_abon"]["PRIMARY"][]="num_empr";
$tabindexref["bannette_abon"]["i_num_empr"][]="num_empr";


//  ###################### bannette_contenu
$tabindexref["bannette_contenu"]["PRIMARY"][]="num_bannette";
$tabindexref["bannette_contenu"]["PRIMARY"][]="num_notice";
$tabindexref["bannette_contenu"]["date_ajout"][]="date_ajout";
$tabindexref["bannette_contenu"]["i_num_notice"][]="num_notice";


//  ###################### bannette_empr_categs
$tabindexref["bannette_empr_categs"]["PRIMARY"][]="empr_categ_num_bannette";
$tabindexref["bannette_empr_categs"]["PRIMARY"][]="empr_categ_num_categ";


//  ###################### bannette_empr_groupes
$tabindexref["bannette_empr_groupes"]["PRIMARY"][]="empr_groupe_num_bannette";
$tabindexref["bannette_empr_groupes"]["PRIMARY"][]="empr_groupe_num_groupe";


//  ###################### bannette_equation
$tabindexref["bannette_equation"]["PRIMARY"][]="num_bannette";
$tabindexref["bannette_equation"]["PRIMARY"][]="num_equation";


//  ###################### bannette_exports
$tabindexref["bannette_exports"]["PRIMARY"][]="num_bannette";
$tabindexref["bannette_exports"]["PRIMARY"][]="export_format";


//  ###################### bannette_facettes
$tabindexref["bannette_facettes"]["bannette_facettes_key"][]="num_ban_facette";
$tabindexref["bannette_facettes"]["bannette_facettes_key"][]="ban_facette_critere";
$tabindexref["bannette_facettes"]["bannette_facettes_key"][]="ban_facette_ss_critere";


//  ###################### bannette_tpl
$tabindexref["bannette_tpl"]["PRIMARY"][]="bannettetpl_id";


//  ###################### bannettes
$tabindexref["bannettes"]["PRIMARY"][]="id_bannette";
$tabindexref["bannettes"]["i_bannette_tpl_num"][]="bannette_tpl_num";


//  ###################### bannettes_descriptors
$tabindexref["bannettes_descriptors"]["PRIMARY"][]="num_bannette";
$tabindexref["bannettes_descriptors"]["PRIMARY"][]="num_noeud";


//  ###################### budgets
$tabindexref["budgets"]["PRIMARY"][]="id_budget";


//  ###################### bulletins
$tabindexref["bulletins"]["PRIMARY"][]="bulletin_id";
$tabindexref["bulletins"]["bulletin_numero"][]="bulletin_numero";
$tabindexref["bulletins"]["bulletin_notice"][]="bulletin_notice";
$tabindexref["bulletins"]["date_date"][]="date_date";
$tabindexref["bulletins"]["i_num_notice"][]="num_notice";


//  ###################### cache_amendes
$tabindexref["cache_amendes"]["id_empr"][]="id_empr";


//  ###################### caddie
$tabindexref["caddie"]["PRIMARY"][]="idcaddie";
$tabindexref["caddie"]["caddie_type"][]="type";


//  ###################### caddie_content
$tabindexref["caddie_content"]["PRIMARY"][]="caddie_id";
$tabindexref["caddie_content"]["PRIMARY"][]="object_id";
$tabindexref["caddie_content"]["PRIMARY"][]="content";
$tabindexref["caddie_content"]["object_id"][]="object_id";


//  ###################### caddie_procs
$tabindexref["caddie_procs"]["PRIMARY"][]="idproc";


//  ###################### campaigns
$tabindexref["campaigns"]["PRIMARY"][]="id_campaign";


//  ###################### campaigns_descriptors
$tabindexref["campaigns_descriptors"]["PRIMARY"][]="num_campaign";
$tabindexref["campaigns_descriptors"]["PRIMARY"][]="num_noeud";


//  ###################### campaigns_logs
$tabindexref["campaigns_logs"]["i_campaign_log_num_campaign"][]="campaign_log_num_campaign";
$tabindexref["campaigns_logs"]["i_campaign_log_num_recipient"][]="campaign_log_num_recipient";


//  ###################### campaigns_recipients
$tabindexref["campaigns_recipients"]["PRIMARY"][]="id_campaign_recipient";
$tabindexref["campaigns_recipients"]["i_campaign_recipient_num_campaign"][]="campaign_recipient_num_campaign";


//  ###################### campaigns_stats
$tabindexref["campaigns_stats"]["PRIMARY"][]="campaign_stat_num_campaign";


//  ###################### campaigns_tags
$tabindexref["campaigns_tags"]["PRIMARY"][]="num_campaign";
$tabindexref["campaigns_tags"]["PRIMARY"][]="num_tag";


//  ###################### cashdesk
$tabindexref["cashdesk"]["PRIMARY"][]="cashdesk_id";


//  ###################### cashdesk_locations
$tabindexref["cashdesk_locations"]["PRIMARY"][]="cashdesk_loc_cashdesk_num";
$tabindexref["cashdesk_locations"]["PRIMARY"][]="cashdesk_loc_num";


//  ###################### cashdesk_sections
$tabindexref["cashdesk_sections"]["PRIMARY"][]="cashdesk_section_cashdesk_num";
$tabindexref["cashdesk_sections"]["PRIMARY"][]="cashdesk_section_num";


//  ###################### categ_custom
$tabindexref["categ_custom"]["PRIMARY"][]="idchamp";


//  ###################### categ_custom_dates
$tabindexref["categ_custom_dates"]["PRIMARY"][]="categ_custom_champ";
$tabindexref["categ_custom_dates"]["PRIMARY"][]="categ_custom_origine";
$tabindexref["categ_custom_dates"]["PRIMARY"][]="categ_custom_order";
$tabindexref["categ_custom_dates"]["categ_custom_champ"][]="categ_custom_champ";
$tabindexref["categ_custom_dates"]["categ_custom_origine"][]="categ_custom_origine";


//  ###################### categ_custom_lists
$tabindexref["categ_custom_lists"]["editorial_custom_champ"][]="categ_custom_champ";
$tabindexref["categ_custom_lists"]["editorial_champ_list_value"][]="categ_custom_champ";
$tabindexref["categ_custom_lists"]["editorial_champ_list_value"][]="categ_custom_list_value";


//  ###################### categ_custom_values
$tabindexref["categ_custom_values"]["editorial_custom_champ"][]="categ_custom_champ";
$tabindexref["categ_custom_values"]["editorial_custom_origine"][]="categ_custom_origine";
$tabindexref["categ_custom_values"]["i_ccv_st"][]="categ_custom_small_text";
$tabindexref["categ_custom_values"]["i_ccv_t"][]="categ_custom_text";
$tabindexref["categ_custom_values"]["i_ccv_i"][]="categ_custom_integer";
$tabindexref["categ_custom_values"]["i_ccv_d"][]="categ_custom_date";
$tabindexref["categ_custom_values"]["i_ccv_f"][]="categ_custom_float";


//  ###################### categories
$tabindexref["categories"]["PRIMARY"][]="num_noeud";
$tabindexref["categories"]["PRIMARY"][]="langue";
$tabindexref["categories"]["categ_langue"][]="langue";
$tabindexref["categories"]["libelle_categorie"][]="libelle_categorie";
$tabindexref["categories"]["i_num_thesaurus"][]="num_thesaurus";


//  ###################### chat_groups
$tabindexref["chat_groups"]["PRIMARY"][]="id_chat_group";


//  ###################### chat_messages
$tabindexref["chat_messages"]["PRIMARY"][]="id_chat_message";
$tabindexref["chat_messages"]["i_from_user_num"][]="chat_message_from_user_num";
$tabindexref["chat_messages"]["i_from_user_num"][]="chat_message_from_user_type";


//  ###################### chat_users_groups
$tabindexref["chat_users_groups"]["PRIMARY"][]="chat_user_group_num";
$tabindexref["chat_users_groups"]["PRIMARY"][]="chat_user_group_user_type";
$tabindexref["chat_users_groups"]["PRIMARY"][]="chat_user_group_user_num";


//  ###################### classements
$tabindexref["classements"]["PRIMARY"][]="id_classement";


//  ###################### cms
$tabindexref["cms"]["PRIMARY"][]="id_cms";


//  ###################### cms_articles
$tabindexref["cms_articles"]["PRIMARY"][]="id_article";
$tabindexref["cms_articles"]["i_cms_article_title"][]="article_title";
$tabindexref["cms_articles"]["i_cms_article_publication_state"][]="article_publication_state";
$tabindexref["cms_articles"]["i_cms_article_num_parent"][]="num_section";


//  ###################### cms_articles_descriptors
$tabindexref["cms_articles_descriptors"]["PRIMARY"][]="num_article";
$tabindexref["cms_articles_descriptors"]["PRIMARY"][]="num_noeud";


//  ###################### cms_build
$tabindexref["cms_build"]["PRIMARY"][]="id_build";
$tabindexref["cms_build"]["cms_build_index"][]="build_version_num";
$tabindexref["cms_build"]["cms_build_index"][]="build_obj";
$tabindexref["cms_build"]["i_build_parent_build_version_num"][]="build_parent";
$tabindexref["cms_build"]["i_build_parent_build_version_num"][]="build_version_num";
$tabindexref["cms_build"]["i_build_obj_build_version_num"][]="build_obj";
$tabindexref["cms_build"]["i_build_obj_build_version_num"][]="build_version_num";


//  ###################### cms_cache_cadres
$tabindexref["cms_cache_cadres"]["PRIMARY"][]="cache_cadre_hash";
$tabindexref["cms_cache_cadres"]["PRIMARY"][]="cache_cadre_type_content";
$tabindexref["cms_cache_cadres"]["i_cache_cadre_create_date"][]="cache_cadre_create_date";


//  ###################### cms_cadre_content
$tabindexref["cms_cadre_content"]["PRIMARY"][]="id_cadre_content";
$tabindexref["cms_cadre_content"]["i_cadre_content_num_cadre"][]="cadre_content_num_cadre";
$tabindexref["cms_cadre_content"]["i_cadre_content_num_cadre_content_cadre_content_type"][]="cadre_content_num_cadre_content";
$tabindexref["cms_cadre_content"]["i_cadre_content_num_cadre_content_cadre_content_type"][]="cadre_content_type";


//  ###################### cms_cadres
$tabindexref["cms_cadres"]["PRIMARY"][]="id_cadre";
$tabindexref["cms_cadres"]["i_cadre_memo_url"][]="cadre_memo_url";
$tabindexref["cms_cadres"]["i_cadre_object"][]="cadre_object";


//  ###################### cms_collections
$tabindexref["cms_collections"]["PRIMARY"][]="id_collection";
$tabindexref["cms_collections"]["i_cms_collection_title"][]="collection_title";


//  ###################### cms_documents
$tabindexref["cms_documents"]["PRIMARY"][]="id_document";
$tabindexref["cms_documents"]["i_cms_document_title"][]="document_title";
$tabindexref["cms_documents"]["i_document_num_object_document_type_object"][]="document_num_object";
$tabindexref["cms_documents"]["i_document_num_object_document_type_object"][]="document_type_object";


//  ###################### cms_documents_links
$tabindexref["cms_documents_links"]["PRIMARY"][]="document_link_type_object";
$tabindexref["cms_documents_links"]["PRIMARY"][]="document_link_num_object";
$tabindexref["cms_documents_links"]["PRIMARY"][]="document_link_num_document";
$tabindexref["cms_documents_links"]["i_document_link_num_document"][]="document_link_num_document";


//  ###################### cms_editorial_custom
$tabindexref["cms_editorial_custom"]["PRIMARY"][]="idchamp";
$tabindexref["cms_editorial_custom"]["i_num_type"][]="num_type";


//  ###################### cms_editorial_custom_dates
$tabindexref["cms_editorial_custom_dates"]["PRIMARY"][]="cms_editorial_custom_champ";
$tabindexref["cms_editorial_custom_dates"]["PRIMARY"][]="cms_editorial_custom_origine";
$tabindexref["cms_editorial_custom_dates"]["PRIMARY"][]="cms_editorial_custom_order";
$tabindexref["cms_editorial_custom_dates"]["cms_editorial_custom_champ"][]="cms_editorial_custom_champ";
$tabindexref["cms_editorial_custom_dates"]["cms_editorial_custom_origine"][]="cms_editorial_custom_origine";


//  ###################### cms_editorial_custom_lists
$tabindexref["cms_editorial_custom_lists"]["editorial_custom_champ"][]="cms_editorial_custom_champ";
$tabindexref["cms_editorial_custom_lists"]["editorial_champ_list_value"][]="cms_editorial_custom_champ";
$tabindexref["cms_editorial_custom_lists"]["editorial_champ_list_value"][]="cms_editorial_custom_list_value";


//  ###################### cms_editorial_custom_values
$tabindexref["cms_editorial_custom_values"]["editorial_custom_champ"][]="cms_editorial_custom_champ";
$tabindexref["cms_editorial_custom_values"]["editorial_custom_origine"][]="cms_editorial_custom_origine";
$tabindexref["cms_editorial_custom_values"]["i_ccv_st"][]="cms_editorial_custom_small_text";
$tabindexref["cms_editorial_custom_values"]["i_ccv_t"][]="cms_editorial_custom_text";
$tabindexref["cms_editorial_custom_values"]["i_ccv_i"][]="cms_editorial_custom_integer";
$tabindexref["cms_editorial_custom_values"]["i_ccv_d"][]="cms_editorial_custom_date";
$tabindexref["cms_editorial_custom_values"]["i_ccv_f"][]="cms_editorial_custom_float";


//  ###################### cms_editorial_fields_global_index
$tabindexref["cms_editorial_fields_global_index"]["PRIMARY"][]="num_obj";
$tabindexref["cms_editorial_fields_global_index"]["PRIMARY"][]="type";
$tabindexref["cms_editorial_fields_global_index"]["PRIMARY"][]="code_champ";
$tabindexref["cms_editorial_fields_global_index"]["PRIMARY"][]="code_ss_champ";
$tabindexref["cms_editorial_fields_global_index"]["PRIMARY"][]="ordre";
$tabindexref["cms_editorial_fields_global_index"]["PRIMARY"][]="lang";
$tabindexref["cms_editorial_fields_global_index"]["i_value"][]="value";


//  ###################### cms_editorial_publications_states
$tabindexref["cms_editorial_publications_states"]["PRIMARY"][]="id_publication_state";


//  ###################### cms_editorial_types
$tabindexref["cms_editorial_types"]["PRIMARY"][]="id_editorial_type";
$tabindexref["cms_editorial_types"]["i_editorial_type_element"][]="editorial_type_element";


//  ###################### cms_editorial_words_global_index
$tabindexref["cms_editorial_words_global_index"]["PRIMARY"][]="num_obj";
$tabindexref["cms_editorial_words_global_index"]["PRIMARY"][]="type";
$tabindexref["cms_editorial_words_global_index"]["PRIMARY"][]="code_champ";
$tabindexref["cms_editorial_words_global_index"]["PRIMARY"][]="code_ss_champ";
$tabindexref["cms_editorial_words_global_index"]["PRIMARY"][]="num_word";
$tabindexref["cms_editorial_words_global_index"]["PRIMARY"][]="position";
$tabindexref["cms_editorial_words_global_index"]["i_num_word"][]="num_word";


//  ###################### cms_hash
$tabindexref["cms_hash"]["PRIMARY"][]="hash";


//  ###################### cms_managed_modules
$tabindexref["cms_managed_modules"]["PRIMARY"][]="managed_module_name";


//  ###################### cms_modules_extensions_datas
$tabindexref["cms_modules_extensions_datas"]["PRIMARY"][]="id_extension_datas";


//  ###################### cms_pages
$tabindexref["cms_pages"]["PRIMARY"][]="id_page";


//  ###################### cms_pages_env
$tabindexref["cms_pages_env"]["PRIMARY"][]="page_env_num_page";


//  ###################### cms_sections
$tabindexref["cms_sections"]["PRIMARY"][]="id_section";
$tabindexref["cms_sections"]["i_cms_section_title"][]="section_title";
$tabindexref["cms_sections"]["i_cms_section_publication_state"][]="section_publication_state";
$tabindexref["cms_sections"]["i_cms_section_num_parent"][]="section_num_parent";


//  ###################### cms_sections_descriptors
$tabindexref["cms_sections_descriptors"]["PRIMARY"][]="num_section";
$tabindexref["cms_sections_descriptors"]["PRIMARY"][]="num_noeud";


//  ###################### cms_toolkits
$tabindexref["cms_toolkits"]["PRIMARY"][]="cms_toolkit_name";


//  ###################### cms_vars
$tabindexref["cms_vars"]["PRIMARY"][]="id_var";


//  ###################### cms_version
$tabindexref["cms_version"]["PRIMARY"][]="id_version";


//  ###################### collection_custom
$tabindexref["collection_custom"]["PRIMARY"][]="idchamp";


//  ###################### collection_custom_dates
$tabindexref["collection_custom_dates"]["PRIMARY"][]="collection_custom_champ";
$tabindexref["collection_custom_dates"]["PRIMARY"][]="collection_custom_origine";
$tabindexref["collection_custom_dates"]["PRIMARY"][]="collection_custom_order";
$tabindexref["collection_custom_dates"]["collection_custom_champ"][]="collection_custom_champ";
$tabindexref["collection_custom_dates"]["collection_custom_origine"][]="collection_custom_origine";


//  ###################### collection_custom_lists
$tabindexref["collection_custom_lists"]["editorial_custom_champ"][]="collection_custom_champ";
$tabindexref["collection_custom_lists"]["editorial_champ_list_value"][]="collection_custom_champ";
$tabindexref["collection_custom_lists"]["editorial_champ_list_value"][]="collection_custom_list_value";


//  ###################### collection_custom_values
$tabindexref["collection_custom_values"]["editorial_custom_champ"][]="collection_custom_champ";
$tabindexref["collection_custom_values"]["editorial_custom_origine"][]="collection_custom_origine";
$tabindexref["collection_custom_values"]["i_ccv_st"][]="collection_custom_small_text";
$tabindexref["collection_custom_values"]["i_ccv_t"][]="collection_custom_text";
$tabindexref["collection_custom_values"]["i_ccv_i"][]="collection_custom_integer";
$tabindexref["collection_custom_values"]["i_ccv_d"][]="collection_custom_date";
$tabindexref["collection_custom_values"]["i_ccv_f"][]="collection_custom_float";


//  ###################### collections
$tabindexref["collections"]["PRIMARY"][]="collection_id";
$tabindexref["collections"]["collection_name"][]="collection_name";
$tabindexref["collections"]["collection_parent"][]="collection_parent";


//  ###################### collections_state
$tabindexref["collections_state"]["PRIMARY"][]="collstate_id";
$tabindexref["collections_state"]["i_colls_arc"][]="collstate_archive";
$tabindexref["collections_state"]["i_colls_empl"][]="collstate_emplacement";
$tabindexref["collections_state"]["i_colls_type"][]="collstate_type";
$tabindexref["collections_state"]["i_colls_orig"][]="collstate_origine";
$tabindexref["collections_state"]["i_colls_cote"][]="collstate_cote";
$tabindexref["collections_state"]["i_colls_stat"][]="collstate_statut";
$tabindexref["collections_state"]["i_colls_serial"][]="id_serial";
$tabindexref["collections_state"]["i_colls_loc"][]="location_id";


//  ###################### collstate_bulletins
$tabindexref["collstate_bulletins"]["PRIMARY"][]="collstate_bulletins_num_collstate";
$tabindexref["collstate_bulletins"]["PRIMARY"][]="collstate_bulletins_num_bulletin";


//  ###################### collstate_custom
$tabindexref["collstate_custom"]["PRIMARY"][]="idchamp";


//  ###################### collstate_custom_dates
$tabindexref["collstate_custom_dates"]["PRIMARY"][]="collstate_custom_champ";
$tabindexref["collstate_custom_dates"]["PRIMARY"][]="collstate_custom_origine";
$tabindexref["collstate_custom_dates"]["PRIMARY"][]="collstate_custom_order";
$tabindexref["collstate_custom_dates"]["collstate_custom_champ"][]="collstate_custom_champ";
$tabindexref["collstate_custom_dates"]["collstate_custom_origine"][]="collstate_custom_origine";


//  ###################### collstate_custom_lists
$tabindexref["collstate_custom_lists"]["collstate_custom_champ"][]="collstate_custom_champ";
$tabindexref["collstate_custom_lists"]["i_ccl_lv"][]="collstate_custom_list_value";


//  ###################### collstate_custom_values
$tabindexref["collstate_custom_values"]["collstate_custom_champ"][]="collstate_custom_champ";
$tabindexref["collstate_custom_values"]["collstate_custom_origine"][]="collstate_custom_origine";
$tabindexref["collstate_custom_values"]["i_ccv_st"][]="collstate_custom_small_text";
$tabindexref["collstate_custom_values"]["i_ccv_t"][]="collstate_custom_text";
$tabindexref["collstate_custom_values"]["i_ccv_i"][]="collstate_custom_integer";
$tabindexref["collstate_custom_values"]["i_ccv_d"][]="collstate_custom_date";
$tabindexref["collstate_custom_values"]["i_ccv_f"][]="collstate_custom_float";


//  ###################### comptes
$tabindexref["comptes"]["PRIMARY"][]="id_compte";
$tabindexref["comptes"]["i_cpt_proprio_id"][]="proprio_id";


//  ###################### connectors
$tabindexref["connectors"]["PRIMARY"][]="connector_id";


//  ###################### connectors_categ
$tabindexref["connectors_categ"]["PRIMARY"][]="connectors_categ_id";


//  ###################### connectors_categ_sources
$tabindexref["connectors_categ_sources"]["PRIMARY"][]="num_categ";
$tabindexref["connectors_categ_sources"]["PRIMARY"][]="num_source";
$tabindexref["connectors_categ_sources"]["i_num_source"][]="num_source";


//  ###################### connectors_out
$tabindexref["connectors_out"]["PRIMARY"][]="connectors_out_id";


//  ###################### connectors_out_oai_deleted_records
$tabindexref["connectors_out_oai_deleted_records"]["PRIMARY"][]="num_set";
$tabindexref["connectors_out_oai_deleted_records"]["PRIMARY"][]="num_notice";


//  ###################### connectors_out_oai_tokens
$tabindexref["connectors_out_oai_tokens"]["PRIMARY"][]="connectors_out_oai_token_token";


//  ###################### connectors_out_setcache_values
$tabindexref["connectors_out_setcache_values"]["PRIMARY"][]="connectors_out_setcache_values_cachenum";
$tabindexref["connectors_out_setcache_values"]["PRIMARY"][]="connectors_out_setcache_values_value";


//  ###################### connectors_out_setcaches
$tabindexref["connectors_out_setcaches"]["PRIMARY"][]="connectors_out_setcache_id";
$tabindexref["connectors_out_setcaches"]["connectors_out_setcache_setnum"][]="connectors_out_setcache_setnum";


//  ###################### connectors_out_setcateg_sets
$tabindexref["connectors_out_setcateg_sets"]["PRIMARY"][]="connectors_out_setcategset_setnum";
$tabindexref["connectors_out_setcateg_sets"]["PRIMARY"][]="connectors_out_setcategset_categnum";


//  ###################### connectors_out_setcategs
$tabindexref["connectors_out_setcategs"]["PRIMARY"][]="connectors_out_setcateg_id";
$tabindexref["connectors_out_setcategs"]["connectors_out_setcateg_name"][]="connectors_out_setcateg_name";


//  ###################### connectors_out_sets
$tabindexref["connectors_out_sets"]["PRIMARY"][]="connector_out_set_id";
$tabindexref["connectors_out_sets"]["connector_out_set_caption"][]="connector_out_set_caption";


//  ###################### connectors_out_sources
$tabindexref["connectors_out_sources"]["PRIMARY"][]="connectors_out_source_id";


//  ###################### connectors_out_sources_esgroups
$tabindexref["connectors_out_sources_esgroups"]["PRIMARY"][]="connectors_out_source_esgroup_sourcenum";
$tabindexref["connectors_out_sources_esgroups"]["PRIMARY"][]="connectors_out_source_esgroup_esgroupnum";


//  ###################### connectors_sources
$tabindexref["connectors_sources"]["PRIMARY"][]="source_id";


//  ###################### contact_form_objects
$tabindexref["contact_form_objects"]["PRIMARY"][]="id_object";
$tabindexref["contact_form_objects"]["i_num_contact_form"][]="num_contact_form";


//  ###################### contact_forms
$tabindexref["contact_forms"]["PRIMARY"][]="id_contact_form";


//  ###################### contribution_area_areas
$tabindexref["contribution_area_areas"]["PRIMARY"][]="id_area";


//  ###################### contribution_area_clipboard
$tabindexref["contribution_area_clipboard"]["PRIMARY"][]="id_clipboard";


//  ###################### contribution_area_computed_fields
$tabindexref["contribution_area_computed_fields"]["PRIMARY"][]="id_computed_fields";


//  ###################### contribution_area_computed_fields_used
$tabindexref["contribution_area_computed_fields_used"]["PRIMARY"][]="id_computed_fields_used";


//  ###################### contribution_area_equations
$tabindexref["contribution_area_equations"]["PRIMARY"][]="contribution_area_equation_id";


//  ###################### contribution_area_forms
$tabindexref["contribution_area_forms"]["PRIMARY"][]="id_form";


//  ###################### contribution_area_status
$tabindexref["contribution_area_status"]["PRIMARY"][]="contribution_area_status_id";


//  ###################### coordonnees
$tabindexref["coordonnees"]["PRIMARY"][]="id_contact";
$tabindexref["coordonnees"]["i_num_entite"][]="num_entite";


//  ###################### demandes
$tabindexref["demandes"]["PRIMARY"][]="id_demande";
$tabindexref["demandes"]["i_num_demandeur"][]="num_demandeur";
$tabindexref["demandes"]["i_date_demande"][]="date_demande";
$tabindexref["demandes"]["i_deadline_demande"][]="deadline_demande";


//  ###################### demandes_actions
$tabindexref["demandes_actions"]["PRIMARY"][]="id_action";
$tabindexref["demandes_actions"]["i_date_action"][]="date_action";
$tabindexref["demandes_actions"]["i_deadline_action"][]="deadline_action";
$tabindexref["demandes_actions"]["i_num_demande"][]="num_demande";
$tabindexref["demandes_actions"]["i_actions_user"][]="actions_num_user";
$tabindexref["demandes_actions"]["i_actions_user"][]="actions_type_user";


//  ###################### demandes_custom
$tabindexref["demandes_custom"]["PRIMARY"][]="idchamp";


//  ###################### demandes_custom_dates
$tabindexref["demandes_custom_dates"]["PRIMARY"][]="demandes_custom_champ";
$tabindexref["demandes_custom_dates"]["PRIMARY"][]="demandes_custom_origine";
$tabindexref["demandes_custom_dates"]["PRIMARY"][]="demandes_custom_order";
$tabindexref["demandes_custom_dates"]["demandes_custom_champ"][]="demandes_custom_champ";
$tabindexref["demandes_custom_dates"]["demandes_custom_origine"][]="demandes_custom_origine";


//  ###################### demandes_custom_lists
$tabindexref["demandes_custom_lists"]["i_demandes_custom_champ"][]="demandes_custom_champ";
$tabindexref["demandes_custom_lists"]["i_demandes_champ_list_value"][]="demandes_custom_champ";
$tabindexref["demandes_custom_lists"]["i_demandes_champ_list_value"][]="demandes_custom_list_value";


//  ###################### demandes_custom_values
$tabindexref["demandes_custom_values"]["i_demandes_custom_champ"][]="demandes_custom_champ";
$tabindexref["demandes_custom_values"]["i_demandes_custom_origine"][]="demandes_custom_origine";


//  ###################### demandes_notes
$tabindexref["demandes_notes"]["PRIMARY"][]="id_note";
$tabindexref["demandes_notes"]["i_date_note"][]="date_note";
$tabindexref["demandes_notes"]["i_num_action"][]="num_action";
$tabindexref["demandes_notes"]["i_num_note_parent"][]="num_note_parent";
$tabindexref["demandes_notes"]["i_notes_user"][]="notes_num_user";
$tabindexref["demandes_notes"]["i_notes_user"][]="notes_type_user";


//  ###################### demandes_theme
$tabindexref["demandes_theme"]["PRIMARY"][]="id_theme";


//  ###################### demandes_type
$tabindexref["demandes_type"]["PRIMARY"][]="id_type";


//  ###################### demandes_users
$tabindexref["demandes_users"]["PRIMARY"][]="num_user";
$tabindexref["demandes_users"]["PRIMARY"][]="num_demande";


//  ###################### docs_codestat
$tabindexref["docs_codestat"]["PRIMARY"][]="idcode";
$tabindexref["docs_codestat"]["statisdoc_owner"][]="statisdoc_owner";


//  ###################### docs_location
$tabindexref["docs_location"]["PRIMARY"][]="idlocation";
$tabindexref["docs_location"]["locdoc_owner"][]="locdoc_owner";


//  ###################### docs_section
$tabindexref["docs_section"]["PRIMARY"][]="idsection";
$tabindexref["docs_section"]["sdoc_owner"][]="sdoc_owner";


//  ###################### docs_statut
$tabindexref["docs_statut"]["PRIMARY"][]="idstatut";
$tabindexref["docs_statut"]["statusdoc_owner"][]="statusdoc_owner";


//  ###################### docs_type
$tabindexref["docs_type"]["PRIMARY"][]="idtyp_doc";


//  ###################### docsloc_section
$tabindexref["docsloc_section"]["PRIMARY"][]="num_section";
$tabindexref["docsloc_section"]["PRIMARY"][]="num_location";


//  ###################### docwatch_categories
$tabindexref["docwatch_categories"]["PRIMARY"][]="id_category";


//  ###################### docwatch_datasource_monitoring_website
$tabindexref["docwatch_datasource_monitoring_website"]["PRIMARY"][]="datasource_monitoring_website_num_datasource";


//  ###################### docwatch_datasources
$tabindexref["docwatch_datasources"]["PRIMARY"][]="id_datasource";
$tabindexref["docwatch_datasources"]["i_docwatch_datasource_title"][]="datasource_title";


//  ###################### docwatch_items
$tabindexref["docwatch_items"]["PRIMARY"][]="id_item";
$tabindexref["docwatch_items"]["i_docwatch_item_type"][]="item_type";
$tabindexref["docwatch_items"]["i_docwatch_item_title"][]="item_title";
$tabindexref["docwatch_items"]["i_docwatch_item_num_article"][]="item_num_article";
$tabindexref["docwatch_items"]["i_docwatch_item_num_section"][]="item_num_section";
$tabindexref["docwatch_items"]["i_docwatch_item_num_notice"][]="item_num_notice";
$tabindexref["docwatch_items"]["i_docwatch_item_num_watch"][]="item_num_watch";


//  ###################### docwatch_items_descriptors
$tabindexref["docwatch_items_descriptors"]["PRIMARY"][]="num_item";
$tabindexref["docwatch_items_descriptors"]["PRIMARY"][]="num_noeud";


//  ###################### docwatch_items_tags
$tabindexref["docwatch_items_tags"]["PRIMARY"][]="num_item";
$tabindexref["docwatch_items_tags"]["PRIMARY"][]="num_tag";


//  ###################### docwatch_selectors
$tabindexref["docwatch_selectors"]["PRIMARY"][]="id_selector";


//  ###################### docwatch_tags
$tabindexref["docwatch_tags"]["PRIMARY"][]="id_tag";


//  ###################### docwatch_watches
$tabindexref["docwatch_watches"]["PRIMARY"][]="id_watch";
$tabindexref["docwatch_watches"]["i_docwatch_watch_title"][]="watch_title";


//  ###################### dsi_archive
$tabindexref["dsi_archive"]["PRIMARY"][]="num_banette_arc";
$tabindexref["dsi_archive"]["PRIMARY"][]="num_notice_arc";
$tabindexref["dsi_archive"]["PRIMARY"][]="date_diff_arc";


//  ###################### editions_states
$tabindexref["editions_states"]["PRIMARY"][]="id_editions_state";


//  ###################### empr
$tabindexref["empr"]["PRIMARY"][]="id_empr";
$tabindexref["empr"]["empr_cb"][]="empr_cb";
$tabindexref["empr"]["empr_nom"][]="empr_nom";
$tabindexref["empr"]["empr_date_adhesion"][]="empr_date_adhesion";
$tabindexref["empr"]["empr_date_expiration"][]="empr_date_expiration";
$tabindexref["empr"]["i_empr_categ"][]="empr_categ";
$tabindexref["empr"]["i_empr_codestat"][]="empr_codestat";
$tabindexref["empr"]["i_empr_location"][]="empr_location";
$tabindexref["empr"]["i_empr_statut"][]="empr_statut";
$tabindexref["empr"]["i_empr_typabt"][]="type_abt";
$tabindexref["empr"]["i_empr_login"][]="empr_login";


//  ###################### empr_caddie
$tabindexref["empr_caddie"]["PRIMARY"][]="idemprcaddie";


//  ###################### empr_caddie_content
$tabindexref["empr_caddie_content"]["PRIMARY"][]="empr_caddie_id";
$tabindexref["empr_caddie_content"]["PRIMARY"][]="object_id";
$tabindexref["empr_caddie_content"]["object_id"][]="object_id";


//  ###################### empr_caddie_procs
$tabindexref["empr_caddie_procs"]["PRIMARY"][]="idproc";


//  ###################### empr_categ
$tabindexref["empr_categ"]["PRIMARY"][]="id_categ_empr";


//  ###################### empr_codestat
$tabindexref["empr_codestat"]["PRIMARY"][]="idcode";


//  ###################### empr_custom
$tabindexref["empr_custom"]["PRIMARY"][]="idchamp";


//  ###################### empr_custom_dates
$tabindexref["empr_custom_dates"]["PRIMARY"][]="empr_custom_champ";
$tabindexref["empr_custom_dates"]["PRIMARY"][]="empr_custom_origine";
$tabindexref["empr_custom_dates"]["PRIMARY"][]="empr_custom_order";
$tabindexref["empr_custom_dates"]["empr_custom_champ"][]="empr_custom_champ";
$tabindexref["empr_custom_dates"]["empr_custom_origine"][]="empr_custom_origine";


//  ###################### empr_custom_lists
$tabindexref["empr_custom_lists"]["empr_custom_champ"][]="empr_custom_champ";
$tabindexref["empr_custom_lists"]["i_ecl_lv"][]="empr_custom_list_value";


//  ###################### empr_custom_values
$tabindexref["empr_custom_values"]["empr_custom_champ"][]="empr_custom_champ";
$tabindexref["empr_custom_values"]["empr_custom_origine"][]="empr_custom_origine";
$tabindexref["empr_custom_values"]["i_ecv_st"][]="empr_custom_small_text";
$tabindexref["empr_custom_values"]["i_ecv_t"][]="empr_custom_text";
$tabindexref["empr_custom_values"]["i_ecv_i"][]="empr_custom_integer";
$tabindexref["empr_custom_values"]["i_ecv_d"][]="empr_custom_date";
$tabindexref["empr_custom_values"]["i_ecv_f"][]="empr_custom_float";


//  ###################### empr_devices
$tabindexref["empr_devices"]["PRIMARY"][]="empr_num";
$tabindexref["empr_devices"]["PRIMARY"][]="device_id";


//  ###################### empr_grilles
$tabindexref["empr_grilles"]["PRIMARY"][]="empr_grille_categ";
$tabindexref["empr_grilles"]["PRIMARY"][]="empr_grille_location";


//  ###################### empr_groupe
$tabindexref["empr_groupe"]["PRIMARY"][]="empr_id";
$tabindexref["empr_groupe"]["PRIMARY"][]="groupe_id";


//  ###################### empr_renewal_form_fields
$tabindexref["empr_renewal_form_fields"]["PRIMARY"][]="empr_renewal_form_field_code";


//  ###################### empr_statut
$tabindexref["empr_statut"]["PRIMARY"][]="idstatut";


//  ###################### empr_temp
$tabindexref["empr_temp"]["cb"][]="cb";


//  ###################### empty_words_calculs
$tabindexref["empty_words_calculs"]["PRIMARY"][]="id_calcul";


//  ###################### entites
$tabindexref["entites"]["PRIMARY"][]="id_entite";
$tabindexref["entites"]["raison_sociale"][]="raison_sociale";


//  ###################### entrepots_localisations
$tabindexref["entrepots_localisations"]["PRIMARY"][]="loc_id";
$tabindexref["entrepots_localisations"]["loc_code"][]="loc_code";


//  ###################### equations
$tabindexref["equations"]["PRIMARY"][]="id_equation";


//  ###################### error_log


//  ###################### es_cache
$tabindexref["es_cache"]["PRIMARY"][]="escache_groupname";
$tabindexref["es_cache"]["PRIMARY"][]="escache_unique_id";
$tabindexref["es_cache"]["PRIMARY"][]="escache_value";


//  ###################### es_cache_blob
$tabindexref["es_cache_blob"]["PRIMARY"][]="es_cache_objectref";
$tabindexref["es_cache_blob"]["PRIMARY"][]="es_cache_objecttype";
$tabindexref["es_cache_blob"]["PRIMARY"][]="es_cache_objectformat";
$tabindexref["es_cache_blob"]["PRIMARY"][]="es_cache_owner";
$tabindexref["es_cache_blob"]["cache_index"][]="es_cache_owner";
$tabindexref["es_cache_blob"]["cache_index"][]="es_cache_objectformat";
$tabindexref["es_cache_blob"]["cache_index"][]="es_cache_objecttype";
$tabindexref["es_cache_blob"]["i_es_cache_expirationdate"][]="es_cache_expirationdate";


//  ###################### es_cache_int
$tabindexref["es_cache_int"]["PRIMARY"][]="es_cache_objectref";
$tabindexref["es_cache_int"]["PRIMARY"][]="es_cache_objecttype";
$tabindexref["es_cache_int"]["PRIMARY"][]="es_cache_objectformat";
$tabindexref["es_cache_int"]["PRIMARY"][]="es_cache_owner";
$tabindexref["es_cache_int"]["cache_index"][]="es_cache_owner";
$tabindexref["es_cache_int"]["cache_index"][]="es_cache_objectformat";
$tabindexref["es_cache_int"]["cache_index"][]="es_cache_objecttype";


//  ###################### es_converted_cache
$tabindexref["es_converted_cache"]["PRIMARY"][]="es_converted_cache_objecttype";
$tabindexref["es_converted_cache"]["PRIMARY"][]="es_converted_cache_objectref";
$tabindexref["es_converted_cache"]["PRIMARY"][]="es_converted_cache_format";


//  ###################### es_esgroup_esusers
$tabindexref["es_esgroup_esusers"]["PRIMARY"][]="esgroupuser_usernum";
$tabindexref["es_esgroup_esusers"]["PRIMARY"][]="esgroupuser_groupnum";
$tabindexref["es_esgroup_esusers"]["PRIMARY"][]="esgroupuser_usertype";


//  ###################### es_esgroups
$tabindexref["es_esgroups"]["PRIMARY"][]="esgroup_id";
$tabindexref["es_esgroups"]["esgroup_name"][]="esgroup_name";


//  ###################### es_esusers
$tabindexref["es_esusers"]["PRIMARY"][]="esuser_id";
$tabindexref["es_esusers"]["esuser_username"][]="esuser_username";


//  ###################### es_methods
$tabindexref["es_methods"]["PRIMARY"][]="id_method";
$tabindexref["es_methods"]["i_groupe_method_available"][]="groupe";
$tabindexref["es_methods"]["i_groupe_method_available"][]="method";
$tabindexref["es_methods"]["i_groupe_method_available"][]="available";


//  ###################### es_methods_users
$tabindexref["es_methods_users"]["PRIMARY"][]="num_method";
$tabindexref["es_methods_users"]["PRIMARY"][]="num_user";


//  ###################### es_searchcache
$tabindexref["es_searchcache"]["PRIMARY"][]="es_searchcache_searchid";
$tabindexref["es_searchcache"]["i_es_searchcache_date"][]="es_searchcache_date";


//  ###################### es_searchsessions
$tabindexref["es_searchsessions"]["PRIMARY"][]="es_searchsession_id";


//  ###################### etagere
$tabindexref["etagere"]["PRIMARY"][]="idetagere";
$tabindexref["etagere"]["i_id_tri"][]="id_tri";


//  ###################### etagere_caddie
$tabindexref["etagere_caddie"]["PRIMARY"][]="etagere_id";
$tabindexref["etagere_caddie"]["PRIMARY"][]="caddie_id";


//  ###################### exemplaires
$tabindexref["exemplaires"]["PRIMARY"][]="expl_id";
$tabindexref["exemplaires"]["expl_cb"][]="expl_cb";
$tabindexref["exemplaires"]["expl_typdoc"][]="expl_typdoc";
$tabindexref["exemplaires"]["expl_cote"][]="expl_cote";
$tabindexref["exemplaires"]["expl_notice"][]="expl_notice";
$tabindexref["exemplaires"]["expl_codestat"][]="expl_codestat";
$tabindexref["exemplaires"]["expl_owner"][]="expl_owner";
$tabindexref["exemplaires"]["expl_bulletin"][]="expl_bulletin";
$tabindexref["exemplaires"]["i_expl_location"][]="expl_location";
$tabindexref["exemplaires"]["i_expl_section"][]="expl_section";
$tabindexref["exemplaires"]["i_expl_statut"][]="expl_statut";
$tabindexref["exemplaires"]["i_expl_lastempr"][]="expl_lastempr";


//  ###################### exemplaires_temp
$tabindexref["exemplaires_temp"]["cb"][]="cb";


//  ###################### exercices
$tabindexref["exercices"]["PRIMARY"][]="id_exercice";


//  ###################### expl_custom
$tabindexref["expl_custom"]["PRIMARY"][]="idchamp";


//  ###################### expl_custom_dates
$tabindexref["expl_custom_dates"]["PRIMARY"][]="expl_custom_champ";
$tabindexref["expl_custom_dates"]["PRIMARY"][]="expl_custom_origine";
$tabindexref["expl_custom_dates"]["PRIMARY"][]="expl_custom_order";
$tabindexref["expl_custom_dates"]["expl_custom_champ"][]="expl_custom_champ";
$tabindexref["expl_custom_dates"]["expl_custom_origine"][]="expl_custom_origine";


//  ###################### expl_custom_lists
$tabindexref["expl_custom_lists"]["expl_custom_champ"][]="expl_custom_champ";
$tabindexref["expl_custom_lists"]["i_excl_lv"][]="expl_custom_list_value";


//  ###################### expl_custom_values
$tabindexref["expl_custom_values"]["expl_custom_champ"][]="expl_custom_champ";
$tabindexref["expl_custom_values"]["expl_custom_origine"][]="expl_custom_origine";
$tabindexref["expl_custom_values"]["i_excv_st"][]="expl_custom_small_text";
$tabindexref["expl_custom_values"]["i_excv_t"][]="expl_custom_text";
$tabindexref["expl_custom_values"]["i_excv_i"][]="expl_custom_integer";
$tabindexref["expl_custom_values"]["i_excv_d"][]="expl_custom_date";
$tabindexref["expl_custom_values"]["i_excv_f"][]="expl_custom_float";


//  ###################### explnum
$tabindexref["explnum"]["PRIMARY"][]="explnum_id";
$tabindexref["explnum"]["explnum_notice"][]="explnum_notice";
$tabindexref["explnum"]["explnum_bulletin"][]="explnum_bulletin";
$tabindexref["explnum"]["explnum_repertoire"][]="explnum_repertoire";
$tabindexref["explnum"]["i_explnum_nomfichier"][]="explnum_nomfichier";
$tabindexref["explnum"]["i_e_explnum_signature"][]="explnum_signature";
$tabindexref["explnum"]["i_f_explnumwew"][]="explnum_index_wew";
$tabindexref["explnum"]["i_explnum_mimetype"][]="explnum_mimetype";


//  ###################### explnum_custom
$tabindexref["explnum_custom"]["PRIMARY"][]="idchamp";


//  ###################### explnum_custom_dates
$tabindexref["explnum_custom_dates"]["PRIMARY"][]="explnum_custom_champ";
$tabindexref["explnum_custom_dates"]["PRIMARY"][]="explnum_custom_origine";
$tabindexref["explnum_custom_dates"]["PRIMARY"][]="explnum_custom_order";
$tabindexref["explnum_custom_dates"]["explnum_custom_champ"][]="explnum_custom_champ";
$tabindexref["explnum_custom_dates"]["explnum_custom_origine"][]="explnum_custom_origine";


//  ###################### explnum_custom_lists
$tabindexref["explnum_custom_lists"]["explnum_custom_champ"][]="explnum_custom_champ";
$tabindexref["explnum_custom_lists"]["explnum_champ_list_value"][]="explnum_custom_champ";
$tabindexref["explnum_custom_lists"]["explnum_champ_list_value"][]="explnum_custom_list_value";


//  ###################### explnum_custom_values
$tabindexref["explnum_custom_values"]["explnum_custom_champ"][]="explnum_custom_champ";
$tabindexref["explnum_custom_values"]["i_encv_st"][]="explnum_custom_small_text";
$tabindexref["explnum_custom_values"]["i_encv_t"][]="explnum_custom_text";
$tabindexref["explnum_custom_values"]["i_encv_i"][]="explnum_custom_integer";
$tabindexref["explnum_custom_values"]["i_encv_d"][]="explnum_custom_date";
$tabindexref["explnum_custom_values"]["i_encv_f"][]="explnum_custom_float";
$tabindexref["explnum_custom_values"]["explnum_custom_origine"][]="explnum_custom_origine";


//  ###################### explnum_doc
$tabindexref["explnum_doc"]["PRIMARY"][]="id_explnum_doc";


//  ###################### explnum_doc_actions
$tabindexref["explnum_doc_actions"]["PRIMARY"][]="num_explnum_doc";
$tabindexref["explnum_doc_actions"]["PRIMARY"][]="num_action";


//  ###################### explnum_doc_sugg
$tabindexref["explnum_doc_sugg"]["PRIMARY"][]="num_explnum_doc";
$tabindexref["explnum_doc_sugg"]["PRIMARY"][]="num_suggestion";


//  ###################### explnum_lenders
$tabindexref["explnum_lenders"]["PRIMARY"][]="explnum_lender_num_explnum";
$tabindexref["explnum_lenders"]["PRIMARY"][]="explnum_lender_num_lender";


//  ###################### explnum_licence
$tabindexref["explnum_licence"]["PRIMARY"][]="id_explnum_licence";


//  ###################### explnum_licence_profile_explnums
$tabindexref["explnum_licence_profile_explnums"]["PRIMARY"][]="explnum_licence_profile_explnums_explnum_num";
$tabindexref["explnum_licence_profile_explnums"]["PRIMARY"][]="explnum_licence_profile_explnums_profile_num";
$tabindexref["explnum_licence_profile_explnums"]["i_elpe_explnum_profile_num"][]="explnum_licence_profile_explnums_profile_num";


//  ###################### explnum_licence_profile_rights
$tabindexref["explnum_licence_profile_rights"]["PRIMARY"][]="explnum_licence_profile_num";
$tabindexref["explnum_licence_profile_rights"]["PRIMARY"][]="explnum_licence_right_num";


//  ###################### explnum_licence_profiles
$tabindexref["explnum_licence_profiles"]["PRIMARY"][]="id_explnum_licence_profile";
$tabindexref["explnum_licence_profiles"]["i_elp_explnum_licence_num"][]="explnum_licence_profile_explnum_licence_num";


//  ###################### explnum_licence_rights
$tabindexref["explnum_licence_rights"]["PRIMARY"][]="id_explnum_licence_right";
$tabindexref["explnum_licence_rights"]["i_elr_explnum_licence_num"][]="explnum_licence_right_explnum_licence_num";


//  ###################### explnum_location
$tabindexref["explnum_location"]["PRIMARY"][]="num_explnum";
$tabindexref["explnum_location"]["PRIMARY"][]="num_location";


//  ###################### explnum_segments
$tabindexref["explnum_segments"]["PRIMARY"][]="explnum_segment_id";
$tabindexref["explnum_segments"]["i_ensg_explnum_num"][]="explnum_segment_explnum_num";
$tabindexref["explnum_segments"]["i_ensg_speaker"][]="explnum_segment_speaker_num";


//  ###################### explnum_speakers
$tabindexref["explnum_speakers"]["PRIMARY"][]="explnum_speaker_id";
$tabindexref["explnum_speakers"]["i_ensk_explnum_num"][]="explnum_speaker_explnum_num";
$tabindexref["explnum_speakers"]["i_ensk_author"][]="explnum_speaker_author";


//  ###################### explnum_statut
$tabindexref["explnum_statut"]["PRIMARY"][]="id_explnum_statut";


//  ###################### external_count
$tabindexref["external_count"]["PRIMARY"][]="rid";
$tabindexref["external_count"]["recid"][]="recid";


//  ###################### facettes
$tabindexref["facettes"]["PRIMARY"][]="id_facette";
$tabindexref["facettes"]["i_facette_visible"][]="facette_visible";


//  ###################### facettes_external
$tabindexref["facettes_external"]["PRIMARY"][]="id_facette";


//  ###################### faq_questions
$tabindexref["faq_questions"]["PRIMARY"][]="id_faq_question";


//  ###################### faq_questions_categories
$tabindexref["faq_questions_categories"]["i_faq_categ"][]="num_faq_question";
$tabindexref["faq_questions_categories"]["i_faq_categ"][]="num_categ";


//  ###################### faq_questions_fields_global_index
$tabindexref["faq_questions_fields_global_index"]["PRIMARY"][]="id_faq_question";
$tabindexref["faq_questions_fields_global_index"]["PRIMARY"][]="code_champ";
$tabindexref["faq_questions_fields_global_index"]["PRIMARY"][]="code_ss_champ";
$tabindexref["faq_questions_fields_global_index"]["PRIMARY"][]="lang";
$tabindexref["faq_questions_fields_global_index"]["PRIMARY"][]="ordre";
$tabindexref["faq_questions_fields_global_index"]["i_value"][]="value";
$tabindexref["faq_questions_fields_global_index"]["i_code_champ_code_ss_champ"][]="code_champ";
$tabindexref["faq_questions_fields_global_index"]["i_code_champ_code_ss_champ"][]="code_ss_champ";


//  ###################### faq_questions_words_global_index
$tabindexref["faq_questions_words_global_index"]["PRIMARY"][]="id_faq_question";
$tabindexref["faq_questions_words_global_index"]["PRIMARY"][]="code_champ";
$tabindexref["faq_questions_words_global_index"]["PRIMARY"][]="code_ss_champ";
$tabindexref["faq_questions_words_global_index"]["PRIMARY"][]="num_word";
$tabindexref["faq_questions_words_global_index"]["PRIMARY"][]="position";
$tabindexref["faq_questions_words_global_index"]["PRIMARY"][]="field_position";
$tabindexref["faq_questions_words_global_index"]["code_champ"][]="code_champ";
$tabindexref["faq_questions_words_global_index"]["i_id_mot"][]="num_word";
$tabindexref["faq_questions_words_global_index"]["i_id_mot"][]="id_faq_question";
$tabindexref["faq_questions_words_global_index"]["i_code_champ_code_ss_champ_num_word"][]="code_champ";
$tabindexref["faq_questions_words_global_index"]["i_code_champ_code_ss_champ_num_word"][]="code_ss_champ";
$tabindexref["faq_questions_words_global_index"]["i_code_champ_code_ss_champ_num_word"][]="num_word";
$tabindexref["faq_questions_words_global_index"]["i_num_word"][]="num_word";


//  ###################### faq_themes
$tabindexref["faq_themes"]["PRIMARY"][]="id_theme";


//  ###################### faq_types
$tabindexref["faq_types"]["PRIMARY"][]="id_type";


//  ###################### fiche
$tabindexref["fiche"]["PRIMARY"][]="id_fiche";


//  ###################### frais
$tabindexref["frais"]["PRIMARY"][]="id_frais";


//  ###################### frbr_cadres
$tabindexref["frbr_cadres"]["PRIMARY"][]="id_cadre";


//  ###################### frbr_cadres_content
$tabindexref["frbr_cadres_content"]["PRIMARY"][]="id_cadre_content";


//  ###################### frbr_cataloging_categories
$tabindexref["frbr_cataloging_categories"]["PRIMARY"][]="id_cataloging_category";


//  ###################### frbr_cataloging_datanodes
$tabindexref["frbr_cataloging_datanodes"]["PRIMARY"][]="id_cataloging_datanode";
$tabindexref["frbr_cataloging_datanodes"]["i_cataloging_datanode_title"][]="cataloging_datanode_title";


//  ###################### frbr_cataloging_items
$tabindexref["frbr_cataloging_items"]["PRIMARY"][]="num_cataloging_item";
$tabindexref["frbr_cataloging_items"]["PRIMARY"][]="type_cataloging_item";
$tabindexref["frbr_cataloging_items"]["PRIMARY"][]="cataloging_item_num_datanode";
$tabindexref["frbr_cataloging_items"]["i_cataloging_item_num_datanode"][]="cataloging_item_num_datanode";


//  ###################### frbr_datanodes
$tabindexref["frbr_datanodes"]["PRIMARY"][]="id_datanode";


//  ###################### frbr_datanodes_content
$tabindexref["frbr_datanodes_content"]["PRIMARY"][]="id_datanode_content";


//  ###################### frbr_managed_entities
$tabindexref["frbr_managed_entities"]["PRIMARY"][]="managed_entity_name";


//  ###################### frbr_pages
$tabindexref["frbr_pages"]["PRIMARY"][]="id_page";


//  ###################### frbr_pages_content
$tabindexref["frbr_pages_content"]["PRIMARY"][]="id_page_content";


//  ###################### frbr_place
$tabindexref["frbr_place"]["PRIMARY"][]="place_num_page";
$tabindexref["frbr_place"]["PRIMARY"][]="place_num_cadre";
$tabindexref["frbr_place"]["PRIMARY"][]="place_cadre_type";


//  ###################### gestfic0_custom
$tabindexref["gestfic0_custom"]["PRIMARY"][]="idchamp";


//  ###################### gestfic0_custom_lists
$tabindexref["gestfic0_custom_lists"]["gestfic0_custom_champ"][]="gestfic0_custom_champ";
$tabindexref["gestfic0_custom_lists"]["gestfic0_champ_list_value"][]="gestfic0_custom_champ";
$tabindexref["gestfic0_custom_lists"]["gestfic0_champ_list_value"][]="gestfic0_custom_list_value";


//  ###################### gestfic0_custom_values
$tabindexref["gestfic0_custom_values"]["gestfic0_custom_champ"][]="gestfic0_custom_champ";
$tabindexref["gestfic0_custom_values"]["gestfic0_custom_origine"][]="gestfic0_custom_origine";
$tabindexref["gestfic0_custom_values"]["i_gcv_st"][]="gestfic0_custom_small_text";
$tabindexref["gestfic0_custom_values"]["i_gcv_t"][]="gestfic0_custom_text";
$tabindexref["gestfic0_custom_values"]["i_gcv_i"][]="gestfic0_custom_integer";
$tabindexref["gestfic0_custom_values"]["i_gcv_d"][]="gestfic0_custom_date";
$tabindexref["gestfic0_custom_values"]["i_gcv_f"][]="gestfic0_custom_float";


//  ###################### grids_generic
$tabindexref["grids_generic"]["PRIMARY"][]="grid_generic_type";
$tabindexref["grids_generic"]["PRIMARY"][]="grid_generic_filter";


//  ###################### grilles
$tabindexref["grilles"]["PRIMARY"][]="grille_typdoc";
$tabindexref["grilles"]["PRIMARY"][]="grille_niveau_biblio";
$tabindexref["grilles"]["PRIMARY"][]="grille_localisation";


//  ###################### groupe
$tabindexref["groupe"]["PRIMARY"][]="id_groupe";
$tabindexref["groupe"]["libelle_groupe"][]="libelle_groupe";
$tabindexref["groupe"]["i_resp_groupe"][]="resp_groupe";


//  ###################### groupexpl
$tabindexref["groupexpl"]["PRIMARY"][]="id_groupexpl";


//  ###################### groupexpl_expl
$tabindexref["groupexpl_expl"]["PRIMARY"][]="groupexpl_num";
$tabindexref["groupexpl_expl"]["PRIMARY"][]="groupexpl_expl_num";


//  ###################### harvest_field
$tabindexref["harvest_field"]["PRIMARY"][]="id_harvest_field";


//  ###################### harvest_profil
$tabindexref["harvest_profil"]["PRIMARY"][]="id_harvest_profil";


//  ###################### harvest_profil_import
$tabindexref["harvest_profil_import"]["PRIMARY"][]="id_harvest_profil_import";


//  ###################### harvest_profil_import_field
$tabindexref["harvest_profil_import_field"]["PRIMARY"][]="num_harvest_profil_import";
$tabindexref["harvest_profil_import_field"]["PRIMARY"][]="harvest_profil_import_field_xml_id";


//  ###################### harvest_search_field
$tabindexref["harvest_search_field"]["PRIMARY"][]="num_harvest_profil";
$tabindexref["harvest_search_field"]["PRIMARY"][]="num_source";


//  ###################### harvest_src
$tabindexref["harvest_src"]["PRIMARY"][]="id_harvest_src";


//  ###################### import_marc
$tabindexref["import_marc"]["PRIMARY"][]="id_import";
$tabindexref["import_marc"]["i_nonot_orig"][]="no_notice";
$tabindexref["import_marc"]["i_nonot_orig"][]="origine";
$tabindexref["import_marc"]["i_origine"][]="origine";


//  ###################### index_concept
$tabindexref["index_concept"]["PRIMARY"][]="num_object";
$tabindexref["index_concept"]["PRIMARY"][]="type_object";
$tabindexref["index_concept"]["PRIMARY"][]="num_concept";
$tabindexref["index_concept"]["i_num_concept_type_object"][]="num_concept";
$tabindexref["index_concept"]["i_num_concept_type_object"][]="type_object";
$tabindexref["index_concept"]["i_type_object_num_object"][]="type_object";
$tabindexref["index_concept"]["i_type_object_num_object"][]="num_object";


//  ###################### indexation_stack
$tabindexref["indexation_stack"]["PRIMARY"][]="indexation_stack_entity_id";
$tabindexref["indexation_stack"]["PRIMARY"][]="indexation_stack_entity_type";
$tabindexref["indexation_stack"]["PRIMARY"][]="indexation_stack_datatype";


//  ###################### indexint
$tabindexref["indexint"]["PRIMARY"][]="indexint_id";
$tabindexref["indexint"]["indexint_name"][]="indexint_name";
$tabindexref["indexint"]["indexint_name"][]="num_pclass";


//  ###################### indexint_custom
$tabindexref["indexint_custom"]["PRIMARY"][]="idchamp";


//  ###################### indexint_custom_dates
$tabindexref["indexint_custom_dates"]["PRIMARY"][]="indexint_custom_champ";
$tabindexref["indexint_custom_dates"]["PRIMARY"][]="indexint_custom_origine";
$tabindexref["indexint_custom_dates"]["PRIMARY"][]="indexint_custom_order";
$tabindexref["indexint_custom_dates"]["indexint_custom_champ"][]="indexint_custom_champ";
$tabindexref["indexint_custom_dates"]["indexint_custom_origine"][]="indexint_custom_origine";


//  ###################### indexint_custom_lists
$tabindexref["indexint_custom_lists"]["editorial_custom_champ"][]="indexint_custom_champ";
$tabindexref["indexint_custom_lists"]["editorial_champ_list_value"][]="indexint_custom_champ";
$tabindexref["indexint_custom_lists"]["editorial_champ_list_value"][]="indexint_custom_list_value";


//  ###################### indexint_custom_values
$tabindexref["indexint_custom_values"]["editorial_custom_champ"][]="indexint_custom_champ";
$tabindexref["indexint_custom_values"]["editorial_custom_origine"][]="indexint_custom_origine";
$tabindexref["indexint_custom_values"]["i_icv_st"][]="indexint_custom_small_text";
$tabindexref["indexint_custom_values"]["i_icv_t"][]="indexint_custom_text";
$tabindexref["indexint_custom_values"]["i_icv_i"][]="indexint_custom_integer";
$tabindexref["indexint_custom_values"]["i_icv_d"][]="indexint_custom_date";
$tabindexref["indexint_custom_values"]["i_icv_f"][]="indexint_custom_float";


//  ###################### infopages
$tabindexref["infopages"]["PRIMARY"][]="id_infopage";


//  ###################### lenders
$tabindexref["lenders"]["PRIMARY"][]="idlender";


//  ###################### liens_actes
$tabindexref["liens_actes"]["PRIMARY"][]="num_acte";
$tabindexref["liens_actes"]["PRIMARY"][]="num_acte_lie";
$tabindexref["liens_actes"]["i_num_acte"][]="num_acte";
$tabindexref["liens_actes"]["i_num_acte_lie"][]="num_acte_lie";


//  ###################### lignes_actes
$tabindexref["lignes_actes"]["PRIMARY"][]="id_ligne";
$tabindexref["lignes_actes"]["num_acte"][]="num_acte";


//  ###################### lignes_actes_applicants
$tabindexref["lignes_actes_applicants"]["PRIMARY"][]="ligne_acte_num";
$tabindexref["lignes_actes_applicants"]["PRIMARY"][]="empr_num";


//  ###################### lignes_actes_relances
$tabindexref["lignes_actes_relances"]["PRIMARY"][]="num_ligne";
$tabindexref["lignes_actes_relances"]["PRIMARY"][]="date_relance";


//  ###################### lignes_actes_statuts
$tabindexref["lignes_actes_statuts"]["PRIMARY"][]="id_statut";


//  ###################### linked_mots
$tabindexref["linked_mots"]["PRIMARY"][]="num_mot";
$tabindexref["linked_mots"]["PRIMARY"][]="num_linked_mot";
$tabindexref["linked_mots"]["PRIMARY"][]="type_lien";


//  ###################### lists
$tabindexref["lists"]["PRIMARY"][]="id_list";


//  ###################### locked_entities
$tabindexref["locked_entities"]["PRIMARY"][]="id_entity";
$tabindexref["locked_entities"]["PRIMARY"][]="type";


//  ###################### log_expl_retard
$tabindexref["log_expl_retard"]["PRIMARY"][]="id_log";


//  ###################### log_retard
$tabindexref["log_retard"]["PRIMARY"][]="id_log";


//  ###################### logopac
$tabindexref["logopac"]["PRIMARY"][]="id_log";
$tabindexref["logopac"]["lopac_date_log"][]="date_log";


//  ###################### mails_waiting
$tabindexref["mails_waiting"]["PRIMARY"][]="id_mail";


//  ###################### mailtpl
$tabindexref["mailtpl"]["PRIMARY"][]="id_mailtpl";


//  ###################### map_echelles
$tabindexref["map_echelles"]["PRIMARY"][]="map_echelle_id";


//  ###################### map_emprises
$tabindexref["map_emprises"]["PRIMARY"][]="map_emprise_id";
$tabindexref["map_emprises"]["i_map_emprise_obj_num"][]="map_emprise_obj_num";


//  ###################### map_hold_areas
$tabindexref["map_hold_areas"]["PRIMARY"][]="id_obj";


//  ###################### map_projections
$tabindexref["map_projections"]["PRIMARY"][]="map_projection_id";


//  ###################### map_refs
$tabindexref["map_refs"]["PRIMARY"][]="map_ref_id";


//  ###################### mots
$tabindexref["mots"]["PRIMARY"][]="id_mot";
$tabindexref["mots"]["mot"][]="mot";


//  ###################### noeuds
$tabindexref["noeuds"]["PRIMARY"][]="id_noeud";
$tabindexref["noeuds"]["num_parent"][]="num_parent";
$tabindexref["noeuds"]["num_thesaurus"][]="num_thesaurus";
$tabindexref["noeuds"]["autorite"][]="autorite";
$tabindexref["noeuds"]["key_path"][]="path";
$tabindexref["noeuds"]["i_num_renvoi_voir"][]="num_renvoi_voir";


//  ###################### nomenclature_children_records
$tabindexref["nomenclature_children_records"]["PRIMARY"][]="child_record_num_record";


//  ###################### nomenclature_exotic_instruments
$tabindexref["nomenclature_exotic_instruments"]["PRIMARY"][]="id_exotic_instrument";


//  ###################### nomenclature_exotic_other_instruments
$tabindexref["nomenclature_exotic_other_instruments"]["PRIMARY"][]="id_exotic_other_instrument";


//  ###################### nomenclature_families
$tabindexref["nomenclature_families"]["PRIMARY"][]="id_family";


//  ###################### nomenclature_formations
$tabindexref["nomenclature_formations"]["PRIMARY"][]="id_formation";


//  ###################### nomenclature_instruments
$tabindexref["nomenclature_instruments"]["PRIMARY"][]="id_instrument";


//  ###################### nomenclature_musicstands
$tabindexref["nomenclature_musicstands"]["PRIMARY"][]="id_musicstand";


//  ###################### nomenclature_notices_nomenclatures
$tabindexref["nomenclature_notices_nomenclatures"]["PRIMARY"][]="id_notice_nomenclature";
$tabindexref["nomenclature_notices_nomenclatures"]["i_notice_nomenclature_num_notice"][]="notice_nomenclature_num_notice";


//  ###################### nomenclature_types
$tabindexref["nomenclature_types"]["PRIMARY"][]="id_type";


//  ###################### nomenclature_voices
$tabindexref["nomenclature_voices"]["PRIMARY"][]="id_voice";


//  ###################### nomenclature_workshops
$tabindexref["nomenclature_workshops"]["PRIMARY"][]="id_workshop";


//  ###################### nomenclature_workshops_instruments
$tabindexref["nomenclature_workshops_instruments"]["PRIMARY"][]="id_workshop_instrument";


//  ###################### notice_onglet
$tabindexref["notice_onglet"]["PRIMARY"][]="id_onglet";


//  ###################### notice_statut
$tabindexref["notice_statut"]["PRIMARY"][]="id_notice_statut";


//  ###################### notice_tpl
$tabindexref["notice_tpl"]["PRIMARY"][]="notpl_id";


//  ###################### notice_tplcode
$tabindexref["notice_tplcode"]["PRIMARY"][]="num_notpl";
$tabindexref["notice_tplcode"]["PRIMARY"][]="notplcode_localisation";
$tabindexref["notice_tplcode"]["PRIMARY"][]="notplcode_typdoc";
$tabindexref["notice_tplcode"]["PRIMARY"][]="notplcode_niveau_biblio";


//  ###################### notice_usage
$tabindexref["notice_usage"]["PRIMARY"][]="id_usage";
$tabindexref["notice_usage"]["usage_libelle"][]="usage_libelle";


//  ###################### notices
$tabindexref["notices"]["PRIMARY"][]="notice_id";
$tabindexref["notices"]["typdoc"][]="typdoc";
$tabindexref["notices"]["tparent_id"][]="tparent_id";
$tabindexref["notices"]["ed1_id"][]="ed1_id";
$tabindexref["notices"]["ed2_id"][]="ed2_id";
$tabindexref["notices"]["coll_id"][]="coll_id";
$tabindexref["notices"]["subcoll_id"][]="subcoll_id";
$tabindexref["notices"]["cb"][]="code";
$tabindexref["notices"]["indexint"][]="indexint";
$tabindexref["notices"]["sig_index"][]="signature";
$tabindexref["notices"]["i_notice_n_biblio"][]="niveau_biblio";
$tabindexref["notices"]["i_notice_n_hierar"][]="niveau_hierar";
$tabindexref["notices"]["notice_eformat"][]="eformat";
$tabindexref["notices"]["i_date_parution"][]="date_parution";
$tabindexref["notices"]["i_not_statut"][]="statut";
$tabindexref["notices"]["i_map_echelle_num"][]="map_echelle_num";
$tabindexref["notices"]["i_map_projection_num"][]="map_projection_num";


//  ###################### notices_authorities_sources
$tabindexref["notices_authorities_sources"]["PRIMARY"][]="num_authority_source";
$tabindexref["notices_authorities_sources"]["PRIMARY"][]="num_notice";


//  ###################### notices_authperso
$tabindexref["notices_authperso"]["PRIMARY"][]="notice_authperso_notice_num";
$tabindexref["notices_authperso"]["PRIMARY"][]="notice_authperso_authority_num";


//  ###################### notices_categories
$tabindexref["notices_categories"]["PRIMARY"][]="notcateg_notice";
$tabindexref["notices_categories"]["PRIMARY"][]="num_noeud";
$tabindexref["notices_categories"]["PRIMARY"][]="num_vedette";
$tabindexref["notices_categories"]["num_noeud"][]="num_noeud";


//  ###################### notices_custom
$tabindexref["notices_custom"]["PRIMARY"][]="idchamp";


//  ###################### notices_custom_dates
$tabindexref["notices_custom_dates"]["PRIMARY"][]="notices_custom_champ";
$tabindexref["notices_custom_dates"]["PRIMARY"][]="notices_custom_origine";
$tabindexref["notices_custom_dates"]["PRIMARY"][]="notices_custom_order";
$tabindexref["notices_custom_dates"]["notices_custom_champ"][]="notices_custom_champ";
$tabindexref["notices_custom_dates"]["notices_custom_origine"][]="notices_custom_origine";


//  ###################### notices_custom_lists
$tabindexref["notices_custom_lists"]["notices_custom_champ"][]="notices_custom_champ";
$tabindexref["notices_custom_lists"]["i_ncl_lv"][]="notices_custom_list_value";


//  ###################### notices_custom_values
$tabindexref["notices_custom_values"]["notices_custom_champ"][]="notices_custom_champ";
$tabindexref["notices_custom_values"]["notices_custom_origine"][]="notices_custom_origine";
$tabindexref["notices_custom_values"]["i_ncv_st"][]="notices_custom_small_text";
$tabindexref["notices_custom_values"]["i_ncv_t"][]="notices_custom_text";
$tabindexref["notices_custom_values"]["i_ncv_i"][]="notices_custom_integer";
$tabindexref["notices_custom_values"]["i_ncv_d"][]="notices_custom_date";
$tabindexref["notices_custom_values"]["i_ncv_f"][]="notices_custom_float";


//  ###################### notices_externes
$tabindexref["notices_externes"]["PRIMARY"][]="num_notice";
$tabindexref["notices_externes"]["i_recid"][]="recid";
$tabindexref["notices_externes"]["i_notice_recid"][]="num_notice";
$tabindexref["notices_externes"]["i_notice_recid"][]="recid";


//  ###################### notices_fields_global_index
$tabindexref["notices_fields_global_index"]["PRIMARY"][]="id_notice";
$tabindexref["notices_fields_global_index"]["PRIMARY"][]="code_champ";
$tabindexref["notices_fields_global_index"]["PRIMARY"][]="code_ss_champ";
$tabindexref["notices_fields_global_index"]["PRIMARY"][]="lang";
$tabindexref["notices_fields_global_index"]["PRIMARY"][]="ordre";
$tabindexref["notices_fields_global_index"]["i_value"][]="value";
$tabindexref["notices_fields_global_index"]["i_code_champ_code_ss_champ"][]="code_champ";
$tabindexref["notices_fields_global_index"]["i_code_champ_code_ss_champ"][]="code_ss_champ";
$tabindexref["notices_fields_global_index"]["i_id_notice"][]="id_notice";


//  ###################### notices_global_index
$tabindexref["notices_global_index"]["PRIMARY"][]="num_notice";
$tabindexref["notices_global_index"]["PRIMARY"][]="no_index";


//  ###################### notices_langues
$tabindexref["notices_langues"]["PRIMARY"][]="num_notice";
$tabindexref["notices_langues"]["PRIMARY"][]="type_langue";
$tabindexref["notices_langues"]["PRIMARY"][]="code_langue";


//  ###################### notices_mots_global_index
$tabindexref["notices_mots_global_index"]["PRIMARY"][]="id_notice";
$tabindexref["notices_mots_global_index"]["PRIMARY"][]="code_champ";
$tabindexref["notices_mots_global_index"]["PRIMARY"][]="code_ss_champ";
$tabindexref["notices_mots_global_index"]["PRIMARY"][]="num_word";
$tabindexref["notices_mots_global_index"]["PRIMARY"][]="position";
$tabindexref["notices_mots_global_index"]["PRIMARY"][]="field_position";
$tabindexref["notices_mots_global_index"]["code_champ"][]="code_champ";
$tabindexref["notices_mots_global_index"]["i_id_mot"][]="num_word";
$tabindexref["notices_mots_global_index"]["i_id_mot"][]="id_notice";
$tabindexref["notices_mots_global_index"]["i_code_champ_code_ss_champ_num_word"][]="code_champ";
$tabindexref["notices_mots_global_index"]["i_code_champ_code_ss_champ_num_word"][]="code_ss_champ";
$tabindexref["notices_mots_global_index"]["i_code_champ_code_ss_champ_num_word"][]="num_word";
$tabindexref["notices_mots_global_index"]["i_num_word"][]="num_word";
$tabindexref["notices_mots_global_index"]["i_id_notice"][]="id_notice";


//  ###################### notices_relations
$tabindexref["notices_relations"]["PRIMARY"][]="id_notices_relations";
$tabindexref["notices_relations"]["linked_notice"][]="linked_notice";
$tabindexref["notices_relations"]["relation_type"][]="relation_type";
$tabindexref["notices_relations"]["num_notice"][]="num_notice";
$tabindexref["notices_relations"]["direction"][]="direction";


//  ###################### notices_titres_uniformes
$tabindexref["notices_titres_uniformes"]["PRIMARY"][]="ntu_num_notice";
$tabindexref["notices_titres_uniformes"]["PRIMARY"][]="ntu_num_tu";
$tabindexref["notices_titres_uniformes"]["i_ntu_ntu_num_tu"][]="ntu_num_tu";


//  ###################### offres_remises
$tabindexref["offres_remises"]["PRIMARY"][]="num_fournisseur";
$tabindexref["offres_remises"]["PRIMARY"][]="num_produit";


//  ###################### onto_files
$tabindexref["onto_files"]["PRIMARY"][]="id_onto_file";
$tabindexref["onto_files"]["i_of_onto_file_title"][]="onto_file_title";


//  ###################### onto_uri
$tabindexref["onto_uri"]["PRIMARY"][]="uri_id";
$tabindexref["onto_uri"]["uri"][]="uri";


//  ###################### ontologies
$tabindexref["ontologies"]["PRIMARY"][]="id_ontology";


//  ###################### opac_filters
$tabindexref["opac_filters"]["PRIMARY"][]="opac_filter_view_num";
$tabindexref["opac_filters"]["PRIMARY"][]="opac_filter_path";


//  ###################### opac_liste_lecture
$tabindexref["opac_liste_lecture"]["PRIMARY"][]="id_liste";
$tabindexref["opac_liste_lecture"]["i_num_empr"][]="num_empr";


//  ###################### opac_liste_lecture_notices
$tabindexref["opac_liste_lecture_notices"]["PRIMARY"][]="opac_liste_lecture_num";
$tabindexref["opac_liste_lecture_notices"]["PRIMARY"][]="opac_liste_lecture_notice_num";


//  ###################### opac_sessions
$tabindexref["opac_sessions"]["PRIMARY"][]="empr_id";


//  ###################### opac_views
$tabindexref["opac_views"]["PRIMARY"][]="opac_view_id";


//  ###################### opac_views_empr
$tabindexref["opac_views_empr"]["PRIMARY"][]="emprview_view_num";
$tabindexref["opac_views_empr"]["PRIMARY"][]="emprview_empr_num";


//  ###################### origin_authorities
$tabindexref["origin_authorities"]["PRIMARY"][]="id_origin_authorities";


//  ###################### origine_notice
$tabindexref["origine_notice"]["PRIMARY"][]="orinot_id";
$tabindexref["origine_notice"]["orinot_nom"][]="orinot_nom";


//  ###################### ouvertures
$tabindexref["ouvertures"]["PRIMARY"][]="date_ouverture";
$tabindexref["ouvertures"]["PRIMARY"][]="num_location";
$tabindexref["ouvertures"]["i_ouvert_num_location_date_ouverture"][]="ouvert";
$tabindexref["ouvertures"]["i_ouvert_num_location_date_ouverture"][]="num_location";
$tabindexref["ouvertures"]["i_ouvert_num_location_date_ouverture"][]="date_ouverture";


//  ###################### paiements
$tabindexref["paiements"]["PRIMARY"][]="id_paiement";


//  ###################### param_subst
$tabindexref["param_subst"]["PRIMARY"][]="subst_module_param";
$tabindexref["param_subst"]["PRIMARY"][]="subst_module_num";
$tabindexref["param_subst"]["PRIMARY"][]="subst_type_param";
$tabindexref["param_subst"]["PRIMARY"][]="subst_sstype_param";


//  ###################### parametres
$tabindexref["parametres"]["PRIMARY"][]="id_param";
$tabindexref["parametres"]["typ_sstyp"][]="type_param";
$tabindexref["parametres"]["typ_sstyp"][]="sstype_param";


//  ###################### parametres_uncached
$tabindexref["parametres_uncached"]["PRIMARY"][]="id_param";
$tabindexref["parametres_uncached"]["typ_sstyp"][]="type_param";
$tabindexref["parametres_uncached"]["typ_sstyp"][]="sstype_param";


//  ###################### pclassement
$tabindexref["pclassement"]["PRIMARY"][]="id_pclass";


//  ###################### perio_relance
$tabindexref["perio_relance"]["PRIMARY"][]="rel_id";


//  ###################### planificateur
$tabindexref["planificateur"]["PRIMARY"][]="id_planificateur";


//  ###################### pnb_loans
$tabindexref["pnb_loans"]["PRIMARY"][]="id_pnb_loan";


//  ###################### pnb_orders
$tabindexref["pnb_orders"]["PRIMARY"][]="id_pnb_order";


//  ###################### pnb_orders_expl
$tabindexref["pnb_orders_expl"]["PRIMARY"][]="pnb_order_num";
$tabindexref["pnb_orders_expl"]["PRIMARY"][]="pnb_order_expl_num";


//  ###################### pret
$tabindexref["pret"]["PRIMARY"][]="pret_idexpl";
$tabindexref["pret"]["i_pret_idempr"][]="pret_idempr";
$tabindexref["pret"]["i_pret_arc_id"][]="pret_arc_id";


//  ###################### pret_archive
$tabindexref["pret_archive"]["PRIMARY"][]="arc_id";
$tabindexref["pret_archive"]["i_pa_expl_id"][]="arc_expl_id";
$tabindexref["pret_archive"]["i_pa_idempr"][]="arc_id_empr";
$tabindexref["pret_archive"]["i_pa_expl_notice"][]="arc_expl_notice";
$tabindexref["pret_archive"]["i_pa_expl_bulletin"][]="arc_expl_bulletin";
$tabindexref["pret_archive"]["i_pa_arc_fin"][]="arc_fin";
$tabindexref["pret_archive"]["i_pa_arc_empr_categ"][]="arc_empr_categ";
$tabindexref["pret_archive"]["i_pa_arc_expl_location"][]="arc_expl_location";
$tabindexref["pret_archive"]["i_pa_arc_expl_section"][]="arc_expl_section";


//  ###################### pret_custom
$tabindexref["pret_custom"]["PRIMARY"][]="idchamp";


//  ###################### pret_custom_dates
$tabindexref["pret_custom_dates"]["PRIMARY"][]="pret_custom_champ";
$tabindexref["pret_custom_dates"]["PRIMARY"][]="pret_custom_origine";
$tabindexref["pret_custom_dates"]["PRIMARY"][]="pret_custom_order";
$tabindexref["pret_custom_dates"]["pret_custom_champ"][]="pret_custom_champ";
$tabindexref["pret_custom_dates"]["pret_custom_origine"][]="pret_custom_origine";


//  ###################### pret_custom_lists
$tabindexref["pret_custom_lists"]["i_pret_custom_champ"][]="pret_custom_champ";
$tabindexref["pret_custom_lists"]["i_pret_champ_list_value"][]="pret_custom_champ";
$tabindexref["pret_custom_lists"]["i_pret_champ_list_value"][]="pret_custom_list_value";


//  ###################### pret_custom_values
$tabindexref["pret_custom_values"]["i_pret_custom_champ"][]="pret_custom_champ";
$tabindexref["pret_custom_values"]["i_pret_custom_origine"][]="pret_custom_origine";


//  ###################### print_cart_tpl
$tabindexref["print_cart_tpl"]["PRIMARY"][]="id_print_cart_tpl";


//  ###################### procs
$tabindexref["procs"]["PRIMARY"][]="idproc";
$tabindexref["procs"]["idproc"][]="idproc";


//  ###################### procs_classements
$tabindexref["procs_classements"]["PRIMARY"][]="idproc_classement";


//  ###################### publisher_custom
$tabindexref["publisher_custom"]["PRIMARY"][]="idchamp";


//  ###################### publisher_custom_dates
$tabindexref["publisher_custom_dates"]["PRIMARY"][]="publisher_custom_champ";
$tabindexref["publisher_custom_dates"]["PRIMARY"][]="publisher_custom_origine";
$tabindexref["publisher_custom_dates"]["PRIMARY"][]="publisher_custom_order";
$tabindexref["publisher_custom_dates"]["publisher_custom_champ"][]="publisher_custom_champ";
$tabindexref["publisher_custom_dates"]["publisher_custom_origine"][]="publisher_custom_origine";


//  ###################### publisher_custom_lists
$tabindexref["publisher_custom_lists"]["editorial_custom_champ"][]="publisher_custom_champ";
$tabindexref["publisher_custom_lists"]["editorial_champ_list_value"][]="publisher_custom_champ";
$tabindexref["publisher_custom_lists"]["editorial_champ_list_value"][]="publisher_custom_list_value";


//  ###################### publisher_custom_values
$tabindexref["publisher_custom_values"]["editorial_custom_champ"][]="publisher_custom_champ";
$tabindexref["publisher_custom_values"]["editorial_custom_origine"][]="publisher_custom_origine";
$tabindexref["publisher_custom_values"]["i_pcv_st"][]="publisher_custom_small_text";
$tabindexref["publisher_custom_values"]["i_pcv_t"][]="publisher_custom_text";
$tabindexref["publisher_custom_values"]["i_pcv_i"][]="publisher_custom_integer";
$tabindexref["publisher_custom_values"]["i_pcv_d"][]="publisher_custom_date";
$tabindexref["publisher_custom_values"]["i_pcv_f"][]="publisher_custom_float";


//  ###################### publishers
$tabindexref["publishers"]["PRIMARY"][]="ed_id";
$tabindexref["publishers"]["ed_name"][]="ed_name";
$tabindexref["publishers"]["ed_ville"][]="ed_ville";


//  ###################### quotas
$tabindexref["quotas"]["PRIMARY"][]="quota_type";
$tabindexref["quotas"]["PRIMARY"][]="constraint_type";
$tabindexref["quotas"]["PRIMARY"][]="elements";


//  ###################### quotas_finance
$tabindexref["quotas_finance"]["PRIMARY"][]="quota_type";
$tabindexref["quotas_finance"]["PRIMARY"][]="constraint_type";
$tabindexref["quotas_finance"]["PRIMARY"][]="elements";


//  ###################### quotas_opac_views
$tabindexref["quotas_opac_views"]["PRIMARY"][]="quota_type";
$tabindexref["quotas_opac_views"]["PRIMARY"][]="constraint_type";
$tabindexref["quotas_opac_views"]["PRIMARY"][]="elements";


//  ###################### rapport_demandes
$tabindexref["rapport_demandes"]["PRIMARY"][]="id_item";


//  ###################### rdfstore_g2t
$tabindexref["rdfstore_g2t"]["gt"][]="g";
$tabindexref["rdfstore_g2t"]["gt"][]="t";
$tabindexref["rdfstore_g2t"]["tg"][]="t";
$tabindexref["rdfstore_g2t"]["tg"][]="g";


//  ###################### rdfstore_id2val
$tabindexref["rdfstore_id2val"]["id"][]="id";
$tabindexref["rdfstore_id2val"]["id"][]="val_type";
$tabindexref["rdfstore_id2val"]["v"][]="val";


//  ###################### rdfstore_index
$tabindexref["rdfstore_index"]["PRIMARY"][]="num_object";


//  ###################### rdfstore_o2val
$tabindexref["rdfstore_o2val"]["id"][]="id";
$tabindexref["rdfstore_o2val"]["vh"][]="val_hash";
$tabindexref["rdfstore_o2val"]["v"][]="val";


//  ###################### rdfstore_s2val
$tabindexref["rdfstore_s2val"]["id"][]="id";
$tabindexref["rdfstore_s2val"]["vh"][]="val_hash";


//  ###################### rdfstore_setting
$tabindexref["rdfstore_setting"]["k"][]="k";


//  ###################### rdfstore_triple
$tabindexref["rdfstore_triple"]["t"][]="t";
$tabindexref["rdfstore_triple"]["sp"][]="s";
$tabindexref["rdfstore_triple"]["sp"][]="p";
$tabindexref["rdfstore_triple"]["os"][]="o";
$tabindexref["rdfstore_triple"]["os"][]="s";
$tabindexref["rdfstore_triple"]["po"][]="p";
$tabindexref["rdfstore_triple"]["po"][]="o";
$tabindexref["rdfstore_triple"]["misc"][]="misc";


//  ###################### recouvrements
$tabindexref["recouvrements"]["PRIMARY"][]="recouvr_id";


//  ###################### rent_account_types_sections
$tabindexref["rent_account_types_sections"]["PRIMARY"][]="account_type_num_section";
$tabindexref["rent_account_types_sections"]["PRIMARY"][]="account_type_marclist";


//  ###################### rent_accounts
$tabindexref["rent_accounts"]["PRIMARY"][]="id_account";


//  ###################### rent_accounts_invoices
$tabindexref["rent_accounts_invoices"]["PRIMARY"][]="account_invoice_num_account";
$tabindexref["rent_accounts_invoices"]["PRIMARY"][]="account_invoice_num_invoice";


//  ###################### rent_invoices
$tabindexref["rent_invoices"]["PRIMARY"][]="id_invoice";


//  ###################### rent_pricing_system_grids
$tabindexref["rent_pricing_system_grids"]["PRIMARY"][]="id_pricing_system_grid";


//  ###################### rent_pricing_systems
$tabindexref["rent_pricing_systems"]["PRIMARY"][]="id_pricing_system";


//  ###################### resa
$tabindexref["resa"]["PRIMARY"][]="id_resa";
$tabindexref["resa"]["resa_date_fin"][]="resa_date_fin";
$tabindexref["resa"]["resa_date"][]="resa_date";
$tabindexref["resa"]["resa_cb"][]="resa_cb";
$tabindexref["resa"]["i_idbulletin"][]="resa_idbulletin";
$tabindexref["resa"]["i_idnotice"][]="resa_idnotice";
$tabindexref["resa"]["i_resa_idempr"][]="resa_idempr";


//  ###################### resa_archive
$tabindexref["resa_archive"]["PRIMARY"][]="resarc_id";
$tabindexref["resa_archive"]["i_pa_idempr"][]="resarc_id_empr";
$tabindexref["resa_archive"]["i_pa_notice"][]="resarc_idnotice";
$tabindexref["resa_archive"]["i_pa_bulletin"][]="resarc_idbulletin";
$tabindexref["resa_archive"]["i_pa_resarc_date"][]="resarc_date";


//  ###################### resa_loc
$tabindexref["resa_loc"]["PRIMARY"][]="resa_loc";
$tabindexref["resa_loc"]["PRIMARY"][]="resa_emprloc";
$tabindexref["resa_loc"]["i_resa_emprloc"][]="resa_emprloc";


//  ###################### resa_planning
$tabindexref["resa_planning"]["PRIMARY"][]="id_resa";
$tabindexref["resa_planning"]["resa_date_fin"][]="resa_date_fin";
$tabindexref["resa_planning"]["resa_date"][]="resa_date";


//  ###################### resa_ranger
$tabindexref["resa_ranger"]["PRIMARY"][]="resa_cb";


//  ###################### responsability
$tabindexref["responsability"]["PRIMARY"][]="id_responsability";
$tabindexref["responsability"]["PRIMARY"][]="responsability_author";
$tabindexref["responsability"]["PRIMARY"][]="responsability_notice";
$tabindexref["responsability"]["PRIMARY"][]="responsability_fonction";
$tabindexref["responsability"]["responsability_notice"][]="responsability_notice";
$tabindexref["responsability"]["i_responsability_author"][]="responsability_author";


//  ###################### responsability_authperso
$tabindexref["responsability_authperso"]["PRIMARY"][]="id_responsability_authperso";
$tabindexref["responsability_authperso"]["PRIMARY"][]="responsability_authperso_author";
$tabindexref["responsability_authperso"]["PRIMARY"][]="responsability_authperso_num";
$tabindexref["responsability_authperso"]["PRIMARY"][]="responsability_authperso_fonction";
$tabindexref["responsability_authperso"]["responsability_authperso_num"][]="responsability_authperso_num";
$tabindexref["responsability_authperso"]["responsability_authperso_author"][]="responsability_authperso_author";


//  ###################### responsability_tu
$tabindexref["responsability_tu"]["PRIMARY"][]="id_responsability_tu";
$tabindexref["responsability_tu"]["PRIMARY"][]="responsability_tu_author_num";
$tabindexref["responsability_tu"]["PRIMARY"][]="responsability_tu_num";
$tabindexref["responsability_tu"]["PRIMARY"][]="responsability_tu_fonction";
$tabindexref["responsability_tu"]["responsability_tu_author"][]="responsability_tu_author_num";
$tabindexref["responsability_tu"]["responsability_tu_num"][]="responsability_tu_num";


//  ###################### rss_content
$tabindexref["rss_content"]["PRIMARY"][]="rss_id";


//  ###################### rss_flux
$tabindexref["rss_flux"]["PRIMARY"][]="id_rss_flux";
$tabindexref["rss_flux"]["i_id_tri_rss_flux"][]="id_tri_rss_flux";


//  ###################### rss_flux_content
$tabindexref["rss_flux_content"]["PRIMARY"][]="num_rss_flux";
$tabindexref["rss_flux_content"]["PRIMARY"][]="type_contenant";
$tabindexref["rss_flux_content"]["PRIMARY"][]="num_contenant";


//  ###################### rubriques
$tabindexref["rubriques"]["PRIMARY"][]="id_rubrique";


//  ###################### sauv_lieux
$tabindexref["sauv_lieux"]["PRIMARY"][]="sauv_lieu_id";


//  ###################### sauv_log
$tabindexref["sauv_log"]["PRIMARY"][]="sauv_log_id";


//  ###################### sauv_sauvegardes
$tabindexref["sauv_sauvegardes"]["PRIMARY"][]="sauv_sauvegarde_id";


//  ###################### sauv_tables
$tabindexref["sauv_tables"]["PRIMARY"][]="sauv_table_id";
$tabindexref["sauv_tables"]["sauv_table_nom"][]="sauv_table_nom";


//  ###################### scan_request_explnum
$tabindexref["scan_request_explnum"]["PRIMARY"][]="scan_request_explnum_num_request";
$tabindexref["scan_request_explnum"]["PRIMARY"][]="scan_request_explnum_num_explnum";


//  ###################### scan_request_linked_records
$tabindexref["scan_request_linked_records"]["PRIMARY"][]="scan_request_linked_record_num_request";
$tabindexref["scan_request_linked_records"]["PRIMARY"][]="scan_request_linked_record_num_notice";
$tabindexref["scan_request_linked_records"]["PRIMARY"][]="scan_request_linked_record_num_bulletin";


//  ###################### scan_request_priorities
$tabindexref["scan_request_priorities"]["PRIMARY"][]="id_scan_request_priority";


//  ###################### scan_request_status
$tabindexref["scan_request_status"]["PRIMARY"][]="id_scan_request_status";


//  ###################### scan_request_status_workflow
$tabindexref["scan_request_status_workflow"]["PRIMARY"][]="scan_request_status_workflow_from_num";
$tabindexref["scan_request_status_workflow"]["PRIMARY"][]="scan_request_status_workflow_to_num";


//  ###################### scan_requests
$tabindexref["scan_requests"]["PRIMARY"][]="id_scan_request";


//  ###################### search_cache
$tabindexref["search_cache"]["PRIMARY"][]="object_id";


//  ###################### search_perso
$tabindexref["search_perso"]["PRIMARY"][]="search_id";


//  ###################### search_persopac
$tabindexref["search_persopac"]["PRIMARY"][]="search_id";


//  ###################### search_persopac_empr_categ
$tabindexref["search_persopac_empr_categ"]["i_id_s_persopac"][]="id_search_persopac";
$tabindexref["search_persopac_empr_categ"]["i_id_categ_empr"][]="id_categ_empr";


//  ###################### search_segments
$tabindexref["search_segments"]["PRIMARY"][]="id_search_segment";


//  ###################### search_segments_facets
$tabindexref["search_segments_facets"]["PRIMARY"][]="num_search_segment";
$tabindexref["search_segments_facets"]["PRIMARY"][]="num_facet";


//  ###################### search_segments_search_perso
$tabindexref["search_segments_search_perso"]["PRIMARY"][]="num_search_segment";
$tabindexref["search_segments_search_perso"]["PRIMARY"][]="num_search_perso";


//  ###################### search_universes
$tabindexref["search_universes"]["PRIMARY"][]="id_search_universe";


//  ###################### serialcirc
$tabindexref["serialcirc"]["PRIMARY"][]="id_serialcirc";
$tabindexref["serialcirc"]["i_num_serialcirc_abt"][]="num_serialcirc_abt";


//  ###################### serialcirc_ask
$tabindexref["serialcirc_ask"]["PRIMARY"][]="id_serialcirc_ask";
$tabindexref["serialcirc_ask"]["i_num_serialcirc_ask_perio"][]="num_serialcirc_ask_perio";
$tabindexref["serialcirc_ask"]["i_num_serialcirc_ask_serialcirc"][]="num_serialcirc_ask_serialcirc";
$tabindexref["serialcirc_ask"]["i_num_serialcirc_ask_empr"][]="num_serialcirc_ask_empr";
$tabindexref["serialcirc_ask"]["i_serialcirc_ask_type"][]="serialcirc_ask_type";
$tabindexref["serialcirc_ask"]["i_serialcirc_ask_statut"][]="serialcirc_ask_statut";


//  ###################### serialcirc_circ
$tabindexref["serialcirc_circ"]["PRIMARY"][]="id_serialcirc_circ";
$tabindexref["serialcirc_circ"]["i_num_serialcirc_circ_diff"][]="num_serialcirc_circ_diff";
$tabindexref["serialcirc_circ"]["i_num_serialcirc_circ_expl"][]="num_serialcirc_circ_expl";
$tabindexref["serialcirc_circ"]["i_num_serialcirc_circ_empr"][]="num_serialcirc_circ_empr";
$tabindexref["serialcirc_circ"]["i_num_serialcirc_circ_serialcirc"][]="num_serialcirc_circ_serialcirc";


//  ###################### serialcirc_copy
$tabindexref["serialcirc_copy"]["PRIMARY"][]="id_serialcirc_copy";
$tabindexref["serialcirc_copy"]["i_num_serialcirc_copy_empr"][]="num_serialcirc_copy_empr";
$tabindexref["serialcirc_copy"]["i_num_serialcirc_copy_bulletin"][]="num_serialcirc_copy_bulletin";


//  ###################### serialcirc_diff
$tabindexref["serialcirc_diff"]["PRIMARY"][]="id_serialcirc_diff";
$tabindexref["serialcirc_diff"]["i_num_serialcirc_diff_serialcirc"][]="num_serialcirc_diff_serialcirc";
$tabindexref["serialcirc_diff"]["i_serialcirc_diff_empr_type"][]="serialcirc_diff_empr_type";
$tabindexref["serialcirc_diff"]["i_serialcirc_diff_type_diff"][]="serialcirc_diff_type_diff";
$tabindexref["serialcirc_diff"]["i_num_serialcirc_diff_empr"][]="num_serialcirc_diff_empr";


//  ###################### serialcirc_expl
$tabindexref["serialcirc_expl"]["PRIMARY"][]="id_serialcirc_expl";
$tabindexref["serialcirc_expl"]["i_num_serialcirc_expl_id"][]="num_serialcirc_expl_id";
$tabindexref["serialcirc_expl"]["i_num_serialcirc_expl_serialcirc"][]="num_serialcirc_expl_serialcirc";
$tabindexref["serialcirc_expl"]["i_num_serialcirc_expl_serialcirc_diff"][]="num_serialcirc_expl_serialcirc_diff";
$tabindexref["serialcirc_expl"]["i_num_serialcirc_expl_current_empr"][]="num_serialcirc_expl_current_empr";


//  ###################### serialcirc_group
$tabindexref["serialcirc_group"]["PRIMARY"][]="id_serialcirc_group";
$tabindexref["serialcirc_group"]["i_num_serialcirc_group_diff"][]="num_serialcirc_group_diff";
$tabindexref["serialcirc_group"]["i_num_serialcirc_group_empr"][]="num_serialcirc_group_empr";


//  ###################### serialcirc_tpl
$tabindexref["serialcirc_tpl"]["PRIMARY"][]="serialcirctpl_id";


//  ###################### serie_custom
$tabindexref["serie_custom"]["PRIMARY"][]="idchamp";


//  ###################### serie_custom_dates
$tabindexref["serie_custom_dates"]["PRIMARY"][]="serie_custom_champ";
$tabindexref["serie_custom_dates"]["PRIMARY"][]="serie_custom_origine";
$tabindexref["serie_custom_dates"]["PRIMARY"][]="serie_custom_order";
$tabindexref["serie_custom_dates"]["serie_custom_champ"][]="serie_custom_champ";
$tabindexref["serie_custom_dates"]["serie_custom_origine"][]="serie_custom_origine";


//  ###################### serie_custom_lists
$tabindexref["serie_custom_lists"]["editorial_custom_champ"][]="serie_custom_champ";
$tabindexref["serie_custom_lists"]["editorial_champ_list_value"][]="serie_custom_champ";
$tabindexref["serie_custom_lists"]["editorial_champ_list_value"][]="serie_custom_list_value";


//  ###################### serie_custom_values
$tabindexref["serie_custom_values"]["editorial_custom_champ"][]="serie_custom_champ";
$tabindexref["serie_custom_values"]["editorial_custom_origine"][]="serie_custom_origine";
$tabindexref["serie_custom_values"]["i_scv_st"][]="serie_custom_small_text";
$tabindexref["serie_custom_values"]["i_scv_t"][]="serie_custom_text";
$tabindexref["serie_custom_values"]["i_scv_i"][]="serie_custom_integer";
$tabindexref["serie_custom_values"]["i_scv_d"][]="serie_custom_date";
$tabindexref["serie_custom_values"]["i_scv_f"][]="serie_custom_float";


//  ###################### series
$tabindexref["series"]["PRIMARY"][]="serie_id";


//  ###################### sessions
$tabindexref["sessions"]["PRIMARY"][]="SESSID";


//  ###################### sessions_tokens
$tabindexref["sessions_tokens"]["PRIMARY"][]="sessions_tokens_SESSID";
$tabindexref["sessions_tokens"]["PRIMARY"][]="sessions_tokens_type";
$tabindexref["sessions_tokens"]["i_st_sessions_tokens_type"][]="sessions_tokens_type";
$tabindexref["sessions_tokens"]["i_st_sessions_tokens_token"][]="sessions_tokens_token";


//  ###################### shorturls
$tabindexref["shorturls"]["PRIMARY"][]="id_shorturl";
$tabindexref["shorturls"]["i_shorturl_hash"][]="shorturl_hash";


//  ###################### skos_custom
$tabindexref["skos_custom"]["PRIMARY"][]="idchamp";


//  ###################### skos_custom_dates
$tabindexref["skos_custom_dates"]["PRIMARY"][]="skos_custom_champ";
$tabindexref["skos_custom_dates"]["PRIMARY"][]="skos_custom_origine";
$tabindexref["skos_custom_dates"]["PRIMARY"][]="skos_custom_order";
$tabindexref["skos_custom_dates"]["skos_custom_champ"][]="skos_custom_champ";
$tabindexref["skos_custom_dates"]["skos_custom_origine"][]="skos_custom_origine";


//  ###################### skos_custom_lists
$tabindexref["skos_custom_lists"]["skos_custom_champ"][]="skos_custom_champ";
$tabindexref["skos_custom_lists"]["skos_champ_list_value"][]="skos_custom_champ";
$tabindexref["skos_custom_lists"]["skos_champ_list_value"][]="skos_custom_list_value";


//  ###################### skos_custom_values
$tabindexref["skos_custom_values"]["skos_custom_champ"][]="skos_custom_champ";
$tabindexref["skos_custom_values"]["i_encv_st"][]="skos_custom_small_text";
$tabindexref["skos_custom_values"]["i_encv_t"][]="skos_custom_text";
$tabindexref["skos_custom_values"]["i_encv_i"][]="skos_custom_integer";
$tabindexref["skos_custom_values"]["i_encv_d"][]="skos_custom_date";
$tabindexref["skos_custom_values"]["i_encv_f"][]="skos_custom_float";
$tabindexref["skos_custom_values"]["skos_custom_origine"][]="skos_custom_origine";


//  ###################### skos_fields_global_index
$tabindexref["skos_fields_global_index"]["PRIMARY"][]="id_item";
$tabindexref["skos_fields_global_index"]["PRIMARY"][]="code_champ";
$tabindexref["skos_fields_global_index"]["PRIMARY"][]="code_ss_champ";
$tabindexref["skos_fields_global_index"]["PRIMARY"][]="lang";
$tabindexref["skos_fields_global_index"]["PRIMARY"][]="ordre";
$tabindexref["skos_fields_global_index"]["i_value"][]="value";
$tabindexref["skos_fields_global_index"]["i_code_champ_code_ss_champ"][]="code_champ";
$tabindexref["skos_fields_global_index"]["i_code_champ_code_ss_champ"][]="code_ss_champ";


//  ###################### skos_words_global_index
$tabindexref["skos_words_global_index"]["PRIMARY"][]="id_item";
$tabindexref["skos_words_global_index"]["PRIMARY"][]="code_champ";
$tabindexref["skos_words_global_index"]["PRIMARY"][]="code_ss_champ";
$tabindexref["skos_words_global_index"]["PRIMARY"][]="num_word";
$tabindexref["skos_words_global_index"]["PRIMARY"][]="position";
$tabindexref["skos_words_global_index"]["PRIMARY"][]="field_position";
$tabindexref["skos_words_global_index"]["code_champ"][]="code_champ";
$tabindexref["skos_words_global_index"]["i_id_mot"][]="num_word";
$tabindexref["skos_words_global_index"]["i_id_mot"][]="id_item";
$tabindexref["skos_words_global_index"]["i_code_champ_code_ss_champ_num_word"][]="code_champ";
$tabindexref["skos_words_global_index"]["i_code_champ_code_ss_champ_num_word"][]="code_ss_champ";
$tabindexref["skos_words_global_index"]["i_code_champ_code_ss_champ_num_word"][]="num_word";
$tabindexref["skos_words_global_index"]["i_num_word"][]="num_word";


//  ###################### source_sync
$tabindexref["source_sync"]["PRIMARY"][]="source_id";


//  ###################### sources_enrichment
$tabindexref["sources_enrichment"]["PRIMARY"][]="source_enrichment_num";
$tabindexref["sources_enrichment"]["PRIMARY"][]="source_enrichment_typnotice";
$tabindexref["sources_enrichment"]["PRIMARY"][]="source_enrichment_typdoc";
$tabindexref["sources_enrichment"]["i_s_enrichment_typnoti"][]="source_enrichment_typnotice";
$tabindexref["sources_enrichment"]["i_s_enrichment_typdoc"][]="source_enrichment_typdoc";


//  ###################### statopac
$tabindexref["statopac"]["PRIMARY"][]="id_log";
$tabindexref["statopac"]["sopac_date_log"][]="date_log";


//  ###################### statopac_request
$tabindexref["statopac_request"]["PRIMARY"][]="idproc";


//  ###################### statopac_vues
$tabindexref["statopac_vues"]["PRIMARY"][]="id_vue";


//  ###################### statopac_vues_col
$tabindexref["statopac_vues_col"]["PRIMARY"][]="id_col";


//  ###################### sticks_sheets
$tabindexref["sticks_sheets"]["PRIMARY"][]="id_sticks_sheet";


//  ###################### storages
$tabindexref["storages"]["PRIMARY"][]="id_storage";
$tabindexref["storages"]["i_storage_class"][]="storage_class";


//  ###################### sub_collections
$tabindexref["sub_collections"]["PRIMARY"][]="sub_coll_id";
$tabindexref["sub_collections"]["sub_coll_name"][]="sub_coll_name";


//  ###################### subcollection_custom
$tabindexref["subcollection_custom"]["PRIMARY"][]="idchamp";


//  ###################### subcollection_custom_dates
$tabindexref["subcollection_custom_dates"]["PRIMARY"][]="subcollection_custom_champ";
$tabindexref["subcollection_custom_dates"]["PRIMARY"][]="subcollection_custom_origine";
$tabindexref["subcollection_custom_dates"]["PRIMARY"][]="subcollection_custom_order";
$tabindexref["subcollection_custom_dates"]["subcollection_custom_champ"][]="subcollection_custom_champ";
$tabindexref["subcollection_custom_dates"]["subcollection_custom_origine"][]="subcollection_custom_origine";


//  ###################### subcollection_custom_lists
$tabindexref["subcollection_custom_lists"]["editorial_custom_champ"][]="subcollection_custom_champ";
$tabindexref["subcollection_custom_lists"]["editorial_champ_list_value"][]="subcollection_custom_champ";
$tabindexref["subcollection_custom_lists"]["editorial_champ_list_value"][]="subcollection_custom_list_value";


//  ###################### subcollection_custom_values
$tabindexref["subcollection_custom_values"]["editorial_custom_champ"][]="subcollection_custom_champ";
$tabindexref["subcollection_custom_values"]["editorial_custom_origine"][]="subcollection_custom_origine";
$tabindexref["subcollection_custom_values"]["i_scv_st"][]="subcollection_custom_small_text";
$tabindexref["subcollection_custom_values"]["i_scv_t"][]="subcollection_custom_text";
$tabindexref["subcollection_custom_values"]["i_scv_i"][]="subcollection_custom_integer";
$tabindexref["subcollection_custom_values"]["i_scv_d"][]="subcollection_custom_date";
$tabindexref["subcollection_custom_values"]["i_scv_f"][]="subcollection_custom_float";


//  ###################### subst_files
$tabindexref["subst_files"]["PRIMARY"][]="id_subst_file";


//  ###################### suggestions
$tabindexref["suggestions"]["PRIMARY"][]="id_suggestion";


//  ###################### suggestions_categ
$tabindexref["suggestions_categ"]["PRIMARY"][]="id_categ";


//  ###################### suggestions_origine
$tabindexref["suggestions_origine"]["PRIMARY"][]="origine";
$tabindexref["suggestions_origine"]["PRIMARY"][]="num_suggestion";
$tabindexref["suggestions_origine"]["PRIMARY"][]="type_origine";
$tabindexref["suggestions_origine"]["i_origine"][]="origine";
$tabindexref["suggestions_origine"]["i_origine"][]="type_origine";
$tabindexref["suggestions_origine"]["i_num_suggestion"][]="num_suggestion";


//  ###################### suggestions_source
$tabindexref["suggestions_source"]["PRIMARY"][]="id_source";


//  ###################### sur_location
$tabindexref["sur_location"]["PRIMARY"][]="surloc_id";


//  ###################### taches
$tabindexref["taches"]["PRIMARY"][]="id_tache";


//  ###################### taches_docnum
$tabindexref["taches_docnum"]["PRIMARY"][]="id_tache_docnum";


//  ###################### taches_type
$tabindexref["taches_type"]["PRIMARY"][]="id_type_tache";


//  ###################### tags
$tabindexref["tags"]["PRIMARY"][]="id_tag";


//  ###################### thesaurus
$tabindexref["thesaurus"]["PRIMARY"][]="id_thesaurus";
$tabindexref["thesaurus"]["libelle_thesaurus"][]="libelle_thesaurus";


//  ###################### thresholds
$tabindexref["thresholds"]["PRIMARY"][]="id_threshold";


//  ###################### titres_uniformes
$tabindexref["titres_uniformes"]["PRIMARY"][]="tu_id";
$tabindexref["titres_uniformes"]["i_tu_tu_oeuvre_type"][]="tu_oeuvre_type";
$tabindexref["titres_uniformes"]["i_tu_tu_oeuvre_nature"][]="tu_oeuvre_nature";


//  ###################### transacash
$tabindexref["transacash"]["PRIMARY"][]="transacash_id";


//  ###################### transaction_payment_methods
$tabindexref["transaction_payment_methods"]["PRIMARY"][]="transaction_payment_method_id";


//  ###################### transactions
$tabindexref["transactions"]["PRIMARY"][]="id_transaction";
$tabindexref["transactions"]["i_realisee"][]="realisee";
$tabindexref["transactions"]["i_compte_id"][]="compte_id";


//  ###################### transactype
$tabindexref["transactype"]["PRIMARY"][]="transactype_id";


//  ###################### transferts
$tabindexref["transferts"]["PRIMARY"][]="id_transfert";
$tabindexref["transferts"]["etat_transfert"][]="etat_transfert";
$tabindexref["transferts"]["i_etat_transfert_origine"][]="etat_transfert";
$tabindexref["transferts"]["i_etat_transfert_origine"][]="origine";


//  ###################### transferts_demande
$tabindexref["transferts_demande"]["PRIMARY"][]="id_transfert_demande";
$tabindexref["transferts_demande"]["num_transfert"][]="num_transfert";
$tabindexref["transferts_demande"]["num_location_source"][]="num_location_source";
$tabindexref["transferts_demande"]["num_location_dest"][]="num_location_dest";
$tabindexref["transferts_demande"]["num_expl"][]="num_expl";
$tabindexref["transferts_demande"]["i_resa_trans"][]="resa_trans";


//  ###################### transferts_source
$tabindexref["transferts_source"]["PRIMARY"][]="trans_source_numexpl";


//  ###################### translation
$tabindexref["translation"]["PRIMARY"][]="trans_table";
$tabindexref["translation"]["PRIMARY"][]="trans_field";
$tabindexref["translation"]["PRIMARY"][]="trans_lang";
$tabindexref["translation"]["PRIMARY"][]="trans_num";
$tabindexref["translation"]["i_lang"][]="trans_lang";


//  ###################### tris
$tabindexref["tris"]["PRIMARY"][]="id_tri";


//  ###################### tu_custom
$tabindexref["tu_custom"]["PRIMARY"][]="idchamp";


//  ###################### tu_custom_dates
$tabindexref["tu_custom_dates"]["PRIMARY"][]="tu_custom_champ";
$tabindexref["tu_custom_dates"]["PRIMARY"][]="tu_custom_origine";
$tabindexref["tu_custom_dates"]["PRIMARY"][]="tu_custom_order";
$tabindexref["tu_custom_dates"]["tu_custom_champ"][]="tu_custom_champ";
$tabindexref["tu_custom_dates"]["tu_custom_origine"][]="tu_custom_origine";


//  ###################### tu_custom_lists
$tabindexref["tu_custom_lists"]["editorial_custom_champ"][]="tu_custom_champ";
$tabindexref["tu_custom_lists"]["editorial_champ_list_value"][]="tu_custom_champ";
$tabindexref["tu_custom_lists"]["editorial_champ_list_value"][]="tu_custom_list_value";


//  ###################### tu_custom_values
$tabindexref["tu_custom_values"]["editorial_custom_champ"][]="tu_custom_champ";
$tabindexref["tu_custom_values"]["editorial_custom_origine"][]="tu_custom_origine";
$tabindexref["tu_custom_values"]["i_tcv_st"][]="tu_custom_small_text";
$tabindexref["tu_custom_values"]["i_tcv_t"][]="tu_custom_text";
$tabindexref["tu_custom_values"]["i_tcv_i"][]="tu_custom_integer";
$tabindexref["tu_custom_values"]["i_tcv_d"][]="tu_custom_date";
$tabindexref["tu_custom_values"]["i_tcv_f"][]="tu_custom_float";


//  ###################### tu_distrib
$tabindexref["tu_distrib"]["PRIMARY"][]="distrib_num_tu";
$tabindexref["tu_distrib"]["PRIMARY"][]="distrib_ordre";


//  ###################### tu_oeuvres_events
$tabindexref["tu_oeuvres_events"]["i_toe_oeuvre_event_tu_num"][]="oeuvre_event_tu_num";
$tabindexref["tu_oeuvres_events"]["i_toe_oeuvre_event_authperso_authority_num"][]="oeuvre_event_authperso_authority_num";


//  ###################### tu_oeuvres_links
$tabindexref["tu_oeuvres_links"]["PRIMARY"][]="oeuvre_link_from";
$tabindexref["tu_oeuvres_links"]["PRIMARY"][]="oeuvre_link_to";
$tabindexref["tu_oeuvres_links"]["PRIMARY"][]="oeuvre_link_type";
$tabindexref["tu_oeuvres_links"]["PRIMARY"][]="oeuvre_link_expression";
$tabindexref["tu_oeuvres_links"]["PRIMARY"][]="oeuvre_link_other_link";
$tabindexref["tu_oeuvres_links"]["i_oeuvre_link_from"][]="oeuvre_link_from";
$tabindexref["tu_oeuvres_links"]["i_oeuvre_link_to"][]="oeuvre_link_to";


//  ###################### tu_ref
$tabindexref["tu_ref"]["PRIMARY"][]="ref_num_tu";
$tabindexref["tu_ref"]["PRIMARY"][]="ref_ordre";


//  ###################### tu_subdiv
$tabindexref["tu_subdiv"]["PRIMARY"][]="subdiv_num_tu";
$tabindexref["tu_subdiv"]["PRIMARY"][]="subdiv_ordre";


//  ###################### tva_achats
$tabindexref["tva_achats"]["PRIMARY"][]="id_tva";


//  ###################### type_abts
$tabindexref["type_abts"]["PRIMARY"][]="id_type_abt";


//  ###################### type_comptes
$tabindexref["type_comptes"]["PRIMARY"][]="id_type_compte";


//  ###################### types_produits
$tabindexref["types_produits"]["PRIMARY"][]="id_produit";
$tabindexref["types_produits"]["libelle"][]="libelle";


//  ###################### upload_repertoire
$tabindexref["upload_repertoire"]["PRIMARY"][]="repertoire_id";


//  ###################### users
$tabindexref["users"]["PRIMARY"][]="userid";


//  ###################### users_groups
$tabindexref["users_groups"]["PRIMARY"][]="grp_id";
$tabindexref["users_groups"]["i_users_groups_grp_name"][]="grp_name";


//  ###################### vedette
$tabindexref["vedette"]["PRIMARY"][]="id_vedette";
$tabindexref["vedette"]["i_grammar"][]="grammar";


//  ###################### vedette_grammars_by_entity
$tabindexref["vedette_grammars_by_entity"]["PRIMARY"][]="entity_type";
$tabindexref["vedette_grammars_by_entity"]["PRIMARY"][]="grammar";


//  ###################### vedette_link
$tabindexref["vedette_link"]["PRIMARY"][]="num_vedette";
$tabindexref["vedette_link"]["PRIMARY"][]="num_object";
$tabindexref["vedette_link"]["PRIMARY"][]="type_object";


//  ###################### vedette_object
$tabindexref["vedette_object"]["PRIMARY"][]="object_type";
$tabindexref["vedette_object"]["PRIMARY"][]="object_id";
$tabindexref["vedette_object"]["PRIMARY"][]="num_vedette";
$tabindexref["vedette_object"]["PRIMARY"][]="subdivision";
$tabindexref["vedette_object"]["PRIMARY"][]="position";
$tabindexref["vedette_object"]["i_vedette_object_object"][]="object_type";
$tabindexref["vedette_object"]["i_vedette_object_object"][]="object_id";
$tabindexref["vedette_object"]["i_vedette_object_vedette"][]="num_vedette";


//  ###################### vedette_schemes_by_entity
$tabindexref["vedette_schemes_by_entity"]["PRIMARY"][]="entity_type";
$tabindexref["vedette_schemes_by_entity"]["PRIMARY"][]="scheme";


//  ###################### visionneuse_params
$tabindexref["visionneuse_params"]["PRIMARY"][]="visionneuse_params_id";
$tabindexref["visionneuse_params"]["visionneuse_params_class"][]="visionneuse_params_class";


//  ###################### visits_statistics
$tabindexref["visits_statistics"]["PRIMARY"][]="visits_statistics_id";
$tabindexref["visits_statistics"]["i_vs_visits_statistics_date"][]="visits_statistics_date";
$tabindexref["visits_statistics"]["i_vs_visits_statistics_location_visits_statistics_type"][]="visits_statistics_location";
$tabindexref["visits_statistics"]["i_vs_visits_statistics_location_visits_statistics_type"][]="visits_statistics_type";


//  ###################### voir_aussi
$tabindexref["voir_aussi"]["PRIMARY"][]="num_noeud_orig";
$tabindexref["voir_aussi"]["PRIMARY"][]="num_noeud_dest";
$tabindexref["voir_aussi"]["PRIMARY"][]="langue";
$tabindexref["voir_aussi"]["num_noeud_dest"][]="num_noeud_dest";


//  ###################### words
$tabindexref["words"]["PRIMARY"][]="id_word";
$tabindexref["words"]["i_word_lang"][]="word";
$tabindexref["words"]["i_word_lang"][]="lang";
$tabindexref["words"]["i_stem_lang"][]="stem";
$tabindexref["words"]["i_stem_lang"][]="lang";


//  ###################### z_attr
$tabindexref["z_attr"]["PRIMARY"][]="attr_bib_id";
$tabindexref["z_attr"]["PRIMARY"][]="attr_libelle";


//  ###################### z_bib
$tabindexref["z_bib"]["PRIMARY"][]="bib_id";


//  ###################### z_notices
$tabindexref["z_notices"]["PRIMARY"][]="znotices_id";
$tabindexref["z_notices"]["idx_z_notices_idq"][]="znotices_query_id";
$tabindexref["z_notices"]["idx_z_notices_isbn"][]="isbn";
$tabindexref["z_notices"]["idx_z_notices_titre"][]="titre";
$tabindexref["z_notices"]["idx_z_notices_auteur"][]="auteur";


//  ###################### z_query
$tabindexref["z_query"]["PRIMARY"][]="zquery_id";
$tabindexref["z_query"]["zquery_date"][]="zquery_date";
