<?php
// +-------------------------------------------------+
// Â© 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: opac_view.tpl.php,v 1.7 2019-05-27 12:19:16 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $tpl_opac_view_list_tableau, $msg, $tpl_opac_view_list_tableau_ligne, $pmb_opac_url, $tpl_opac_view_form, $current_module, $charset, $tpl_opac_view_create_form, $tpl_opac_view_list_sel_tableau, $tpl_opac_view_list_sel_tableau_ligne;

//*******************************************************************
// Définition des templates pour les listes en edition
//*******************************************************************
$tpl_opac_view_list_tableau = "
<h1>".$msg["opac_view_list_title"]."</h1>

	<div class='row'>
		<table>
		<tr>
			<th>".$msg["opac_view_list_id"]."</th>
			<th>".$msg["opac_view_list_name"]."</th>
			<th>".$msg["opac_view_list_comment"]."</th>
			<th>".$msg["opac_view_list_link"]."</th>
		</tr>
		!!lignes_tableau!!
		</table>
	</div>
<!--	Bouton Ajouter	-->
<div class='row'>
	<input class='bouton' value='".$msg["opac_view_add"]."' type='button'  onClick=\"document.location='./admin.php?categ=opac&sub=opac_view&section=list&action=add'\" >
	<input class='bouton' value='".$msg["opac_view_gen"]."' type='button'  onClick=\"document.location='./admin.php?categ=opac&sub=opac_view&section=list&action=gen'\" >

</div>
";

$tpl_opac_view_list_tableau_ligne = "
<tr class='!!pair_impair!!' '!!tr_surbrillance!!' style=\"cursor: pointer;\">
	<td !!td_javascript!! >!!opac_view_id!!</td>
	<td !!td_javascript!! >!!name!!</td>
	<td !!td_javascript!! >!!comment!!</td>
	<td ><a href=\"$pmb_opac_url?opac_view=!!opac_view_id!!\" alt='!!name!!' title='!!name!!' target='_blank'>$pmb_opac_url?opac_view=!!opac_view_id!!</a></td>
</tr>
";

$tpl_opac_view_form = "
<script type='text/javascript'>

function test_form(form) {
	if(form.name.value.length == 0)	{
		alert(\"".$msg["opac_view_form_name_empty"]."\");
		return false;
	}
	return true;
}

function confirm_delete() {
    result = confirm('".$msg["confirm_suppr"]."');
    if(result) {

        document.location='./admin.php?categ=opac&sub=opac_view&section=list&action=delete&opac_view_id=!!opac_view_id!!';
	} else
        document.forms['opac_view_form'].elements['name'].focus();
}
function check_link(id) {
	w=window.open(document.getElementById(id).value);
	w.focus();
}
</script>
!!form_modif_requete!!
<form class='form-$current_module' name='opac_view_form' method='post' action='!!action!!'>
	<h3>!!libelle!!</h3>
	<div class='form-contenu'>
		<!--	nom	-->
		<div class='row'>
			<label class='etiquette' for='name'>".$msg["opac_view_form_name"]."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-80em' id='name' name='name' value=\"!!name!!\" />
		</div>

		<!--	Multi critère	-->
		<div class='row'>
			<input type='radio' name='opac_view_wo_query' id='opac_view_w_query' value='0' !!opac_view_w_query_checked!! />
			<label class='etiquette' for='opac_view_w_query' >".$msg["opac_view_form_search"]."</label>
			<input type='radio' name='opac_view_wo_query' id='opac_view_wo_query' value='1' !!opac_view_wo_query_checked!! />
			<label class='etiquette' for='opac_view_wo_query' >".$msg['opac_view_wo_query']."</label>
		</div>
		<div class='row'>
			!!requete_human!!
			<input type='hidden' name='requete' value='!!requete!!' />
			<input type='button' id='search_bt' class='bouton' value='".$msg["opac_view_form_add_search"]."' !!search_build!! />
		</div>
		
		<!--	Paramètres de la vue	-->
		<div class='row'>
			<label class='etiquette' >".$msg["opac_view_form_parameters"]."</label>
		</div>
		<div class='row'>
			!!parameters!!
		</div>

		<!--	Filtres de la vue	-->
		<div class='row'>
			<label class='etiquette' >".$msg["opac_view_form_filters"]."</label>
		</div>
		<div class='row'>
			!!filters!!
		</div>

		<!--	visibilité Opac	-->
		<div class='row'>
			<label class='etiquette' >".$msg["opac_view_form_opac_visible_title"]."</label>
		</div>
		<div class='row'>
			<select name='opac_view_form_visible' id='opac_view_form_visible' onchange=''>
				<option value='0' !!opac_visible_selected_0!!>".$msg["opac_view_form_opac_visible_no"]."</option>
				<option value='1' !!opac_visible_selected_1!!>".$msg["opac_view_form_opac_visible"]."</option>
				<option value='2' !!opac_visible_selected_2!!>".$msg["opac_view_form_opac_visible_connected"]."</option>
			</select>
		</div>

		<!--	commentaire de la vue	-->
		<div class='row'>
			<label class='etiquette' >".$msg["opac_view_form_comment"]."</label>
		</div>
		<div class='row'>
			<textarea name='comment' rows='3' cols='75' wrap='virtual'>!!comment!!</textarea>
		</div>

		<!-- date maj et validite -->
		<div class='row'>
			<div class='colonne5'>
				<label class='etiquette' >".htmlentities($msg['opac_view_form_last_gen'],ENT_QUOTES, $charset)."</label>
			</div>
			<div class='colonne_suite'>
				!!last_gen!!
			</div>
		</div>
		<div class='row'>
			<div class='colonne5'>
				<label class='etiquette' >".htmlentities($msg['opac_view_form_ttl'],ENT_QUOTES, $charset)."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' class='saisie-5em' name='ttl' value='!!ttl!!' />
			</div>
		</div>
		<div class='row'></div>
	</div>

	<input type='hidden' name='opac_view_id' value='!!opac_view_id!!' />

	<!--	Boutons	-->
	<div class='row'>
		<div class='left'>
			<input type='button' class='bouton' value='".$msg["opac_view_form_annuler"]."' !!annul!! />
			<input type='button' value='".$msg["opac_view_form_save"]."' class='bouton' id='btsubmit' onClick=\"if (test_form(this.form)) this.form.submit();\" />
			</div>
		<div class='right'>
			!!delete!!
			</div>
		</div>
	<div class='row'></div>
</form>
<script type='text/javascript'>
	document.forms['opac_view_form'].elements['name'].focus();
</script>
";

$tpl_opac_view_create_form = "
<script type='text/javascript'>

function test_form(form) {
	if(form.name.value.length == 0)	{
		alert(\"".$msg["opac_view_form_name_empty"]."\");
		return false;
	}
	return true;
}

function check_link(id) {
	w=window.open(document.getElementById(id).value);
	w.focus();
}
</script>
!!form_modif_requete!!
<form class='form-$current_module' name='opac_view_form' method='post' action='!!action!!'>
	<h3>!!libelle!!</h3>
	<div class='form-contenu'>
		<!--	nom	-->
		<div class='row'>
			<label class='etiquette' for='name'>".$msg["opac_view_form_name"]."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-80em' id='name' name='name' value=\"!!name!!\" />
		</div>

	</div>
	<!--	Boutons	-->
	<div class='row'>
		<div class='left'>
			<input type='button' class='bouton' value='".$msg["opac_view_form_annuler"]."' !!annul!! />
			<input type='button' value='".$msg["opac_view_form_save"]."' class='bouton' id='btsubmit' onClick=\"if (test_form(this.form)) this.form.submit();\" />
		</div>
		<div class='right'>

			</div>
		</div>
	<div class='row'></div>
</form>
<script type='text/javascript'>
	document.forms['opac_view_form'].elements['name'].focus();
</script>	";

$tpl_opac_view_list_sel_tableau = "
<div class='row'>
	!!forcage!!
</div>
<table>
	<tr>
		<th>".$msg["opac_view_list_select"]."</th>
		<th>".$msg["opac_view_list_default"]."</th>
		<th>".$msg["opac_view_list_name"]."</th>
		<th>".$msg["opac_view_list_comment"]."</th>
	</tr>
	!!lignes_tableau!!
</table>
";

$tpl_opac_view_list_sel_tableau_ligne = "
<tr class='!!class!!' !!tr_surbrillance!!>
	<td><input type=checkbox name='form_empr_opac_view[]' value='!!opac_view_id!!' !!checked!! class='checkbox' !!disabled!!/></td>
	<td><input type='radio' name='form_empr_opac_view_default' value='!!opac_view_id!!' !!radio_checked!! !!disabled!!></td>
	<td>!!name!!</td>
	<td>!!comment!!</td>
</tr>
";
?>
