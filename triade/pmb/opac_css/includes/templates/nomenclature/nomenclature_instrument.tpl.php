<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_instrument.tpl.php,v 1.3 2023/08/17 09:47:56 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg, $nomenclature_instrument_content_form_tpl, $nomenclature_instrument_dialog_tpl;

/*
 * Exemple de test de la complétion Ajax des instruments
 	// $param1 : id du pupitre préféré. si 0 on retourne tous les instruments
	// $param2 = 0: Instruments du pupitre préféré seulement
	// $param2 = 1: Instruments du pupitre préféré en premier, puis les autres
	 
<input type='text' completion='instruments' callback='mis_en_forme_instrument' param1='3' param2='1' class='saisie-50em' name='code' id='code' value='' />
	
<script src='./javascript/ajax.js'></script>
<script>
	ajax_parse_dom();
	
	function mis_en_forme_instrument(id){
		var str=document.getElementById(id).value;
		var res = str.split(' - ');
		if(res[0]) document.getElementById(id).value=res[0];
	}
</script>
 */

$nomenclature_instrument_content_form_tpl="		
<script>

	function test_form(form){
		if(form.code.value.length == 0){
			alert('".addslashes($msg["admin_nomenclature_instrument_form_code_error"])."');
			return false;
		}
		if(form.name.value.length == 0){
			alert('".addslashes($msg["admin_nomenclature_instrument_form_name_error"])."');
			return false;
		}
		return true;
	}
	
</script>
<div class='row'>
	<label class='etiquette' for='code'>".$msg['admin_nomenclature_instrument_form_code']."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-50em' name='code' id='code' value='!!code!!' />
</div>				
<div class='row'>
	<label class='etiquette' for='name'>".$msg['admin_nomenclature_instrument_form_name']."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-50em' name='name' id='name' value='!!name!!' />
</div>					
<div class='row'>
	<label class='etiquette' for='musicstand'>".$msg['admin_nomenclature_instrument_form_musicstand']."</label>
</div>
<div class='row'>
	!!musicstand!!
</div>				
<div class='row'>
	<label class='etiquette' for='name'>".$msg['admin_nomenclature_instrument_form_standard']."</label>
</div>
<div class='row'>
	<input type='checkbox' name='standard' id='standard' value='1' !!checked!!/>
</div>	
";

$nomenclature_instrument_dialog_tpl = "
<div style='width: 400px; height: 500px; overflow: auto;'>
<form data-dojo-attach-point='containerNode' data-dojo-attach-event='onreset:_onReset,onsubmit:_onSubmit' \${!nameAttrSetting}>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='code'>".encoding_normalize::utf8_normalize($msg['admin_nomenclature_instrument_form_code'])."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='code' id='code' value='' />
		</div>
		<div class='row'>
			<label class='etiquette' for='name'>".encoding_normalize::utf8_normalize($msg['admin_nomenclature_instrument_form_name'])."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='name' id='name' value='' />
		</div>
		<div class='row'>
			<label class='etiquette' for='musicstand'>".encoding_normalize::utf8_normalize($msg['admin_nomenclature_instrument_form_musicstand'])."</label>
		</div>
		<div class='row'>
			!!musicstand!!
		</div>
		<div class='row'>
		</div>
	</div>
	<div class='erreur' id='nomenclature_instrument_save_error'></div>
	<div class='row'>
		<div class='left'>
			<button data-dojo-type='dijit/form/Button' id='nomenclature_instrument_form_exit' type='button'>".encoding_normalize::utf8_normalize($msg['admin_nomenclature_instrument_form_exit'])."</button>
			<button data-dojo-type='dijit/form/Button' id='nomenclature_instrument_form_save' type='submit'>".encoding_normalize::utf8_normalize($msg['admin_nomenclature_instrument_form_save'])."</button>
		</div>
		<div class='right'>
		</div>
	</div>
	<div class='row'></div>
</form>
</div>";

