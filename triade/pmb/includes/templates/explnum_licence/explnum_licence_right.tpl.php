<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_licence_right.tpl.php,v 1.4 2019-05-27 10:39:09 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $admin_explnum_licence_right_form, $current_module, $msg, $charset;

//statuts de contribution
$admin_explnum_licence_right_form = "<form class='form-$current_module' id='explnumlicencerightform' name='explnumlicencerightform' method=post action=\"./admin.php?categ=docnum&sub=licence&action=settings&id=!!explnum_licence_id!!&what=rights&rightaction=save&rightid=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<div class='row'>
			<label class='etiquette' for='explnum_licence_right_type'>".$msg["explnum_licence_right_type"]."</label>
		</div>
		<div class='row'>
			<input type='radio' group='right_type' value='1' !!explnum_licence_right_type_1!! name='explnum_licence_right_type' id='explnum_licence_right_type_1'>
			<label class='etiquette' for='explnum_licence_right_type_1'>".$msg["explnum_licence_right_quotation_right_authorisation"]."</label>
			<input type='radio' group='right_type' value='0' !!explnum_licence_right_type_0!! name='explnum_licence_right_type' id='explnum_licence_right_type_0'>
			<label class='etiquette' for='explnum_licence_right_type_0'>".$msg["explnum_licence_right_quotation_right_prohibition"]."</label>
		</div>
	</div>
	<div class='row'>
		<label class='etiquette' for='explnum_licence_right_label'>".htmlentities($msg["docnum_statut_libelle"], ENT_QUOTES, $charset)."</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-50em' id='explnum_licence_right_label' name='explnum_licence_right_label' value='!!explnum_licence_right_label!!' data-translation-fieldname='explnum_licence_right_label'>
	</div>
	<div class='row'>
		<label class='etiquette' for='explnum_licence_right_logo_url'>".htmlentities($msg["explnum_licence_logo_url"], ENT_QUOTES, $charset)."</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-50em' id='explnum_licence_right_logo_url' name='explnum_licence_right_logo_url' value='!!explnum_licence_right_logo_url!!' data-translation-fieldname='explnum_licence_right_logo_url'>
	</div>
	<div class='row'>
		<label class='etiquette' for='explnum_licence_right_explanation'>".htmlentities($msg["explnum_licence_explanation"], ENT_QUOTES, $charset)."</label>
	</div>
	<div class='row'>
		<textarea class='saisie-50em' id='explnum_licence_right_explanation' name='explnum_licence_right_explanation' data-translation-fieldname='explnum_licence_right_explanation'>!!explnum_licence_right_explanation!!</textarea>
	</div>
	<!-- Boutons -->
	<div class='row'>
		<div class='left'>
			<input class='bouton' type='button' value='". $msg['76'] ."' onClick=\"history.go(-1);\">&nbsp;
			<input class='bouton' type='submit' value='". $msg['77'] ."' onClick=\"return test_form(this.form)\">
		</div>
		<div class='right'>
			!!bouton_supprimer!!
		</div>
	</div>
</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['explnumlicencerightform'].elements['explnum_licence_right_label'].focus();</script>";