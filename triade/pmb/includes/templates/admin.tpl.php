<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: admin.tpl.php,v 1.280 2019-05-27 15:45:22 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $pmb_logs_activate, $pmb_opac_view_activate, $pmb_nomenclature_activate, $pmb_quotas_avances, $pmb_utiliser_calendrier, $pmb_gestion_financiere, $pmb_planificateur_allow;
global $pmb_mails_waiting, $pmb_sur_location_activate, $pmb_selfservice_allow, $pmb_transferts_actif, $pmb_javascript_office_editor, $opac_visionneuse_allow;
global $opac_search_universes_activate, $opac_websubscribe_show, $opac_serialcirc_active, $ldap_accessible, $faq_active, $acquisition_gestion_tva, $acquisition_sugg_categ;
global $file_in, $suffix, $mimetype, $output, $admin_menu_new, $msg, $pmb_gestion_abonnement, $pmb_gestion_tarif_prets, $pmb_gestion_amende, $pmb_allow_external_search;
global $acquisition_active, $gestion_acces_active, $demandes_active, $pmb_scan_request_activate, $plugins, $sur_location_menu, $admin_menu_docs, $pmb_map_activate, $admin_menu_noti_onglet;
global $admin_menu_notices, $admin_menu_collstate, $admin_menu_search_persopac, $admin_menu_opac, $admin_menu_opac_view, $admin_menu_abonnements, $admin_menu_empr, $admin_menu_users;
global $admin_menu_import, $admin_menu_convert, $charset, $admin_menu_harvest, $admin_menu_mailtpl, $admin_menu_misc, $PMBuserid, $admin_menu_z3950, $admin_menu_sauvegarde;
global $admin_menu_calendrier, $admin_menu_finance, $pmb_gestion_financiere_caisses, $admin_menu_acquisition, $admin_menu_external_services, $admin_menu_connecteurs, $admin_menu_selfservice;
global $admin_menu_visionneuse, $admin_menu_act, $admin_menu_transferts, $admin_menu_upload_docnum, $admin_menu_demandes, $admin_menu_faq, $admin_menu_nomenclature, $admin_menu_formation;
global $admin_menu_voice, $admin_menu_instrument, $admin_menu_material, $admin_menu_scan_request, $admin_layout, $current_module, $admin_layout_end, $admin_user_javascript;
global $admin_npass_form, $admin_user_form, $fiches_active, $thesaurus_concepts_active, $dsi_active, $semantic_active, $pmb_extension_tab, $frbr_active, $modelling_active;
global $user_acquisition_adr_form, $admin_param_form, $password_field, $admin_user_list, $cms_active, $admin_user_alert_row, $admin_user_link1, $admin_codstat_form, $location_map_tpl;
global $admin_location_form_sur_loc_part, $admin_location_form, $admin_section_form, $admin_statut_form, $admin_orinot_form, $admin_onglet_form, $admin_notice_usage_form;
global $admin_map_echelle_form, $admin_map_projection_form, $admin_map_ref_form, $admin_typdoc_form, $admin_lender_form, $admin_support_form, $admin_emplacement_form, $admin_categlec_form;
global $admin_statlec_form, $admin_empr_statut_form, $admin_proc_form, $admin_proc_view_remote, $admin_zbib_form, $admin_zattr_form, $admin_convert_end, $noimport, $n_errors, $errors_msg;
global $admin_calendrier_form, $admin_calendrier_form_mois_start, $admin_calendrier_form_mois_commentaire, $admin_calendrier_form_mois_end, $admin_notice_statut_form;
global $admin_collstate_statut_form, $admin_abonnements_periodicite_form, $admin_procs_clas_form, $admin_menu_infopages, $admin_infopages_form, $admin_group_form, $admin_menu_planificateur;
global $admin_menu_authorities, $admin_menu_opac_views, $admin_menu_cms_editorial, $admin_liste_jscript, $admin_docnum_statut_form, $admin_menu_loans, $admin_authorities_statut_form;
global $admin_menu_contact_form, $admin_menu_search_universes, $admin_menu_pnb, $admin_vignette_menu, $admin_composed_vedettes_menu;

if(!isset($file_in)) $file_in = '';
if(!isset($suffix)) $suffix = '';
if(!isset($mimetype)) $mimetype = '';
if(!isset($output)) $output = '';

// ---------------------------------------------------------------------------
//	$admin_menu_new : Menu vertical de l'administration
// ---------------------------------------------------------------------------

$admin_menu_new = "
<div id='menu'>
<h3 onclick='menuHide(this,event)'>$msg[7]</h3>
<ul>
	<li><a href='./admin.php?categ=docs'>".$msg['admin_menu_exemplaires']."</a></li>
	<li><a href='./admin.php?categ=notices'>".$msg['admin_menu_notices']."</a></li>
	<li><a href='./admin.php?categ=authorities'>".$msg['admin_menu_authorities']."</a></li>
	<li><a href='./admin.php?categ=docnum'>".$msg["admin_menu_upload_docnum"]."</a></li>
	<li><a href='./admin.php?categ=collstate'>".$msg['admin_etats_collections']."</a></li>
	<li><a href='./admin.php?categ=abonnements'>".$msg['admin_menu_abonnements']."</a></li>
	<li><a href='./admin.php?categ=empr'>$msg[22]</a></li>
	<li><a href='./admin.php?categ=users'>$msg[25]</a></li>
	<li><a href='./admin.php?categ=cms_editorial'>".$msg['editorial_content']."</a></li>
	<li><a href='./admin.php?categ=loans'>".$msg['admin_menu_loans']."</a></li>
	<li><a href='./admin.php?categ=pnb'>".$msg['admin_menu_pnb']."</a></li>
	<li><a href='./admin.php?categ=composed_vedettes&sub=grammars'>".$msg['admin_menu_composed_vedettes']."</a></li>
</ul>
<h3 onclick='menuHide(this,event)'>".$msg['opac_admin_menu']."</h3>
<ul>
	<li><a href='./admin.php?categ=infopages'>".$msg["infopages_admin_menu"]."</a></li>
	<li><a href='./admin.php?categ=opac&sub=search_persopac&section=liste'>".$msg["search_persopac_list_title"]."</a></li>
	<li><a href='./admin.php?categ=opac&sub=navigopac&action='>".$msg["exemplaire_admin_navigopac"]."</a></li>
	<li><a href='./admin.php?categ=opac&sub=facettes'>".$msg["opac_facette"]."</a></li>
	".($pmb_logs_activate?"<li><a href='./admin.php?categ=opac&sub=stat&section=view_list'>".$msg["stat_opac_menu"]."</a></li>":"")."
	".($opac_visionneuse_allow?"<li><a href='./admin.php?categ=visionneuse'>".$msg["visionneuse_admin_menu"]."</a></li>":"")."
	".($pmb_opac_view_activate?"<li><a href='./admin.php?categ=opac&sub=opac_view&section=list'>".$msg["opac_view_admin_menu"]."</a></li>":"")."
	<li><a href='./admin.php?categ=contact_form'>".$msg["admin_opac_contact_form"]."</a></li>
	<li><a href='./admin.php?categ=opac&sub=maintenance'>".$msg["admin_opac_maintenance"]."</a></li>
";
	if($opac_search_universes_activate){
		$admin_menu_new.="<li><a href='./admin.php?categ=search_universes'>".$msg['admin_menu_search_universes']."</a></li>";
	}

$admin_menu_new .="
</ul>
<h3 onclick='menuHide(this,event)'>".$msg['admin_menu_act']."</h3>
<ul>
	<li><a href='./admin.php?categ=proc&sub=proc&action='>".$msg['admin_menu_act_perso']."</a></li>
	<li><a href='./admin.php?categ=proc&sub=clas&action='>".$msg['admin_menu_act_perso_clas']."</a></li>
</ul>
";

if($pmb_nomenclature_activate)
	$admin_menu_new.="
	<h3 onclick='menuHide(this,event)'>".$msg['admin_menu_nomenclature']."</h3>
	<ul>
		<li><a href='./admin.php?categ=family&sub=family&action='>".$msg['admin_menu_nomenclature_tutti']."</a></li>
		<li><a href='./admin.php?categ=formation&sub=formation&action='>".$msg['admin_menu_nomenclature_formations']."</a></li>
		<li><a href='./admin.php?categ=voice&sub=voice&action='>".$msg['admin_menu_nomenclature_voice']."</a></li>
		<li><a href='./admin.php?categ=instrument&sub=instrument&action='>".$msg['admin_menu_nomenclature_instruments']."</a></li>
		<li><a href='./admin.php?categ=material&sub=material&action='>".$msg['admin_menu_nomenclature_material']."</a></li>
	</ul>";
$admin_menu_new.="
<h3 onclick='menuHide(this,event)'>".$msg['admin_menu_modules']."</h3>
<ul>
";

if ($pmb_quotas_avances) $admin_menu_new.="<li><a href='./admin.php?categ=quotas'>".$msg["admin_quotas"]."</a></li>";

if ($pmb_utiliser_calendrier) $admin_menu_new.="<li><a href='./admin.php?categ=calendrier'>".$msg["admin_calendrier"]."</a></li>";

if (($pmb_gestion_financiere)&&(($pmb_gestion_abonnement==2)||($pmb_gestion_tarif_prets==2)||($pmb_gestion_amende))) $admin_menu_new.="<li><a href='./admin.php?categ=finance'>".$msg["admin_gestion_financiere"]."</a></li>";

$admin_menu_new.="
	<li><a href='./admin.php?categ=import'>$msg[519]</a></li>
	<li><a href='./admin.php?categ=convert'>".$msg["admin_conversion"]."</a></li>
	<li><a href='./admin.php?categ=harvest'>".$msg["admin_harvest"]."</a></li>
	<li><a href='./admin.php?categ=misc'>$msg[27]</a></li>
	<li><a href='./admin.php?categ=z3950'>Z39.50</a></li>
	".($pmb_planificateur_allow?"<li><a href='./admin.php?categ=planificateur'>".$msg["planificateur_admin_menu"]."</a></li>":"")."
	<li><a href='./admin.php?categ=external_services'>".$msg["es_admin_menu"]."</a></li>
	".($pmb_allow_external_search?"<li><a href='./admin.php?categ=connecteurs'>".$msg["admin_connecteurs_menu"]."</a></li>":"")."
	".($pmb_selfservice_allow?"<li><a href='./admin.php?categ=selfservice'>".$msg["selfservice_admin_menu"]."</a></li>":"")."
	<li><a href='./admin.php?categ=sauvegarde'>$msg[28]</a></li>";

if ($acquisition_active) $admin_menu_new.="\n<li><a href='./admin.php?categ=acquisition'>".$msg["admin_acquisition"]."</a></li>";

//pour les tranferts
if ($pmb_transferts_actif) {
	$admin_menu_new.="\n<li><a href='./admin.php?categ=transferts'>".$msg["admin_menu_transferts"]."</a></li>";
}
if ($gestion_acces_active==1) {
	$admin_menu_new.="\n<li><a href='./admin.php?categ=acces'>".$msg["admin_menu_acces"]."</a></li>";
}
if ($pmb_javascript_office_editor) {
	$admin_menu_new.="\n<li><a href='./admin.php?categ=html_editor'>".$msg["admin_html_editor"]."</a></li>";
}
if($demandes_active) {
	$admin_menu_new.="\n<li><a href='./admin.php?categ=demandes'>".$msg["admin_demandes"]."</a></li>";
}
if($faq_active) {
	$admin_menu_new.="\n<li><a href='./admin.php?categ=faq'>".$msg["admin_faq"]."</a></li>";
}

$admin_menu_new.="
	<li><a href='./admin.php?categ=mailtpl'>".$msg["admin_mailtpl"]."</a></li>";
if($pmb_scan_request_activate) {
	$admin_menu_new.="<li><a href='./admin.php?categ=scan_request'>".$msg['admin_menu_scan_request']."</a></li>";
}
$admin_menu_new.="</ul>";
$plugins = plugins::get_instance();
$admin_menu_new.=$plugins->get_menu('admin')."</div>";


// ---------------------------------------------------------------------------
//		Menus horizontaux : sous-onglets
// ---------------------------------------------------------------------------
// $admin_menu_docs : menu Exemplaires

$sur_location_menu="";
if($pmb_sur_location_activate)
$sur_location_menu="
	<span".ongletSelect("categ=docs&sub=sur_location").">
		<a title='".$msg["sur_location_admin_menu_title"]."' href='./admin.php?categ=docs&sub=sur_location&action='>
			".$msg["sur_location_admin_menu"]."
		</a>
	</span>";
			
$admin_menu_docs = "
<h1>$msg[admin_menu_exemplaires] <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=docs&sub=typdoc").">
		<a title='$msg[724]' href='./admin.php?categ=docs&sub=typdoc&action='>
			$msg[admin_menu_docs_type]
		</a>
	</span>
	<span".ongletSelect("categ=docs&sub=location").">
		<a title='$msg[728]' href='./admin.php?categ=docs&sub=location&action='>
			$msg[21]
		</a>
	</span>
	$sur_location_menu
	<span".ongletSelect("categ=docs&sub=section").">
		<a title='$msg[726]' href='./admin.php?categ=docs&sub=section&action='>
			$msg[19]
		</a>
	</span>
	<span".ongletSelect("categ=docs&sub=statut").">
		<a title='$msg[727]' href='./admin.php?categ=docs&sub=statut&action='>
			$msg[20]
		</a>
	</span>
	<span".ongletSelect("categ=docs&sub=codstat").">
		<a title='$msg[725]' href='./admin.php?categ=docs&sub=codstat&action='>
			$msg[24]
		</a>
	</span>
	<span".ongletSelect("categ=docs&sub=lenders").">
		<a title='$msg[732]' href='./admin.php?categ=docs&sub=lenders&action='>
			$msg[554]
		</a>
	</span>
	<span".ongletSelect("categ=docs&sub=perso").">
		<a title='$msg[admin_menu_docs_perso]' href='./admin.php?categ=docs&sub=perso&action='>
			$msg[admin_menu_docs_perso]
		</a>
	</span>
</div>
";

// $admin_menu_notices : menu Notices
if($pmb_map_activate) {
	$admin_menu_noti_onglet="
		<span".ongletSelect("categ=notices&sub=map_echelle").">
			<a title='$msg[admin_menu_noti_statut]' href='./admin.php?categ=notices&sub=map_echelle&action='>
				$msg[admin_menu_noti_map_echelle]
			</a>
		</span>
		<span".ongletSelect("categ=notices&sub=map_projection").">
			<a title='$msg[admin_menu_noti_statut]' href='./admin.php?categ=notices&sub=map_projection&action='>
				$msg[admin_menu_noti_map_projection]
			</a>
		</span>
		<span".ongletSelect("categ=notices&sub=map_ref").">
			<a title='$msg[admin_menu_noti_statut]' href='./admin.php?categ=notices&sub=map_ref&action='>
				$msg[admin_menu_noti_map_ref]
			</a>
		</span>
	";
} else {
	$admin_menu_noti_onglet="";
}
$admin_menu_notices = "
<h1>$msg[admin_menu_notices] <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=notices&sub=orinot").">
		<a title='$msg[orinot_origine]' href='./admin.php?categ=notices&sub=orinot&action='>
			$msg[orinot_origine_short]
		</a>
	</span>
	<span".ongletSelect("categ=notices&sub=statut").">
		<a title='$msg[admin_menu_noti_statut]' href='./admin.php?categ=notices&sub=statut&action='>
			$msg[admin_menu_noti_statut]
		</a>
	</span>
	$admin_menu_noti_onglet
	<span".ongletSelect("categ=notices&sub=perso").">
		<a title='$msg[admin_menu_noti_perso]' href='./admin.php?categ=notices&sub=perso&action='>
			$msg[admin_menu_noti_perso]
		</a>
	</span>
	<span".ongletSelect("categ=notices&sub=onglet").">
		<a title='$msg[admin_menu_noti_onglet_title]' href='./admin.php?categ=notices&sub=onglet&action='>
			$msg[admin_menu_noti_onglet]
		</a>
	</span>
	<span".ongletSelect("categ=notices&sub=notice_usage").">
		<a title='$msg[admin_menu_notice_usage]' href='./admin.php?categ=notices&sub=notice_usage&action='>
			$msg[admin_menu_notice_usage]
		</a>
	</span>
</div>
";

// $admin_menu_notices : menu Etats des collections
$admin_menu_collstate = "
<h1>".$msg["admin_menu_collstate"]." <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=notices&sub=emplacement").">
		<a title='".$msg["admin_menu_collstate_emplacement"]."' href='./admin.php?categ=collstate&sub=emplacement&action='>
			".$msg["admin_menu_collstate_emplacement"]."
		</a>
	</span>
	<span".ongletSelect("categ=notices&sub=support").">
		<a title='".$msg["admin_menu_collstate_support"]."' href='./admin.php?categ=collstate&sub=support&action='>
			".$msg["admin_menu_collstate_support"]."
		</a>
	</span>
	<span".ongletSelect("categ=notices&sub=statut").">
		<a title='".$msg["admin_menu_collstate_statut"]."' href='./admin.php?categ=collstate&sub=statut&action='>
			".$msg["admin_menu_collstate_statut"]."
		</a>
	</span>
	<span".ongletSelect("categ=notices&sub=perso").">
		<a title='$msg[admin_menu_collstate_perso]' href='./admin.php?categ=collstate&sub=perso&action='>
			".$msg["admin_collstate_collstate_perso"]."
		</a>
	</span>
</div>
";
		
$admin_menu_search_persopac = "
<h1>".$msg["admin_menu_search_persopac"]." <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=search_persopac&sub=liste").">
		<a title='".$msg["search_persopac_list_title"]."' href='./admin.php?categ=search_persopac&sub=liste&action='>
			".$msg["search_persopac_list_title"]."
		</a>
	</span>

</div>
";

//Menu opac en gestion
$admin_menu_opac = "
<h1><span>".$msg['admin_menu_opac']." > !!menu_sous_rub!!</span></h1>";

// vues
$admin_menu_opac_view = "
<h1>".$msg["opac_view_admin_menu"]." <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=opac_view&sub=list").">
		<a title='".$msg["opac_view_list_title"]."' href='./admin.php?categ=opac_view&sub=list&action='>
			".$msg["opac_view_list_title"]."
		</a>
	</span>
</div>
";

// $admin_menu_abonnements : menu Abonnements
$admin_menu_abonnements = "
<h1>$msg[admin_menu_abonnements] <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=abonnements&sub=periodicite").">
		<a title='$msg[admin_menu_abonnements_periodicite]' href='./admin.php?categ=abonnements&sub=periodicite&action='>
			$msg[admin_menu_abonnements_periodicite]
		</a>
	</span>
	<span".ongletSelect("categ=abonnements&sub=status").">
		<a title='".$msg['admin_menu_abonnements_status']."' href='./admin.php?categ=abonnements&sub=status&action='>
			".$msg['admin_menu_abonnements_status']."
		</a>
	</span>
</div>
";

// $admin_menu_empr : menu Lecteurs
// show ldap_import only if $ldap_accessible=1
$admin_menu_empr = "
<h1>$msg[22] <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=empr&sub=categ&action").">
		<a title='$msg[729]' href='./admin.php?categ=empr&sub=categ&action='>
			".$msg["lecteurs_categories"]."
		</a>
	</span>
	<span".ongletSelect("categ=empr&sub=statut&action").">
		<a title='$msg[empr_statut_menu]' href='./admin.php?categ=empr&sub=statut&action='>
			$msg[empr_statut_menu]
		</a>
	</span>
	<span".ongletSelect("categ=empr&sub=codstat&action").">
		<a title='$msg[730]' href='./admin.php?categ=empr&sub=codstat&action='>
			$msg[24]
		</a>
	</span>
	<span".ongletSelect("categ=empr&sub=implec").">
		<a title='$msg[import_lec_alt]' href='./admin.php?categ=empr&sub=implec&action='>
			$msg[import_lec_lien]
		</a>
	</span>
";
if ($ldap_accessible) $admin_menu_empr .= "
	<span".ongletSelect("categ=empr&sub=ldap").">
		<a title='$msg[import_ldap]' href='./admin.php?categ=empr&sub=ldap&action='>
			$msg[import_ldap]
		</a>
	</span>";
if ($ldap_accessible) $admin_menu_empr .= "
	<span".ongletSelect("categ=empr&sub=exldap").">
		<a title='$msg[menu_suppr_exldap]' href='./admin.php?categ=empr&sub=exldap&action='>
			$msg[menu_suppr_exldap]
		</a>
	</span>";
$admin_menu_empr .= "
	<span".ongletSelect("categ=empr&sub=parperso").">
		<a title='$msg[parametres_perso_lec_alt]' href='./admin.php?categ=empr&sub=parperso&action='>
			$msg[parametres_perso_lec_lien]
		</a>
	</span>
	<span".ongletSelect("categ=empr&sub=renewal_form").">
		<a title='".$msg['empr_renewal_form']."' href='./admin.php?categ=empr&sub=renewal_form&action='>
			".$msg['empr_renewal_form']."
		</a>
	</span>
</div>";

// $admin_menu_users : menu Utilisateurs
$admin_menu_users = "
<h1>$msg[25] <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=users&sub=users").">
		<a title='$msg[731]' href='./admin.php?categ=users&sub=users&action='>
			$msg[26]
		</a>
	</span>
	<span".ongletSelect("categ=users&sub=groups").">
		<a title='$msg[731]' href='./admin.php?categ=users&sub=groups&action='>
			$msg[admin_usr_grp_ges]
		</a>
	</span>
	</div>
";

// $admin_menu_import : menu Imports
$admin_menu_import = "
<h1>$msg[519] <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=import&sub=import").">
		<a title='$msg[733]' href='./admin.php?categ=import&sub=import&action='>
			$msg[500]
		</a>
	</span>
	<span".ongletSelect("categ=import&sub=import_expl").">
		<a title='$msg[734]' href='./admin.php?categ=import&sub=import_expl&action='>
			$msg[520]
		</a>
	</span>
	<span".ongletSelect("categ=import&sub=pointage_expl").">
		<a href='./admin.php?categ=import&sub=pointage_expl&action='>
			".$msg[569]."
		</a>
	</span>
	<span".ongletSelect("categ=import&sub=import_skos").">
		<a href='./admin.php?categ=import&sub=import_skos&action='>
			".$msg["ontology_skos_admin_import"]."
		</a>
	</span>
</div>
";

// $admin_menu_convert : menu Outils de Conversion/Export de formats
$admin_menu_convert = "
<h1>$msg[admin_conversion] <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=convert&sub=import").">
		<a title='$msg[admin_convExterne]' href='./admin.php?categ=convert&sub=import&action='>
			$msg[admin_convExterne]
		</a>
	</span>
	<span".ongletSelect("categ=convert&sub=export").">
		<a title='$msg[admin_ExportPMB]' href='./admin.php?categ=convert&sub=export&action='>
			$msg[admin_ExportPMB]
		</a>
	</span>
	<span".ongletSelect("categ=convert&sub=paramgestion").">
		<a title='".htmlentities($msg['admin_param_export_gestion'],ENT_QUOTES,$charset)."' href='./admin.php?categ=convert&sub=paramgestion&action='>
			$msg[admin_param_export_gestion]
		</a>
	</span>
	<span".ongletSelect("categ=convert&sub=paramopac").">
		<a title='".htmlentities($msg['admin_param_export_opac'],ENT_QUOTES,$charset)."' href='./admin.php?categ=convert&sub=paramopac&action='>
			$msg[admin_param_export_opac]
		</a>
	</span>
</div>
";
			
$admin_menu_harvest = "
<h1>$msg[admin_harvest] <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=harvest&sub=build").">
		<a title='$msg[admin_harvest_build_menu]' href='./admin.php?categ=harvest&sub=build&action='>
			$msg[admin_harvest_build_menu]
		</a>
	</span>
	<span".ongletSelect("categ=harvest&sub=profil").">
		<a title='$msg[admin_harvest_profil_title]' href='./admin.php?categ=harvest&sub=profil&action='>
			$msg[admin_harvest_profil_title]
		</a>
	</span>

</div>
";
			
$admin_menu_mailtpl = "
<h1>$msg[admin_mailtpl] <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=mailtpl&sub=build").">
		<a title='$msg[admin_mailtpl_menu]' href='./admin.php?categ=mailtpl&sub=build&action='>
			$msg[admin_mailtpl_menu]
		</a>
	</span>
	<span".ongletSelect("categ=mailtpl&sub=print_cart").">
		<a title='".$msg['admin_print_cart_tpl_menu']."' href='./admin.php?categ=mailtpl&sub=print_cart_tpl&action='>
			".$msg['admin_print_cart_tpl_menu']."
		</a>
	</span>
	<span".ongletSelect("categ=mailtpl&sub=img").">
		<a title='$msg[admin_mailtpl_img_menu]' href='./admin.php?categ=mailtpl&sub=img&action='>
			$msg[admin_mailtpl_img_menu]
		</a>
	</span>
</div>
";
			
// $admin_menu_misc : menu Outils
$admin_menu_misc = "
<h1>$msg[27] <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=netbase").">
		<a title='$msg[735]' href='./admin.php?categ=netbase'>
			$msg[329]
		</a>
	</span>
	<span".ongletSelect("categ=chklnk").">
		<a title='".$msg['chklnk_titre']."' href='./admin.php?categ=chklnk'>
			".$msg['chklnk_titre']."
		</a>
	</span>
	<span".ongletSelect("categ=alter&sub=").">
		<a title='$msg[740]' href='./admin.php?categ=alter&sub='>
			$msg[1801]
		</a>
	</span>
	<span".ongletSelect("categ=misc&sub=tables").">
		<a title='$msg[740]' href='./admin.php?categ=misc&sub=tables'>
			$msg[31]
		</a>
	</span>
	<span".ongletSelect("categ=misc&sub=mysql").">
		<a title='$msg[741]' href='./admin.php?categ=misc&sub=mysql&action='>
			$msg[32]
		</a>
	</span>
	".($PMBuserid == 1 ? "
		<span".ongletSelect("categ=misc&sub=files").">
			<a title='".$msg['files']."' href='./admin.php?categ=misc&sub=files&action='>
				".$msg['files']."
			</a>
		</span>" : 
	""
	)."<span".ongletSelect("categ=param").">
		<a title='$msg[1600]' href='./admin.php?categ=param&action='>
			$msg[1600]
		</a>
	</span>
</div>
";

// $admin_menu_z3950 : menu z39.50
$admin_menu_z3950 = "
<h1>Z39.50 <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=z3950&sub=zbib").">
		<a title='".$msg["z3950_menu_admin_title"]."' href='./admin.php?categ=z3950&sub=zbib'>
			".$msg["z3950_serveurs"]."
		</a>
	</span>
</div>
";

// $admin_menu_sauvegarde : menu Sauvegarde
$admin_menu_sauvegarde = "
<h1>$msg[28] <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=sauvegarde&sub=lieux").">
		<a title='$msg[sauv_menu_lieux_c]' href='./admin.php?categ=sauvegarde&sub=lieux'>
			$msg[sauv_menu_lieux]
		</a>
	</span>
	<span".ongletSelect("categ=sauvegarde&sub=tables").">
		<a title='$msg[sauv_menu_tables_c]' href='./admin.php?categ=sauvegarde&sub=tables'>
			$msg[sauv_menu_tables]
		</a>
	</span>
	<span".ongletSelect("categ=sauvegarde&sub=gestsauv").">
		<a title='$msg[sauv_menu_jeux_c]' href='./admin.php?categ=sauvegarde&sub=gestsauv'>
			$msg[sauv_menu_jeux]
		</a>
	</span>
	<span".ongletSelect("categ=sauvegarde&sub=launch").">
		<a title='$msg[sauv_menu_launch_c]' href='./admin.php?categ=sauvegarde&sub=launch'>
			$msg[sauv_menu_launch]
		</a>
	</span>
	<span".ongletSelect("categ=sauvegarde&sub=list").">
		<a title='$msg[sauv_menu_liste_c]' href='./admin.php?categ=sauvegarde&sub=list'>
			$msg[sauv_menu_liste]
		</a>
	</span>
</div>
";

// $admin_menu_calendrier : menu Calendrier
$admin_menu_calendrier = "
<h1>".$msg["admin_calendrier"]." <span>> !!menu_sous_rub!!</span></h1>";

// $admin_menu_finance : menu Gestion financiere
$admin_menu_finance = "
<h1>".$msg["admin_gestion_financiere"]." <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>";
if (($pmb_gestion_financiere)&&($pmb_gestion_abonnement==2)) 
	$admin_menu_finance.="
		<span".ongletSelect("categ=finance&sub=abts").">
			<a title='".$msg["finance_abts"]."' href='./admin.php?categ=finance&sub=abts'>
				".$msg["finance_abts"]."
			</a>
		</span>
	";
if (($pmb_gestion_financiere)&&($pmb_gestion_tarif_prets==2)) 
	$admin_menu_finance.="
	<span".ongletSelect("categ=finance&sub=prets").">
		<a title='".$msg["finance_prets"]."' href='./admin.php?categ=finance&sub=prets'>
			".$msg["finance_prets"]."
		</a>
	</span>
	";

if (($pmb_gestion_financiere)&&($pmb_gestion_amende))
	$admin_menu_finance.="
	<span".ongletSelect("categ=finance&sub=amendes").">
		<a title='".$msg["finance_amendes"]."' href='./admin.php?categ=finance&sub=amendes'>
			".$msg["finance_amendes"]."
		</a>
	</span>
	<span".ongletSelect("categ=finance&sub=amendes_relance").">
		<a title='".$msg["finance_amendes_relances"]."' href='./admin.php?categ=finance&sub=amendes_relance'>
			".$msg["finance_amendes_relances"]."
		</a>
	</span>
";

$admin_menu_finance.="
	<span".ongletSelect("categ=finance&sub=blocage").">
		<a title='".$msg["finance_blocage"]."' href='./admin.php?categ=finance&sub=blocage'>
			".$msg["finance_blocage"]."
		</a>
	</span>
";

if (($pmb_gestion_financiere))
$admin_menu_finance.="
	<span".ongletSelect("categ=finance&sub=transactype").">
		<a title='".$msg["transaction_admin"]."' href='./admin.php?categ=finance&sub=transactype'>
			".$msg["transaction_admin"]."
		</a>
	</span>	
    <span".ongletSelect("categ=finance&sub=transaction_payment_method").">
		<a title='".$msg["transaction_payment_method_admin"]."' href='./admin.php?categ=finance&sub=transaction_payment_method'>
			".$msg["transaction_payment_method_admin"]."
		</a>
	</span>";

if (($pmb_gestion_financiere)&&($pmb_gestion_financiere_caisses))
	$admin_menu_finance.="
	<span".ongletSelect("categ=finance&sub=cashdesk").">
		<a title='".$msg["cashdesk_admin"]."' href='./admin.php?categ=finance&sub=cashdesk'>
			".$msg["cashdesk_admin"]."
		</a>
	</span>";

$admin_menu_finance.="
</div>";

// $admin_menu_acquisition : menu Acquisition
$admin_menu_acquisition = "
<h1>$msg[acquisition_menu] <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=acquisition&sub=entite").">
		<a title='$msg[acquisition_menu_ref_entite]' href='./admin.php?categ=acquisition&sub=entite'>
			$msg[acquisition_menu_ref_entite]
		</a>
	</span>
	<span".ongletSelect("categ=acquisition&sub=compta").">
		<a title='$msg[acquisition_menu_ref_compta]' href='./admin.php?categ=acquisition&sub=compta'>
			$msg[acquisition_menu_ref_compta]
		</a>
	</span>
";

//Pas d'affichage de la tva sur achats si on ne la gere pas 
if ($acquisition_gestion_tva) $admin_menu_acquisition.= "
	<span".ongletSelect("categ=acquisition&sub=tva").">
		<a title='$msg[acquisition_menu_ref_tva]' href='./admin.php?categ=acquisition&sub=tva'>
			$msg[acquisition_menu_ref_tva]
		</a>
	</span>
";
$admin_menu_acquisition.= "
	<span".ongletSelect("categ=acquisition&sub=type").">
		<a title='$msg[acquisition_menu_ref_type]' href='./admin.php?categ=acquisition&sub=type'>
			$msg[acquisition_menu_ref_type]
		</a>
	</span>
	<span".ongletSelect("categ=acquisition&sub=frais").">
		<a title='$msg[acquisition_menu_ref_frais]' href='./admin.php?categ=acquisition&sub=frais'>
			$msg[acquisition_menu_ref_frais]
		</a>
	</span>
	<span".ongletSelect("categ=acquisition&sub=mode").">
		<a title='$msg[acquisition_menu_ref_mode]' href='./admin.php?categ=acquisition&sub=mode'>
			$msg[acquisition_menu_ref_mode]
		</a>
	</span>
	<span".ongletSelect("categ=acquisition&sub=budget").">
		<a title='$msg[acquisition_menu_ref_budget]' href='./admin.php?categ=acquisition&sub=budget'>
			$msg[acquisition_menu_ref_budget]
		</a>
	</span>
";
if($acquisition_sugg_categ=='1') $admin_menu_acquisition.= "
	<span".ongletSelect("categ=acquisition&sub=categ").">
		<a title='$msg[acquisition_menu_ref_categ]' href='./admin.php?categ=acquisition&sub=categ'>
			$msg[acquisition_menu_ref_categ]
		</a>
	</span>";
$admin_menu_acquisition.= "
	<span".ongletSelect("categ=acquisition&sub=src").">
		<a title='$msg[acquisition_menu_ref_src]' href='./admin.php?categ=acquisition&sub=src'>
			$msg[acquisition_menu_ref_src]
		</a>
	</span>";
$admin_menu_acquisition.= "
	<span".ongletSelect("categ=acquisition&sub=lgstat").">
		<a title='".htmlentities($msg['acquisition_menu_ref_lgstat'],ENT_QUOTES,$charset)."' href='./admin.php?categ=acquisition&sub=lgstat'>"
			.htmlentities($msg['acquisition_menu_ref_lgstat'],ENT_QUOTES,$charset)."
		</a>
	</span>";	
$admin_menu_acquisition.= "
	<span".ongletSelect("categ=acquisition&sub=pricing_systems").">
		<a title='".htmlentities($msg['acquisition_menu_pricing_systems'],ENT_QUOTES,$charset)."' href='./admin.php?categ=acquisition&sub=pricing_systems'>"
			.htmlentities($msg['acquisition_menu_pricing_systems'],ENT_QUOTES,$charset)."
		</a>
	</span>";
$admin_menu_acquisition.= "
	<span".ongletSelect("categ=acquisition&sub=account_types").">
		<a title='".htmlentities($msg['acquisition_menu_account_types'],ENT_QUOTES,$charset)."' href='./admin.php?categ=acquisition&sub=account_types'>"
				.htmlentities($msg['acquisition_menu_account_types'],ENT_QUOTES,$charset)."
		</a>
	</span>";				
$admin_menu_acquisition.= "
	<span".ongletSelect("categ=acquisition&sub=thresholds").">
		<a title='".htmlentities($msg['acquisition_menu_thresholds'],ENT_QUOTES,$charset)."' href='./admin.php?categ=acquisition&sub=thresholds'>"
				.htmlentities($msg['acquisition_menu_thresholds'],ENT_QUOTES,$charset)."
		</a>
	</span>";
$admin_menu_acquisition.= "
</div>
";

//Services Externes
$admin_menu_external_services = "
<h1>".$msg["es_admin_menu"]." <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=external_services&sub=general").">
		<a title='".$msg["es_admin_general"]."' href='./admin.php?categ=external_services&sub=general'>
			".$msg["es_admin_general"]."
		</a>
	</span>
	<span".ongletSelect("categ=external_services&sub=peruser").">
		<a title='".$msg["es_admin_peruser"]."' href='./admin.php?categ=external_services&sub=peruser'>
			".$msg["es_admin_peruser"]."
		</a>
	</span>
	<span".ongletSelect("categ=external_services&sub=esusers").">
		<a title='".$msg["es_admin_esusers"]."' href='./admin.php?categ=external_services&sub=esusers'>
			".$msg["es_admin_esusers"]."
		</a>
	</span>
	<span".ongletSelect("categ=external_services&sub=esusergroups").">
		<a title='".$msg["es_admin_esusergroups"]."' href='./admin.php?categ=external_services&sub=esusergroups'>
			".$msg["es_admin_esusergroups"]."
		</a>
	</span>
	<!--<span".ongletSelect("categ=external_services&sub=es_tests").">
		<a title='Tests' href='./admin.php?categ=external_services&sub=es_tests'>
			Tests
		</a>
	</span>-->
</div>";

//Connecteurs pour web services
$admin_menu_connecteurs = "
<h1>".$msg["admin_menu_connecteurs"]." <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=connecteurs&sub=in").">
		<a title='".$msg["admin_connecteurs_in"]."' href='./admin.php?categ=connecteurs&sub=in'>
			".$msg["admin_connecteurs_in"]."
		</a>
	</span>
	<span".ongletSelect("categ=connecteurs&sub=categ").">
		<a title='".$msg["admin_connecteurs_categ"]."' href='./admin.php?categ=connecteurs&sub=categ'>
			".$msg["admin_connecteurs_categ"]."
		</a>
	</span>
	<span".ongletSelect("categ=connecteurs&sub=out").">
		<a title='".$msg["admin_connecteurs_out"]."' href='./admin.php?categ=connecteurs&sub=out'>
			".$msg["admin_connecteurs_out"]."
		</a>
	</span>
	<span".ongletSelect("categ=connecteurs&sub=out_auth").">
		<a title='".$msg["admin_connecteurs_outauth"]."' href='./admin.php?categ=connecteurs&sub=out_auth'>
			".$msg["admin_connecteurs_outauth"]."
		</a>
	</span>
	<span".ongletSelect("categ=connecteurs&sub=out_sets").">
		<a title='".$msg["admin_connecteurs_sets"]."' href='./admin.php?categ=connecteurs&sub=out_sets'>
			".$msg["admin_connecteurs_sets"]."
		</a>
	</span>
	<span".ongletSelect("categ=connecteurs&sub=categout_sets").">
		<a title='".$msg["admin_connecteurs_categsets"]."' href='./admin.php?categ=connecteurs&sub=categout_sets'>
			".$msg["admin_connecteurs_categsets"]."
		</a>
	</span>
	<span".ongletSelect("categ=connecteurs&sub=enrichment").">
		<a title='".$msg["admin_connecteurs_enrichment"]."' href='./admin.php?categ=connecteurs&sub=enrichment'>
			".$msg["admin_connecteurs_enrichment"]."
		</a>
	</span>
</div>";

//Borne de prêt 
$admin_menu_selfservice = "
<h1>".$msg["selfservice_admin_menu"]." <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=selfservice&sub=pret").">
		<a title='".$msg["selfservice_admin_pret"]."' href='./admin.php?categ=selfservice&sub=pret'>
			".$msg["selfservice_admin_pret"]."
		</a>
	</span>
	<span".ongletSelect("categ=selfservice&sub=retour").">
		<a title='".$msg["selfservice_admin_retour"]."' href='./admin.php?categ=selfservice&sub=retour'>
			".$msg["selfservice_admin_retour"]."
		</a>
	</span>
</div>";

//Visionneuse
$admin_menu_visionneuse = "
<h1>".$msg["admin_menu_opac"]." > ".$msg["visionneuse_admin_menu"]."<span> > !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=visionneuse&sub=class").">
		<a title='".$msg["visionneuse_admin_class"]."' href='./admin.php?categ=visionneuse&sub=class'>
			".$msg["visionneuse_admin_class"]."
		</a>
	</span>
	<span".ongletSelect("categ=visionneuse&sub=mimetype").">
		<a title='".$msg["visionneuse_admin_mimetype"]."' href='./admin.php?categ=visionneuse&sub=mimetype'>
			".$msg["visionneuse_admin_mimetype"]."
		</a>
	</span>
</div>";

// Menus pour actions perso
$admin_menu_act = "
<h1>".$msg["admin_menu_act"]." > !!menu_sous_rub!!</h1>";



//Menus pour les transferts
$admin_menu_transferts = "
<h1>".$msg["admin_menu_transferts"]." <span>> !!menu_sous_rub!!</span></h1>
<div class=\"hmenu\">
	<span".ongletSelect("categ=transferts&sub=general").">
		<a title='".$msg["admin_tranferts_general"]."' href='./admin.php?categ=transferts&sub=general'>
			".$msg["admin_tranferts_general"]."
		</a>
	</span>
	<span".ongletSelect("categ=transferts&sub=circ").">
		<a title='".$msg["admin_tranferts_circ"]."' href='./admin.php?categ=transferts&sub=circ'>
			".$msg["admin_tranferts_circ"]."
		</a>
	</span>
	<span".ongletSelect("categ=transferts&sub=opac").">
		<a title='".$msg["admin_tranferts_opac"]."' href='./admin.php?categ=transferts&sub=opac'>
			".$msg["admin_tranferts_opac"]."
		</a>
	</span>
	<span".ongletSelect("categ=transferts&sub=ordreloc").">
		<a title='".$msg["admin_tranferts_ordre_localisation"]."' href='./admin.php?categ=transferts&sub=ordreloc'>
			".$msg["admin_tranferts_ordre_localisation"]."
		</a>
	</span>
	<span".ongletSelect("categ=transferts&sub=statutsdef").">
		<a title='".$msg["admin_tranferts_statuts_defaut"]."' href='./admin.php?categ=transferts&sub=statutsdef'>
			".$msg["admin_tranferts_statuts_defaut"]."
		</a>
	</span>
	<span".ongletSelect("categ=transferts&sub=purge").">
		<a title='".$msg["admin_tranferts_purge"]."' href='./admin.php?categ=transferts&sub=purge'>
		".$msg["admin_tranferts_purge"]."
		</a>
	</span>
</div>
";

//$admin_menu_upload_docnum = upload des documents numériques
$admin_menu_upload_docnum ="
<h1>".$msg["admin_menu_upload_docnum"]." <span>> !!menu_sous_rub!!</span></h1>
<div class=\"hmenu\">
	<span".ongletSelect("categ=docnum&sub=rep").">
		<a title='".htmlentities($msg["upload_repertoire"],ENT_QUOTES,$charset)."' href='./admin.php?categ=docnum&sub=rep'>
			".$msg["upload_repertoire"]."
		</a>
	</span>
	<span".ongletSelect("categ=docnum&sub=storages").">
		<a title='".htmlentities($msg["storage_menu"],ENT_QUOTES,$charset)."' href='./admin.php?categ=docnum&sub=storages'>
			".$msg["storage_menu"]."
		</a>
	</span>
	<span".ongletSelect("categ=docnum&sub=statut").">
		<a title='".htmlentities($msg["admin_menu_docnum_statut"],ENT_QUOTES,$charset)."' href='./admin.php?categ=docnum&sub=statut'>
			".$msg["admin_menu_docnum_statut"]."
		</a>
	</span>
	<span".ongletSelect("categ=docnum&sub=perso").">
		<a title='".htmlentities($msg["admin_menu_noti_perso"],ENT_QUOTES,$charset)."' href='./admin.php?categ=docnum&sub=perso'>
			".$msg["admin_menu_noti_perso"]."
		</a>
	</span>
	<span".ongletSelect("categ=docnum&sub=licence").">
		<a title='".htmlentities($msg["admin_menu_noti_licence"],ENT_QUOTES,$charset)."' href='./admin.php?categ=docnum&sub=licence'>
			".$msg["admin_menu_noti_licence"]."
		</a>
	</span>
</div>";

//$admin_menu_demandes = demandes de recherche
$admin_menu_demandes ="
<h1>".$msg["admin_demandes"]." <span>> !!menu_sous_rub!!</span></h1>
<div class=\"hmenu\">
	<span".ongletSelect("categ=demandes&sub=theme").">
		<a title='".htmlentities($msg["demandes_theme"],ENT_QUOTES,$charset)."' href='./admin.php?categ=demandes&sub=theme'>
			".$msg["demandes_theme"]."
		</a>
	</span>
	<span".ongletSelect("categ=demandes&sub=type").">
		<a title='".htmlentities($msg["demandes_type"],ENT_QUOTES,$charset)."' href='./admin.php?categ=demandes&sub=type'>
			".$msg["demandes_type"]."
		</a>
	</span>
	<span".ongletSelect("categ=demandes&sub=perso").">
		<a title='".htmlentities($msg["admin_menu_demandes_perso"],ENT_QUOTES,$charset)."' href='./admin.php?categ=demandes&sub=perso'>
			".$msg["admin_menu_demandes_perso"]."
		</a>
	</span>
</div>";

//$admin_menu_faq = FAQ
$admin_menu_faq ="
<h1>".$msg["admin_faq"]." <span>> !!menu_sous_rub!!</span></h1>
<div class=\"hmenu\">
	<span".ongletSelect("categ=faq&sub=theme").">
		<a title='".htmlentities($msg["faq_theme"],ENT_QUOTES,$charset)."' href='./admin.php?categ=faq&sub=theme'>
			".$msg["faq_theme"]."
		</a>
	</span>
	<span".ongletSelect("categ=faq&sub=type").">
		<a title='".htmlentities($msg["faq_type"],ENT_QUOTES,$charset)."' href='./admin.php?categ=faq&sub=type'>
			".$msg["faq_type"]."
		</a>
	</span>
</div>";

//$admin_menu_nomenclature = nomenclature
$admin_menu_nomenclature ="
<h1>".$msg["admin_nomenclature"]." <span>> !!menu_sous_rub!!</span></h1>
<div class=\"hmenu\">
	<span".ongletSelect("categ=family&sub=family").">
		<a title='".htmlentities($msg["admin_nomenclature_family"],ENT_QUOTES,$charset)."' href='./admin.php?categ=family&sub=family'>
			".$msg["admin_nomenclature_family"]."
		</a>
	</span>
</div>";

//$admin_menu_formation = formation
$admin_menu_formation ="
<h1>".$msg["admin_nomenclature"]." <span>> !!menu_sous_rub!!</span></h1>
<div class=\"hmenu\">
	<span".ongletSelect("categ=formation&sub=formation").">
		<a title='".htmlentities($msg["admin_nomenclature_formation"],ENT_QUOTES,$charset)."' href='./admin.php?categ=formation&sub=formation'>
			".$msg["admin_nomenclature_formation"]."
		</a>
	</span>
</div>";

//$admin_menu_voice = voice
$admin_menu_voice ="
<h1>".$msg["admin_nomenclature"]." <span>> !!menu_sous_rub!!</span></h1>
<div class=\"hmenu\">
	<span".ongletSelect("categ=voice&sub=voice").">
		<a title='".htmlentities($msg["admin_nomenclature_voice"],ENT_QUOTES,$charset)."' href='./admin.php?categ=voice&sub=voice'>
			".$msg["admin_nomenclature_voice"]."
		</a>
	</span>
</div>";

//$admin_menu_instrument = instrument
$admin_menu_instrument ="
<h1>".$msg["admin_nomenclature"]." <span>> !!menu_sous_rub!!</span></h1>
<div class=\"hmenu\">
	<span".ongletSelect("categ=instrument&sub=instrument").">
		<a title='".htmlentities($msg["admin_nomenclature_instrument"],ENT_QUOTES,$charset)."' href='./admin.php?categ=instrument&sub=instrument'>
			".$msg["admin_nomenclature_instrument"]."
		</a>
	</span>
</div>";

//$admin_menu_material = material
$admin_menu_material ="
<h1>".$msg["admin_nomenclature"]." <span>> !!menu_sous_rub!!</span></h1>
<div class=\"hmenu\">
	<span".ongletSelect("categ=material&sub=material").">
		<a title='".htmlentities($msg["admin_nomenclature_material"],ENT_QUOTES,$charset)."' href='./admin.php?categ=material&sub=material'>
			".$msg["admin_nomenclature_material"]."
		</a>
	</span>
</div>";

$admin_menu_scan_request ="
<h1>".$msg["admin_scan_request"]." <span>> !!menu_sous_rub!!</span></h1>
<div class=\"hmenu\">
	<span".ongletSelect("categ=scan_request&sub=status").">
		<a title='".htmlentities($msg["admin_scan_request_status"],ENT_QUOTES,$charset)."' href='./admin.php?categ=scan_request&sub=status'>
			".$msg["admin_scan_request_status"]."
		</a>
	</span>
	<span".ongletSelect("categ=scan_request&sub=workflow").">
		<a title='".htmlentities($msg["admin_scan_request_workflow"],ENT_QUOTES,$charset)."' href='./admin.php?categ=scan_request&sub=workflow'>
			".$msg["admin_scan_request_workflow"]."
		</a>
	</span>
	<span".ongletSelect("categ=scan_request&sub=priorities").">
		<a title='".htmlentities($msg["admin_scan_request_priorities"],ENT_QUOTES,$charset)."' href='./admin.php?categ=scan_request&sub=priorities'>
			".$msg["admin_scan_request_priorities"]."
		</a>
	</span>
	<span".ongletSelect("categ=scan_request&sub=upload_folder").">
		<a title='".htmlentities($msg["upload_folder_storage"],ENT_QUOTES,$charset)."' href='./admin.php?categ=scan_request&sub=upload_folder'>
			".$msg["upload_folder_storage"]."
		</a>
	</span>
</div>";

//    ----------------------------------
// $admin_layout : layout page administration
$admin_layout = "
<!-- conteneur -->
<div id='conteneur'  class='$current_module'>".
$admin_menu_new."
<!-- contenu -->
<div id='contenu'>
!!menu_contextuel!!
";

// $admin_layout_end : layout page administration (fin)
$admin_layout_end = '
</div>
<!-- /conteneur -->
</div>
';


// $admin_user_Javascript : scripts pour la gestion des utilisateurs
$admin_user_javascript = "
<script type='text/javascript'>
	function test_pwd(form, status)
	{
		if(form.form_pwd.value.length == 0)
		{
				alert(\"$msg[79]\");
				return false;
		}
		if(form.form_pwd.value != form.form_pwd2.value)
		{
				alert(\"$msg[80]\");
				return false;
		}

		return true;
	}

	function test_form_create(form, status)
	{
		if(form.form_login.value.replace(/^\s+|\s+$/g, '').length == 0)
		{
				alert(\"$msg[81]\");
				return false;
		}

		if(!form.form_admin.checked && !form.form_catal.checked && !form.form_circ.checked && !form.form_extensions.checked
			&& !form.form_restrictcirc.checked
			&& !form.form_fiches.checked
			&& !form.form_auth.checked
			&& !form.form_dsi.checked
			&& !form.form_pref.checked
			&& !form.form_thesaurus.checked
			&& !form.form_acquisition.checked
			&& !form.form_cms.checked
			&& !form.form_edition.checked
		){
				alert(\"$msg[84]\");
				return false;
		}

		if(status == 1) {
				if(form.form_pwd.value.length == 0)
				{
					alert(\"$msg[82]\");
					return false;
				}
				if(form.form_pwd.value != form.form_pwd2.value)
				{
					alert(\"$msg[83]\");
					return false;
				}

		}

		return true;
	}
</script>
";

// $admin_npass_form : template form changement password
$admin_npass_form = "
<form class='form-$current_module' id='userform' name='userform' method='post' action='./admin.php?categ=users&sub=users&action=pwd&id=!!id!!'>
<h3><span onclick='menuHide(this,event)'>$msg[86] !!myUser!!</span></h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='form_pwd'>$msg[87]</label>
		<input class='saisie-20em' id='form_pwd' type='password' name='form_pwd' />
		</div>
	<div class='row'>
		<label class='etiquette' for='form_pwd2'>$msg[88]</label>
		<input class='saisie-20em' id='form_pwd2' type='password' name='form_pwd2' />
		</div>
	</div>
<div class='row'>
	<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=users&sub=users'\" />&nbsp;
	<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_pwd(this.form)\" />
	</div>
</form>
";

// $admin_user_form : template form user
$admin_user_form = "
<script type=\"text/javascript\">
<!--
function setValue(f_element, factor) {
    var maxv = 50;
    var minv = 1;

    var vl = document.forms['account_form'].elements[f_element].value;
    if((vl < maxv) && (factor == 1))
       vl++;
    if((vl > minv) && (factor == -1))
        vl--;
    document.forms['account_form'].elements[f_element].value = vl;
}
function test_pwd(form, status) {
	if(form.passw.value.length != 0) {
		if(form.passw.value != form.passw2.value) {
			alert(\"$msg[80]\");
			return false;
		}
    }
	return true;
}

function account_calcule_section(selectBox) {
	for (i=0; i<selectBox.options.length; i++) {
		id=selectBox.options[i].value;
	    list=document.getElementById(\"docloc_section\"+id);
	    list.style.display=\"none\";
	}

	id=selectBox.options[selectBox.selectedIndex].value;
	list=document.getElementById(\"docloc_section\"+id);
	list.style.display=\"block\";
}
-->
</script>
<form class='form-$current_module' name='userform' method='post' action='./admin.php?categ=users&sub=users&action=update&id=!!id!!'>
<h3><span onclick='menuHide(this,event)'>!!title!!</span></h3>
<div class='form-contenu'>
	<div class='row'>
		<div class='colonne3'>
			<label class='etiquette'>$msg[91] &nbsp;</label><br />
			<input type='text' class='saisie-20em' name='form_login' value='!!login!!' />
		</div>
		<div class='colonne3'>
			<label class='etiquette'>$msg[67] &nbsp;</label><br />
			<input type='text' class='saisie-20em' name='form_nom' value='!!nom!!' />
		</div>
		<div class='colonne3'>
			<label class='etiquette'>$msg[68] &nbsp;</label><br />
			<input type='text' class='saisie-20em' name='form_prenom' value='!!prenom!!' />
		</div>
	</div>

	<div class='row'>
		<div class='colonne3'>
			<label class='etiquette'>$msg[user_langue] &nbsp;</label><br />
			!!select_lang!!
		</div>
		<div class='colonne_suite'>
			<!-- sel_group -->
		</div>
	</div>
	<div class='row'><span class='space-wide-space'>&nbsp;</span><hr /></div>
	<div class='row'>
		<div class='colonne3'>
			<label class='etiquette'>".$msg['email']." &nbsp;</label><br />
			<input type='text' class='saisie-20em' name='form_user_email' value='!!user_email!!' />
		</div>
		<div class='colonne3'>
			<span class='ui-panel-display'>
				<input type='checkbox' class='checkbox' !!alter_resa_mail!! value='1' name='form_user_alert_resamail' />
				<label class='etiquette'>".$msg['alert_resa_user_mail']." &nbsp;</label>
			</span>
			<span class='ui-panel-display'>
				".($acquisition_active ? "<input type='checkbox' class='checkbox' !!alert_sugg_mail!! value='1' name='form_user_alert_suggmail' />
				<label class='etiquette'>".$msg['alert_sugg_user_mail']." &nbsp;</label>" : "")."			
			</span>	
		</div>
		<div class='colonne3'>
			<span class='ui-panel-display'>
				".($demandes_active ? "<input type='checkbox' class='checkbox' !!alert_demandes_mail!! value='1' name='form_user_alert_demandesmail' />
				<label class='etiquette'>".$msg['alert_demandes_user_mail']." &nbsp;</label>" : "")."
			</span>
			<span class='ui-panel-display'>
				".($opac_websubscribe_show ? "<input type='checkbox' class='checkbox' !!alert_subscribe_mail!! value='1' name='form_user_alert_subscribemail' />
				<label class='etiquette'>".$msg['alert_subscribe_user_mail']." &nbsp;</label>" : "")."
			</span>	
		</div>
	</div>
	<div class='row'><span class='space-wide-space'>&nbsp;</span><hr /></div>
	<div class='row'>
		<div class='colonne3'></div>
		<div class='colonne3'></div>
		<div class='colonne3'></div>
	</div>
	".($opac_serialcirc_active ? "
	<div class='row'>
		<div class='colonne3'>
			<span class='space-wide-space'>&nbsp;</span>
		</div>
		<div class='colonne3'>
			<input type='checkbox' class='checkbox' !!alert_serialcirc_mail!! value='1' name='form_user_alert_serialcircmail' />
			<label class='etiquette'>".$msg['alert_subscribe_serialcirc_mail']." &nbsp;</label>
		</div>
		<div class='row'><span class='space-wide-space'>&nbsp;</span><hr /></div>
	</div>
	" : "")."
	!!password_field!!

<div class='row'>
	<div class='row'>
		<label class='etiquette' for='form_nb_per_page_search'>$msg[nb_enreg_par_page]</label>
	</div>
	<div class='colonne4'>
	<!--	Nombre d'enregistrements par page en recherche	-->
		<label class='etiquette' for='form_nb_per_page_search'>$msg[900]</label><br />
		<input type='text' class='saisie-10em' name='form_nb_per_page_search' value='!!nb_per_page_search!!' size='4' />
	</div>
	<div class='colonne4'>
	<!--	Nombre d'enregistrements par page en sélection d'autorités	-->
		<label class='etiquette'>${msg[901]}</label><br />
		<input class='saisie-10em' type='text' id='form_nb_per_page_select' name='form_nb_per_page_select' value='!!nb_per_page_select!!' size='4' />
	</div>	
	<div class='colonne_suite'>
		<label class='etiquette' for='form_nb_per_page_gestion'>${msg[902]}</label><br />
		<input type='text' class='saisie-10em' id='form_nb_per_page_gestion' name='form_nb_per_page_gestion' value='!!nb_per_page_gestion!!' size='4' />
	</div>
</div>

<div class='row'><hr /></div>
<div class='row'>
	<div class='row'><label class='etiquette'>$msg[92]</label></div>

<div class='colonne4'>
		<input type='checkbox' class='checkbox' !!circ_flg!! value='1' id='form_circ' name='form_circ' /><label for='form_circ'>$msg[5]</label><br />\n
		<input type='checkbox' class='checkbox' !!modif_cb_expl_flg!! value='1' id='form_catal_modif_cb_expl' name='form_catal_modif_cb_expl' /><label for='form_catal_modif_cb_expl'><i>".$msg['catal_modif_cb_expl_droit']."</i></label><br/>\n
		<input type='checkbox' class='checkbox' !!restrictcirc_flg!! value='1' id='form_restrictcirc' name='form_restrictcirc' /><label for='form_restrictcirc'><i>".$msg["restrictcirc_auth"]."</i></label><br />
		<input type='checkbox' class='checkbox' !!admin_flg!! value='1' id='form_admin' name='form_admin' /><label for='form_admin'>$msg[7]</label><br />\n";
if ($fiches_active) $admin_user_form .= "<input type='checkbox' class='checkbox' !!fiches_flg!! value='1' id='form_fiches' name='form_fiches' /><label for='form_fiches'>".$msg["onglet_fichier"]."</label><br />\n";	
if ($thesaurus_concepts_active) $admin_user_form .= "<input type='checkbox' class='checkbox' !!concepts_flg!! value='1' id='form_concepts' name='form_concepts' /><label for='form_concepts'>".$msg["ontology_skos_menu"]."</label><br />\n";	
$admin_user_form .= "
		</div>
<div class='colonne4'>
		<input type='checkbox' class='checkbox' !!catal_flg!! value='1' id='form_catal' name='form_catal' /><label for='form_catal'>$msg[93]</label><br />\n
		<input type='checkbox' class='checkbox' !!edit_flg!! value='1' id='form_edition' name='form_edition' /><label for='form_edition'>$msg[1100]</label><br />\n
		<input type='checkbox' class='checkbox' !!edit_forcing_flg!! value='1' id='form_edition_forcing' name='form_edition_forcing' /><label for='form_edition_forcing'>".$msg["edit_droit_forcing"]."</label><br />\n
		<input type='checkbox' class='checkbox' !!sauv_flg!! value='1' id='form_sauv' name='form_sauv' /><label for='form_sauv'>$msg[28]</label><br />	
		<input type='checkbox' class='checkbox' !!cms_flg!! value='1' id='form_cms' name='form_cms' /><label for='form_cms'>".$msg["cms_onglet_title"]."</label><br />
		<input type='checkbox' class='checkbox' !!cms_build_flg!! value='1' id='form_cms_build' name='form_cms_build' /><label for='form_cms_build'>".$msg["cms_build_tab"]."</label><br />\n
</div>
<div class='colonne4'>
	<input type='checkbox' class='checkbox' !!auth_flg!! value='1' id='form_auth' name='form_auth' /><label for='form_auth'>$msg[132]</label><br />\n";
if ($dsi_active) $admin_user_form .= "<input type='checkbox' class='checkbox' !!dsi_flg!! value='1' id='form_dsi' name='form_dsi' /><label for='form_dsi'>".$msg["dsi_droit"]."</label><br />\n";
	else $admin_user_form .= "<br />\n";
$admin_user_form .= "<input type='checkbox' class='checkbox' !!pref_flg!! value='1' id='form_pref' name='form_pref' /><label for='form_pref'>$msg[933]</label><br />\n";
if ($acquisition_active) $admin_user_form .= "<input type='checkbox' class='checkbox' !!acquisition_account_invoice_flg!! value='1' id='form_acquisition_account_invoice_flg' name='form_acquisition_account_invoice_flg' /><label for='form_acquisition_account_invoice_flg'>".$msg['acquisition_account_invoice_flg']."</label><br>\n";
	else $admin_user_form .= "<br />\n";
if ($semantic_active)  $admin_user_form .= "<input type='checkbox' class='checkbox' !!semantic_flg!! value='1' id='form_semantic' name='form_semantic' /><label for='form_semantic'>".$msg['semantic_flg']."</label>\n";
	else $admin_user_form .= "<br />\n";
$admin_user_form .= "</div>
<div class='colonne_suite'>
	<input type='checkbox' class='checkbox' !!thesaurus_flg!! value='1' id='form_thesaurus' name='form_thesaurus' /><label for='form_thesaurus'>$msg[thesaurus_auth]</label><br />\n";
if ($acquisition_active) $admin_user_form .= "<input type='checkbox' class='checkbox' !!acquisition_flg!! value='1' id='form_acquisition' name='form_acquisition' /><label for='form_acquisition'>".$msg["acquisition_droit"]."</label><br />\n";
	else $admin_user_form .= "<br />\n";
if ($pmb_transferts_actif) $admin_user_form .= "<input type='checkbox' class='checkbox' !!transferts_flg!! value='1' id='form_transferts' name='form_transferts' /><label for='form_transferts'>".$msg["transferts_droit"]."</label><br />\n";
	else $admin_user_form .= "<br />\n";
if ($pmb_extension_tab) $admin_user_form .= "<input type='checkbox' class='checkbox' !!extensions_flg!! value='1' id='form_extensions' name='form_extensions' /><label for='form_extensions'>".$msg["extensions_droit"]."</label><br />\n";
	else $admin_user_form .= "<br />\n";
if ($demandes_active) $admin_user_form .= "<input type='checkbox' class='checkbox' !!demandes_flg!! value='1' id='form_demandes' name='form_demandes' /><label for='form_demandes'>".$msg["demandes_droit"]."</label><br />\n";
	else $admin_user_form .= "<br />\n";
if ($frbr_active) $admin_user_form .= "<input type='checkbox' class='checkbox' !!frbr_flg!! value='1' id='form_frbr' name='form_frbr' /><label for='form_frbr'>".$msg["frbr"]."</label><br />\n";
	else $admin_user_form .= "<br />\n";
if ($modelling_active) $admin_user_form .= "<input type='checkbox' class='checkbox' !!modelling_flg!! value='1' id='form_modelling' name='form_modelling' /><label for='form_modelling'>".$msg["modelling"]."</label><br />\n";
	else $admin_user_form .= "<br />\n";
	
$admin_user_form .= "
	</div>
</div>
<div class='row'>
	!!form_param_default!!
</div>
<div class='row'></div>
</div>
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=users&sub=users'\" />&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form_create(this.form, !!form_type!!)\" />
		!!button_duplicate!!
		<input type='hidden' name='form_actif' value='1'>
		</div>
	<div class='right'>
		!!bouton_suppression!!
		</div>
	</div>
<div class='row'>&nbsp;</div>
</form>
";


$user_acquisition_adr_form = "
<div class='row'>
	<div class='child'>
		<div class='colonne2'>".htmlentities($msg['acquisition_adr_liv'], ENT_QUOTES, $charset)."</div>
		<div class='colonne2'>".htmlentities($msg['acquisition_adr_fac'], ENT_QUOTES, $charset)."</div>
	</div>
</div>
<div class='row'>
	<div class='child'>
		<div class='colonne2'>
			<div class='colonne' >					
				<input type='hidden' id='id_adr_liv[!!id_bibli!!]' name='id_adr_liv[!!id_bibli!!]' value='!!id_adr_liv!!' />
				<textarea  id='adr_liv[!!id_bibli!!]' name='adr_liv[!!id_bibli!!]' class='saisie-30emr' readonly='readonly' cols='50' rows='6' wrap='virtual'>!!adr_liv!!</textarea>&nbsp;
			</div>
			<div class='colonne_suite' >
				<input type='button' class='bouton_small' tabindex='1' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=coord&caller=!!form_name!!&param1=id_adr_liv[!!id_bibli!!]&param2=adr_liv[!!id_bibli!!]&id_bibli=!!id_bibli!!', 'selector'); \" />&nbsp;
				<input type='button' class='bouton_small' tabindex='1' value='X' onclick=\"document.getElementById('id_adr_liv[!!id_bibli!!]').value='0';document.getElementById('adr_liv[!!id_bibli!!]').value='';\" />
			</div>
		</div>
		<div class='colonne2'>
			<div class='colonne'>
				<input type='hidden' id='id_adr_fac[!!id_bibli!!]' name='id_adr_fac[!!id_bibli!!]' value='!!id_adr_fac!!' />
				<textarea id='adr_fac[!!id_bibli!!]' name='adr_fac[!!id_bibli!!]'  class='saisie-30emr' readonly='readonly' cols='50' rows='6' wrap='virtual'>!!adr_fac!!</textarea>&nbsp;
			</div>
			<div class='colonne_suite'>
				<input type='button' class='bouton_small' tabindex='1' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=coord&caller=!!form_name!!&param1=id_adr_fac[!!id_bibli!!]&param2=adr_fac[!!id_bibli!!]&id_bibli=!!id_bibli!!', 'selector'); \" />&nbsp;
				<input type='button' class='bouton_small' tabindex='1' value='X' onclick=\"document.getElementById('id_adr_fac[!!id_bibli!!]').value='0';document.getElementById('adr_fac[!!id_bibli!!]').value='';\" />
			</div>
		</div>
	</div>
</div>
";

$admin_param_form = "
<form class='form-$current_module' name='paramform' method='post' action='./admin.php?categ=param&action=update&id_param=!!id_param!!#justmodified'>
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<div class='form-contenu'>
	<div class='row'>
		<div class='colonne5 align_right'>
				<label class='etiquette'>$msg[1602] &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				!!type_param!! <input type='hidden' name='form_type_param' value='!!type_param!!' />
				</div>
		</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='colonne5 align_right'>
				<label class='etiquette'>$msg[1603] &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				!!sstype_param!! <input type='hidden' name='form_sstype_param' value='!!sstype_param!!' />
				</div>
		</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='colonne5 align_right'>
				<label class='etiquette'>$msg[1604] &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				<textarea name='form_valeur_param' rows='10' cols='90' wrap='virtual'>!!valeur_param!!</textarea>
				</div>
		</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='colonne5 align_right'>
				<label class='etiquette'>".$msg['param_explication']." &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				<textarea name='comment_param' rows='10' cols='90' wrap='virtual'>!!comment_param!!</textarea>
				</div>
		</div>
	<div class='row'> </div>
	</div>
	<div class='row'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=param'\">
		<input class='bouton' type='submit' value=' $msg[77] ' />
		<input type='hidden' class='text' name='form_id_param' value='!!id_param!!' readonly />
			</div>
</form>
<script type='text/javascript'>document.forms['paramform'].elements['form_valeur_param'].focus();</script>
";


$password_field = "
<div class='row'>
	<div class='colonne3'>
		<label class='etiquette'>$msg[2]</label><br />
		<input type='password' name='form_pwd' class='ui-width-medium saisie-20em'>
		</div>
	<div class='colonne3'>
		<label class='etiquette'>$msg[88]</label><br />
		<input type='password' name='form_pwd2' class='ui-width-medium saisie-20em'>
		</div>
	</div>
<div class='row'>&nbsp;</div>
<hr />
";

// $admin_user_list : template liste utilisateurs
$admin_user_list = "
<div class='row'>&nbsp;</div>
<div class='row'>
	<div class='colonne4'>
		<label class='etiquette'>!!user_name!! (!!user_login!!)</label>
		</div>
	<div class='colonne_suite'>
		!!user_link!!
		</div>
	<div class='colonne_suite' style='float:right;'>
		!!user_created_date!!
	</div>
	</div>
<div class='row'>
	<table class='brd'>";

// Première ligne
$admin_user_list .= "
		<tr >
			<td class='brd'>!!nusercirc!!$msg[5]</td>
			<td class='brd'>!!nusercatal!!$msg[93]</td>
			<td class='brd'>!!nuserauth!!$msg[132]</td>
			<td class='brd'>!!nuserthesaurus!!".$msg["thesaurus_auth"]."</td>
		</tr>";

// Deuxième ligne
$admin_user_list .= "
		<tr>
			<td class='brd'>!!nusermodifcbexpl!!<i>".$msg['catal_modif_cb_expl_droit']."</i></td>
			<td class='brd'>!!nuseredit!!$msg[1100]</td>
			<td class='brd'>";
					if ($dsi_active) $admin_user_list .= "!!nuserdsi!!$msg[dsi_droit]</td>";
					else $admin_user_list .= "&nbsp;</td>";
$admin_user_list .= "<td class='brd'>";
					if ($acquisition_active) $admin_user_list .= "!!nuseracquisition!!$msg[acquisition_droit]</td>";
					else $admin_user_list .= "&nbsp;</td>";
$admin_user_list .= "				
		</tr>";

// Troisième ligne
$admin_user_list .= "
		<tr>
			<td class='brd'>!!nuserrestrictcirc!!<i>".$msg["restrictcirc_auth"]."</i></td>
			<td class='brd'>!!nusereditforcing!!$msg[edit_droit_forcing]</td>
			<td class='brd'>!!nuserpref!!$msg[933]</td>
			<td class='brd'>";
				if ($pmb_transferts_actif)
					$admin_user_list .= "!!nusertransferts!!$msg[transferts_droit]</td>";
				else $admin_user_list .= "&nbsp;</td>";
$admin_user_list .= "
		</tr>";

// Quatrième ligne
$admin_user_list .= "
		<tr>
			<td class='brd'>!!nuseradmin!!$msg[7]</td>";
		$admin_user_list .= "<td class='brd'>!!nusersauv!!$msg[28]</td>";
			$admin_user_list .= "<td class='brd'>";
			if ($cms_active) 
				$admin_user_list .= "!!nusercms!!$msg[cms_onglet_title]</td>";
			else $admin_user_list .= "&nbsp;</td>";	
$admin_user_list .= "
			<td class='brd'>";
			if ($cms_active)
				$admin_user_list .= "!!nusercms_build!!$msg[cms_build_tab]</td>";
			else $admin_user_list .= "&nbsp;</td>";
$admin_user_list .= "
		</tr>";

// Cinquième ligne
$admin_user_list .= "
		<tr>
		<td class='brd'>";
			if ($pmb_extension_tab) $admin_user_list .="!!nuserextensions!!$msg[extensions_droit]</td>";
			else $admin_user_list .= "&nbsp;</td>";
$admin_user_list .= "<td class='brd'>";
			if ($demandes_active) 
				$admin_user_list .= "!!nuserdemandes!!$msg[demandes_droit]</td>";
			else $admin_user_list .= "&nbsp;</td>";
			$admin_user_list .= "<td class='brd'>";
			if ($fiches_active)
				$admin_user_list .= "!!nuserfiches!!$msg[onglet_fichier]</td>";
			else $admin_user_list .= "&nbsp;</td>";
			$admin_user_list .= "<td class='brd'>";
			if ($acquisition_active)
				$admin_user_list .= "!!nuseracquisition_account_invoice!!".$msg['acquisition_account_invoice_flg']."</td>";
			else $admin_user_list .= "&nbsp;</td>";
			$admin_user_list .= "
		</tr>";

// Sixième  ligne
$admin_user_list .= "
		<tr>
			<td class='brd'>";
if($semantic_active){
	$admin_user_list .= "!!nusersemantic!!<i>".$msg["semantic_flg"]."</i>";
}
$admin_user_list .= "</td>
			<td class='brd'>";
if($thesaurus_concepts_active){
	$admin_user_list .= "!!nuserconcepts!!<i>".$msg["ontology_skos_menu"]."</i>";
}
$admin_user_list .= "</td>
			<td class='brd'>";
if($modelling_active){
	$admin_user_list .= "!!nusermodelling!!".$msg["modelling"];
}
$admin_user_list .= "</td>
			<td class='brd'></td>
		</tr>";
// Septième ligne
$admin_user_list .= "
		!!user_alert_resamail!!";

// Huitième ligne
if ($demandes_active) $admin_user_list .= "
		!!user_alert_demandesmail!!";

// Neuvième ligne
if ($opac_websubscribe_show) $admin_user_list .= "
		!!user_alert_subscribemail!!";

// 10eme ligne
if ($acquisition_active) $admin_user_list .= "
		!!user_alert_suggmail!!";

$admin_user_list .= "</table>
</div>
<div class='row'>&nbsp;</div>
<hr />
";

$admin_user_alert_row = "
		<tr>
				<td colspan=4 class='brd'>
				!!user_alert!! &nbsp;
				</td>
		</tr>";

$admin_user_link1 = "
	<input class='bouton' type='button' value=' $msg[62] ' onClick=\"document.location='./admin.php?categ=users&sub=users&action=modif&id=!!nuserid!!'\">&nbsp;
	<input class='bouton' type='button' value=' $msg[mot_de_passe] ' onClick=\"document.location='./admin.php?categ=users&sub=users&action=pwd&id=!!nuserid!!'\">
	";
	
// commented because now use the confirmation_delete function used also from the other submodules
// so we show also the name we want to delete - Marco Vaninetti


// $admin_codstat_form : template form code stat
$admin_codstat_form = "
<form class='form-$current_module' name=typdocform method=post action=\"./admin.php?categ=docs&sub=codstat&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='form_cb'>$msg[103]</label>
		</div>
	<div class='row'>
		<input type=text name=form_libelle value='!!libelle!!' class='saisie-50em' />
		</div>
	<div class='row'>
		<label class='etiquette'>$msg[proprio_codage_interne]</label>
		</div>
	<div class='row'>
		<input type='text' name='form_statisdoc_codage_import' value='!!statisdoc_codage_import!!' class='saisie-20em' />
		</div>
	<div class='row'>
		<label class='etiquette'>$msg[proprio_codage_proprio]</label>
		</div>
	<div class='row'>
		!!lender!!
		</div>
	</div>

<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=docs&sub=codstat'\" />&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\" />
		</div>
	<div class='right'>
		<input class='bouton' type='button' value='$msg[supprimer]' onClick=\"javascript:confirmation_delete(!!id!!,'!!libelle_suppr!!')\" />
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['typdocform'].elements['form_libelle'].focus();</script>
";

$admin_location_form_sur_loc_part="";
if($pmb_sur_location_activate)
$admin_location_form_sur_loc_part = "
	<div class='row'>
		<label class='etiquette'>$msg[sur_location_select_surloc]</label>
		</div>
	<div class='row'>
		!!sur_loc_selector!! 
		<label class='etiquette' >$msg[sur_location_use_surloc]</label> 
		<input type=checkbox name='form_location_use_surloc' value='1' !!checkbox_use_surloc!! class='checkbox' />
	</div>
";

//    ----------------------------------------------------
//    Onglet map
//    ----------------------------------------------------
global $pmb_map_activate;
$location_map_tpl = "";
if ($pmb_map_activate)
	$location_map_tpl = "
<!-- onglet 14 -->
<div id='el14Parent' class='parent'>
	<h3>
    	<img src='".get_url_icon('plus.gif')."' class='img_plus' name='imEx' id='el14Img' onClick=\"expandBase('el14', true); return false;\" title='".$msg["notice_map_onglet_title"]."' border='0' /> ".$msg["notice_map_onglet_title"]."
	</h3>
</div>

<div id='el14Child' class='child' etirable='yes' title='".htmlentities($msg['notice_map_onglet_title'],ENT_QUOTES, $charset)."'>
	<div id='el14Child_0' title='".htmlentities($msg['notice_map'],ENT_QUOTES, $charset)."' movable='yes'>
		<div id='el14Child_0b' class='row'>
			!!location_map!!
		</div>
	</div>
</div>";

// $admin_location_form : template form des localisations
$admin_location_form = "
<form class='form-$current_module' name=typdocform method=post action=\"./admin.php?categ=docs&sub=location&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='form_cb'>$msg[103]</label>
		</div>
	<div class='row'>
		<input type=text name='form_libelle' value=\"!!libelle!!\" class='saisie-50em' />
		</div>
	<div class='row'>
		<label class='etiquette' >$msg[docs_location_pic]</label>
		</div>
	<div class='row'>
		<input type=text name='form_location_pic' value=\"!!location_pic!!\" class='saisie-50em' />
		</div>
	<div class='row'>
		<div class='colonne4'>
			<label class='etiquette' >$msg[opac_object_visible]</label>
			<input type=checkbox name='form_location_visible_opac' value='1' !!checkbox!! class='checkbox' />
		</div>
		<div class='colonne4'>
			<label class='etiquette' >CSS</label>
			<input type=text name='form_css_style' value='!!css_style!!' />
		</div>
		<div class='colonne_suite'>
			<label class='etiquette' >$msg[location_infopage_assoc]</label>
			!!loc_infopage!!
		</div>
		</div>
	<div class='row'>
		<label class='etiquette'>$msg[proprio_codage_interne]</label>
		</div>
	<div class='row'>
		<input type='text' name='form_locdoc_codage_import' value='!!locdoc_codage_import!!' class='saisie-20em' />
		</div>
	<div class='row'>
		<label class='etiquette'>$msg[proprio_codage_proprio]</label>
		</div>
	<div class='row'>
		!!lender!!
		</div>
	$admin_location_form_sur_loc_part	
<br />
<hr />".$location_map_tpl."
<br />
<div class='row'></div>
<div class='row'><label class='etiquette'>$msg[location_details_name]</label></div><div class='row'><input type='text' name='form_locdoc_name' value='!!loc_name!!' class='saisie-50em' /></div>
<div class='row'><label class='etiquette'>$msg[location_details_adr1]</label></div><div class='row'><input type='text' name='form_locdoc_adr1' value='!!loc_adr1!!' class='saisie-50em' /></div>
<div class='row'><label class='etiquette'>$msg[location_details_adr2]</label></div><div class='row'><input type='text' name='form_locdoc_adr2' value='!!loc_adr2!!' class='saisie-50em' /></div>
<div class='row'><label class='etiquette'>$msg[location_details_cp] / $msg[location_details_town]</label></div>
	<div class='row'>
		<div class='colonne4'>
			<input type='text' name='form_locdoc_cp', ' value='!!loc_cp!!' maxlength='15' class='saisie-10em' />
			</div>
		<div class='colonne_suite'>
			<input type='text' name='form_locdoc_town', ' value='!!loc_town!!'' class='saisie-50em' />
			</div>
		</div>

<div class='row'><label class='etiquette'>$msg[location_details_state] / $msg[location_details_country]</label></div>
	<div class='row'>
		<div class='colonne3'>
			<input type='text' name='form_locdoc_state',' value='!!loc_state!!' class='saisie-20em' />
			</div>
		<div class='colonne_suite'>
			<input type='text' name='form_locdoc_country' value='!!loc_country!!' class='saisie-20em' />
			</div>
		</div>
<div class='row'><label class='etiquette'>$msg[location_details_phone]</label></div><div class='row'><input type='text' name='form_locdoc_phone' value='!!loc_phone!!' maxlength='100' class='saisie-20em' /></div>
<div class='row'><label class='etiquette'>$msg[location_details_email]</label></div><div class='row'><input type='text' name='form_locdoc_email' value='!!loc_email!!' maxlength='100' class='saisie-20em' /></div>
<div class='row'><label class='etiquette'>$msg[location_details_website]</label></div><div class='row'><input type='text' name='form_locdoc_website' value='!!loc_website!!' maxlength='100' class='saisie-50em' /></div>
<div class='row'><label class='etiquette'>$msg[location_details_logo]</label></div><div class='row'><input type='text' name='form_locdoc_logo', ' value='!!loc_logo!!' maxlength='255' class='saisie-50em' /></div>
<div class='row'><label class='etiquette'>$msg[location_details_commentaire]</label></div><div class='row'><textarea class='saisie-50em' name='form_locdoc_commentaire' id='form_locdoc_commentaire' cols='55' rows='5'>!!loc_commentaire!!</textarea></div>
	</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=docs&sub=location'\" />&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\" />
		<input type='hidden' name='form_actif' value='1'>
		</div>
	<div class='right'>
		<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id!!,'!!libelle_suppr!!')\" />
		</div>
</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['typdocform'].elements['form_libelle'].focus();</script>
";

// $admin_section_form : template form section
$admin_section_form = "
<form class='form-$current_module' name=typdocform method=post action=\"./admin.php?categ=docs&sub=section&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='form_cb'>$msg[103]</label>
		</div>
	<div class='row'>
		<input type=text name='form_libelle' value='!!libelle!!' class='saisie-50em' />
		</div>
	<div class='row'>
		<label class='etiquette' >$msg[docs_section_pic]</label>
		</div>
	<div class='row'>
		<input type=text name='form_section_pic' value='!!section_pic!!' maxlength='255' class='saisie-50em' />
		</div>
	<div class='row'>
		<label class='etiquette' >$msg[opac_object_visible]</label>
		<input type=checkbox name='form_section_visible_opac' value='1' !!checkbox!! class='checkbox' />
		</div>
<div class='row'>
	<div class='colonne2'>
		<div class='row'>
			<label class='etiquette'>$msg[proprio_codage_interne]</label>
			</div>
		<div class='row'>
			<input type='text' name='form_sdoc_codage_import' value='!!sdoc_codage_import!!' class='saisie-20em' />
			</div>
		<div class='row'>
			<label class='etiquette'>$msg[proprio_codage_proprio]</label>
			</div>
		<div class='row'>
			!!lender!!
			</div>
		</div>
	<div class='colonne_suite'>
		<div class='row'>
			<label class='etiquette'>$msg[section_visible_loc]</label>
			</div>
		<div class='row'>
			!!num_locations!!
			</div>
		</div>
	</div>
<div class='row'>&nbsp;</div>
</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=docs&sub=section'\" />&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\" />
		</div>
	<div class='right'>
		<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id!!,'!!libelle_suppr!!')\" />
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['typdocform'].elements['form_libelle'].focus();</script>
";

// $admin_statut_form : template form statuts
$admin_statut_form = "
<form class='form-$current_module' name=typdocform method=post action=\"./admin.php?categ=docs&sub=statut&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='form_libelle'>$msg[103]</label>
		</div>
	<div class='row'>
		<input type=text name='form_libelle' value='!!libelle!!' class='saisie-50em' />
		</div>
	<div class='row'>
		<label class='etiquette' for='form_libelle_opac'>".$msg["docs_statut_form_libelle_opac"]."</label>
		</div>
	<div class='row'>
		<input type=text name='form_libelle_opac' value='!!libelle_opac!!' class='saisie-50em' />
		</div>
	<div class='row'>
		<label class='etiquette' for='form_pret'>$msg[117]</label>
		<input type=checkbox name=form_pret value='!!pret!!' !!checkbox!! class='checkbox' onClick=\"test_check(this.form)\" />
		</div>
	<div class='row'>
		<label class='etiquette' for='form_allow_resa'>".$msg["statut_allow_resa_title"]."</label>
		<input type=checkbox name=form_allow_resa value='1' !!checkbox_allow_resa!! class='checkbox'  />
		</div>";

if ($pmb_transferts_actif=="1")
	$admin_statut_form .= "
	<div class='row'>
		<label class='etiquette' for='form_trans'>".$msg["transferts_statut_lib_transferable"]."</label>
		<input type=checkbox name=form_trans value='!!trans!!' !!checkbox_trans!! class='checkbox' onClick=\"test_check_trans(this.form)\" />
		</div>";
$admin_statut_form .= "
	<div class='row'>
		<label class='etiquette' for='form_visible_opac'>".$msg["opac_object_visible"]."</label>
		<input type=checkbox name=form_visible_opac value='!!visible_opac!!' !!checkbox_visible_opac!! class='checkbox' onClick=\"test_check_visible_opac(this.form)\" />
		</div>
	<div class='row'>
		<label class='etiquette'>$msg[proprio_codage_interne]</label>
		</div>
	<div class='row'>
		<input type='text' name='form_statusdoc_codage_import' value='!!statusdoc_codage_import!!' class='saisie-20em' />
		</div>
	<div class='row'>
		<label class='etiquette'>$msg[proprio_codage_proprio]</label>
		</div>
	<div class='row'>
		!!lender!!
		</div>
	</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=docs&sub=statut'\">&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\">
		</div>
	<div class='right'>
		<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id!!,'!!libelle_suppr!!')\" />
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['typdocform'].elements['form_libelle'].focus();</script>
";

// $admin_orinot_form : template form origine notice
$admin_orinot_form = "
<form class='form-$current_module' name=orinotform method=post action=\"./admin.php?categ=notices&sub=orinot&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' >$msg[orinot_nom]</label>
		</div>
	<div class='row'>
		<input type=text name='form_nom' value='!!nom!!' class='saisie-50em' />
		</div>
	<div class='row'>
		<label class='etiquette' >$msg[orinot_pays]</label>
		</div>
	<div class='row'>
		<input type=text name='form_pays' value='!!pays!!' class='saisie-50em' />
		</div>
	<div class='row'>
		<label class='etiquette' >$msg[orinot_diffusable]</label>
		<input type=checkbox name=form_diffusion value='1' !!checkbox!! class='checkbox' />
		</div>
	</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=notices&sub=orinot'\">&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\">
		</div>
	<div class='right'>
		<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id!!,'!!nom_suppr!!')\" />
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['orinotform'].elements['form_nom'].focus();</script>
";

// $admin_onglet_form : Onglet personalisé de la notice
$admin_onglet_form = "
<form class='form-$current_module' name=ongletform method=post action=\"./admin.php?categ=notices&sub=onglet&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' >$msg[admin_noti_onglet_name]</label>
	</div>
	<div class='row'>
		<input type=text name='form_nom' value='!!nom!!' class='saisie-50em' />
	</div>
</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=notices&sub=onglet'\">&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\">
		</div>
	<div class='right'>
		<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id!!,'!!nom_suppr!!')\" />
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['ongletform'].elements['form_nom'].focus();</script>
";

// $admin_notice_usage_form : template form droit d'usage notice
$admin_notice_usage_form = "
<form class='form-$current_module' id='notice_usageform' name='notice_usageform' method='post' action=\"./admin.php?categ=notices&sub=notice_usage&action=update&id_usage=!!id_usage!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' >".$msg['notice_usage_libelle']."</label>
	</div>
	<div class='row'>
		<input type=text id='usage_libelle' name='usage_libelle' value='!!usage_libelle!!' class='saisie-50em' data-translation-fieldname='usage_libelle' />
	</div>
</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' ".$msg['76']." ' onClick=\"document.location='./admin.php?categ=notices&sub=notice_usage'\">&nbsp;
		<input class='bouton' type='submit' value=' ".$msg['77']." ' onClick=\"return test_form(this.form)\">
	</div>
	<div class='right'>
		!!bouton_supprimer!!
	</div>
</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['notice_usageform'].elements['usage_libelle'].focus();</script>
";

$admin_map_echelle_form = "
<form class='form-$current_module' name=map_echelleform method=post action=\"./admin.php?categ=notices&sub=map_echelle&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' >$msg[admin_noti_map_echelle_name]</label>
	</div>
	<div class='row'>
		<input type=text name='form_nom' value='!!nom!!' class='saisie-50em' />
	</div>
</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=notices&sub=map_echelle'\">&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\">
		</div>
	<div class='right'>
		<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id!!,'!!nom_suppr!!')\" />
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['map_map_echelleform'].elements['form_nom'].focus();</script>
";

$admin_map_projection_form = "
<form class='form-$current_module' name=map_projectionform method=post action=\"./admin.php?categ=notices&sub=map_projection&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' >$msg[admin_noti_map_projection_name]</label>
	</div>
	<div class='row'>
		<input type=text name='form_nom' value='!!nom!!' class='saisie-50em' />
	</div>
</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=notices&sub=map_projection'\">&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\">
		</div>
	<div class='right'>
		<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id!!,'!!nom_suppr!!')\" />
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['map_map_projectionform'].elements['form_nom'].focus();</script>
";

$admin_map_ref_form = "
<form class='form-$current_module' name=map_refform method=post action=\"./admin.php?categ=notices&sub=map_ref&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' >$msg[admin_noti_map_ref_name]</label>
	</div>
	<div class='row'>
		<input type=text name='form_nom' value='!!nom!!' class='saisie-50em' />
	</div>
</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=notices&sub=map_ref'\">&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\">
		</div>
	<div class='right'>
		<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id!!,'!!nom_suppr!!')\" />
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['map_map_refform'].elements['form_nom'].focus();</script>
";

// $admin_typdoc_form : template form types doc
$admin_typdoc_form = "
<form class='form-$current_module' name=typdocform method=post action=\"./admin.php?categ=docs&sub=typdoc&action=update&id=!!id!!\">
	<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
	<!--    Contenu du form    -->
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='form_libelle'>".$msg[103]."</label>
		</div>
		<div class='row'>
			<input type='text' id='form_libelle' name='form_libelle' value='!!libelle!!' class='saisie-50em' />
		</div>
		
		<!-- form_pret -->
		<!-- form_short_loan_duration -->
		<!-- form_resa -->
		<!-- tarif_pret -->

		<div class='row'>
			<label class='etiquette' for='form_tdoc_codage_import' >".$msg['proprio_codage_interne']."</label>
		</div>
		<div class='row'>
			<input type='text' id='form_tdoc_codage_import' name='form_tdoc_codage_import' value='!!tdoc_codage_import!!' class='saisie-20em' />
		</div>
		<div class='row'>
			<label class='etiquette'>".$msg['proprio_codage_proprio']."</label>
		</div>
		<div class='row'>
			<!-- lender -->
		</div>
	</div>
	<!-- Boutons -->
	<div class='row'>
		<div class='left'>
			<input class='bouton' type='button' value='".$msg[76]."' onClick=\"document.location='./admin.php?categ=docs&sub=typdoc'\" />&nbsp;
			<input class='bouton' type='submit' value='".$msg[77]."' onClick=\"return test_form(this.form)\" />&nbsp;
		</div>
		<div class='right'>
			<input class='bouton' type='button' value='".$msg['supprimer']."' onClick=\"javascript:confirmation_delete(!!id!!,'!!libelle_suppr!!')\" />
		</div>
	</div>
	<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['typdocform'].elements['form_libelle'].focus();</script>
<script type='text/javascript'>
function test_form(form) {
	if(form.form_libelle.value.length == 0) {
		alert('".$msg[98]."');
		return false;
	}
	if(isNaN(form.form_pret.value) || form.form_pret.value.length == 0) {
		alert('".$msg[119]."');
		return false;
	}
	if(isNaN(form.form_short_loan_duration.value) || form.form_short_loan_duration.value.length == 0) {
		alert('".$msg['short_loan_duration_error']."');
		return false;
	}
	if(isNaN(form.form_resa.value) || form.form_resa.value.length == 0) {
		alert('".$msg['resa_duration_error']."');
		return false;
	}
	return true;
}
</script>
";

// $admin_lender_form : template form lenders
$admin_lender_form = "
<form class='form-$current_module' name='lenderform' method='post' action=\"./admin.php?categ=docs&sub=lenders&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='form_libelle'>$msg[558]</label>
	</div>
	<div class='row'>
		<input type='text' id='form_libelle' name='form_libelle' value='!!libelle!!' class='saisie-50em' />
	</div>
</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=docs&sub=lenders'\" />&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\" />
		</div>
	<div class='right'>
		<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id!!,'!!libelle_suppr!!')\" />
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['lenderform'].elements['form_libelle'].focus();</script>
";
// $admin_support_form : template form supports
$admin_support_form = "
<form class='form-$current_module' name='supportform' method='post' action=\"./admin.php?categ=collstate&sub=support&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='form_libelle'>".$msg["admin_collstate_support_nom"]."</label>
	</div>
	<div class='row'>
		<input type='text' id='form_libelle' name='form_libelle' value='!!libelle!!' class='saisie-50em' />
	</div>
</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=collstate&sub=support'\" />&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\" />
		</div>
	<div class='right'>
	!!supprimer!!
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['supportform'].elements['form_libelle'].focus();</script>
";
// $admin_emplacement_form : template form emplacements
$admin_emplacement_form = "
<form class='form-$current_module' name='emplacementform' method='post' action=\"./admin.php?categ=collstate&sub=emplacement&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='form_libelle'>".$msg["admin_collstate_emplacement_nom"]."</label>
	</div>
	<div class='row'>
		<input type='text' id='form_libelle' name='form_libelle' value='!!libelle!!' class='saisie-50em' />
	</div>
</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=collstate&sub=emplacement'\" />&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\" />
		</div>
	<div class='right'>
		!!supprimer!!
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['emplacementform'].elements['form_libelle'].focus();</script>
";

// $admin_categlec_form : template form categ lecteurs
$admin_categlec_form = "
<form class='form-$current_module' name='typdocform' method='post' action=\"./admin.php?categ=empr&sub=categ&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='form_cb'>$msg[103]</label>
		</div>
	<div class='row'>
		<input type=text name='form_libelle' value='!!libelle!!' class='saisie-50em' />
		</div>
	<div class='row'>
		<label class='etiquette' for='form_duree_adhesion'>$msg[1400]</label>
		</div>
	<div class='row'>
		<input type=text name='form_duree_adhesion' value='!!duree_adhesion!!' maxlength='10' class='saisie-5em' />
		</div>
	!!tarif_adhesion!!
	<div class='row'>
		<label class='etiquette' for='form_age_min'>$msg[empr_categ_age_min]</label>
		</div>
	<div class='row'>
		<input type=text name='form_age_min' value='!!age_min!!' maxlength='3' class='saisie-5em' />
		</div>
	<div class='row'>
		<label class='etiquette' for='form_age_max'>$msg[empr_categ_age_max]</label>
		</div>
	<div class='row'>
		<input type=text name='form_age_max' value='!!age_max!!' maxlength='3' class='saisie-5em' />
		</div>
	</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=empr&sub=categ'\">&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\" />
		</div>
	<div class='right'>
		<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id!!,'!!libelle_suppr!!')\" />
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['typdocform'].elements['form_libelle'].focus();</script>
";

// $admin_statlec_form : template form codestat lecteurs
$admin_statlec_form = "
<form class='form-$current_module' name='typdocform' method='post' action=\"./admin.php?categ=empr&sub=codstat&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='form_cb'>$msg[103]</label>
		</div>
	<div class='row'>
		<input type='text' name='form_libelle' value='!!libelle!!' class='saisie-50em' />
		</div>
	</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=empr&sub=codstat'\" />&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\" />
		</div>
	<div class='right'>
		<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id!!,'!!libelle_suppr!!')\" />
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['typdocform'].elements['form_libelle'].focus();</script>
";

// $admin_empr_statut_form : template formulaire statuts emprunteurs
$admin_empr_statut_form = "
<form class='form-$current_module' name=statutform method=post action=\"./admin.php?categ=empr&sub=statut&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='form_cb'>$msg[103]</label>
	</div>
	<div class='row'>
		<input type=text name='statut_libelle' value='!!libelle!!' class='saisie-50em' />
	</div>
	<div class='row'>
		<input type=checkbox name=allow_loan value='1' id=allow_loan !!checkbox_loan!! class='checkbox' />
		<label class='etiquette' for='allow_loan'>".$msg['empr_allow_loan']."</label>
	</div>
	<div class='row'>
		<input type=checkbox name=allow_loan_hist value='1' id=allow_loan_hist !!checkbox_loan_hist!! class='checkbox'/>
		<label class='etiquette' for='allow_loan_hist'>".$msg['empr_allow_loan_hist']."</label>
	</div>
	<div class='row'>
		<input type=checkbox name=allow_book value='1' id=allow_book !!checkbox_book!! class='checkbox' />
		<label class='etiquette' for='allow_book'>".$msg['empr_allow_book']."</label>
	</div>
	<div class='row'>
		<input type=checkbox name=allow_opac value='1' id=allow_opac !!checkbox_opac!! class='checkbox' />
		<label class='etiquette' for='allow_opac'>".$msg['empr_allow_opac']."</label>
	</div>
	<div class='row'>
		<input type=checkbox name=allow_dsi value='1' id=allow_dsi !!checkbox_dsi!! class='checkbox' />
		<label class='etiquette' for='allow_dsi'>".$msg['empr_allow_dsi']."</label>
	</div>
	<div class='row'>
		<input type=checkbox name=allow_dsi_priv value='1' id=allow_dsi_priv !!checkbox_dsi_priv!! class='checkbox' />
		<label class='etiquette' for='allow_dsi_priv'>".$msg['empr_allow_dsi_priv']."</label>
	</div>
	<div class='row'>
		<input type=checkbox name=allow_sugg value='1' id=allow_sugg !!checkbox_sugg!! class='checkbox' />
		<label class='etiquette' for='allow_sugg'>".$msg['empr_allow_sugg']."</label>
	</div>
	<div class='row'>
		<input type=checkbox name=allow_dema value='1' id=allow_dema !!checkbox_dema!! class='checkbox' />
		<label class='etiquette' for='allow_dema'>".$msg['empr_allow_dema']."</label>
	</div>
	<div class='row'>
		<input type=checkbox name=allow_liste_lecture value='1' id=allow_liste_lecture !!checkbox_liste_lecture!! class='checkbox' />
		<label class='etiquette' for='allow_liste_lecture'>".$msg['empr_allow_liste_lecture']."</label>
	</div>
	<div class='row'>
		<input type=checkbox name=allow_prol value='1' id=allow_prol !!checkbox_prol!! class='checkbox' />
		<label class='etiquette' for='allow_prol'>".$msg['empr_allow_prol']."</label>
	</div>
	<div class='row'>
		<input type=checkbox name=allow_avis value='1' id=allow_avis !!checkbox_avis!! class='checkbox' />
		<label class='etiquette' for='allow_avis'>".$msg['empr_allow_avis']."</label>
	</div>
	<div class='row'>
		<input type=checkbox name=allow_tag value='1' id=allow_tag !!checkbox_tag!! class='checkbox' />
		<label class='etiquette' for='allow_tag'>".$msg['empr_allow_tag']."</label>
	</div>
	<div class='row'>
		<input type=checkbox name=allow_pwd value='1' id=allow_pwd !!checkbox_pwd!! class='checkbox' />
		<label class='etiquette' for='allow_pwd'>".$msg['empr_allow_pwd']."</label>
	</div>
	<div class='row'>
		<input type=checkbox name=allow_self_checkout value='1' id=allow_self_checkout !!allow_self_checkout!! class='checkbox' />
		<label class='etiquette' for='allow_self_checkout'>".$msg['empr_allow_self_checkout']."</label>
	</div>
	<div class='row'>
		<input type=checkbox name=allow_self_checkin value='1' id=allow_self_checkin !!allow_self_checkin!! class='checkbox' />
		<label class='etiquette' for='allow_self_checkin'>".$msg['empr_allow_self_checkin']."</label>
	</div>
	<div class='row'>
		<input type='checkbox' name='allow_serialcirc' value='1' id='allow_serialcirc' !!allow_serialcirc!! class='checkbox' />
		<label class='etiquette' for='allow_serialcirc'>".$msg['empr_allow_serialcirc']."</label>
	</div>
	<div class='row'>
		<input type='checkbox' name='allow_scan_request' value='1' id='allow_scan_request' !!allow_scan_request!! class='checkbox' />
		<label class='etiquette' for='allow_scan_request'>".$msg['empr_allow_scan_request']."</label>
	</div>
	<div class='row'>
		<input type='checkbox' name='allow_contribution' value='1' id='allow_contribution' !!allow_contribution!! class='checkbox' />
		<label class='etiquette' for='allow_contribution'>".$msg['empr_allow_contribution']."</label>
	</div>
</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=empr&sub=statut'\">&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\">
		</div>
	<div class='right'>
		<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id!!,'!!libelle_suppr!!')\" />
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['statutform'].elements['statut_libelle'].focus();</script>
";

// $admin_proc_form : template form procédures stockées
$admin_proc_form = "
<form class='form-$current_module' name='maj_proc' method='post' action='!!action!!'>
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class=colonne2>
		<div class='row'>
		<label class='etiquette' for='form_name'>$msg[705]</label>
		</div>
		<div class='row'>
		<input type='text' name='f_proc_name' value='!!name!!' maxlength='255' class='saisie-50em' />
		</div>
	</div>
	<div class=colonne_suite>
		<div class='row'>
		<label class='etiquette' for='form_classement'>$msg[proc_clas_proc]</label>
		</div>
		<div class='row'>
		!!classement!!
		</div>
	</div>
	<div class='row'>
		<label class='etiquette' for='form_code'>$msg[706]</label>
	</div>
	<div class='row'>
		<textarea cols='80' rows='8' name='f_proc_code'>!!code!!</textarea>
	</div>
	<div class='row'>
		<label class='etiquette' for='form_comment'>$msg[707]</label>
	</div>
	<div class='row'>
		<input type='text' name='f_proc_comment' value='!!comment!!' maxlength='255' class='saisie-50em' />
	</div>
	<div class='row'>
		<label class='etiquette' for='form_notice_tpl'>".$msg['notice_tpl_notice_id']."</label>
	</div>
	<div class='row'>
		!!notice_tpl!!
	</div>
	<div class='row'>
		<label class='etiquette' for='autorisations_all'>".$msg["procs_autorisations_all"]."</label>
		<input type='checkbox' id='autorisations_all' name='autorisations_all' value='1' !!autorisations_all!! />
	</div>
	<div class='row'>
		<label class='etiquette' for='form_comment'>$msg[procs_autorisations]</label>
		<input type='button' class='bouton_small align_middle' value='".$msg['tout_cocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,1);'>
		<input type='button' class='bouton_small align_middle' value='".$msg['tout_decocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,0);'>
	</div>
	<div class='row'>
		!!autorisations_users!!
	</div>
</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input type='button' class='bouton' value='$msg[76]' onClick='document.location=\"./admin.php?categ=proc&sub=proc\"' />&nbsp;
		<input type='submit' class='bouton' value='$msg[77]' onClick=\"return test_form(this.form)\" />&nbsp;
		<input type='button' class='bouton' value=' $msg[708] ' onClick=\"document.location='./admin.php?categ=proc&sub=proc&action=execute&id=!!id!!'\" />&nbsp;
		</div>
	<div class='right'>
		<input type='button' class='bouton' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id!!,'!!name_suppr!!')\" />
	</div>
</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['maj_proc'].elements['f_proc_name'].focus();</script>";

// $admin_proc_form : template form procédures stockées
$admin_proc_view_remote = "
<h3><span onclick='menuHide(this,event)'>>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
	!!additional_information!!
	</div>
	<div class=colonne2>
		<div class='row'>
		<label class='etiquette' for='form_name'>$msg[remote_procedures_procedure_name]</label>
		</div>
		<div class='row'>
		<input type='text' readonly name='f_proc_name' value='!!name!!' maxlength='255' class='saisie-50em' />
		</div>
	</div>
	<div class='row'>
		<label class='etiquette' for='form_code'>$msg[remote_procedures_procedure_sql]</label>
		</div>
	<div class='row'>
		<textarea cols='80' readonly rows='8' name='f_proc_code'>!!code!!</textarea>
		</div>
	<div class='row'>
		<label class='etiquette' for='form_comment'>$msg[remote_procedures_procedure_comment]</label>
		</div>
	<div class='row'>
		<input type='text' readonly name='f_proc_comment' value='!!comment!!' maxlength='255' class='saisie-50em' />
	</div>
	<div class='row'>
		!!parameters_title!!
	</div>
	<div class='row'>
		!!parameters_content!!
	</div>
</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input type='button' class='bouton' value='".$msg["remote_procedures_back"]."' onClick='document.location=\"./admin.php?categ=proc&sub=proc\"' />&nbsp;
		<input class='bouton' type='button' value=\"".$msg["remote_procedures_import"]."\" onClick=\"document.location='./admin.php?categ=proc&sub=proc&action=import_remote&id=!!id!!'\" />
		</div>
</div>
<div class='row'></div>
<script type='text/javascript'>document.forms['maj_proc'].elements['f_proc_name'].focus();</script>";

// $admin_zbib_form : template form zbib
$admin_zbib_form = "
<form class='form-$current_module' name=zbibform method=post action=\"./admin.php?categ=z3950&sub=zbib&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<div class='form-contenu'>
	<div class='row'>
		<div class='colonne4 align_right'>
				<label class='etiquette'>$msg[admin_Nom] &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				<input type=text name=form_nom value='!!nom!!' size=50 />
				</div>
		</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='colonne4 align_right'>
				<label class='etiquette'>$msg[admin_Utilisation] &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				<input type=text name=form_search_type value='!!search_type!!' size=50/>
				</div>
		</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='colonne4 align_right'>
				<label class='etiquette'>$msg[admin_Base] &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				<input type=text name=form_base value='!!base!!' size=50 />
				</div>
		</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='colonne4 align_right'>
				<label class='etiquette'>$msg[admin_URL] &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				<input type=text name=form_url value='!!url!!' size=50>
				</div>
		</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='colonne4 align_right'>
				<label class='etiquette'>$msg[admin_NumPort] &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				<input type=text name=form_port value='!!port!!' size='10' />
				</div>
		</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='colonne4 align_right'>
				<label class='etiquette'>$msg[admin_Format] &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				<input type=text name=form_format value='!!format!!' size='50' />
				</div>
		</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='colonne4 align_right'>
				<label class='etiquette'>$msg[z3950_sutrs] &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				<input type=text name=form_sutrs value='!!sutrs!!' size=50>
				</div>
		</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='colonne4 align_right'>
				<label class='etiquette'>$msg[admin_user] &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				<input type=text name=form_user value='!!user!!' size='50' />
				</div>
		</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='colonne4 align_right'>
				<label class='etiquette'>$msg[admin_password] &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				<input type=text name=form_password value='!!password!!' size=50>
				</div>
		</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='colonne4 align_right'>
				<label class='etiquette'>$msg[zbib_zfunc] &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				<input type=text name=form_zfunc value='!!zfunc!!' size=50>
				</div>
		</div>
	<div class='row'> </div>
	</div>
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value='$msg[76]'  onClick=\"document.location='./admin.php?categ=z3950&sub=zbib'\">&nbsp;
		<input class='bouton' type='button' value='$msg[admin_Attributs]' onClick=\"document.location='./admin.php?categ=z3950&sub=zattr&action=edit&bib_id=!!id!!'\">&nbsp;
		<input class='bouton' type='submit' value='$msg[77]' onClick=\"return test_form(this.form)\">
		</div>
	<div class='right'>
		<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete('!!id!!','!!nom!!')\" />
		</div>
	</div>
<div class='row'></div>
</form><script type='text/javascript'>document.forms['zbibform'].elements['form_nom'].focus();</script>
";

// $admin_zattr_form : template form attributs zbib - changed by martizva
$admin_zattr_form = "
<form class='form-$current_module' name=zattrform method=post action=\"./admin.php?categ=z3950&sub=zattr&action=update&bib_id=!!bib_id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<div class='form-contenu'>
!!code!!

	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='colonne4 align_right'>
				<label class='etiquette'>$msg[admin_Attributs] &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				<input type=text name=form_attr_attr value='!!attr_attr!!' size=25>
				<input type=hidden name=form_attr_bib_id value='!!attr_bib_id!!'>
				</div>
		</div>
	<div class='row'> </div>
	

</div>
	<div class='row'>
		<div class='left'>
			<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=z3950&sub=zattr&bib_id=!!attr_bib_id!!'\" />&nbsp;
			<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\" />&nbsp;
			</div>
		<div class='right'>
			<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete('bib_id=!!attr_bib_id!!&attr_libelle=!!attr_libelle!!','!!local_attr_libelle!!')\" />
		</div>
	</div>
<div class='row'></div>
</form><script type='text/javascript'>document.forms['zattrform'].elements['form_attr_libelle'].focus();</script>
";

// $admin_convert_end form - FIX MaxMan
$admin_convert_end = "
<br /><br />
<form class='form-$current_module' action=\"folow_import.php\" method=\"post\" name=\"destfic\">
<h3><span onclick='menuHide(this,event)'>".$msg["admin_conversion_end11"]."</span></h3>
<div class='form-contenu'>
	<div class='row'>";

if (($output=="yes")&&(!$noimport)) {
	$admin_convert_end .= "
		<input id=\"admin_conversion_end5\" type=\"radio\" name=\"deliver\" value=\"1\" checked><label for=\"admin_conversion_end5\">&nbsp;".$msg["admin_conversion_end5"]."</label><br />
		<input id=\"admin_conversion_end6\" type=\"radio\" name=\"deliver\" value=\"2\" checked><label for=\"admin_conversion_end6\">&nbsp;".$msg["admin_conversion_end6"]."</label><br />";
}
$admin_convert_end .= "
		<input id=\"admin_conversion_end7\" type=\"radio\" name=\"deliver\" value=\"3\" checked><label for=\"admin_conversion_end7\">&nbsp;".$msg["admin_conversion_end7"]."</label><br />
		<input type=\"hidden\" name=\"file_in\" value=\"$file_in\">
		<input type=\"hidden\" name=\"suffix\" value=\"$suffix\">
		<input type=\"hidden\" name=\"mimetype\" value=\"$mimetype\">
		</div>
	";
if (($output=="yes")&&(!$noimport)) {
	$admin_convert_end .= "<!--select_func_import-->";
}
$admin_convert_end .= "</div><div class='row'>
	<input type=\"submit\" class='bouton' value=\"".$msg["admin_conversion_end8"]."\"/>
	</div>
</form>
<br />
<div class='row'>
<span class='center'><b>".$msg["admin_conversion_end9"]."</b></span>";

if(!isset($n_errors)) $n_errors = 0;
if ($n_errors==0) {
	$admin_convert_end .= "<span class='center'><b>".$msg["admin_conversion_end10"]."</b></span>";
} else {
	$admin_convert_end .= "  $errors_msg  </div> ";
}

// $admin_calendrier_form : template form calendrier des jours d'ouverture
$admin_calendrier_form = "
<form class='form-$current_module' id='calendrier' name='calendrier' method='post' action='./admin.php?categ=calendrier'>
<h3><span onclick='menuHide(this,event)'>$msg[calendrier_titre_form]";
$admin_calendrier_form .= " - !!biblio_name!!<br />!!localisation!!";
$admin_calendrier_form .= "</span></h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='date_deb'>$msg[calendrier_date_debut]</label>
		<input class='saisie-10em' id='date_deb' type='text' name='date_deb' />
		&nbsp;
		<label class='etiquette' for='date_fin'>$msg[calendrier_date_fin]</label>
		<input class='saisie-10em' id='date_fin' type='text' name='date_fin' />
		</div>
	<div class='row'>
		<label class='etiquette' >$msg[calendrier_jours_concernes]</label>
		<label class='etiquette' for='j2'>$msg[1018]</label><input id='j2' type='checkbox' name='j2' value=1 />&nbsp;
		<label class='etiquette' for='j3'>$msg[1019]</label><input id='j3' type='checkbox' name='j3' value=1 />&nbsp;
		<label class='etiquette' for='j4'>$msg[1020]</label><input id='j4' type='checkbox' name='j4' value=1 />&nbsp;
		<label class='etiquette' for='j5'>$msg[1021]</label><input id='j5' type='checkbox' name='j5' value=1 />&nbsp;
		<label class='etiquette' for='j6'>$msg[1022]</label><input id='j6' type='checkbox' name='j6' value=1 />&nbsp;
		<label class='etiquette' for='j7'>$msg[1023]</label><input id='j7' type='checkbox' name='j7' value=1 />&nbsp;
		<label class='etiquette' for='j1'>$msg[1024]</label><input id='j1' type='checkbox' name='j1' value=1 />&nbsp;
		</div>
	<div class='row'>
		<label class='etiquette' for='commentaire'>$msg[calendrier_commentaire]</label>
		<input class='saisie-30em' id='commentaire' type='text' name='commentaire' />
		</div>
	<div class='row'>
		<label class='etiquette' for='duplicate'>$msg[calendrier_duplicate] :</label>
		!!duplicate_location!!
		</div>
	</div>
<div class='row'>
	<input type='hidden' name='loc' value='!!book_location_id!!'  />
	<input class='bouton' type='submit' value=' $msg[calendrier_ouvrir] ' onClick=\"this.form.faire.value='ouvrir'\" />&nbsp;
	<input class='bouton' type='submit' value=' $msg[calendrier_fermer] ' onClick=\"this.form.faire.value='fermer'\" />&nbsp;
	<input class='bouton' type='submit' value=' $msg[calendrier_initialization] ' onClick=\"this.form.faire.value='initialization'\" />&nbsp;
	<input type='hidden' name='faire' value='' />
	</div>
</form>
";

// $admin_calendrier_form : template form calendrier pour un mois pour les commentaires par jour
$admin_calendrier_form_mois_start = "
<form class='form-$current_module' id='calendrier' name='calendrier' method='post' action='./admin.php?categ=calendrier'>
<h3><span onclick='menuHide(this,event)'>$msg[calendrier_titre_form_commentaire]</span></h3>
<div class='form-contenu'>";

$admin_calendrier_form_mois_commentaire = " <input class='saisie-5em' id='commentaire' type='text' name='!!name!!' value='!!commentaire!!' />" ;
$admin_calendrier_form_mois_commentaire = " <textarea name='!!name!!' class='saisie-5em' rows='4' wrap='virtual'>!!commentaire!!</textarea>";
				
$admin_calendrier_form_mois_end = "	</div>
<div class='row'>
	<input class='bouton' type='button' value='$msg[76]' onClick=\"document.location='./admin.php?categ=calendrier'\">&nbsp;
	<input class='bouton' type='submit' value='$msg[77]' onClick=\"this.form.faire.value='commentaire'\">
	<input type='hidden' name='faire' value='' />
	<input type='hidden' name='loc' value='!!book_location_id!!'  />
	<input type='hidden' name='annee_mois' value='!!annee_mois!!' />
	</div>
</form>
";

// $admin_notice_statut_form : template form statuts de notices
$admin_notice_statut_form = "
<form class='form-$current_module' name=typdocform method=post action=\"./admin.php?categ=notices&sub=statut&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' ><strong>$msg[noti_statut_gestion]</strong></label>
		</div>
	<div class='row'>
		<label class='etiquette' for='form_libelle'>$msg[noti_statut_libelle]</label>
		</div>
	<div class='row'>
		<input type=text name='form_gestion_libelle' value='!!gestion_libelle!!' class='saisie-50em' />
		</div>
	<div class='row'>
		<label class='etiquette' for='form_visible_gestion'>$msg[noti_statut_visu_gestion]</label>
		<input type=checkbox name=form_visible_gestion value='1' !!checkbox_visible_gestion!! class='checkbox' />&nbsp;
		</div>
	<div class='row'>
		<div class='colonne5'>
			<label class='etiquette' for='form_class_html'>$msg[noti_statut_class_html]</label>
		</div>
		<div class='colonne_suite'>
			!!class_html!!
		</div>
		</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<label class='etiquette' ><strong>$msg[noti_statut_opac]</strong></label>
		</div>
	<div class='row'>
		<label class='etiquette' for='form_libelle'>$msg[noti_statut_libelle]</label>
		</div>
	<div class='row'>
		<input type=text name='form_opac_libelle' value='!!opac_libelle!!' class='saisie-50em' />
		</div>
	<div class='row'>&nbsp;</div>
	<div class='colonne2'>
		<label class='etiquette'>$msg[notice_statut_visibilite_generale]</label>
		</div>
	<div class='colonne_suite'>
		<label class='etiquette'>$msg[notice_statut_visibilite_restrict]</label>
		</div>
	<div class='colonne2'>
		<label class='etiquette' for='form_visible_opac'>$msg[noti_statut_visu_opac_form]</label>
		<input type=checkbox name=form_visible_opac value='1' !!checkbox_visible_opac!! class='checkbox' />
		</div>
	<div class='colonne_suite'>
		<label class='etiquette' for='form_visu_abon'>$msg[noti_statut_visible_opac_abon]</label>
		<input type=checkbox name=form_visu_abon value='1' !!checkbox_visu_abon!! class='checkbox' />
		</div>
	<div class='row'>&nbsp;</div>
	<div class='colonne2'>
		<label class='etiquette' for='form_expl_visu_expl'>$msg[noti_statut_visu_expl]</label>
		<input type=checkbox name=form_visu_expl value='1' !!checkbox_visu_expl!! class='checkbox' />
		</div>
	<div class='colonne_suite'>
		<label class='etiquette' for='form_expl_visu_abon'>$msg[noti_statut_expl_visible_opac_abon]</label>
		<input type=checkbox name=form_expl_visu_abon value='1' !!checkbox_expl_visu_abon!! class='checkbox' />
		</div>
	<div class='row'>&nbsp;</div>
	<div class='colonne2'>
		<label class='etiquette' for='form_explnum_visu_expl'>$msg[noti_statut_visu_explnum]</label>
		<input type=checkbox name=form_explnum_visu value='1' !!checkbox_explnum_visu!! class='checkbox' />
		</div>
	<div class='colonne_suite'>
		<label class='etiquette' for='form_expl_visu_abon'>$msg[noti_statut_explnum_visible_opac_abon]</label>
		<input type=checkbox name=form_explnum_visu_abon value='1' !!checkbox_explnum_visu_abon!! class='checkbox' />
		</div>
	<div class='row'>&nbsp;</div>
	<div class='colonne2'>
		<label class='etiquette' for='form_scan_request_opac'>".$msg['noti_statut_scan_request_opac']."</label>
		<input type='checkbox' name='form_scan_request_opac' value='1' !!checkbox_scan_request_opac!! class='checkbox' />
		</div>
	<div class='colonne_suite'>
		<label class='etiquette' for='form_scan_request_opac_abon'>".$msg['noti_statut_scan_request_opac_abon']."</label>
		<input type='checkbox' name='form_scan_request_opac_abon' value='1' !!checkbox_scan_request_opac_abon!! class='checkbox' />
		</div>
	<div class='row'></div>
	</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=notices&sub=statut'\">&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\">
		</div>
	<div class='right'>
		!!bouton_supprimer!!
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['typdocform'].elements['form_gestion_libelle'].focus();</script>
";

// $admin_notice_statut_form : template form statuts des etats de collections
$admin_collstate_statut_form = "
<form class='form-$current_module' name='admin' method=post action=\"./admin.php?categ=collstate&sub=statut&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' ><strong>".$msg["collstate_statut_gestion"]."</strong></label>
		</div>
	<div class='row'>
		<label class='etiquette' for='form_gestion_libelle'>".$msg["collstate_statut_libelle"]."</label>
		</div>
	<div class='row'>
		<input type=text name='form_gestion_libelle' value='!!gestion_libelle!!' class='saisie-50em' />
		</div>
	<div class='row'>
		<div class='colonne5'>
			<label class='etiquette' for='form_class_html'>".$msg["collstate_statut_class_html"]."</label>
		</div>
		<div class='colonne_suite'>
			!!class_html!!
		</div>
		</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<label class='etiquette' ><strong>".$msg["collstate_statut_opac"]."</strong></label>
		</div>
	<div class='row'>
		<label class='etiquette' for='form_opac_libelle'>".$msg["collstate_statut_libelle"]."</label>
		</div>
	<div class='row'>
		<input type=text name='form_opac_libelle' value='!!opac_libelle!!' class='saisie-50em' />
		</div>
	<div class='row'>&nbsp;</div>
	<div class='colonne2'>
		<label class='etiquette'>".$msg["collstate_statut_visibilite_generale"]."</label>
		</div>
	<div class='colonne_suite'>
		<label class='etiquette'>".$msg["collstate_statut_visibilite_restrict"]."</label>
		</div>
	<div class='colonne2'>
		<label class='etiquette' for='form_visible_opac'>".$msg["collstate_statut_visu_opac_form"]."</label>
		<input type=checkbox name=form_visible_opac value='1' !!checkbox_visible_opac!! class='checkbox' />
		</div>
	<div class='colonne_suite'>
		<label class='etiquette' for='form_visu_abon'>".$msg["collstate_statut_visible_opac_abon"]."</label>
		<input type=checkbox name=form_visu_abon value='1' !!checkbox_visu_abon!! class='checkbox' />
		</div>
	<div class='row'></div>
	</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=collstate&sub=statut'\">&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\">
		</div>
	<div class='right'>
		!!bouton_supprimer!!
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['admin'].elements['form_gestion_libelle'].focus();</script>
";

$admin_abonnements_periodicite_form = "
<form class='form-$current_module' name=typdocform method=post action=\"./admin.php?categ=abonnements&sub=periodicite&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='libelle'>$msg[abonnements_periodicite_libelle]</label>
		</div>
	<div class='row'>
		<input type=text name='libelle' value='!!libelle!!' class='saisie-50em' />
		</div>
	
	<div class='row'>
		<label class='etiquette' for='duree'>$msg[abonnements_periodicite_duree]</label>
		</div>
	<div class='row'>
		<input type=text name='duree' value='!!duree!!' class='saisie-50em' />
		</div>
				
	<div class='row'>
		<label class='etiquette' for='unite'>$msg[abonnements_periodicite_unite]</label>
		</div>
	<div class='row'>
		!!unite!!
		</div>
				
	<div class='row'>
		<label class='etiquette' for='seuil_periodicite'>$msg[seuil_periodicite]</label>
		</div>
	<div class='row'>
		<input type=text name='seuil_periodicite' value='!!seuil_periodicite!!' class='saisie-50em' />
		</div>
	
	<div class='row'>
		<label class='etiquette' for='retard_periodicite'>$msg[retard_periodicite]</label>
		</div>
	<div class='row'>
		<input type=text name='retard_periodicite' value='!!retard_periodicite!!' class='saisie-50em' />
		</div>
	
	<div class='row'>
		<label class='etiquette' for='consultation_duration'>".$msg["serialcirc_consultation_duration"]."</label>
		</div>
	<div class='row'>
		<input type=text name='consultation_duration' value='!!consultation_duration!!' class='saisie-50em' />
		</div>
			
	</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=abonnements&sub=periodicite'\">&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\">
		</div>
	<div class='right'>
		!!bouton_supprimer!!
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['typdocform'].elements['libelle'].focus();</script>
";

// $admin_procs_clas_form : template form classements de procédures
$admin_procs_clas_form = "
<form class='form-$current_module' name='proc_clas_form' method=post action=\"./admin.php?categ=proc&sub=clas&action=update&idproc_classement=!!idproc_classement!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='form_libproc_classement'>$msg[proc_clas_lib]</label>
		</div>
	<div class='row'>
		<input type=text name=form_libproc_classement value='!!libelle!!' class='saisie-50em' />
		</div>
	</div>

<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=proc&sub=clas'\" />&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\" />
		</div>
	<div class='right'>
		<input class='bouton' type='button' value='$msg[supprimer]' onClick=\"javascript:confirmation_delete(!!idproc_classement!!,'!!libelle_suppr!!')\" />
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['proc_clas_form'].elements['form_libproc_classement'].focus();</script>
";

$admin_menu_infopages = "
<h1><span>".$msg['admin_menu_opac']." > !!menu_sous_rub!!</span></h1>
";

// $admin_infopages_form : template form des pages d'info
$admin_infopages_form = "
<form class='form-$current_module' name='infopagesform' method=post action=\"./admin.php?categ=infopages&sub=infopages&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
	<div class='colonne2'>
		<div class='row'>
			<label class='etiquette' for='form_title_infopage'>".$msg['infopage_title_infopage']."</label>
			</div>
		<div class='row'>
			<input type=text name='form_title_infopage' value=\"!!title_infopage!!\" class='saisie-50em' />
			</div>
	</div>
	<div class='colonne_suite'>
		<div class='row'>
			<label class='etiquette' for='form_valid_infopage'>".$msg['infopage_valid_infopage']."</label>
			<input type=checkbox name='form_valid_infopage' value='1' !!checkbox!! class='checkbox' /><br />
			<label class='etiquette' for='form_restrict_infopage'>".$msg['infopage_restrict_infopage']."</label>
			<input type=checkbox name='form_restrict_infopage' value='1' !!restrict_checkbox!! class='checkbox' />
		</div>
	</div>
	<div class='row'>
		<label class='etiquette' for='form_content_infopage'>".$msg['infopages_content_infopage']."</label>
		</div>
	<div class='row'>
		<textarea id='form_content_infopage' name='form_content_infopage' cols='120' rows='40'>!!content_infopage!!</textarea>
		</div>

	</div>
	<div class='row'>
		<label class='etiquette' for='form_content_infopage'>".$msg['infopages_classement_list']."</label>
	</div>
	<div class='row'>
		<select data-dojo-type='dijit/form/ComboBox' id='classementGen_!!object_type!!' name='classementGen_!!object_type!!'>
			!!classements_liste!!
		</select>
	</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=infopages&sub=infopages'\" />&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\" />&nbsp;
		!!duplicate!!
		</div>
	<div class='right'>
		<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id!!,'!!libelle_suppr!!')\" />
		</div>
</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['infopagesform'].elements['form_title_infopage'].focus();</script>
";


// $admin_group_form : template groupe
$admin_group_form = "
<form class='form-$current_module' name='groupform' method=post action=\"./admin.php?categ=users&sub=groups&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='form_libelle'>".$msg['admin_usr_grp_lib']."</label>
		</div>
	<div class='row'>
		<input type=text id='form_libelle' name='form_libelle' value='!!libelle!!' class='saisie-50em' />
		</div>
	</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=users&sub=groups'\" />&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\" />
	</div>
	<div class='right'>
		<input class='bouton' type='button' value='".$msg['supprimer']."' onClick=\"javascript:confirmation_delete(!!id!!,'!!libelle_suppr!!')\" />
	</div>
</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['groupform'].elements['form_libelle'].focus();</script>
";

//Planificateur
$admin_menu_planificateur = "
<h1>".$msg["admin_menu_modules"]." > ".$msg["planificateur_admin_menu"]."<span> > !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=planificateur&sub=manager").">
		<a title='".$msg["planificateur_admin_manager"]."' href='./admin.php?categ=planificateur&sub=manager'>
			".$msg["planificateur_admin_manager"]."
		</a>
	</span>
	<span".ongletSelect("categ=planificateur&sub=reporting").">
		<a title='".$msg["planificateur_admin_reporting"]."' href='./admin.php?categ=planificateur&sub=reporting'>
			".$msg["planificateur_admin_reporting"]."
		</a>
	</span>
</div>";

// $admin_menu_authorities : menu Autorités
$admin_menu_authorities = "
<h1>$msg[admin_menu_authorities] <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=authorities&sub=origins").">
		<a title='".$msg['origins']."' href='./admin.php?categ=authorities&sub=origins&action='>
			".$msg['origins']."
		</a>
	</span>	
	<span".ongletSelect("categ=authorities&sub=statuts").">
		<a title='".$msg['20']."' href='./admin.php?categ=authorities&sub=statuts&action='>
			".$msg['20']."
		</a>
	</span>
	<span".ongletSelect("categ=authorities&sub=perso&type_field=author").">
		<a title='$msg[admin_menu_docs_perso_author]' href='./admin.php?categ=authorities&sub=perso&type_field=author&action='>
			$msg[admin_menu_docs_perso_author]
		</a>
	</span>
	<span".ongletSelect("categ=authorities&sub=perso&type_field=categ").">
		<a title='$msg[admin_menu_docs_perso_categ]' href='./admin.php?categ=authorities&sub=perso&type_field=categ&action='>
			$msg[admin_menu_docs_perso_categ]
		</a>
	</span>
	<span".ongletSelect("categ=authorities&sub=perso&type_field=publisher").">
		<a title='$msg[admin_menu_docs_perso_publisher]' href='./admin.php?categ=authorities&sub=perso&type_field=publisher&action='>
			$msg[admin_menu_docs_perso_publisher]
		</a>
	</span>
	<span".ongletSelect("categ=authorities&sub=perso&type_field=collection").">
		<a title='$msg[admin_menu_docs_perso_collection]' href='./admin.php?categ=authorities&sub=perso&type_field=collection&action='>
			$msg[admin_menu_docs_perso_collection]
		</a>
	</span>
	<span".ongletSelect("categ=authorities&sub=perso&type_field=subcollection").">
		<a title='$msg[admin_menu_docs_perso_subcollection]' href='./admin.php?categ=authorities&sub=perso&type_field=subcollection&action='>
			$msg[admin_menu_docs_perso_subcollection]
		</a>
	</span>
	<span".ongletSelect("categ=authorities&sub=perso&type_field=serie").">
		<a title='$msg[admin_menu_docs_perso_serie]' href='./admin.php?categ=authorities&sub=perso&type_field=serie&action='>
			$msg[admin_menu_docs_perso_serie]
		</a>
	</span>
	<span".ongletSelect("categ=authorities&sub=perso&type_field=tu").">
		<a title='$msg[admin_menu_docs_perso_tu]' href='./admin.php?categ=authorities&sub=perso&type_field=tu&action='>
			$msg[admin_menu_docs_perso_tu]
		</a>
	</span>
	<span".ongletSelect("categ=authorities&sub=perso&type_field=indexint").">
		<a title='$msg[admin_menu_docs_perso_indexint]' href='./admin.php?categ=authorities&sub=perso&type_field=indexint&action='>
			$msg[admin_menu_docs_perso_indexint]
		</a>
	</span>
	<span".ongletSelect("categ=authorities&sub=perso&type_field=skos").">
		<a title='$msg[admin_menu_docs_perso_skos]' href='./admin.php?categ=authorities&sub=perso&type_field=skos&action='>
			$msg[admin_menu_docs_perso_skos]
		</a>
	</span>
	<span".ongletSelect("categ=authorities&sub=authperso").">
		<a title='$msg[admin_menu_authperso]' href='./admin.php?categ=authorities&sub=authperso'>
			$msg[admin_menu_authperso]
		</a>
	</span>	
	<span".ongletSelect("categ=authorities&sub=templates").">
		<a title='$msg[admin_menu_authperso]' href='./admin.php?categ=authorities&sub=templates'>
			$msg[admin_menu_authperso_template]
		</a>
	</span>
</div>
";
/* en cours...
<span".ongletSelect("categ=notices&sub=perso").">
<a title='$msg[admin_menu_docs_perso_authperso]' href='./admin.php?categ=authorities&sub=perso&type_field=authperso&action='>
$msg[admin_menu_docs_perso_authperso]
</a>
</span>
<span".ongletSelect("categ=notices&sub=authperso").">
<a title='$msg[admin_menu_authperso]' href='./admin.php?categ=authorities&sub=authperso'>
$msg[admin_menu_authperso]
</a>
</span>
*/
// $admin_menu_opac_views : menu Vues Opac
$admin_menu_opac_views = "
<div class='hmenu'>
	<span".ongletSelect("categ=opac&sub=opac_view&section=list").">
		<a title='$msg[opac_view_admin_menu_list]' href='./admin.php?categ=opac&sub=opac_view&section=list'>
			$msg[opac_view_admin_menu_list]
		</a>
	</span>";
if($pmb_opac_view_activate == 2){		
	$admin_menu_opac_views.= "			
	<span".ongletSelect("categ=opac&sub=opac_view&section=affect").">
		<a title='$msg[opac_view_admin_menu_affect]' href='./admin.php?categ=opac&sub=opac_view&section=affect'>
			$msg[opac_view_admin_menu_affect]
		</a>
	</span>";
}
$admin_menu_opac_views.= "	
</div>
";

$admin_menu_cms_editorial = "
<h1>".$msg['editorial_content']." <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=cms_editorial&sub=type&elem=section").">
		<a title='".$msg['editorial_content_type_section']."' href='./admin.php?categ=cms_editorial&sub=type&elem=section&action='>
			".$msg['editorial_content_type_section']."
		</a>
	</span>	
	<span".ongletSelect("categ=cms_editorial&sub=type&elem=article").">
		<a title='".$msg['editorial_content_type_article']."' href='./admin.php?categ=cms_editorial&sub=type&elem=article&action='>
			".$msg['editorial_content_type_article']."
		</a>
	</span>
	<span".ongletSelect("categ=cms_editorial&sub=publication_state").">
		<a title='".$msg['editorial_content_publication_state']."' href='./admin.php?categ=cms_editorial&sub=publication_state&action='>
			".$msg['editorial_content_publication_state']."
		</a>
	</span>
</div>
";

$admin_liste_jscript = "
	<script type='text/javascript' src='./javascript/ajax.js'></script>
	<script type='text/javascript'>
		function showListItems(obj) {
		

			kill_frame_items();

			var pos=findPos(obj);
			var what = 	obj.getAttribute('what');
			var item = 		obj.getAttribute('item');
			var total = 		obj.getAttribute('total');
		
			var url='./admin/docs/frame_liste_items.php?what='+what+'&item='+item+'&total='+total;
			var list_view=document.createElement('iframe');
			list_view.setAttribute('id','frame_list_items');
			list_view.setAttribute('name','list_items');
			list_view.src=url;
		
			var att=document.getElementById('att');
			list_view.style.visibility='hidden';
			list_view.style.display='block';
			list_view=att.appendChild(list_view);

			list_view.style.left=(pos[0])+'px';
			list_view.style.top=(pos[1])+'px';

			list_view.style.visibility='visible';
		}

		function kill_frame_items() {
			var list_view=document.getElementById('frame_list_items');
			if (list_view)
				list_view.parentNode.removeChild(list_view);
		}
		</script>
";

// $admin_docnum_statut_form : template form statuts des documents numériques
$admin_docnum_statut_form = "
<form class='form-$current_module' name=typdocform method=post action=\"./admin.php?categ=docnum&sub=statut&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' ><strong>".$msg["docnum_statut_gestion"]."</strong></label>
	</div>
	<div class='row'>
		<label class='etiquette' for='form_libelle'>".$msg["docnum_statut_libelle"]."</label>
	</div>
	<div class='row'>
		<input type=text name='form_gestion_libelle' value='!!gestion_libelle!!' class='saisie-50em' />
	</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='colonne5'>
			<label class='etiquette' for='form_class_html'>".$msg["docnum_statut_class_html"]."</label>
		</div>
		<div class='colonne_suite'>
			!!class_html!!
		</div>
	</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<label class='etiquette' ><strong>".$msg["docnum_statut_opac"]."</strong></label>
	</div>
	<div class='row'>
		<label class='etiquette' for='form_libelle'>".$msg["docnum_statut_libelle"]."</label>
	</div>
	<div class='row'>
		<input type=text name='form_opac_libelle' value='!!opac_libelle!!' class='saisie-50em' />
	</div>
	<div class='row'>&nbsp;</div>
	<div class='colonne2'>
		<label class='etiquette'>".$msg["docnum_statut_visibilite_generale"]."</label>
	</div>
	<div class='colonne_suite'>
		<label class='etiquette'>".$msg["docnum_statut_visibilite_restrict"]."</label>
	</div>
	<div class='colonne2'>
		<label class='etiquette' for='form_visible_opac'>".$msg["docnum_statut_visu_opac_form"]."</label>
		<input type=checkbox name=form_visible_opac value='1' !!checkbox_visible_opac!! class='checkbox' />
	</div>
	<div class='colonne_suite'>
		<label class='etiquette' for='form_visible_opac_abon'>".$msg["docnum_statut_visu_opac_abon"]."</label>
		<input type=checkbox name=form_visible_opac_abon value='1' !!checkbox_visible_opac_abon!! class='checkbox' />
	</div>
	<div class='row'>&nbsp;</div>
	<div class='colonne2'>
		<label class='etiquette' for='form_consult_opac'>".$msg["docnum_statut_cons_opac_form"]."</label>
		<input type=checkbox name=form_consult_opac value='1' !!checkbox_consult_opac!! class='checkbox' />
	</div>
	<div class='colonne_suite'>
		<label class='etiquette' for='form_consult_opac_abon'>".$msg["docnum_statut_cons_opac_abon"]."</label>
		<input type=checkbox name=form_consult_opac_abon value='1' !!checkbox_consult_opac_abon!! class='checkbox' />
	</div>
	<div class='row'>&nbsp;</div>
	<div class='colonne2'>
		<label class='etiquette' for='form_visible_opac'>".$msg["docnum_statut_down_opac_form"]."</label>
		<input type=checkbox name=form_download_opac value='1' !!checkbox_download_opac!! class='checkbox' />
	</div>
	<div class='colonne_suite'>
		<label class='etiquette' for='form_download_opac_abon'>".$msg["docnum_statut_down_opac_abon"]."</label>
		<input type=checkbox name=form_download_opac_abon value='1' !!checkbox_download_opac_abon!! class='checkbox' />
	</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<label class='etiquette' for='form_thumbnail_visible_opac_override'>".$msg["docnum_statut_thumbnail_visible_opac_override"]."</label>
		<input type=checkbox name='form_thumbnail_visible_opac_override' value='1' !!checkbox_thumbnail_visible_opac_override!! class='checkbox' />
	</div>
	<div class='row'>&nbsp;</div>
</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=docnum&sub=statut'\">&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\">
	</div>
	<div class='right'>
		!!bouton_supprimer!!
	</div>
</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['typdocform'].elements['form_gestion_libelle'].focus();</script>
";

$admin_menu_loans = "
<h1>".$msg["admin_menu_loans"]." <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=loans&sub=perso").">
		<a title='".$msg["admin_menu_loans_perso"]."' href='./admin.php?categ=loans&sub=perso&action='>
			".$msg["admin_menu_loans_perso"]."
		</a>
	</span>
</div>
";

$admin_authorities_statut_form = "
<form class='form-$current_module' name='statutform' method=post action=\"./admin.php?categ=authorities&sub=statuts&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
<div class='row'>
<label class='etiquette' for='form_libelle'>".$msg["docnum_statut_libelle"]."</label>
		</div>
		<div class='row'>
			<input type=text name='form_gestion_libelle' value='!!gestion_libelle!!' class='saisie-50em' />
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<div class='colonne5'>
				<label class='etiquette' for='form_class_html'>".$msg["docnum_statut_class_html"]."</label>
			</div>
			<div class='colonne_suite'>
				!!class_html!!
			</div>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<div class='colonne5'>
				<label class='etiquette' for='form_used_for'>".$msg["authorities_used_for"]."</label>
				</div>
				<div class='colonne_suite'>
				!!list_authorities!!
				</div>
				</div>
				<div class='row'>&nbsp;</div>
				</div>
				<!-- Boutons -->
				<div class='row'>
				<div class='left'>
				<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=authorities&sub=statuts&action='\">&nbsp;
				<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\">
				</div>
				<div class='right'>
				!!bouton_supprimer!!
				</div>
				</div>
				<div class='row'></div>
				</form>
				<script type='text/javascript'>document.forms['statutform'].elements['form_gestion_libelle'].focus();</script>";

//Formulaire de contact
$admin_menu_contact_form = "
<h1>".$msg["admin_menu_opac"]." > ".$msg["admin_opac_contact_form"]."<span> > !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=contact_form&sub=parameters").">
		<a title='".$msg["admin_opac_contact_form_parameters"]."' href='./admin.php?categ=contact_form&sub=parameters'>
			".$msg["admin_opac_contact_form_parameters"]."
		</a>
	</span>
	<span".ongletSelect("categ=contact_form&sub=objects").">
		<a title='".$msg["admin_opac_contact_form_objects"]."' href='./admin.php?categ=contact_form&sub=objects'>
			".$msg["admin_opac_contact_form_objects"]."
		</a>
	</span>
	<span".ongletSelect("categ=contact_form&sub=recipients").">
		<a title='".$msg["admin_opac_contact_form_recipients"]."' href='./admin.php?categ=contact_form&sub=recipients'>
			".$msg["admin_opac_contact_form_recipients"]."
		</a>
	</span>
</div>";


$admin_menu_search_universes ="
<h1>".$msg["admin_menu_search_universes"]."</h1>
<div class=\"hmenu\">
	<span".ongletSelect("categ=search_universes&sub=universe").">
		<a title='".$msg["admin_search_universe"]."' href='./admin.php?categ=search_universes&sub=universe'>
			".$msg["admin_search_universe"]."
		</a>
	</span>
</div>";

$admin_menu_pnb = "
<h1>".$msg["admin_menu_pnb"]." <span>> !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=pnb&sub=param").">
		<a title='".$msg["admin_menu_pnb_param"]."' href='./admin.php?categ=pnb&sub=param&action='>
			".$msg["admin_menu_pnb_param"]."
		</a>
	</span>			
	<span".ongletSelect("categ=pnb&sub=quotas_simultaneous_loans").">
		<a title='" . $msg["admin_menu_pnb_quotas_simultaneous_loans"] . "' href='./admin.php?categ=pnb&sub=quotas_simultaneous_loans&action='>
			" . $msg["admin_menu_pnb_quotas_simultaneous_loans"] . "
		</a>
	</span>		
	<span".ongletSelect("categ=pnb&sub=quotas_prolongation").">
		<a title='" . $msg["admin_menu_pnb_quotas_prolongation"] . "' href='./admin.php?categ=pnb&sub=quotas_prolongation&action='>
			" . $msg["admin_menu_pnb_quotas_prolongation"] . "
		</a>
	</span>	
	<span".ongletSelect("categ=pnb&sub=drm_parameters").">
		<a title='" . $msg["admin_menu_pnb_drm_parameters"] . "' href='./admin.php?categ=pnb&sub=drm_parameters&action='>
			" . $msg["admin_menu_pnb_drm_parameters"] . "
		</a>
	</span>
</div>
";

$admin_vignette_menu ="
<h1>".$msg["admin_vignette_menu"]."</h1>
<div class=\"hmenu\">
	<span".ongletSelect("categ=vignette&sub=record").">
		<a title='$msg[admin_vignette_menu_record]' href='./admin.php?categ=vignette&sub=record&action='>
			$msg[admin_vignette_menu_record]
		</a>
	</span>
	<div style='display:none'><!--    TO DO    -->
    	<span".ongletSelect("categ=vignette&sub=author").">
    		<a title='$msg[admin_vignette_menu_author]' href='./admin.php?categ=vignette&sub=author&action='>
    			$msg[admin_vignette_menu_author]
    		</a>
    	</span>
    	<span".ongletSelect("categ=vignette&sub=categ").">
    		<a title='$msg[admin_vignette_menu_categ]' href='./admin.php?categ=vignette&sub=categ&action='>
    			$msg[admin_vignette_menu_categ]
    		</a>
    	</span>
    	<span".ongletSelect("categ=vignette&sub=publisher").">
    		<a title='$msg[admin_vignette_menu_publisher]' href='./admin.php?categ=vignette&sub=publisher&action='>
    			$msg[admin_vignette_menu_publisher]
    		</a>
    	</span>
    	<span".ongletSelect("categ=vignette&sub=collection").">
    		<a title='$msg[admin_vignette_menu_collection]' href='./admin.php?categ=vignette&sub=collection&action='>
    			$msg[admin_vignette_menu_collection]
    		</a>
    	</span>
    	<span".ongletSelect("categ=vignette&sub=subcollection").">
    		<a title='$msg[admin_vignette_menu_subcollection]' href='./admin.php?categ=vignette&sub=subcollection&action='>
    			$msg[admin_vignette_menu_subcollection]
    		</a>
    	</span>
    	<span".ongletSelect("categ=vignette&sub=serie").">
    		<a title='$msg[admin_vignette_menu_serie]' href='./admin.php?categ=vignette&sub=serie&action='>
    			$msg[admin_vignette_menu_serie]
    		</a>
    	</span>
    	<span".ongletSelect("categ=vignette&sub=tu").">
    		<a title='$msg[admin_vignette_menu_tu]' href='./admin.php?categ=vignette&sub=tu&action='>
    			$msg[admin_vignette_menu_tu]
    		</a>
    	</span>
    	<span".ongletSelect("categ=vignette&sub=indexint").">
    		<a title='$msg[admin_vignette_menu_indexint]' href='./admin.php?categ=vignette&sub=indexint&action='>
    			$msg[admin_vignette_menu_indexint]
    		</a>
    	</span>
	</div>
</div>";

$admin_composed_vedettes_menu = "
<h1>".$msg["admin_menu_composed_vedettes"]."</h1>
<div class='hmenu'>
	<span".ongletSelect("categ=composed_vedettes&sub=grammars").">
		<a title='".$msg["composed_vedettes_grammars"]."' href='./admin.php?categ=composed_vedettes&sub=grammars'>
			".$msg["composed_vedettes_grammars"]."
		</a>
	</span>
	<span".ongletSelect("categ=composed_vedettes&sub=schemes").">
		<a title='".$msg["ontology_skos_conceptscheme"]."' href='./admin.php?categ=composed_vedettes&sub=schemes'>
			".$msg["ontology_skos_conceptscheme"]."
		</a>
	</span>";
