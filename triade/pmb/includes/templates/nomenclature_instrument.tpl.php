<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_instrument.tpl.php,v 1.4 2019-05-27 12:35:59 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $nomenclature_instrument_dialog_tpl, $msg;

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
