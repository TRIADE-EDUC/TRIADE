<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: parametres_perso.tpl.php,v 1.28 2019-05-27 12:33:51 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

// templates pour les forms paramètres personalisés emprunteurs
//	----------------------------------
global $msg, $form_list, $current_module, $form_edit, $base_path;

$form_list="<div class=''>
	<div>!!liste_champs_perso!!</div>
    <p><input type='button' class='bouton' value='".$msg['parperso_new_field']."' onClick='document.location=\"!!base_url!!&action=nouv\"'/></p></div>
";

$form_edit="<form class='form-$current_module' id='parperso_form' name='formulaire' action='!!base_url!!' method='post'>
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
			<input class='saisie-30em' id='titre' type='text' name='titre' value='!!titre!!' data-translation-fieldname='titre' />
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
		<div class='row' style='display:!!multiple_visible!!'>
			<input type='checkbox' name='multiple' value='1' id='multiple' !!multiple_checked!! />&nbsp;
			<label class='etiquette' for='multiple'>!!msg_visible!!</label>
		</div>
		<div class='row' style='display:!!opac_sort_visible!!'>
			<input type='checkbox' id='opac_sort' name='opac_sort' value='1' !!opac_sort_checked!! />&nbsp;
			<label class='etiquette' for='opac_sort'>".$msg['parperso_opac_sort']."</label>
		</div>	
		<div class='row' style='display:!!obligatoire_visible!!'>
			<input type='checkbox' id='obligatoire' name='obligatoire' value='1' !!obligatoire_checked!! />&nbsp;
			<label class='etiquette' for='obligatoire'>".$msg['parperso_mandatory']."</label>
		</div>
		<div class='row' style='display:!!search_visible!!'>
			<input type='checkbox' name='search' id='search' value='1' !!search_checked!! />&nbsp;
			<label class='etiquette' for='search'>".$msg['parperso_field_search']."</label>
		</div>
		<div class='row' style='display:!!export_visible!!'>
			<input type='checkbox' id='export' name='export' value='1' !!export_checked!! />&nbsp;
			<label class='etiquette' for='export'>".$msg['parperso_exportable']."</label>
		</div>
		<div class='row' style='display:!!exclusion_visible!!'>
			<input type='checkbox' id='exclusion' name='exclusion' value='1' !!exclusion_checked!! />&nbsp;
			<label class='etiquette' for='exclusion'>".$msg['parperso_exclusion']."</label>
		</div>		
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='name'>".$msg['parperso_field_classement']."</label>
		</div>
		<div class='row'>
			<input list='custom_classements' class='saisie-30emr' id='classement' type='text' name='classement' value='!!classement!!' completion='custom_classements' autocomplete='off' param1='!!prefix!!'/>
			<datalist id='custom_classements'>
				!!classements_list!!
			</datalist>
		</div>
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
<script type='text/javascript' src='".$base_path."/javascript/ajax.js'></script>
<script type='text/javascript'>
	ajax_parse_dom();
</script>
";
?>