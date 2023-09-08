<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: series.tpl.php,v 1.42 2019-05-27 12:27:01 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $serie_form;
global $serie_replace;
global $pmb_autorites_verif_js, $base_path, $msg, $current_module, $pmb_form_authorities_editables, $PMBuserid;
// $serie_form : form saisie titre de série
$serie_form = jscript_unload_question();
$serie_form.= $pmb_autorites_verif_js!= "" ? "<script type='text/javascript' src='$base_path/javascript/$pmb_autorites_verif_js'></script>":"";

$serie_form.= "
<script src='javascript/ajax.js'></script>
<script type='text/javascript'>
	require(['dojo/ready', 'apps/pmb/gridform/FormEdit'], function(ready, FormEdit){
	     ready(function(){
	     	new FormEdit();
	     });
	});
</script>
<script type='text/javascript'>
	function test_form(form) {
		if (typeof check_form == 'function') {
			if (!check_form()) {
				return false;
			}
		}
	";

if ($pmb_autorites_verif_js != "") {
	$serie_form.= "
		if(typeof check_perso_serie_form == 'function'){
			var check = check_perso_serie_form(form);
			if (check == false) return false;
		}";
}

$serie_form.= "if(form.serie_nom.value.length == 0)
			{
				alert(\"$msg[338]\");
				return false;
			}
		unload_off();
		return true;
	}
function confirm_delete() {
        result = confirm(\"".$msg['confirm_suppr']."\");
        if(result) {
        	unload_off();
            document.location='!!delete_action!!';
		} else
            document.forms['saisie_serie'].elements['serie_nom'].focus();
    }
</script>
<script type='text/javascript'>
	document.title='!!document_title!!';
</script>
<form class='form-$current_module' id='saisie_serie' name='saisie_serie' method='post' action='!!action!!' onSubmit=\"return false\" enctype='multipart/form-data'>
<div class='row'>
	<div class='left'><h3>!!libelle!!</h3></div>
	<div class='right'>";
	$serie_form.='
	<!-- Selecteur de statut -->
		<label class="etiquette" for="authority_statut">'.$msg['authorities_statut_label'].'</label>
		!!auth_statut_selector!!
	';
	if(isset($pmb_form_authorities_editables)) {
		if (isset($PMBuserid) && $PMBuserid==1 && $pmb_form_authorities_editables==1){
			$serie_form.="<input type='button' class='bouton_small' value='".$msg["authorities_edit_format"]."' id=\"bt_inedit\"/>";
		}
		if ($pmb_form_authorities_editables==1) {
			$serie_form.="<input type='button' class='bouton_small' value=\"".$msg["authorities_origin_format"]."\" id=\"bt_origin_format\"/>";
		}
	}
	$serie_form .= "
	</div>
</div>
<div class='form-contenu'>
	<div class='row'>
		<a onclick='expandAll();return false;' href='#'><img border='0' id='expandall' src='".get_url_icon('expand_all.gif')."'></a>
		<a onclick='collapseAll();return false;' href='#'><img border='0' id='collapseall' src='".get_url_icon('collapse_all.gif')."'></a>
	</div>
	<div id='zone-container'>
		<div id='el0Child_0' class='row' movable='yes' title=\"".htmlentities($msg['233'], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='form_nom'>$msg[233]</label>
			</div>
			<div class='row'>
				<input type='text' class='saisie-80em' name='serie_nom' value=\"!!serie_nom!!\" data-pmb-deb-rech='1'/>
			</div>
		</div>
		!!concept_form!!
		!!thumbnail_url_form!!
		<!-- aut_link -->	
		!!aut_pperso!!
	</div>
</div>
<div class='row'>
	<div class='left'>
		<input type='button' class='bouton' value='$msg[76]' id='btcancel' onClick=\"unload_off();document.location='!!cancel_action!!';\" />
		<input type='button' value='$msg[77]' class='bouton' id='btsubmit' onClick=\"document.getElementById('save_and_continue').value=0; if (test_form(this.form)) this.form.submit();\" />
		<input type='hidden' name='save_and_continue' id='save_and_continue' value='' />
        <input type='button' id='update_continue' class='bouton' value='" . $msg['save_and_continue'] . "' onClick=\"document.getElementById('save_and_continue').value=1;if (test_form(this.form)) this.form.submit();\" />
        !!remplace!!
		!!voir_notices!!
		!!audit_bt!!
		<input type='hidden' name='page' value='!!page!!' />
		<input type='hidden' name='nbr_lignes' value='!!nbr_lignes!!' />
		<input type='hidden' name='user_input' value=\"!!user_input!!\" />
	</div>
	<div class='right'>
		!!delete!!
	</div>
</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	document.forms['saisie_serie'].elements['serie_nom'].focus();
	ajax_parse_dom();
</script>
";

// $serie_replace : form remplacement titre de série
$serie_replace = "
<script src='javascript/ajax.js'></script>
<form class='form-$current_module' name='serie_replace' method='post' action='!!controller_url_base!!&sub=replace&id=!!id!!' onSubmit=\"return false\" >
	<h3>$msg[159] !!serie_name!! </h3>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='par'>$msg[160]</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-80emr' id='serie_libelle' name='serie_libelle' value=\"\" completion=\"series\" autfield=\"n_serie_id\" autexclude=\"!!id!!\"
	    	onkeypress=\"if (window.event) { e=window.event; } else e=event; if (e.keyCode==9) { openPopUp('./select.php?what=serie&caller=serie_replace&param1=n_serie_id&param2=serie_libelle&no_display=!!id!!', 'selector'); }\" />
	
			<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=serie&caller=serie_replace&param1=n_serie_id&param2=serie_libelle&no_display=!!id!!', 'selector')\" title='$msg[157]' value='$msg[parcourir]' />
			<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.serie_libelle.value=''; this.form.n_serie_id.value='0'; \" />
			<input type='hidden' name='n_serie_id' id='n_serie_id' value='0' />
		</div>
		<div class='row'>		
			<input id='aut_link_save' name='aut_link_save' type='checkbox' checked='checked' value='1'>".$msg["aut_replace_link_save"]."
		</div>	
	</div>
	<div class='row'>
		<input type='button' class='bouton' value='$msg[76]' id='btcancel' onClick=\"document.location='!!cancel_action!!';\">
		<input type='button' class='bouton' value='$msg[159]' id='btsubmit' onClick=\"this.form.submit();\" >
	</div>
</form>
<script type='text/javascript'>
	ajax_parse_dom();
	document.forms['serie_replace'].elements['serie_libelle'].focus();
</script>
";

