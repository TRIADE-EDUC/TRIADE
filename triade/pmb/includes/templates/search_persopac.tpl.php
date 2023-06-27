<?php
// +-------------------------------------------------+
// Â© 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_persopac.tpl.php,v 1.17 2019-05-27 10:34:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $tpl_search_persopac_liste_tableau, $tpl_search_persopac_liste_tableau_ligne, $tpl_search_persopac_form, $msg, $current_module, $charset;

//*******************************************************************
// Définition des templates pour les listes en edition
//*******************************************************************
$tpl_search_persopac_liste_tableau = "

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
			<th>".$msg["search_persopac_type"]."</th>
			<th>".$msg["search_persopac_table_edit"]."</th>
		</tr>
		!!lignes_tableau!!
		</table>
	</div>		
<hr />	
<!--	Bouton Ajouter	-->
<div class='row'>
	<input class='bouton' value='".$msg["search_persopac_add"]."' type='button'  onClick=\"document.location='./admin.php?categ=opac&sub=search_persopac&section=liste&action=add'\" >
</div>
";

$tpl_search_persopac_liste_tableau_ligne = "
<tr class='!!pair_impair!!' '!!tr_surbrillance!!' >
	<td class='center'>
		<input type='button' class='bouton_small' value='-' onClick=\"document.location='./admin.php?categ=opac&sub=search_persopac&section=liste&action=up&id=!!id!!'\"/></a>
		<input type='button' class='bouton_small' value='+' onClick=\"document.location='./admin.php?categ=opac&sub=search_persopac&section=liste&action=down&id=!!id!!'\"/>
	</td>
	<td !!td_javascript!! >!!directlink!!</td>
	<td !!td_javascript!! >!!name!!</td>
	<td !!td_javascript!! >!!shortname!!</td>
	<td !!td_javascript!! >!!human!!</td>
	<td !!td_javascript!! >!!type!!</td>
	<td><input class='bouton_small' value='".$msg["search_persopac_modifier"]."' type='button'  onClick=\"document.location='./admin.php?categ=opac&sub=search_persopac&section=liste&action=form&id=!!id!!'\" ></td>
</tr>
";

$tpl_search_persopac_form = jscript_unload_question()."
<script type='text/javascript'>

function test_form(form) {
	if(form.name.value.replace(/^\s+|\s+$/g, '').length == 0)	{
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
        document.location='./admin.php?categ=opac&sub=search_persopac&section=liste&action=delete&id=!!id!!';
	} else
        document.forms['search_persopac_form'].elements['name'].focus();
}
function check_link(id) {
	w=window.open(document.getElementById(id).value);
	w.focus();
}
</script>


<form class='form-$current_module' id='search_persopac_form' name='search_persopac_form' method='post' action='./admin.php?categ=opac&sub=search_persopac&section=liste&action=save'>
	<h3>!!libelle!!</h3>
	<div class='form-contenu'>
		<!--	nom	-->
		<div class='row'>
			<label class='etiquette' for='name'>".$msg['search_persopac_form_name']."</label>
		</div>
		<div class='row'>
			<input class='saisie-80em' id='form_nom' type='text' name='name' value='!!name!!' data-translation-fieldname='search_name'/>
		</div>
		
		<!--	short nom	-->
		<div class='row'>
			<label class='etiquette' for='shortname'>".$msg['search_persopac_form_shortname']."</label>
		</div>
		<div class='row'>
			<input class='saisie-80em' id='shortname' type='text' name='shortname' value='!!shortname!!' data-translation-fieldname='search_shortname'/>
		</div>
		
		<div class='row'>
			<input value='1' name='directlink' id='directlink' !!directlink!! type='checkbox'>
			<label for='directlink' class='etiquette'>".htmlentities($msg["search_persopac_form_direct_search"],ENT_QUOTES,$charset)."</label>  
		</div>	
		<div class='row'>
			<label style='font-size:1.5em'>&rdsh;</label>
			<input value='1' name='directlink_auto_submit' id='directlink_auto_submit' !!directlink_auto_submit!! type='checkbox'>
			<label for='directlink_auto_submit' class='etiquette'>".htmlentities($msg["search_perso_form_directlink_auto_submit"],ENT_QUOTES,$charset)."</label>  
		</div>
		<div class='row'>
			<input value='1' name='limitsearch' !!limitsearch!! type='checkbox'>
			<label for='limitsearch' class='etiquette'>".htmlentities($msg["search_perso_form_limitsearch"],ENT_QUOTES,$charset)."</label>  
		</div>
		<div class='row'>
				!!categorie!!
		</div>
		<div class='row'>&nbsp;</div>		
		<div class='row'>
				!!type!!
		</div>
		<div class='row'>
			<label for='requete' class='etiquette'>".htmlentities($msg["search_perso_form_requete"],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			!!requete_human!!<input type='hidden' name='requete' value=\"!!requete!!\" />!!bouton_modif_requete!!
		</div>
	</div>
	<input type='hidden' name='query' value='!!query!!' />
	<input type='hidden' name='id' value='!!id!!' />
	<input type='hidden' name='human' value='!!human!!' />
<!--	Boutons	-->
<div class='row'>
	<div class='left'>
		<input type='button' class='bouton' id='btexit' value='".htmlentities($msg["search_persopac_form_annuler"],ENT_QUOTES,$charset)."' !!annul!! />
		<input type='button' value='".htmlentities($msg["search_persopac_form_save"],ENT_QUOTES,$charset)."' class='bouton' id='btsubmit' onClick=\"if (test_form(this.form)) this.form.submit();\" />
		</div>
	<div class='right'>
		!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	document.forms['search_persopac_form'].elements['name'].focus();
</script>
!!form_modif_requete!!
";
			
			
			/*
		<div class='row'>
			<label class='etiquette' for='name'>".$msg["search_persopac_form_name"]."</label>
		</div>* 
		<div class='row'>
			<input type='text' class='saisie-80em' id='form_nom' name='name' value=\"!!name!!\" />
		</div>	
		
		*/
?>
