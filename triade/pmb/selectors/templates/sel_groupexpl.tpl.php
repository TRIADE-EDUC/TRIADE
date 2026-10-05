<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_groupexpl.tpl.php,v 1.4 2017/10/13 10:21:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

// templates du sélecteur groupexpl

//-------------------------------------------
//	$sel_header : header
//-------------------------------------------
$sel_header = "
<div class='row'>
	<label for='titre_select_groupexpl' class='etiquette'>".$msg["groupexpl_select"]."</label>
	</div>
<div class='row'>
";

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

$jscript = "<script type='text/javascript' src='./javascript/expl_list.js'></script>
<script type='text/javascript'>
<!--
function set_session(id) {
	var url= './ajax.php?module=ajax&categ=session&sub=last_used&action=save&type=groupexpl&value='+id;
	var request = new http_request();
	request.request(url);
}
function set_parent(dom_id, id_value, libelle_value){
	if(window.parent.document.getElementById(dom_id)) {
		window.parent.document.getElementById(dom_id).innerHTML = reverse_html_entities(libelle_value);
		set_session(id_value);
	}
}
-->
</script>";

//-------------------------------------------
//	$sel_search_form : module de recherche
//-------------------------------------------
$sel_search_form ="
<form name='search_form' method='post' action='$base_url'>
<input type='text' name='f_user_input' value=\"!!deb_rech!!\" />&nbsp;
<input type='submit' class='bouton_small' value='$msg[142]' />&nbsp;
!!bouton_ajouter!!
</form>
<script type='text/javascript'>
<!--
	document.forms['search_form'].elements['f_user_input'].focus();
-->
</script>
";

// ------------------------------------------
// 	$groupexpl_form : form saisie groupe exemplaires
// ------------------------------------------
$groupexpl_form = "
<script type='text/javascript'>
<!--
	function test_form(form) {
		if(form.name.value.length == 0) {
			alert(\"".$msg["groupexpl_form_name_error"]."\");
			return false;
		}
		return true;
	}
-->
</script>
<form name='saisie_groupexpl' method='post' action=\"$base_url&action=update\">
<h3>".$msg["groupexpl_create_button"]."</h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='name'>".$msg['groupexpl_form_name']."</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-50em' name='name' id='name' value=\"!!deb_saisie!!\" />
	</div>
	!!location!!
	<div class='row'>
		<label class='etiquette' for='statut_principal'>".$msg['groupexpl_form_statut_principal']."</label>
	</div>
	<div class='row'>
		!!statut_principal!!
	</div>
	<div class='row'>
		<label class='etiquette' for='statut_others'>".$msg['groupexpl_form_statut_others']."</label>
	</div>
	<div class='row'>
		!!statut_others!!
	</div>
	<div class='row'>
		<label class='etiquette' for='comment'>".$msg["groupexpl_form_comment"]."</label>
		<div class='row'>
			<textarea id='comment' name='comment' cols='50' rows='2'></textarea>
		</div>
	</div>
<div class='row'>
	<input type='button' class='bouton_small' value='$msg[76]' onClick=\"document.location='$base_url';\">
	<input type='submit' value='$msg[77]' class='bouton_small' onClick=\"return test_form(this.form)\">
	</div>
</form>
<script type='text/javascript'>
	document.forms['saisie_groupexpl'].elements['name'].focus();
</script>
";

//-------------------------------------------
//	$sel_footer : footer
//-------------------------------------------
$sel_footer = "
</div>
";
