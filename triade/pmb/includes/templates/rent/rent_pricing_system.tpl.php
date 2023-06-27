<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_pricing_system.tpl.php,v 1.3 2019-05-27 10:01:53 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $rent_pricing_system_form_tpl, $msg, $current_module;

$rent_pricing_system_form_tpl = "
<script src='javascript/pricing_systems.js'></script>
<script type='text/javascript'>
	function test_form(form) {
		if(form.elements['pricing_system_label'].value.replace(/^\s+|\s+$/g, '').length == 0) {
			alert('".addslashes($msg['pricing_system_label_mandatory'])."');
			return false;
		}
		if(!parseInt(form.elements['pricing_system_exercices'].value)) {
			alert('".addslashes($msg['pricing_system_exercices_mandatory'])."');
			return false;
		}
		return true;
	}
	var msg_pricing_system_delete_confirm = '".addslashes($msg['pricing_system_delete_confirm'])."';
	var msg_pricing_system_duplicate_confirm = '".addslashes($msg['pricing_system_duplicate_confirm'])."';
</script>
<form class='form-".$current_module."' id='pricing_system_form' name='pricing_system_form' method='post' action=\"./admin.php?categ=acquisition&sub=pricing_systems&id_entity=!!id_entity!!&action=save&id=!!id!!\">
<h3>!!form_title!!</h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='pricing_system_label'>".htmlentities($msg['pricing_system_label'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='row'>
		<input type='text' id='pricing_system_label' name='pricing_system_label' value=\"!!label!!\" class='saisie-80em' />
	</div>
	<div class='row'>
		<label class='etiquette' for='pricing_system_desc'>".htmlentities($msg['pricing_system_desc'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='row'>
		<textarea id='pricing_system_desc' name='pricing_system_desc' class='saisie-80em' wrap='virtual' rows='3' >!!desc!!</textarea>
	</div>
	<div class='row'>
		<label class='etiquette' for='pricing_system_entities'>".htmlentities($msg['pricing_system_associated_entity'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='row'>
		!!associated_entity!!
	</div>
	<div class='row'>
		<label class='etiquette' for='pricing_system_exercices'>".htmlentities($msg['pricing_system_associated_exercice'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='row'>
		!!exercices!!
	</div>
	<div class='row'></div>
</div>

<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=acquisition&sub=pricing_systems&id_entity=!!id_entity!!' \" />&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\" />
		!!button_duplicate!!
	</div>
	<div class='right'>
		!!button_delete!!
	</div>
</div>
<div class='row'>
</div>
</form>
<script type='text/javascript'>
	document.forms['pricing_system_form'].elements['pricing_system_label'].focus();
</script>

";