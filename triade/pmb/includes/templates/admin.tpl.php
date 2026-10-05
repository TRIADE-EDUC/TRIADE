<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: admin.tpl.php,v 1.348.2.3 2024/11/28 09:03:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $pmb_sur_location_activate, $pmb_transferts_actif;
global $opac_websubscribe_show, $opac_serialcirc_active;
global $file_in, $suffix, $mimetype, $output, $admin_menu_new, $msg;
global $acquisition_active, $demandes_active, $pmb_map_activate;
global $base_path, $charset;
global $admin_layout, $current_module, $admin_layout_end, $admin_user_javascript;
global $admin_npass_form, $admin_user_form, $fiches_active, $thesaurus_concepts_active, $dsi_active, $semantic_active, $pmb_extension_tab, $frbr_active, $modelling_active;
global $user_acquisition_adr_form, $admin_param_form, $password_field, $admin_user_list, $cms_active, $admin_user_alert_row, $location_map_tpl;
global $admin_location_form_sur_loc_part, $admin_location_content_form, $admin_section_content_form, $admin_statut_content_form;
global $admin_typdoc_js_content_form;
global $admin_proc_view_remote, $admin_zattr_form, $admin_convert_end, $noimport, $n_errors, $errors_msg;
global $admin_calendrier_form, $admin_calendrier_form_mois_start, $admin_calendrier_form_mois_commentaire, $admin_calendrier_form_mois_end;
global $admin_infopages_content_form;
global $admin_liste_jscript, $admin_authorities_statut_content_form;
global $acquisition_rent_requests_activate;
global $pmb_contribution_area_activate;
global $animations_active;

if(!isset($file_in)) $file_in = '';
if(!isset($suffix)) $suffix = '';
if(!isset($mimetype)) $mimetype = '';
if(!isset($output)) $output = '';

// ---------------------------------------------------------------------------
//	$admin_menu_new : Menu vertical de l'administration
// ---------------------------------------------------------------------------

global $class_path;
require_once($class_path."/modules/module_admin.class.php");
require_once($class_path."/list/tabs/list_tabs_ui.class.php");
require_once($class_path."/list/tabs/list_tabs_admin_ui.class.php");
$module_admin = module_admin::get_instance();
$admin_menu_new = $module_admin->get_left_menu();

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

function update_form_rights() {
    let num_roles = new Array();
	let form_roles = document.getElementsByName('form_roles[]');
    for(let i=1; i<form_roles.length; i++) {
        if(form_roles[i].checked) {
            num_roles.push(form_roles[i].value);
        }
    }
	var xhr = new http_request();
	xhr.request('".$base_path."/ajax.php?module=admin&categ=users&action=rights_content_form&num_roles='+num_roles.join(','),false,'',true, function(data) {
		var form_rights = document.getElementById('form_rights');
		form_rights.innerHTML = xhr.get_text();
	});
	    
}
-->
</script>
<form class='form-$current_module' name='userform' method='post' action='./admin.php?categ=users&sub=users&action=update&id=!!id!!' data-csrf='true'>
<h3><span onclick='menuHide(this,event)'>!!title!!</span></h3>
<div class='form-contenu'>
	<div class='row'>
		<div class='colonne3'>
			<label class='etiquette' for='form_login'>$msg[91] &nbsp;</label><br />
			<input type='text' class='saisie-20em' id='form_login' name='form_login' value='!!login!!' />
		</div>
		<div class='colonne3'>
			<label class='etiquette' for='form_nom'>$msg[67] &nbsp;</label><br />
			<input type='text' class='saisie-20em' id='form_nom' name='form_nom' value='!!nom!!' />
		</div>
		<div class='colonne3'>
			<label class='etiquette' for='form_prenom'>$msg[68] &nbsp;</label><br />
			<input type='text' class='saisie-20em' id='form_prenom' name='form_prenom' value='!!prenom!!' />
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
    		<div class='colonne2'>
    			<span class='ui-panel-display'>
        			<label class='etiquette' for='form_user_email'>".$msg['email_user']." &nbsp;</label><br />
        			<input type='text' class='saisie-20em' id='form_user_email' name='form_user_email' value='!!user_email!!' />
    			</span>
			</div>
    		<div class='colonne2'>
    			<span class='ui-panel-display'>
        			<label class='etiquette' for='form_user_email_recipient'>".$msg['email_recipient']." &nbsp;<i class='fa fa-info-circle' title='" . $msg['email_recipient_info'] . "'></i>&nbsp;</label>
        			<input type='text' class='saisie-20em' id='form_user_email_recipient' name='form_user_email_recipient' value='!!user_email_recipient!!' />
    			</span>
			</div>
		</div>
		<div class='colonne3'>
			<span class='ui-panel-display'>
				<input type='checkbox' class='checkbox' !!alter_resa_mail!! value='1' id='form_user_alert_resamail' name='form_user_alert_resamail' />
				<label class='etiquette' for='form_user_alert_resamail'>".$msg['alert_resa_user_mail']." &nbsp;</label>
			</span>
			<span class='ui-panel-display'>
				".($pmb_contribution_area_activate ? "<input type='checkbox' class='checkbox' !!alert_contrib_mail!! value='1' id='form_user_alert_contribmail' name='form_user_alert_contribmail' />
				<label class='etiquette' for='form_user_alert_contribmail'>".$msg['alert_contrib_user_mail']." &nbsp;</label>" : "")."
			</span>
			<span class='ui-panel-display'>
				".($acquisition_active ? "<input type='checkbox' class='checkbox' !!alert_sugg_mail!! value='1' id='form_user_alert_suggmail' name='form_user_alert_suggmail' />
				<label class='etiquette' for='form_user_alert_suggmail'>".$msg['alert_sugg_user_mail']." &nbsp;</label>" : "")."
			</span>
		</div>
		<div class='colonne3'>
			<span class='ui-panel-display'>
				".($demandes_active ? "<input type='checkbox' class='checkbox' !!alert_demandes_mail!! value='1' id='form_user_alert_demandesmail' name='form_user_alert_demandesmail' />
				<label class='etiquette' for='form_user_alert_demandesmail'>".$msg['alert_demandes_user_mail']." &nbsp;</label>" : "")."
			</span>
			<span class='ui-panel-display'>
				".($opac_websubscribe_show ? "<input type='checkbox' class='checkbox' !!alert_subscribe_mail!! value='1' id='form_user_alert_subscribemail' name='form_user_alert_subscribemail' />
				<label class='etiquette' for='form_user_alert_subscribemail'>".$msg['alert_subscribe_user_mail']." &nbsp;</label>" : "")."
			</span>
		</div>
		<div class='colonne3'>
			<span class='ui-panel-display'>
				".($animations_active ? "<input type='checkbox' class='checkbox' !!alert_user_alert_animation_mail!! value='1' id='form_alert_user_alert_animation_mail' name='form_alert_user_alert_animation_mail' />
				<label class='etiquette' for='form_alert_user_alert_animation_mail'>".$msg['alert_animation_user_mail']." &nbsp;</label>" : "")."
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
			<input type='checkbox' class='checkbox' !!alert_serialcirc_mail!! value='1' id='form_user_alert_serialcircmail' name='form_user_alert_serialcircmail' />
			<label class='etiquette' for='form_user_alert_serialcircmail'>".$msg['alert_subscribe_serialcirc_mail']." &nbsp;</label>
		</div>
		<div class='row'><span class='space-wide-space'>&nbsp;</span><hr /></div>
	</div>
	" : "")."
	!!password_field!!

<div class='row'>
	<div class='row'>
		<label class='etiquette'>$msg[nb_enreg_par_page]</label>
	</div>
	<div class='colonne4'>
	<!--	Nombre d'enregistrements par page en recherche	-->
		<label class='etiquette' for='form_nb_per_page_search'>$msg[900]</label><br />
		<input type='text' class='saisie-10em' id='form_nb_per_page_search' name='form_nb_per_page_search' value='!!nb_per_page_search!!' size='4' />
	</div>
	<div class='colonne4'>
	<!--	Nombre d'enregistrements par page en sélection d'autorités	-->
		<label class='etiquette' for='form_nb_per_page_select'>{$msg[901]}</label><br />
		<input class='saisie-10em' type='text' id='form_nb_per_page_select' name='form_nb_per_page_select' value='!!nb_per_page_select!!' size='4' />
	</div>
	<div class='colonne_suite'>
		<label class='etiquette' for='form_nb_per_page_gestion'>{$msg[902]}</label><br />
		<input type='text' class='saisie-10em' id='form_nb_per_page_gestion' name='form_nb_per_page_gestion' value='!!nb_per_page_gestion!!' size='4' />
	</div>
</div>
<div class='row'><hr /></div>

<div class='row'>
    <div class='row'>
		<label class='etiquette'>".$msg['roles']."</label>
	</div>
	!!roles_content_form!!
	<div class='row'>
        <label class='etiquette'>$msg[92]</label>
    </div>
    !!rights_content_form!!
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
<div class='row'></div>
</form>
";


$user_acquisition_adr_form = "
<div class='row'>
	<div class='child'>
		<div class='colonne2'><label for='adr_liv[!!id_bibli!!]' style='all:unset'>".htmlentities($msg['acquisition_adr_liv'], ENT_QUOTES, $charset)."</label></div>
		<div class='colonne2'><label for='adr_fac[!!id_bibli!!]' style='all:unset'>".htmlentities($msg['acquisition_adr_fac'], ENT_QUOTES, $charset)."</label></div>
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
<form class='form-$current_module' id='paramform_!!id_param!!' name='paramform' method='post' action='./admin.php?categ=param&action=update&id_param=!!id_param!!#justmodified'>
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
				<label class='etiquette' for='form_valeur_param_!!id_param!!'>$msg[1604] &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				<textarea id='form_valeur_param_!!id_param!!' name='form_valeur_param' rows='10' cols='90' wrap='virtual' !!data-translation!!>!!valeur_param!!</textarea>
				</div>
		</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='colonne5 align_right'>
				<label class='etiquette' for='comment_param_!!id_param!!'>".$msg['param_explication']." &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				<textarea id='comment_param_!!id_param!!' name='comment_param' rows='10' cols='90' wrap='virtual'>!!comment_param!!</textarea>
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
		!!user_selection!!
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
	<table class='brd'>
		!!brd_columns!!
	</table>
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

// commented because now use the confirmation_delete function used also from the other submodules
// so we show also the name we want to delete - Marco Vaninetti


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
        ".get_expandBase_button('el14', 'notice_map_onglet_title')."
	</h3>
</div>

<div id='el14Child' class='child' etirable='yes' title='".htmlentities($msg['notice_map_onglet_title'],ENT_QUOTES, $charset)."'>
	<div id='el14Child_0' title='".htmlentities($msg['notice_map'],ENT_QUOTES, $charset)."' movable='yes'>
		<div id='el14Child_0b' class='row'>
			!!location_map!!
		</div>
	</div>
</div>";

// $admin_location_content_form : template form des localisations
$admin_location_content_form = "
<div class='row'>
	<label class='etiquette' for='form_cb'>$msg[103]</label>
</div>
<div class='row'>
	<input type=text name='form_libelle' value=\"!!libelle!!\" class='saisie-50em' data-translation-fieldname='location_libelle'/>
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
!!location_coords!!
<input type='hidden' name='form_actif' value='1'>
";

// $admin_typdoc_js_content_form : template form types doc
$admin_typdoc_js_content_form = "
<script type='text/javascript'>
function test_form(form) {
	if(form.form_libelle.value.length == 0) {
		alert('".$msg[98]."');
		return false;
	}
	if(form.form_pret && (isNaN(form.form_pret.value) || form.form_pret.value.length == 0)) {
		alert('".$msg[119]."');
		return false;
	}
	if(form.form_short_loan_duration && (isNaN(form.form_short_loan_duration.value) || form.form_short_loan_duration.value.length == 0)) {
		alert('".$msg['short_loan_duration_error']."');
		return false;
	}
	if(form.form_resa && (isNaN(form.form_resa.value) || form.form_resa.value.length == 0)) {
		alert('".$msg['resa_duration_error']."');
		return false;
	}
	return true;
}
</script>
";

// $admin_proc_view_remote : template form procédures stockées
$admin_proc_view_remote = "
<h3><span onclick='menuHide(this,event)'>>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
	!!additional_information!!
	</div>
	<div class=colonne2>
		<div class='row'>
		<label class='etiquette' for='f_proc_name'>$msg[remote_procedures_procedure_name]</label>
		</div>
		<div class='row'>
		<input type='text' readonly id='f_proc_name' name='f_proc_name' value='!!name!!' maxlength='255' class='saisie-50em' />
		</div>
	</div>
	<div class='row'>
		<label class='etiquette' for='f_proc_code'>$msg[remote_procedures_procedure_sql]</label>
		</div>
	<div class='row'>
		<textarea cols='80' readonly rows='8' id='f_proc_code' name='f_proc_code'>!!code!!</textarea>
		</div>
	<div class='row'>
		<label class='etiquette' for='f_proc_comment'>$msg[remote_procedures_procedure_comment]</label>
		</div>
	<div class='row'>
		<input type='text' readonly id='f_proc_comment' name='f_proc_comment' value='!!comment!!' maxlength='255' class='saisie-50em' />
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
	<span class='center'><b>".$msg["admin_conversion_end9"]."</b></span>
</div>
<div class='row'>";
if(!isset($n_errors)) $n_errors = 0;
if ($n_errors==0) {
	$admin_convert_end .= "<span class='center'><b>".$msg["admin_conversion_end10"]."</b></span>";
} else {
	$admin_convert_end .= "  $errors_msg  ";
}
$admin_convert_end .= "</div>";

// $admin_calendrier_form : template form calendrier des jours d'ouverture
$admin_calendrier_form = "
<form class='form-$current_module' id='calendrier' name='calendrier' method='post' action='./admin.php?categ=calendrier'>
<h3><span onclick='menuHide(this,event)'>$msg[calendrier_titre_form]";
$admin_calendrier_form .= " - !!biblio_name!!<br />!!localisation!!";
$admin_calendrier_form .= "</span></h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='date_deb'>$msg[calendrier_date_debut] :</label>";
$admin_calendrier_form .= get_input_date("date_deb", "date_deb");
$admin_calendrier_form .= "&nbsp;
		<label class='etiquette' for='date_fin'>$msg[calendrier_date_fin] :</label>";
$admin_calendrier_form .= get_input_date("date_fin", "date_fin");
$admin_calendrier_form .= "</div>
	<div class='row'>
		<label class='etiquette' >$msg[calendrier_jours_concernes] :</label>
		<label class='etiquette' for='j2'>$msg[1018]</label><input id='j2' type='checkbox' name='j2' value=1 />&nbsp;
		<label class='etiquette' for='j3'>$msg[1019]</label><input id='j3' type='checkbox' name='j3' value=1 />&nbsp;
		<label class='etiquette' for='j4'>$msg[1020]</label><input id='j4' type='checkbox' name='j4' value=1 />&nbsp;
		<label class='etiquette' for='j5'>$msg[1021]</label><input id='j5' type='checkbox' name='j5' value=1 />&nbsp;
		<label class='etiquette' for='j6'>$msg[1022]</label><input id='j6' type='checkbox' name='j6' value=1 />&nbsp;
		<label class='etiquette' for='j7'>$msg[1023]</label><input id='j7' type='checkbox' name='j7' value=1 />&nbsp;
		<label class='etiquette' for='j1'>$msg[1024]</label><input id='j1' type='checkbox' name='j1' value=1 />&nbsp;
        <input type='button' class='bouton_small align_middle' value='".$msg['tout_cocher_checkbox']."' onclick='check_checkbox(\"j1|j2|j3|j4|j5|j6|j7\",1);'>
		<input type='button' class='bouton_small align_middle' value='".$msg['tout_decocher_checkbox']."' onclick='check_checkbox(\"j1|j2|j3|j4|j5|j6|j7\",0);'>
		</div>
	<div class='row'>
		<label class='etiquette' for='commentaire'>$msg[calendrier_commentaire] :</label>
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

$admin_authorities_statut_content_form = "
<div class='row'>&nbsp;</div>
<h3>".$msg["authorities_catalog_search"]."</h3>
<div class='row'>
	<div class='colonne5'>
		<label class='etiquette' for='form_autocomplete_search_1'>".$msg["authorities_autocomplete_search"]."</label>
	</div>
	<div class='colonne_suite'>
		!!form_autocomplete_search!!
	</div>
</div>
<div class='row'>&nbsp;</div>
<div class='row'>
	<div class='colonne5'>
		<label class='etiquette' for='form_autority_searcher_1'>".$msg["authorities_autority_search"]."</label>
	</div>
	<div class='colonne_suite'>
		!!form_autority_searcher!!
	</div>
</div>
<div class='row'>&nbsp;</div>
";

