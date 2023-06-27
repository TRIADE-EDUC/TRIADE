<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_account.tpl.php,v 1.24 2019-05-27 09:50:54 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

require_once($class_path.'/form_mapper/form_mapper.class.php');

global $rent_account_form_tpl, $base_path, $current_module, $pmb_use_uniform_title, $msg, $charset;

$rent_account_form_tpl = "
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
<form class='form-".$current_module."' id='account_form' name='account_form' method='post' action=\"./acquisition.php?categ=rent&sub=!!sub!!&action=update&id_bibli=!!entity_id!!&id=!!id!!\">
<h3>!!form_title!!</h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div id='el_account_coords' class='row'>
		<div class='row'>
			<div class='colonne2'>
				<div class='colonne2' >			
					<label class='etiquette'>".htmlentities($msg['acquisition_coord_lib'], ENT_QUOTES, $charset)."</label>
				</div>
				<div class='colonne_suite'>
					!!entity_label!!
				</div>
			</div>
		</div>
	</div>
	<div class='row'>
		<hr />
	</div>
	<div id='el_account_exercices' class='row'>
		<div class='row'>
			<label class='etiquette' for='account_exercices'>".htmlentities($msg['acquisition_account_exercice'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			!!exercices!!
		</div>
	</div>
	<div id='el_account_request_types' class='row'>
		<div class='row'>
			<label class='etiquette' for='account_request_types'>".htmlentities($msg['acquisition_account_request_type_name'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			!!request_types!!
		</div>
	</div>
	<div id='el_account_types' class='row'>
		<div class='row'>
			<label class='etiquette' for='account_types'>".htmlentities($msg['acquisition_account_type_name'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			!!types!!
		</div>
	</div>
	<div id='el_account_request_types' class='row'>
		<div class='row'>
			<label class='etiquette' for='account_desc'>".htmlentities($msg['acquisition_account_desc'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<textarea id='account_desc' name='account_desc' class='saisie-80em' cols='62' rows='6' wrap='virtual'>!!desc!!</textarea>
		</div>
	</div>
	<div class='row'>
		<div class= 'colonne3'>
			<div id='el_account_receipt_limit_date' class='row'>
				<div class='row'>
					<label class='etiquette' for='account_receipt_limit_date'>".htmlentities($msg['acquisition_account_receipt_limit_date'],ENT_QUOTES,$charset)."</label>
				</div>
				<div class='row'>
					<input type='text' id='account_receipt_limit_date' name='account_receipt_limit_date' value='!!receipt_limit_date!!' data-dojo-type='dijit/form/DateTextBox' required='true' />
				</div>
			</div>
		</div>
		<div class= 'colonne3'>
			<div id='el_account_receipt_effective_date' class='row'>
				<div class='row'>
					<label class='etiquette' for='account_receipt_effective_date'>".htmlentities($msg['acquisition_account_receipt_effective_date'],ENT_QUOTES,$charset)."</label>
				</div>
				<div class='row'>
					<input type='text' id='account_receipt_effective_date' name='account_receipt_effective_date' value='!!receipt_effective_date!!' data-dojo-type='dijit/form/DateTextBox' required='false' />
				</div>
			</div>
		</div>
		<div class= 'colonne3'>
			<div id='el_account_return_date' class='row'>
				<div class='row'>
					<label class='etiquette' for='account_return_date'>".htmlentities($msg['acquisition_account_return_date'],ENT_QUOTES,$charset)."</label>
				</div>
				<div class='row'>
					<input type='text' id='account_return_date' name='account_return_date' value='!!return_date!!' data-dojo-type='dijit/form/DateTextBox' required='false' />
				</div>
			</div>
		</div>
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
	<div id='el_account_title' class='row'>
		<div class='row'>
			<label class='etiquette' for='account_title'>".htmlentities($msg['acquisition_account_title'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<input type='text' data-form-name='account_title' id='account_title' name='account_title' value='!!title!!' class='saisie-80em'/>
		</div>
	</div>
	<div id='el_account_publisher' class='row'>
		<div class='row'>
			<label class='etiquette' for='account_publisher'>".htmlentities($msg['acquisition_account_num_publisher'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<input type='text' data-form-name='account_publisher' id='account_publisher' autfield='account_num_publisher' completion='publishers' class='saisie-20emr' value='!!publisher!!' autocomplete='off' />
			<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=editeur&caller=account_form&p1=account_num_publisher&p2=account_publisher&callback=account_maj_supplier_field&deb_rech='+this.form.account_publisher.value, 'selector')\"/>
			<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.account_publisher.value=''; this.form.account_num_publisher.value='0'; \" />
			<input type='hidden' data-form-name='account_num_publisher' id='account_num_publisher' name='account_num_publisher' value='!!num_publisher!!' />
		</div>
	</div>
	<div id='el_account_supplier' class='row'>
		<div class='row'>
			<label class='etiquette' for='account_supplier'>".htmlentities($msg['acquisition_account_num_supplier'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<input type='text' data-form-name='account_supplier' id='account_supplier' autfield='account_num_supplier' completion='fournisseurs' class='saisie-20emr' value='!!supplier!!' autocomplete='off' param1='!!entity_id!!' />
			<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=fournisseur&caller=account_form&param1=account_num_supplier&param2=account_supplier&id_bibli=!!entity_id!!&deb_rech='+this.form.account_supplier.value, 'selector')\"/>
			<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.account_supplier.value=''; this.form.account_num_supplier.value='0'; \" />
			<input type='hidden' data-form-name='account_num_supplier' id='account_num_supplier' name='account_num_supplier' value='!!num_supplier!!' />
		</div>
	</div>
	<div id='el_account_author' class='row'>
		<div class='row'>
			<label class='etiquette' for='account_author'>".htmlentities($msg['acquisition_account_num_author'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<input type='text' data-form-name='account_author' id='account_author' autfield='account_num_author' completion='authors' class='saisie-20emr' value='!!author!!' autocomplete='off' />
			<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=auteur&caller=account_form&param1=account_num_author&param2=account_author&deb_rech='+this.form.account_author.value, 'selector')\"/>
			<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.account_author.value=''; this.form.account_num_author.value='0'; \" />
			<input type='hidden' data-form-name='account_num_author' id='account_num_author' name='account_num_author' value='!!num_author!!' />
		</div>
	</div>
	<div id='el_account_event_formation' class='row'>
		<div class='row'>
			<label class='etiquette' for='account_event_formation'>".htmlentities($msg['acquisition_account_event_formation'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<input type='text' data-form-name='account_event_formation' id='account_event_formation' name='account_event_formation' value='!!event_formation!!' class='saisie-80em' />
		</div>
	</div>
	<div id='el_account_event_orchestra' class='row'>
		<div class='row'>
			<label class='etiquette' for='account_event_orchestra'>".htmlentities($msg['acquisition_account_event_orchestra'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<input type='text' data-form-name='account_event_orchestra' id='account_event_orchestra' name='account_event_orchestra' value='!!event_orchestra!!' class='saisie-80em'/>
		</div>
	</div>
	<div id='el_account_event_date' class='row'>
		<div class='row'>
			<label class='etiquette' for='account_event_date'>".htmlentities($msg['acquisition_account_event_date'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<input type='text' data-form-name='account_event_date' id='account_event_date' name='account_event_date' value='!!event_date!!' data-dojo-type='dijit/form/DateTextBox' required='false'/>
		</div>
	</div>
	<div id='el_account_event_place' class='row'>
		<div class='row'>
			<label class='etiquette' for='account_event_place'>".htmlentities($msg['acquisition_account_event_place'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<input type='text' data-form-name='account_event_place' id='account_event_place' name='account_event_place' value='!!event_place!!' class='saisie-80em' />
		</div>
	</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<hr />
	</div>
	<div id='el_account_pricing_system' class='row'>
		<div class='row'>
			<label class='etiquette' for='account_pricing_sytem'>".htmlentities($msg['acquisition_account_num_pricing_system'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			!!pricing_systems!! 
			<span id='account_grid_see' !!pricing_system_grid_see_visible!!>
				!!pricing_system_grid_see!!
			</span>
		</div>
	</div>
	<div id='el_account_minutage' class='row'>
		<div class='row'>
			<div class='colonne10'>
				<div class='row'>
					<label class='etiquette' for='account_time'>".htmlentities($msg['acquisition_account_time'],ENT_QUOTES,$charset)."</label>
				</div>
				<div class='row'>
					<input type='number' data-form-name='account_time' min='0' id='account_time' name='account_time' value='!!time!!' class='saisie-5em' onchange=\"account_update_price_from_time(this.value);\"/>
				</div>
			</div>
			<div class='colonne10'>
				<div class='row'>
					<label class='etiquette' for='account_percent'>".htmlentities($msg['acquisition_account_percent'],ENT_QUOTES,$charset)."</label>
				</div>
				<div class='row'>
					<input type='text' id='account_percent' name='account_percent' value='!!percent!!' class='saisie-5em' onchange=\"account_update_price_from_percent(this.value);\" !!percent_enabled!!/>
				</div>
			</div>
			<div class='colonne10'>
				<div class='row'>
					<label class='etiquette' for='account_price'>".$msg['acquisition_account_price']."</label>
				</div>
				<div class='row'>
					<input type='text' id='account_price' name='account_price' value='!!price!!' class='saisie-5em'/>
					<a onclick=\"account_update_price_from_time(document.getElementById('account_time').value); \" title=\"".$msg['refresh']."\" alt=\"".$msg['refresh']."\" style='cursor:pointer;font-size:1.5em;vertical-align:middle;' />
						&nbsp;<i class='fa fa-refresh'></i>&nbsp;
					</a>
				</div>
			</div>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<hr />
		</div>
	</div>
	<div id='el_account_web_minutage' class='row'>
		<div class='row'>
			<label class='etiquette' for='account_web'>".htmlentities($msg['acquisition_account_web'],ENT_QUOTES,$charset)."</label>
			<input type='checkbox' id='account_web' name='account_web' value='1' !!web_checked!! onchange=\"account_change_checkbox_web(this.checked);\" />
		</div>
		<div class='row'>
			<div class='colonne10'>
				<div class='row'>
					<label class='etiquette' for='account_web_percent'>".htmlentities($msg['acquisition_account_web_percent'],ENT_QUOTES,$charset)."</label>
				</div>
				<div class='row'>
					<input type='text' id='account_web_percent' name='account_web_percent' value='!!web_percent!!' class='saisie-5em' onchange=\"account_update_web_price_from_web_percent(this.value);\" !!web_enabled!!/>
				</div>
			</div>
			<div class='colonne10'>
				<div class='row'>
					<label class='etiquette' for='account_web_price'>".$msg['acquisition_account_web_price']."</label>
				</div>
				<div class='row'>
					<input type='text' id='account_web_price' name='account_web_price' value='!!web_price!!' class='saisie-5em' !!web_enabled!!/>
					<a onclick=\"account_update_web_price_from_web_percent(document.getElementById('account_web_percent').value); \" title=\"".$msg['refresh']."\" alt=\"".$msg['refresh']."\" style='cursor:pointer;font-size:1.5em;vertical-align:middle;' />
						&nbsp;<i class='fa fa-refresh'></i>&nbsp;
					</a>
				</div>
			</div>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<hr />
		</div>
	</div>
	<div id='el_account_comment' class='row'>
		<div class='row'>
			<label class='etiquette' for='account_comment'>".htmlentities($msg['acquisition_account_comment'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<textarea id='account_comment' name='account_comment' class='saisie-80em' cols='62' rows='6' wrap='virtual'>!!comment!!</textarea>
		</div>
	</div>
	<div id='el_account_request_status' class='row'>
		<div class='row'>
			<label class='etiquette' for='account_request_status'>".htmlentities($msg['acquisition_account_request_status'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			!!request_status!!
		</div>
	</div>
	<div class='row'>&nbsp;</div>
</div>	
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onclick=\"history.go(-1);\" />&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onclick=\"return test_form(this.form)\" />
	</div>
	<div class='right'>
		!!button_delete!!
	</div>
	<div class='row'></div>
</div>
</form>
<br /><br />
<div class='row'></div>
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
		return true;
	}
	!!js_function_form_hide_fields!!
	document.forms['account_form'].elements['account_title'].focus();
	ajax_parse_dom();
</script>		
";

if (isset($pmb_use_uniform_title) && $pmb_use_uniform_title) {
    if(form_mapper::isMapped('account')){
        $rent_account_form_tpl.= "
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