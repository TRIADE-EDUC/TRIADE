<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pret_parametres_perso.tpl.php,v 1.5 2019/05/27 12:25:04 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

// templates pour les forms paramètres personalisés prêts
//	----------------------------------
global $form_loan_edit, $current_module, $msg;

$form_loan_edit="<form class='form-$current_module' name='formulaire' action='!!base_url!!' method='post'>
	<h3>!!form_titre!!</h3>
	<div class='form-contenu'>
	<input type='hidden' name='idchamp' value='!!idchamp!!'/>
	<div class='row'>
		<label class='etiquette' for='name'>".$msg['parperso_field_name']."</label>
	</div>
	<div class='row'>
		<input class='saisie-20em' id='name' type='text' name='name' value='!!name!!'/>
	</div>
	<div class='row'>
		<label class='etiquette' for='titre'>".$msg['parperso_field_title']."</label>
	</div>
	<div class='row'>
		<input class='saisie-30em' id='titre' type='text' name='titre' value='!!titre!!' data-translation-fieldname='titre'/>
	</div>	
	<div class='row'>
		<label class='etiquette' for='comment'>".$msg[707]."</label>
	</div>
	<div class='row'>
		<textarea class='saisie-80em' id='comment' wrap='virtual' rows='1' name='comment' />!!comment!!</textarea>
	</div>	
	<div class='row'>
		<label class='etiquette' for='type'>".$msg['parperso_input_type']."</label>
	</div>
	<div class='row'>
		!!type_list!!&nbsp;<input type='button' class='bouton' value='".$msg['parperso_options_edit']."' onClick=\"!!onclick!!\"/>
	</div>
	<div class='row'>
		<label class='etiquette' for='datatype'>".$msg['parperso_data_type']."</label>
	</div>
	<div class='row'>
		!!datatype_list!!
	</div>
	<br />
	<div class='row' style='display:!!obligatoire_visible!!'>
		<input type='checkbox' id='obligatoire' name='obligatoire' value='1' !!obligatoire_checked!! />&nbsp;
		<label class='etiquette' for='obligatoire'>".$msg['parperso_mandatory']."</label>
	</div>
	<div class='row' style='display:!!filters_visible!!'>
		<input type='checkbox' id='filters' name='filters' value='1' !!filters_checked!! />&nbsp;
		<label class='etiquette' for='filters'>".$msg['parperso_filters']."</label>
	</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<label class='etiquette' for='pond'>".$msg['parperso_field_pond']."</label>
	</div>
	<div class='row'>
		<input class='saisie-5em' id='pond' type='text' name='pond' value='!!pond!!'/>
	</div>
	</div>
	<div class='row'>
		<div class='left'>
			<input type='button' class='bouton' value='".$msg[76]."' onClick='document.location=\"!!base_url!!\"'/>&nbsp;
			<input type='submit' class='bouton' value='".$msg[77]."' onClick='this.form.action.value=\"!!action!!\"'/>
		</div>
		<div class='right'>	
			!!supprimer!!
		</div>
	</div>
	<div class='row'></div>
	<input type='hidden' value='!!options!!' name='_options'/>
	<input type='hidden' value='!!for!!' name='_for'/>
	<input type='hidden' value='' name='action'/>
	<input type='hidden' name='ordre' value='!!ordre!!'/>
</form>
";
?>