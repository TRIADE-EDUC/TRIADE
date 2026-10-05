<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_serie.tpl.php,v 1.22 2018/03/26 14:03:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

require_once($base_path."/selectors/templates/sel_authorities.tpl.php");

// templates du sélecteur titre de série

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

global $dyn;
global $jscript;
global $jscript_common_authorities_unique, $jscript_common_authorities_link;
global $jscript_common_selector;

if ($dyn==3) {
	$jscript = $jscript_common_authorities_unique;
}elseif ($dyn==2) { // Pour les liens entre autorités
	$jscript = $jscript_common_authorities_link;
}else {
	$jscript = $jscript_common_selector;
}

// ------------------------------------------
// 	$selector_serie_form : form saisie titre de série
// ------------------------------------------

$selector_serie_form = "
<script type='text/javascript'>
<!--
	function test_form(form){
		if(form.serie_nom.value.length == 0){
			alert(\"$msg[338]\");
			return false;
		}
		return true;
	}
-->
</script>
<form name='saisie_serie' method='post' action=\"!!base_url!!&action=update\">
<!-- ajouter un titre de  série -->
<h3>$msg[339]</h3>
<div class='form-contenu'>
	<!-- nom -->
	<div class='row'>
		<label class='etiquette'>$msg[233]</label>
		</div>
	<div class='row'>
		<input type='text' size='40' name='serie_nom' value=\"!!deb_saisie!!\" />
		</div>
	</div>
<div class='row'>
	<input type='button' id='btcancel' class='bouton_small' value='$msg[76]' onClick=\"document.location='!!base_url!!&what=serie';\">
	<input type='submit' id='btsubmit' value='$msg[77]' class='bouton_small' onClick=\"return test_form(this.form)\">
	</div>
</form>
<script type='text/javascript'>
	document.forms['saisie_serie'].elements['serie_nom'].focus();
</script>
";
