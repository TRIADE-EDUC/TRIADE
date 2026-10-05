<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_account.tpl.php,v 1.26.2.2.2.1 2025/03/19 11:04:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $class_path;
require_once($class_path.'/form_mapper/form_mapper.class.php');

global $base_path, $pmb_use_uniform_title, $msg, $charset;
global $rent_account_js_form_tpl, $rent_account_content_form_tpl;

$rent_account_content_form_tpl = "
!!coords_content_form!!
<div class='row'>
	<hr />
</div>
<div id='el_account_exercices' class='row'>
    !!exercices_content_form!!
</div>
<div id='el_account_request_types' class='row'>
    !!request_types_content_form!!
</div>
<div id='el_account_types' class='row'>
    !!types_content_form!!
</div>
<div id='el_account_desc' class='row'>
    !!desc_content_form!!
</div>
<div id='el_account_dates' class='row'>
    !!dates_content_form!!
</div>
<div class='row'>&nbsp;</div>
<div class='row'>
	<hr />
</div>
<div id='el_account_uniform_title' class='row'>
	<div class='row'>
		<label class='etiquette' for='account_uniform_title'>".htmlentities($msg['acquisition_account_num_uniform_title'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='row'>
		<input type='text' data-form-name='account_uniform_title' id='account_uniform_title' autfield='account_uniform_title' completion='titre_uniforme' class='saisie-80emr' value='!!uniform_title!!' autocomplete='off' />
		<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=titre_uniforme&caller=account_form&callback=tu_account_mapper_callback&param1=account_num_uniform_title&param2=account_uniform_title&deb_rech='+encodeURIComponent(this.form.account_uniform_title.value), 'selector')\"/>
		<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.account_uniform_title.value=''; this.form.account_num_uniform_title.value='0'; \" />
		<a onclick=\"account_set_uniform_title_fields(); \" title=\"".$msg['refresh']."\" alt=\"".$msg['refresh']."\" style='cursor:pointer;font-size:1.5em;vertical-align:middle;' />
			&nbsp;<i class='fa fa-refresh'></i>&nbsp;
		</a>
		<input type='hidden' data-form-name='account_num_uniform_title' id='account_num_uniform_title' name='account_num_uniform_title' value='!!num_uniform_title!!' />
	</div>
	<div class='row'>&nbsp;</div>
</div>
!!uniform_title_informations_content_form!!
<div class='row'>&nbsp;</div>
<div class='row'>
	<hr />
</div>
<div id='el_account_pricing_system' class='row'>
!!pricing_system_content_form!!
</div>
<div id='el_account_minutage' class='row'>
    !!minutage_content_form!!
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<hr />
	</div>
</div>
<div id='el_account_web_minutage' class='row'>
    !!web_minutage_content_form!!
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<hr />
	</div>
</div>
<div id='el_account_comment' class='row'>
	!!comment_content_form!!
</div>
<div id='el_account_request_status' class='row'>
	!!request_status_content_form!!
</div>
";

$rent_account_js_form_tpl = "
<script src='javascript/pricing_systems.js'></script>
<script src='javascript/select.js'></script>
<script src='javascript/ajax.js'></script>
<script type='text/javascript'>
	function update_pricing_systems() {
		var account_exercices_selector = document.getElementById('account_exercices');
		var exercice_id = account_exercices_selector[account_exercices_selector.selectedIndex].value;
		var xhr = new http_request();
		xhr.request('".$base_path."/ajax.php?module=acquisition&categ=rent&sub=get_pricing_systems&num_exercice='+exercice_id,false,'',true, function(data) {
			var pricing_systems_selector = document.getElementById('account_num_pricing_system');
			pricing_systems_selector.innerHTML = '';
			var pricing_systems = JSON.parse(xhr.get_text());
			for (var i = 0; i < pricing_systems.length; i++) {
				var option = document.createElement('option');
				option.value = pricing_systems[i].id;
				option.innerHTML = pricing_systems[i].label;
				pricing_systems_selector.appendChild(option);
			}
			account_selected_grid(pricing_systems_selector);
		});
		    
	}
</script>
<script type='text/javascript'>
	function test_form(form){
		if(!parseInt(form.elements['account_exercices'].value)) {
			alert('".addslashes($msg['acquisition_account_num_exercice_mandatory'])."');
			return false;
		}
		if(!parseInt(form.elements['account_num_supplier'].value)) {
			alert('".addslashes($msg['acquisition_account_num_supplier_mandatory'])."');
			return false;
		}
        if(form.elements['account_unlimited_rights'] && form.elements['account_unlimited_rights'].checked) {
            if(form.elements['account_rights_date'] && form.elements['account_rights_date'].value) {
                alert('".addslashes($msg['acquisition_account_rights_conflict'])."');
                return false;
            }
        }
		return true;
	}
	!!js_function_form_hide_fields!!
	document.forms['account_form'].elements['account_title'].focus();
	ajax_parse_dom();
</script>
";

if (isset($pmb_use_uniform_title) && $pmb_use_uniform_title) {
    if(form_mapper::isMapped('account')){
        $rent_account_js_form_tpl.= "
			<!-- dojo demande de location from expression -->
			<script type='text/javascript'>
				require(['dojo/ready', 'apps/form_mapper/FormMapper', 'dojo/_base/lang'], function(ready, FormMapper, lang){
				     ready(function(){
				     	var formMapper = new FormMapper('account', 'account_form');
				     	window['formMapperCallback'] = lang.hitch(formMapper, formMapper.selectorCallback, 'tu');
				     });
				});
			</script>";
    }
}