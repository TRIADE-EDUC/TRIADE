<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: parametres_perso.tpl.php,v 1.32 2024/01/18 15:14:15 dgoron Exp $

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
        !!content_form!!
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
	<input type='hidden' value='' name='action'/>
</form>
<script type='text/javascript' src='".$base_path."/javascript/ajax.js'></script>
<script type='text/javascript'>
	ajax_parse_dom();
</script>
";
