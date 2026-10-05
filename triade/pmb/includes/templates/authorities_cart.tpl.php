<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authorities_cart.tpl.php,v 1.5 2021/06/14 12:43:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $include_path;
require_once("$include_path/templates/export_param.tpl.php");

global $authorities_cart_choix_quoi, $current_module, $msg;

// templates pour la gestion des paniers

$authorities_cart_choix_quoi = "
<script type='text/javascript'>
	function test_form(form) {
		if(!form.elt_flag.checked && !form.elt_no_flag.checked) {
			alert('".addslashes($msg['caddie_no_elements_for_cart'])."');
			return false;
		}
		return true;
	}
</script>
<hr />
<form class='form-$current_module' name='maj_proc' method='post' action='!!action!!' >
	<h3>!!titre_form!!</h3>
	<!--	Contenu du form	-->
	<div class='form-contenu'>
		<div class='row'>
			<input type='checkbox' name='elt_flag' id='elt_flag' value='1' !!elt_flag_checked!!><label for='elt_flag'>$msg[caddie_item_marque]</label>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<input type='checkbox' name='elt_no_flag' id='elt_no_flag' value='1' !!elt_no_flag_checked!!><label for='elt_no_flag'>$msg[caddie_item_NonMarque]</label>
		</div>
	</div>
	<!-- Boutons -->
	<div class='row'>
		<input type='button' class='bouton' value='$msg[76]' onClick='document.location=\"!!action_cancel!!\"' />&nbsp;
		<input type='submit' class='bouton' value='!!bouton_valider!!' onClick=\"if(!test_form(this.form)) {return false;} else {!!onclick_valider!!}\"/>&nbsp;
	</div>
</form>
";

