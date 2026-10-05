<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_author.tpl.php,v 1.32 2018/03/26 14:03:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

require_once($base_path."/selectors/templates/sel_authorities.tpl.php");

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------
	
global $dyn;
global $jscript;
global $jscript_common_authorities_unique, $jscript_common_authorities_link;
global $jscript_common_selector, $jscript_common_selector_simple;
global $selector_author_form;

if ($dyn==3) {
	$jscript = $jscript_common_authorities_unique;
}elseif ($dyn==2) { // Pour les liens entre autorités
	$jscript = $jscript_common_authorities_link;
}elseif ($dyn!=1) {
	$jscript = $jscript_common_selector;
} else {
	$jscript = $jscript_common_selector_simple;
}

// ------------------------------------------
// 	$selector_author_form : form saisie auteur
// ------------------------------------------
global $pmb_autorites_verif_js, $base_path;
$selector_author_form = ($pmb_autorites_verif_js!= "" ? "<script type='text/javascript' src='$base_path/javascript/$pmb_autorites_verif_js'></script>":"")."
<script type='text/javascript'>
function test_form(form) {";

if ($pmb_autorites_verif_js != "") {
	$selector_author_form .= "
	var check = check_perso_author_form(form);
	if (check == false) return false;";
}

$selector_author_form .= "
	if(form.author_name.value.length == 0) {
		return false;
	}
	return true;
}

function display_part(type){
	
	var collectivite_part = document.getElementById('collectivite_part');
	if(type == '70') {
		collectivite_part.style.display = 'none';
	} else {		
		collectivite_part.style.display = 'inline';		
	} 
	
	var label_header = document.getElementById('id_header');
	if(type == '70') {
		label_header.innerHTML='".addslashes($msg[214])."';
	} else if(type == '71'){
		label_header.innerHTML='".addslashes($msg["aut_select_coll"])."';
	} else if(type == '72'){
		label_header.innerHTML='".addslashes($msg["aut_select_congres"])."';
	}
	
	var label_titre = document.getElementById('titre_ajout');
	if(type == '70') {
		label_titre.innerHTML='".addslashes($msg[207])."';
	} else if(type == '71'){
		label_titre.innerHTML='".addslashes($msg["aut_ajout_collectivite"])."';
	} else if(type == '72'){
		label_titre.innerHTML='".addslashes($msg["aut_ajout_congres"])."';
	}
	if(type == '71') 
		document.getElementById('author_nom').setAttribute('completion', 'collectivite_name');
	else if(type == '72')  
		document.getElementById('author_nom').setAttribute('completion', 'congres_name');
	else	
		document.getElementById('author_nom').setAttribute('completion', '');		
} 
</script>
<form name='saisie_auteur' method='post' action=\"!!base_url!!&action=update\">
<!-- ajouter un auteur -->
<h3><label id='titre_ajout'>!!titre_ajout!!</label></h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='author_type'>$msg[205]</label>
		</div>
	<div class='row'>
		<select id='author_type' name='author_type' onchange='display_part(this.value)'>
			<option value='70' !!sel_pp!!>$msg[203]</option>
			<option value='71' !!sel_coll!!>$msg[204]</option>
			<option value='72' !!sel_con!!>".$msg["congres_libelle"]."</option>
			</select>
		</div>
	<div class='row'>
		<label class='etiquette' for='author_nom'>$msg[201]</label>
		</div>
	<div class='row'>
		<input type='text' class='saisie-30em' id='author_nom' name='author_name' autfield='rien' completion='!!completion_name!!' value=\"!!deb_saisie!!\">
		<input id='rien' name='rien' value='' type='hidden'>
		</div>
	<div class='row'>
		<label class='etiquette' for='author_rejete'>$msg[202]</label>
		</div>
	<div class='row'>
		<input type='text' class='saisie-30em' id='author_rejete' name='author_rejete' value=''>
		</div>
	<div class='row'>
		<label class='etiquette' for='date'>$msg[713]</label>
		</div>
	<div class='row'>
		<input type='text' class='saisie-30em' id='date' name='date' value=''>
		</div>
	<div id='collectivite_part' style='!!display!!'>		
		<!--	lieu	-->
		<div class='row'>
			<label class='etiquette' for='form_lieu'>".$msg["congres_lieu_libelle"]."</label>
			</div>
		<div class='row'>
			<input type='text' class='saisie-30em' id='form_lieu' name='lieu' value=''>
		</div>
		
		<!--	ville	-->
		<div class='row'>
			<label class='etiquette' for='form_ville'>".$msg["congres_ville_libelle"]."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-30em' id='form_ville' name='ville' value=\"\" />
		</div>	      
	
		<!--	pays	-->
		<div class='row'>
			<label class='etiquette' for='form_pays'>".$msg["congres_pays_libelle"]."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-30em' id='form_pays' name='pays' value=\"\"  />
		</div>       
	
		<!--	subdivision	-->
		<div class='row'>
			<label class='etiquette' for='form_subdivision'>".$msg["congres_subdivision_libelle"]."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-30em' id='form_subdivision' name='subdivision' value=\"\" />
		</div>
	
		<!--	numero	-->
		<div class='row'>
			<label class='etiquette' for='form_numero'>".$msg["congres_numero_libelle"]."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-30em' id='form_numero' name='numero' value=\"\"  />
		</div>	
	</div>		
	</div>	
<div class='row'>
	<input type='button' id='btcancel' class='bouton_small' value='$msg[76]' onClick=\"document.location='!!base_url!!&what=auteur';\">
	<input type='submit' id='btsubmit' value='$msg[77]' class='bouton_small' onClick=\"return test_form(this.form)\">
	</div>
</form>
<script type='text/javascript'>
	document.forms['saisie_auteur'].elements['author_name'].focus();
</script>
";
