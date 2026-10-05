<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_titre_uniforme.tpl.php,v 1.25 2021/04/30 09:52:01 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

require_once($base_path."/selectors/templates/sel_authorities.tpl.php");

// templates du sélecteur auteur

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

global $dyn;
global $jscript;
global $jscript_common_authorities_link;
global $jscript_common_selector, $jscript_common_selector_simple;
global $add_field, $field_id, $field_name_id;
global $myid;
global $selector_titre_uniforme_form;

if ($dyn==3) {
	$jscript ="
<script type='text/javascript'>
	function set_parent(f_caller, id_value, libelle_value, callback){
		var w=window;
		var i=0;
		if(!(typeof w.parent.$add_field == 'function')) {
			w.parent.document.getElementById('$field_id').value = id_value;
			w.parent.document.getElementById('$field_name_id').value = reverse_html_entities(libelle_value);
			closeCurrentEnv();
			return;
		}
		var n_element=w.parent.document.forms[f_caller].elements['$max_field'].value;
		var flag = 1;
		
		//Vérification que l'élément n'est pas déjà sélectionnée
		for (var i=0; i<n_element; i++) {
			if (w.parent.document.getElementById('$field_id'+i).value==id_value) {
				alert('".addslashes($msg["aut_oeuvre_already_in_use"])."');
				flag = 0;
				break;
			}			
		}
		if(id_value=='$myid'){
			alert('".addslashes($msg["aut_oeuvre_already_in_use"])."');
			flag = 0;
		}		
		if (flag) {
			for (var i=0; i<n_element; i++) {
				if ((w.parent.document.getElementById('$field_id'+i).value==0)||(w.parent.document.getElementById('$field_id'+i).value=='')) break;
			}
		
			if (i==n_element) w.parent.$add_field();
			w.parent.document.getElementById('$field_id'+i).value = id_value;
			w.parent.document.getElementById('$field_name_id'+i+'_display_label').value = reverse_html_entities(libelle_value);
		    if(callback){
			 if(typeof w.parent[callback] == 'function'){
                w.parent[callback](id_value);
		     }
		    }
		}	
	}
</script>";
}elseif ($dyn==2) { // Pour les liens entre autorités
	$jscript = $jscript_common_authorities_link;
}elseif ($dyn!=1) {
	$jscript = $jscript_common_selector;
} else {
	$jscript = $jscript_common_selector_simple;
}

// ------------------------------------------
// 	$selector_titre_uniforme_form : form saisie titres uniformes
// ------------------------------------------
$selector_titre_uniforme_form = "
<script type='text/javascript'>
<!--
	function test_form(form){
		if(form.name.value.length == 0){
			return false;
		}
		return true;
	}
-->

</script>
<form name='saisie_titre_uniforme' method='post' action=\"!!base_url!!&action=update\">
<!-- ajouter un titre uniforme -->
<div class='row'>
	<div class='left'><h3>".$msg["aut_titre_uniforme_ajouter"]."</h3></div>
	<div class='right'>
	<!-- Selecteur de statut -->
		<label class='etiquette'>".$msg['authorities_statut_label']."</label>
		!!auth_statut_selector!!
	</div>
</div>
<div class='form-contenu'>
	<div id='el0Child_0' class='row' movable='yes' title=\"".htmlentities($msg["aut_oeuvre_form_oeuvre_type"], ENT_QUOTES, $charset)."\">
		<div class='row'>
			<label class='etiquette' for='oeuvre_type'>".$msg["aut_oeuvre_form_oeuvre_type"]."</label>
		</div>
		<div class='row'>
			!!oeuvre_type!!
		</div>
	</div>
	<div id='el0Child_1' class='row' movable='yes' title=\"".htmlentities($msg["aut_oeuvre_form_oeuvre_nature"], ENT_QUOTES, $charset)."\">			
		<div class='row'>
			<label class='etiquette' for='oeuvre_nature'>".$msg["aut_oeuvre_form_oeuvre_nature"]."</label>
		</div>
		<div class='row'>
			!!oeuvre_nature!!
		</div>
	</div>
	<!--	nom	-->
	<div class='row'>
		<label class='etiquette' for='form_name'>".$msg["aut_titre_uniforme_form_nom"]."</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-50em' name='name' value=\"!!deb_saisie!!\" />
	</div>
	!!authors!!
</div>	
<div class='row'>
	<input type='button' id='btcancel' class='bouton_small' value='$msg[76]' onClick=\"document.location='!!base_url!!&what=titre_uniforme';\">
	<input type='submit' id='btsubmit' value='$msg[77]' class='bouton_small' onClick=\"return test_form(this.form)\">
	</div>
</form>
<script type='text/javascript'>
	document.forms['saisie_titre_uniforme'].elements['name'].focus();
	ajax_parse_dom();
</script>
";
