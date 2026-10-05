<?php
// +-------------------------------------------------+
// Â© 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_equation.tpl.php,v 1.6 2021/02/04 14:07:09 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

//*******************************************************************
// Définition des templates pour les listes en edition
//*******************************************************************

global $tpl_contribution_area_equation_liste_tableau, $msg, $tpl_contribution_area_equation_liste_tableau_ligne;
global $tpl_contribution_area_equation_form, $current_module, $base_path;

require_once "$base_path/javascript/misc.inc.php";

$tpl_contribution_area_equation_liste_tableau = "

<hr />
<h3>".$msg["search_persopac_list"]."</h3>

	<div class='row'>
		<table>
		<tr>
			<th>".$msg["search_persopac_table_order"]."</th>
			<th>".$msg["search_persopac_table_preflink"]."</th>
			<th>".$msg["search_persopac_table_name"]."</th>
			<th>".$msg["search_persopac_table_shortname"]."</th>
			<th>".$msg["search_persopac_table_humanquery"]."</th>
			<th>".$msg["search_persopac_table_edit"]."</th>
		</tr>
		!!lignes_tableau!!
		</table>
	</div>		
<hr />	
<!--	Bouton Ajouter	-->
<div class='row'>
	<input class='bouton' value='".$msg["search_persopac_add"]."' type='button'  onClick=\"document.location='./modelling.php?categ=opac&sub=search_persopac&section=liste&action=add'\" >
</div>
";

$tpl_contribution_area_equation_liste_tableau_ligne = "
<tr class='!!pair_impair!!' '!!tr_surbrillance!!' >
	<td class='center'>
		<input type='button' class='bouton_small' value='-' onClick=\"document.location='./modelling.php?categ=opac&sub=search_persopac&section=liste&action=up&id=!!id!!'\"/></a>
		<input type='button' class='bouton_small' value='+' onClick=\"document.location='./modelling.php?categ=opac&sub=search_persopac&section=liste&action=down&id=!!id!!'\"/>
	</td>
	<td !!td_javascript!! >!!directlink!!</td>
	<td !!td_javascript!! >!!name!!</td>
	<td !!td_javascript!! >!!shortname!!</td>
	<td !!td_javascript!! >!!human!!</td>	
	<td><input class='bouton_small' value='".$msg["search_persopac_modifier"]."' type='button'  onClick=\"document.location='./modelling.php?categ=opac&sub=search_persopac&section=liste&action=form&id=!!id!!'\" ></td>
</tr>
";

$tpl_contribution_area_equation_form = jscript_unload_question()."
<script type='text/javascript'>

function test_form(form) {
	if(form.contribution_area_equation_name.value.replace(/^\s+|\s+$/g, '').length == 0)	{
		alert(\"".$msg["search_persopac_form_name_empty"]."\");
		return false;
	}
	unload_off();	
	return true;
}

function confirm_delete() {
    result = confirm(\"".$msg['confirm_suppr']."\");
    if(result) {
        unload_off();
        document.location='./modelling.php?categ=contribution_area&sub=equation&action=delete&id=!!id!!';
	} else
        document.forms['contribution_area_equation_form'].elements['contribution_area_equation_name'].focus();
}
function check_link(id) {
	w=window.open(document.getElementById(id).value);
	w.focus();
}
</script>


<form class='form-$current_module' name='contribution_area_equation_form' method='post' action='./modelling.php?categ=contribution_area&sub=equation&action=save'>
	<h3>!!libelle!!</h3>
	<div class='form-contenu'>
		<div class='row'>
			<label for='contribution_area_equation_name' class='etiquette'>".$msg[67]."</label>
		</div>
		<div class='row'>
			<input value='!!name!!' id='contribution_area_equation_name'  name='contribution_area_equation_name' type='text'/>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette'>".$msg['admin_contribution_area_equation_type']."</label>
		</div>
		<div class='row'>
			<span>!!type_label!!</span>
			<input type='hidden' name='contribution_area_equation_type' value='!!type!!' />
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label for='requete' class='etiquette'>".$msg["search_perso_form_requete"]."</label>
		</div>
		<div class='row'>
			!!requete_human!! !!bouton_modif_requete!!
		</div>
	</div>
	<input type='hidden' name='contribution_area_equation_query' value='!!query!!' />
	<input type='hidden' name='id' value='!!id!!' />
	<input type='hidden' name='contribution_area_equation_human_query' value='!!human_query!!' />
<!--	Boutons	-->
<div class='row'>
	<div class='left'>
		<input type='button' class='bouton' value='".$msg["search_persopac_form_annuler"]."' !!annul!! />
		<input type='button' value='".$msg["search_persopac_form_save"]."' class='bouton' id='btsubmit' onClick=\"if (test_form(this.form)) this.form.submit();\" />
		</div>
	<div class='right'>
		!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	document.forms['contribution_area_equation_form'].elements['contribution_area_equation_name'].focus();
</script>
!!form_modif_requete!!
";
