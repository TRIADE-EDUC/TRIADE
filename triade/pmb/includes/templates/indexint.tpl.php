<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexint.tpl.php,v 1.43 2019-05-27 13:32:11 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $indexint_form;
global $indexint_replace;

global $pmb_autorites_verif_js;
global $pmb_form_authorities_editables;
global $PMBuserid, $base_path, $msg, $current_module, $charset;

// $indexint_form : form saisie titre de série
$indexint_form = jscript_unload_question();
$indexint_form.= $pmb_autorites_verif_js!= "" ? "<script type='text/javascript' src='$base_path/javascript/$pmb_autorites_verif_js'></script>":"";
$indexint_form.= "
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
		$indexint_form.= "
				if(typeof check_perso_indexint_form == 'function'){
					var check = check_perso_indexint_form(form);
					if (check == false) return false;
				}";
	}
$indexint_form.=
	"if(form.indexint_nom.value.length == 0) {
			alert(\"$msg[indexint_name_oblig]\");
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
            document.forms['saisie_indexint'].elements['indexint_nom'].focus();
    }
</script>
<script type='text/javascript'>
	document.title='!!document_title!!';
</script>
<form class='form-$current_module' id='saisie_indexint' name='saisie_indexint' method='post' action='!!action!!' enctype='multipart/form-data'>
<div class='row'>
	<div class='left'><h3>!!libelle!!</h3></div>
	<div class='right'>";

	$indexint_form.='
	<!-- Selecteur de statut -->
		<label class="etiquette" for="authority_statut">'.$msg['authorities_statut_label'].'</label>
		!!auth_statut_selector!!
	';

	if ($PMBuserid==1 && $pmb_form_authorities_editables==1){
		$indexint_form.="<input type='button' class='bouton_small' value='".$msg["authorities_edit_format"]."' id=\"bt_inedit\"/>";
	}
	if ($pmb_form_authorities_editables==1) {
		$indexint_form.="<input type='button' class='bouton_small' value=\"".$msg["authorities_origin_format"]."\" id=\"bt_origin_format\"/>";
	}
	$indexint_form .= "
	</div>
</div>
<div class='form-contenu'>
	<div class='row'>
		<a onclick='expandAll();return false;' href='#'><img border='0' id='expandall' src='".get_url_icon('expand_all.gif')."'></a>
		<a onclick='collapseAll();return false;' href='#'><img border='0' id='collapseall' src='".get_url_icon('collapse_all.gif')."'></a>
	</div>
	<div id='zone-container'>
		<div id='el0Child_2' class='row' movable='yes' title=\"".htmlentities($msg['menu_pclassement'], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='indexint_pclassement'>".$msg['menu_pclassement']."</label>
			</div>
			<div class='row'>
				!!indexint_pclassement!!
			</div>
		</div>
		<div id='el0Child_0' class='row' movable='yes' title=\"".htmlentities($msg['indexint_nom'], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='indexint_nom'>".$msg['indexint_nom']."</label>
			</div>
			<div class='row'>
				<input type='text' class='saisie-50em' name='indexint_nom' value=\"!!indexint_nom!!\" data-pmb-deb-rech='1'/>
			</div>
		</div>
		<div id='el0Child_1' class='row' movable='yes' title=\"".htmlentities($msg['indexint_comment'], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='indexint_comment'>".$msg['indexint_comment']."</label>
			</div>
			<div class='row'>
				<textarea id='indexint_comment' class='saisie-80em' name='indexint_comment' cols='62' rows='6' wrap='virtual'>!!indexint_comment!!</textarea>
			</div>
		</div>
		!!concept_form!!
		!!thumbnail_url_form!!
		!!aut_pperso!!
		<!-- aut_link -->
	</div>
</div>
<div class='row'>
	<div class='left'>
		<input type='button' id='btcancel' class='bouton' value='$msg[76]' onClick=\"unload_off();document.location='!!cancel_action!!';\" />
		<input type='submit' id='btsubmit' value='$msg[77]' class='bouton' onClick=\"document.getElementById('save_and_continue').value=0;return test_form(this.form)\" />
		<input type='hidden' name='save_and_continue' id='save_and_continue' value='' />
		<input type='submit' id='update_continue' class='bouton' value='" . $msg['save_and_continue'] . "' onClick=\"document.getElementById('save_and_continue').value=1;return test_form(this.form)\" />
		!!remplace!!
		!!voir_notices!!
		!!audit_bt!!
		<input type='hidden' name='page' value='!!page!!' />
		<input type='hidden' name='nbr_lignes' value='!!nbr_lignes!!' />
		<input type='hidden' name='user_input' value=\"!!user_input!!\" />
		<input type='hidden' name='exact' value=\"!!exact!!\" />
	</div>
	<div class='right'>
		!!delete!!
	</div>
</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	document.forms['saisie_indexint'].elements['indexint_nom'].focus();
	ajax_parse_dom();
</script>
";

// $indexint_replace : form remplacement Indexation interne
$indexint_replace = "
<script src='javascript/ajax.js'></script>
<form class='form-$current_module' name='indexint_replace' method='post' action='!!controller_url_base!!&sub=replace&id=!!id!!&id_pclass=!!id_pclass!!' onSubmit=\"return false\" >
	<h3>$msg[159] !!indexint_name!! </h3>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='par'>$msg[160]</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50emr' id='indexint_libelle' name='indexint_libelle' value=\"\" completion=\"indexint\" autfield=\"n_indexint_id\" autexclude=\"!!id!!\"
	    	onkeypress=\"if (window.event) { e=window.event; } else e=event; if (e.keyCode==9) { openPopUp('./select.php?what=indexint&caller=indexint_replace&param1=n_indexint_id&param2=indexint_libelle&no_display=!!id!!&id_pclass=!!id_pclass!!', 'selector'); }\" />
	
			<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=indexint&caller=indexint_replace&param1=n_indexint_id&param2=indexint_libelle&no_display=!!id!!&id_pclass=!!id_pclass!!', 'selector')\" title='$msg[157]' value='$msg[parcourir]' />
			<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.indexint_libelle.value=''; this.form.n_indexint_id.value='0'; \" />
			<input type='hidden' name='n_indexint_id' id='n_indexint_id' value='0' />
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
	document.forms['indexint_replace'].elements['indexint_libelle'].focus();
</script>
<div class='row'>
	!!liste_remplacantes!!
</div>
";

