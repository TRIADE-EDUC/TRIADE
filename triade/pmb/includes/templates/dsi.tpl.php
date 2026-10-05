<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dsi.tpl.php,v 1.113.2.1.2.1 2025/04/16 13:45:59 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $include_path, $msg, $current_module;

require_once $include_path."/templates/export_param.tpl.php";

global $dsi_desc_field, $dsi_desc_first_desc;
global $dsi_desc_other_desc, $dsi_bannette_content_form, $dsi_bannette_content_form_abo, $dsi_bannette_equation_assoce, $dsi_ban_list_diff; 
global $dsi_bannette_classement_content_form;
global $dsi_bannette_common_content_form, $dsi_bannette_entity_records_content_form, $dsi_bannette_entity_records_content_form_abo;
global $dsi_bannette_archive_content_form, $dsi_bannette_diffusions_history_content_form;
global $dsi_bannette_options_content_form, $dsi_bannette_access_rights_content_form, $dsi_bannette_access_rights_content_form_abo;
global $dsi_bannette_export_content_form, $dsi_bannette_document_content_form;
global $dsi_flux_js_script, $dsi_flux_content_form, $dsi_bannette_form_selvars;
global $dsi_active;

$dsi_desc_field = "
<script src='javascript/ajax.js'></script>
<div id='att' style='z-Index:1000'></div>

<div class='row'>
	<div class='colonne'>
		<div class='row'>
			<label for='f_categ0'>".$msg['dsi_ban_form_desc']."</label>
		</div>
		<div class='row'>
			!!cms_categs!!
			<div id='addcateg'/></div>
		</div>
	</div>
</div>
<script type='text/javascript'>
	ajax_parse_dom();
	function add_categ() {
		templates.add_completion_field('f_categ', 'f_categ_id', 'categories_mul');
	}
	function fonction_selecteur_categ() {
		name=this.getAttribute('id').substring(4);
		name_id = name.substr(0,7)+'_id'+name.substr(7);
		openPopUp('./select.php?what=categorie&caller=saisie_bannette&p1='+name_id+'&p2='+name+'&dyn=1', 'selector_category');
	}
</script>";
$dsi_desc_first_desc = "
<div class='row'>
	<input type='hidden' id='max_categ' name='max_categ' value=\"!!max_categ!!\" />
	<input type='text' class='saisie-30emr' id='f_categ!!icateg!!' name='f_categ!!icateg!!' value=\"!!categ_libelle!!\" completion=\"categories_mul\" autfield=\"f_categ_id!!icateg!!\" />
		
	<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=categorie&caller='+this.form.name+'&p1=f_categ_id!!icateg!!&p2=f_categ!!icateg!!&dyn=1&parent=0&deb_rech=', 'selector_category')\" />
	<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_categ!!icateg!!.value=''; this.form.f_categ_id!!icateg!!.value='0'; \" />
	<input type='hidden' name='f_categ_id!!icateg!!' id='f_categ_id!!icateg!!' value='!!categ_id!!' />
	<input type='button' class='bouton' value='+' onClick=\"add_categ();\"/>
</div>";
$dsi_desc_other_desc = "
<div class='row'>
	<input type='text' class='saisie-30emr' id='f_categ!!icateg!!' name='f_categ!!icateg!!' value=\"!!categ_libelle!!\" completion=\"categories_mul\" autfield=\"f_categ_id!!icateg!!\" />
		
	<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_categ!!icateg!!.value=''; this.form.f_categ_id!!icateg!!.value='0'; \" />
	<input type='hidden' name='f_categ_id!!icateg!!' id='f_categ_id!!icateg!!' value='!!categ_id!!' />
</div>";

$dsi_bannette_classement_content_form = "
<div class='colonne_suite'>
	<div class='row'>
		<label class='etiquette' for='num_classement'>$msg[dsi_ban_form_classement]</label>
	</div>
	<div class='row'>
		!!num_classement!!
	</div>
</div>
";

$dsi_bannette_common_content_form = "
<div class='row'>
	<div class='colonne2'>
		<div class='row'>
			<label class='etiquette' for='nom_bannette'>$msg[dsi_ban_form_nom]</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-30em' id='nom_bannette' name='nom_bannette' value=\"!!nom_bannette!!\" />
		</div>
	</div>
	!!classement!!
</div>
<div class='row'>
	<label for='comment_gestion' class='etiquette'>$msg[dsi_ban_form_com_gestion]</label>
</div>
<div class='row'>
	<textarea id='comment_gestion' name='comment_gestion' cols='120' rows='2' wrap='virtual'>!!comment_gestion!!</textarea>
</div>
<div class='row'>
	<label for='comment_public' class='etiquette'>$msg[dsi_ban_form_com_public]</label>
</div>
<div class='row'>
	!!comment_public_info_empr!!
</div>
<div class='row'>
	<textarea id='comment_public' name='comment_public' cols='120' rows='2' wrap='virtual'>!!comment_public!!</textarea>
</div>
<div class='row'></div>
<div class='row'>
	<label for='comment_public' class='etiquette'>$msg[dsi_ban_form_tpl]</label>
	!!bannette_tpl_list!!
</div>
<div class='row'>
	<label for='entete_mail' class='etiquette'>$msg[dsi_ban_form_entete_mail]</label>
</div>
<div class='row'>
	<textarea id='entete_mail' name='entete_mail' cols='120' rows='6' wrap='virtual'>!!entete_mail!!</textarea>
</div>
<div class='row'>
	!!info_empr!!
</div>
<div class='row'>
	<label for='piedpage_mail' class='etiquette'>$msg[dsi_ban_form_piedpage_mail]</label>
</div>
<div class='row'>
	<textarea id='piedpage_mail' name='piedpage_mail' cols='120' rows='6' wrap='virtual'>!!piedpage_mail!!</textarea>
</div>
";

$dsi_bannette_entity_records_content_form = "
<div class='row'>
	<label for='notice_tpl' class='etiquette'>$msg[dsi_ban_form_select_notice_tpl]</label>
	!!notice_tpl!!
</div>

<div class='row'>&nbsp;</div>	

<div class='row'>
	<label for='display_notice_in_every_group' class='etiquette'>".$msg['dsi_ban_form_display_notice_in_every_group']."</label>
	<input type='checkbox' id='display_notice_in_every_group' name='display_notice_in_every_group' value='1' !!display_notice_in_every_group!! class='saisie-simple'>
</div>

<div class='row'>&nbsp;</div>

<div class='row'>	
	<div class='colonne2'>
		<label for='notice_tpl' class='etiquette'>$msg[dsi_ban_form_regroupe_pperso]</label>
		<input type='radio' name='group_type' value='0' !!checked_group_pperso!! class='saisie-simple'>
		!!pperso_group!!
	</div>	
	<div class='colonne2'>
		<label for='notice_tpl' class='etiquette'>$msg[dsi_ban_form_froup_facette]</label>
		<input type='radio' name='group_type' value='1' !!checked_group_facette!! class='saisie-simple'>
		!!facette_group!!
	</div>	
</div>
";

$dsi_bannette_archive_content_form = "
<div class='row'>
	<div class='colonne2'>
		<label for='archive_number' class='etiquette'>$msg[dsi_archive_number]</label>
		<input type='text' class='saisie-5em' id='archive_number' name='archive_number'  value=\"!!archive_number!!\" />
	</div>
	<div class='colonne_suite'>
		
	</div>
</div>
";

$dsi_bannette_diffusions_history_content_form = "
<div class='row'>
	<div class='colonne2'>
		<label for='diffusions_history' class='etiquette'>$msg[dsi_diffusions_history]</label>
		<input type='checkbox' id='diffusions_history' name='diffusions_history'  value='1' !!diffusions_history!! />
	</div>
	<div class='colonne_suite'>
	
	</div>
</div>
";

$dsi_bannette_options_content_form = "
<div class='row'>
	<div class='colonne2'>
		<label for='date_last_remplissage' class='etiquette'>$msg[dsi_ban_date_last_remp]</label>
		!!date_last_remplissage!!
		</div>
	<div class='colonne_suite'>
		<label for='date_last_envoi' class='etiquette'>$msg[dsi_ban_date_last_envoi]</label>
		!!date_last_envoi!!
	</div>
</div>
!!archive!!
!!diffusions_history!!
<div class='row'><hr /></div>

<div class='row'>
	<label for='proprio_bannette' class='etiquette'>$msg[dsi_ban_proprio_bannette]</label>
	!!proprio_bannette!!
	</div>
<div class='row'></div>

<div class='row'>
	<div class='colonne3'>
		<label for='bannette_auto' class='etiquette'>$msg[dsi_ban_form_ban_auto]</label>
		<input type='checkbox' id='bannette_auto' name='bannette_auto' !!bannette_auto!! value=\"1\" />
		</div>
	<div class='colonne3'>
		<label for='periodicite' class='etiquette'>$msg[dsi_ban_form_periodicite]</label>
		<input type='text' class='saisie-5em' id='periodicite' name='periodicite' value=\"!!periodicite!!\" />
		</div>
	</div>
<div class='row'>
	<div class='colonne3'>
		<label for='diffusion_email' class='etiquette'>$msg[dsi_ban_form_diff_email]</label>
		<input type='checkbox' id='diffusion_email' name='diffusion_email' !!diffusion_email!! value=\"1\" />
	</div>
	<div class='colonne3'>
		<label for='nb_notices_diff' class='etiquette'>$msg[dsi_ban_form_nb_notices_diff]</label>
		<input type='text' id='nb_notices_diff' name='nb_notices_diff' class='saisie-5em' value=\"!!nb_notices_diff!!\" />
	</div>
	<div class='colonne3'>		
		<label for='bannette_aff_notice_number' class='etiquette'>".$msg["dsi_bannette_aff_notice_number"]."</label>
		<input type='checkbox' id='bannette_aff_notice_number' name='bannette_aff_notice_number' !!bannette_aff_notice_number!! value=\"1\" />
	</div>
</div>
<div class='row'>
	<label for='update_type' class='etiquette'>$msg[dsi_ban_update_type]</label>
	!!update_type!!
</div>
";

$dsi_bannette_access_rights_content_form = "
<div class='row'>
	<div class='colonne3'>
		<label for='categorie_lecteurs' class='etiquette'>$msg[dsi_ban_form_categ_lect]</label><br />
		!!categorie_lecteurs!!
	</div>
	<div class='colonne3'>
		<label for='groupe_lecteurs' class='etiquette'>$msg[dsi_ban_form_groupe_lect]</label><br />
		!!groupe_lecteurs!!
	</div>
	<div class='colonne3'>
		<label for='majautocateg' class='etiquette'>".$msg['dsi_ban_confirm_modif_categ']."</label>
		<input type='checkbox' id='majautocateg' name='majautocateg' value='1' /><br />
		<label for='majautogroupe' class='etiquette'>".$msg['dsi_ban_confirm_modif_group']."</label>
		<input type='checkbox' id='majautogroupe' name='majautogroupe' value='1' /><br />
	</div>
</div>
<div class='row'>	
	<label for='bannette_opac_accueil' class='etiquette'>".$msg['bannette_opac_page_accueil']."</label>
	<input type='checkbox' id='bannette_opac_accueil' name='bannette_opac_accueil' !!bannette_opac_accueil_check!! value=\"1\" />
</div>
";

$dsi_bannette_export_content_form = "
<div class='row'>
	<div class='colonne2'>
	<label for='typeexport' class='etiquette'>".$msg['dsi_ban_typeexport']." : </label>
		!!typeexport!!
	</div>
	<div class='colonne_suite'>
	<label for='prefixe_fichier' class='etiquette'>".$msg['dsi_ban_prefixe_fichier']." : </label>
		<input type='text' id='prefixe_fichier' name='prefixe_fichier' class='saisie-15em' value=\"!!prefixe_fichier!!\" />
	</div>
	<div class='row'></div>
</div>
<div class='row' id='liste_parametre' style='!!display_liste_param!!'>&nbsp;!!form_param!!</div>
";

$dsi_bannette_document_content_form = "
<div class='row'>
	<label class='etiquette'>".$msg["dsi_ban_document_title"]."</label>
</div>
<div class='row'>&nbsp;</div>
<div class='row'>
	<div class='colonne2'>
		<label for='document_generate' class='etiquette'>".$msg["dsi_ban_document_generate"]."</label>
		<input type='checkbox' id='document_generate' name='document_generate' !!document_generate!! value='1'>	
	</div>
	<div class='colonne_suite'>
		<label for='document_notice_tpl' class='etiquette'>".$msg["dsi_ban_document_notice_tpl"]."</label>
		!!document_notice_tpl!!
	</div>
</div>
<div class='row'>
	<div class='colonne2'>
		<label for='document_group' class='etiquette'>".$msg["dsi_ban_document_group"]."</label>
		<input type='checkbox' id='document_group' name='document_group' !!document_group!! value='1'>	

	</div>
	<div class='colonne_suite'>
		<label for='document_add_summary' class='etiquette'>".$msg["dsi_ban_document_add_summary"]."</label>
		<input type='checkbox' id='document_add_summary' name='document_add_summary' !!document_add_summary!! value='1'>
	</div>
</div>
<div class='row'>&nbsp;</div>
<div class='row'>
	<div class='colonne2' style='display:none' >		
		<label for='document_insert_docnum' class='etiquette'>".$msg["dsi_ban_document_insert_docnum"]."</label>
		<input type='checkbox' id='document_insert_docnum' name='document_insert_docnum' !!document_insert_docnum!! value='1'>	
	</div>
	<div class='colonne_suite'>		
	</div>
</div>
";

// $dsi_bannette_content_form : form saisie des bannettes publiques
$dsi_bannette_content_form = "
!!bannette_common_content_form!!
!!bannette_entity_content_form!!

<div class='row'><hr /></div>
!!bannette_options_content_form!!
<div class='row'>
	<div class='colonne3'>
		<label for='statut_not_account' class='etiquette'>".$msg["dsi_ban_statut_not_account"]."</label>
		<input type='checkbox' id='statut_not_account' name='statut_not_account' !!statut_not_account!! value=\"1\" />
	</div>
	<div class='colonne3'>
		<label for='associated_campaign' class='etiquette'>".$msg["dsi_ban_associated_campaign"]."</label>
		<input type='checkbox' id='associated_campaign' name='associated_campaign' !!associated_campaign!! value=\"1\" />
	</div>
</div>
<div class='row'>
	<label for='num_sender' class='etiquette'>".$msg['dsi_ban_senders']."</label>
	!!senders!!
</div>
<div class='row'><hr /></div>
!!bannette_access_rights_content_form!!
<div class='row'><hr /></div>	
!!desc_fields!!
<div class='row'><hr /></div>

<div class='row'>
	<label for='num_panier' class='etiquette'>".$msg['dsi_panier_diffuser']."</label>
	!!num_panier!!
</div>
<div class='row'>&nbsp;</div>
<div class='row'>
	<label for='limite_type' class='etiquette'>".$msg['dsi_ban_type_cumul']." : </label>
	!!limite_type!!
	<label for='limite_nombre' class='etiquette'>".$msg['dsi_ban_cumul_taille']." : </label>
	<input type='text' id='limite_nombre' name='limite_nombre' class='saisie-5em' value=\"!!limite_nombre!!\" />
</div>

<div class='row'>&nbsp;</div>
!!bannette_export_content_form!!
<div class='row'><hr /></div>
!!bannette_document_content_form!!
<div class='row'>&nbsp;</div>
<input type='hidden' name='form_actif' value='1'>
";

$dsi_bannette_entity_records_content_form_abo = "
<div class='row'>
	<label for='notice_tpl' class='etiquette'>$msg[dsi_ban_form_select_notice_tpl]</label>
	!!notice_tpl!!
</div>

<div class='row'>&nbsp;</div>

<div class='row'>
	<div class='colonne2'>
		<label for='group_type_0' class='etiquette'>$msg[dsi_ban_form_regroupe_pperso]</label>
		<input type='radio' id='group_type_0' name='group_type' value='0' !!checked_group_pperso!! class='saisie-simple'>
		!!pperso_group!!
	</div>	
    <div class='colonne2'>
        <label for='group_type_1' class='etiquette'>$msg[dsi_ban_form_froup_facette]</label>
		<input type='radio' id='group_type_1' name='group_type' value='1' !!checked_group_facette!! class='saisie-simple'>
		!!facette_group!!
    </div>
</div>
";

$dsi_bannette_access_rights_content_form_abo = "
<div class='row'>
	!!categorie_lecteurs!!
</div>
<div class='row'>
	!!groupe_lecteurs!!
</div>
";

// $dsi_bannette_content_form_abo : form saisie des bannettes privées
$dsi_bannette_content_form_abo = "
!!bannette_common_content_form!!
!!bannette_entity_content_form!!
	
<div class='row'><hr /></div>
!!bannette_options_content_form!!
!!bannette_access_rights_content_form!!

<div class='row'>&nbsp;</div>
!!bannette_export_content_form!!
<input type='hidden' name=id_empr value='!!id_empr!!' />
<input type='hidden' name='form_actif' value='1'>
";

// $dsi_bannette_equation_assoce : template pour association des équations/bannette
$dsi_bannette_equation_assoce = "
<form class='form-$current_module' id='bannette_equation_assoce' name='bannette_equation_assoce' method='post' action='!!form_action!!' >
<h3>$msg[dsi_ban_equ_assoce] : !!nom_bannette!!</h3>
<div class='form-contenu'>
	!!bannette_equations_saved!!
	!!classement!!<br />
	!!equations!!
	</div>
<div class='row'>
	<div class='left'>
		<input type='submit' class='bouton' value='$msg[77]' />
		<input type='hidden' name='id_bannette' value='!!id_bannette!!' />
		<input type='hidden' name='faire' value='enregistrer' />
		<input type='hidden' name='form_cb' value=\"!!form_cb_hidden!!\" />
		<input type='button' class='bouton' value=\"$msg[bt_retour]\" onClick=\"document.location='./dsi.php?categ=bannettes&sub=pro&id_bannette=&suite=search!!link_pagination!!&form_cb=!!form_cb!!';\" />
		</div>
	<div class='right'>
		<input type='button' class='bouton' value=\"".$msg['dsi_ban_affect_lecteurs']."\" onclick=\"document.location='./dsi.php?categ=bannettes&sub=pro&suite=affect_lecteurs!!link_pagination!!&id_bannette=!!id_bannette!!&form_cb=!!form_cb!!'\"/>
		</div>
	</div>
<div class='row'></div>
</form>" ;

// template pour la liste bannettes en diffusion
$dsi_ban_list_diff = "
<h1>!!titre!!</h1>
<form class='form-$current_module' id='bannette_lecteurs_assoce' name='bannette_lecteurs_assoce' method='post' action='!!form_action!!' >
<h3>$msg[dsi_dif_act_ban_contenu]
		<input type='button' class='bouton_small align_middle' value='".$msg['tout_cocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,1);'>
		<input type='button' class='bouton_small align_middle' value='".$msg['tout_decocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,0);'>
</h3>
<div class='form-contenu'>
	<script type='text/javascript' src='./javascript/sorttable.js'></script>
	<script>	
		function confirm_dsi_ban_diffuser() {
       		result = confirm(\"".$msg['confirm_dsi_ban_diffuser']."\");
       		if(result) {
       			return true;
			} else
           		return false;
    	}
    	function confirm_dsi_dif_full_auto() {
       		result = confirm(\"".$msg['confirm_dsi_dif_full_auto']."\");
       		if(result) {
       			return true;
			} else
           		return false;
    	}
	</script>
	<table border='0' width='100%' class='sortable'>
		!!list!!
		</table>
	</div>

<div class='row'>
	<div class='left'>
		<input type='button' class='bouton' name='bt_vider' value=\"".$msg['dsi_ban_vider']."\" onclick=\"this.form.suite.value='vider'; this.form.submit();\" />
		<input type='button' class='bouton' name='bt_remplir' value=\"".$msg['dsi_ban_remplir']."\" onclick=\"this.form.suite.value='remplir'; this.form.submit();\" />
		<input type='button' class='bouton' name='bt_voircontenu' value=\"".$msg['dsi_ban_visualiser']."\" onclick=\"this.form.suite.value='visualiser'; this.form.submit();\" />
		<input type='button' class='bouton' name='bt_diffuser' value=\"".$msg['dsi_ban_diffuser']."\" onclick=\"if(confirm_dsi_ban_diffuser()){this.form.suite.value='diffuser'; this.form.submit();}\" />
		<input type='button' class='bouton' name='bt_diffuser' value=\"".$msg['dsi_dif_full_auto']."\" onclick=\"if(confirm_dsi_dif_full_auto()){this.form.suite.value='full_auto'; this.form.submit();}\" />
		<input type='hidden' name='suite' value='' />
		<input type='hidden' name='id_classement' value='!!id_classement!!' />
		<input type='hidden' name='form_cb' value='!!cle!!' />
		</div>
	<div class='right'>
		<input type='button' class='bouton' name='gen_document' value=\"".$msg["dsi_ban_gen_document"]."\" onclick=\"this.form.suite.value='gen_document'; this.form.submit();\" />	
		<input type='button' class='bouton' name='bt_exporter' value=\"".$msg['dsi_ban_exporter_diff']."\" onclick=\"this.form.suite.value='exporter'; this.form.submit();\" />
		</div>
	</div>
<div class='row'></div>
</form>
";

$dsi_flux_js_script = "
<script type='text/javascript'>
<!--
    function getSort(id,name){
		document.forms.saisie_rss_flux.id_tri_rss_flux.value=id;
		var name = document.createTextNode(name);
		var span = document.getElementById('rss_flux_sort');
		while(span.firstChild){
			span.removeChild(span.firstChild);
		}
		span.appendChild(name);
		
	}
-->
</script>
<script type='text/javascript'>
	document.ready=disableTemplateChoice();
	function disableTemplateChoice(){
		if(document.getElementById('export_court_flux').checked){
			document.forms['saisie_rss_flux'].elements['notice_tpl'].disabled='disabled';
			document.forms['saisie_rss_flux'].elements['format_flux'].disabled='disabled';
		}else if(!document.getElementById('export_court_flux').checked){
			document.forms['saisie_rss_flux'].elements['notice_tpl'].disabled='';
			document.forms['saisie_rss_flux'].elements['format_flux'].disabled='';
		}
	}
    function changeTemplateChoice(){
        if(document.getElementById('notice_tpl').value == '0'){
            document.getElementById('rss_flux_default_format').style.display = 'block';
		}else {
			document.getElementById('rss_flux_default_format').style.display = 'none';
		}
    }
    addLoadEvent(function() {changeTemplateChoice();});
</script>
";

// $dsi_flux_content_form : form saisie des flux RSS
$dsi_flux_content_form = "
<div class='row'><hr /></div>
<div class='row'>
	<label class='etiquette' for='notice_title_tpl'>$msg[dsi_flux_form_format_flux_items]</label>
</div>
<div class='row'>
	&nbsp;
</div>
<div class='row'>
	<label class='etiquette'>$msg[dsi_flux_form_format_flux_item_title]</label>
</div>
<div class='row'>
	!!sel_notice_title_tpl!!
</div>
<div class='row'>
	&nbsp;
</div>
<div class='row'>
	<label class='etiquette'>$msg[dsi_flux_form_format_flux_item_description]</label>
</div>
<div class='row'>
	<input type='radio' name='type_export' onclick='disableTemplateChoice()' value='tpl' id='tpl_rss_flux' !!tpl_rss_flux!! />
	!!sel_notice_tpl!!
    <div id='rss_flux_default_format' class='row'>
        <div class='row'>
			<label class='etiquette' for='format_flux'>$msg[dsi_flux_form_format_flux_default]</label>
		</div>
		<div class='row'>    			
            !!format_flux_default!!
        </div>
	</div>
</div>
<div class='row'>
	<input type='radio' name='type_export' onclick='disableTemplateChoice()' value='export_court' id='export_court_flux' !!export_court!! />
	<label class='etiquette' for='export_court_flux'>$msg[dsi_flux_form_short_export]</label>
</div>
<div class='row'>
	&nbsp;
</div>
<div class='row'>
	<label class='etiquette' for='notice_link_tpl'>$msg[dsi_flux_form_format_flux_item_link]</label>
</div>
<div class='row'>
	!!sel_notice_link_tpl!!
</div>
<div class='row'><hr /></div>
<div class='row'>
	<div class='colonne2'>
		<label class='etiquette'>$msg[dsi_flux_form_paniers]</label>
		!!paniers!!
	</div>";

if($dsi_active == 1) {
	$dsi_flux_content_form .= "
	<div class='colonne_suite'>
		<label class='etiquette'>$msg[dsi_flux_form_bannettes]</label>
		!!bannettes!!
	</div>";
}

$dsi_flux_content_form .= "
</div>
<div class='row'><hr /></div>
<div class='row'>
    <a href=# onClick=\"document.getElementById('history').src='./sort.php?action=0&caller=rss_flux'; document.getElementById('history').style.display='';return false;\" alt=\"".$msg['tris_dispos']."\" title=\"".$msg['tris_dispos']."\">
		<img src='".get_url_icon('orderby_az.gif')."' class='align_middle' style='margin:0px 3px'>
	</a>
	<input type='hidden' value='!!tri!!' name='id_tri_rss_flux'/>
	<span id='rss_flux_sort'>
		!!tri_name!!
	</span>
	<div class='row'><hr /></div>
</div>
	
";

$dsi_bannette_form_selvars="
<select name='!!selector_name!!' id='!!selector_name!!'>
	<option value=!!empr_name!!>".$msg["selvars_empr_name"]."</option>
	<option value=!!empr_first_name!!>".$msg["selvars_empr_first_name"]."</option>
	<option value=!!empr_sexe!!>".$msg["selvars_empr_sexe"]."</option>
	<option value=!!empr_cb!!>".$msg["selvars_empr_cb"]."</option>
	<option value=!!empr_login!!>".$msg["selvars_empr_login"]."</option>
	<option value=!!empr_mail!!>".$msg["selvars_empr_mail"]."</option>
	<option value=!!empr_name_and_adress!!>".$msg["selvars_empr_name_and_adress"]."</option>
	<option value=!!empr_all_information!!>".$msg["selvars_empr_all_information"]."</option>
	<option value='".htmlentities("<a href='".$opac_url_base."empr.php?code=!!code!!&emprlogin=!!login!!&date_conex=!!date_conex!!'>".$msg["selvars_empr_auth_opac"]."</a>",ENT_QUOTES, $charset)."'>".$msg["selvars_empr_auth_opac"]."</option>
	<option value=!!public!!>".$msg["selvars_public"]."</option>
	<option value=!!date!!>".$msg["selvars_date"]."</option>
	<option value=!!equation!!>".$msg["selvars_equation"]."</option>
	<option value=!!nb_notice!!>".$msg["selvars_nb_notices"]."</option>
</select>
<input type='button' class='bouton' value=\" ".$msg["admin_mailtpl_form_selvars_insert"]." \" onClick=\"insert_vars_!!selector_name!!(document.getElementById('!!selector_name!!'), document.getElementById('!!dest_dom_node!!')); return false; \" />
<script type='text/javascript'>

	function insert_vars_!!selector_name!!(theselector,dest){
		var selvars='';
		for (var i=0 ; i< theselector.options.length ; i++){
			if (theselector.options[i].selected){
				selvars=theselector.options[i].value ;
				break;
			}
		}
		if(!selvars) return ;

		if(typeof(tinyMCE)== 'undefined' || dest.id == 'comment_public'){
			var start = dest.selectionStart;
		    var start_text = dest.value.substring(0, start);
		    var end_text = dest.value.substring(start);
		    dest.value = start_text+selvars+end_text;
		}else{
			tinyMCE_execCommand('mceInsertContent',false,selvars);
		}
	}


</script>
";