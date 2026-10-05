<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_indexint.tpl.php,v 1.24 2018/01/24 10:54:46 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

require_once($base_path."/selectors/templates/sel_authorities.tpl.php");

// templates du sélecteur indexint

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

global $dyn;
global $jscript;
global $jscript_common_authorities_unique, $jscript_common_authorities_link;
global $jscript_common_selector;
global $selector_indexint_form;

if ($dyn==3) {
	$jscript = $jscript_common_authorities_unique;
}elseif ($dyn==2) { // Pour les liens entre autorités
	$jscript = $jscript_common_authorities_link;
}else {
	$jscript = $jscript_common_selector;
}

// ------------------------------------------
// 	$selector_indexint_form : form saisie indexint
// ------------------------------------------

$selector_indexint_form = "
<script type='text/javascript'>
<!--
	function test_form(form){
		if(form.indexint_nom.value.length == 0){
			alert(\"$msg[indexint_name_oblig]\");
			return false;
		}
		return true;
	}
-->
</script>
<form name='saisie_indexint' method='post' action=\"!!base_url!!&action=update\">
<h3>$msg[indexint_create_button]</h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette'>$msg[indexint_nom]</label>
		</div>
	<div class='row'>
		<input type='text' size='40' name='indexint_nom' value=\"!!deb_saisie!!\" />
		</div>
	<div class='row'>
		<label class='etiquette'>$msg[indexint_comment]</label>
		</div>
	<div class='row'>
		<input type='text' size='40' name='indexint_comment' value='' />
		</div>
	</div>
<div class='row'>
	<input type='button' id='btcancel' class='bouton_small' value='$msg[76]' onClick=\"document.location='!!base_url!!&what=indexint';\">
	<input type='submit' id='btsubmit' value='$msg[77]' class='bouton_small' onClick=\"return test_form(this.form)\">
	</div>
</form>
<script type='text/javascript'>
	document.forms['saisie_indexint'].elements['indexint_nom'].focus();
</script>
";
