<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: classementGen.tpl.php,v 1.3 2019-05-27 14:55:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $classementGen_selector, $classementGen_list_table_header, $classementGen_list_table_row, $classementGen_list_table_footer, $classementGen_form_edit, $msg, $current_module;

// templates pour la gestion des classements

//Template du selecteur
$classementGen_selector = "<div data-dojo-type='dijit/form/DropDownButton' style='float:right;'>
				<span></span>
			    <div data-dojo-type='dijit/TooltipDialog' id='classementGen_Dialog_!!object_id!!'>
			    	<label class='etiquette'>!!msg_object_classement!!</label>
			   		<br />
					<select data-dojo-type='dijit/form/ComboBox' id='classementGen_!!object_type!!_!!object_id!!' name='classementGen_!!object_type!!_!!object_id!!'>
						!!classements_liste!!
					</select>
			        <br />
			 		<button data-dojo-type='dijit/form/Button' onclick=\"classementGen_save('!!object_type!!',!!object_id!!,'!!url_callback!!');return false;\" type='button'>!!msg_object_classement_save!!</button>
			    </div>
			</div>";

//Table pour gérer les classements
$classementGen_list_table_header = "<table>
	<tr>
		<th>!!title!!</th>
	</tr>";

$classementGen_list_table_row = "<tr class='!!tr_class!!' !!tr_js!! style='cursor: pointer'>
									<td><strong>!!td_lib!!</strong></td>
								</tr>";
$classementGen_list_table_footer = "</table>";

//Formulaire d'édition du classement
$classementGen_form_edit = "
<script type='text/javascript'>
function test_form(form) {
	if(document.getElementById('newClassement').value.length == 0) {
		alert('".$msg["classementGen_list_form_no_empty"]."');
		document.getElementById('newClassement').focus();
		return false;
	}
	return true;
}

function confirm_delete() {
	result = confirm(\"".$msg['confirm_suppr']." ?\");
	if(result){
		document.classementGen_form.action='!!suppr_link!!';
		document.classementGen_form.submit();
	}
}
</script>
	<form class='form-$current_module' name='classementGen_form' method='post' action='!!action_link!!'>
	<h3>!!form_title!!</h3>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette'>!!form_libelle!!</label>
		</div>
		<div class='row'>
			<input type=\"text\" id=\"newClassement\" name=\"newClassement\" value=\"!!newValue!!\" class=\"saisie-50em\" />
			<input type=\"hidden\" id=\"oldClassement\" name=\"oldClassement\" value=\"!!oldValue!!\" />
		</div>
	</div>
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' ".$msg["76"]." ' onClick=\"document.location='!!back_link!!'\" />&nbsp;
		<input class='bouton' type='submit' value=' ".$msg["77"]." ' onClick=\"return test_form(this.form)\" />
	</div>
	<div class='right'>
		<input class='bouton' type='button' value='".$msg["supprimer"]."' onClick=\"confirm_delete();\" />
	</div>
</div>
<div class='row'></div>
</form>
";