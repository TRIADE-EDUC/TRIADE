<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_cart.tpl.php,v 1.37.8.1 2025/04/02 09:44:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

// templates pour la gestion des paniers

global $msg, $current_module, $liaison_tpl, $empr_cart_choix_quoi, $empr_cart_choix_quoi_edition;

$liaison_tpl = "
<div id='el0Parent' class='parent' >
	<h3>
	<img src='".get_url_icon('minus.gif')."' class='img_moins align_bottom' name='imEx' id='el0Img' title='$msg[empr_caddie_used_in]' border='0' onClick=\"expandBase('el0', true); return false;\" />
	$msg[empr_caddie_used_in]
	</h3>
</div>
<div id='el0Child' class='child'>
	<!-- info_liaisons -->
</div>
<div class='row'>&nbsp;</div>";

// $empr_cart_choix_quoi : template form choix des éléments à traiter
$empr_cart_choix_quoi = "
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

// $empr_cart_choix_quoi_edition : template form choix des éléments à éditer
$empr_cart_choix_quoi_edition = "
<hr />
<form class='form-$current_module' name='maj_proc' method='post' action='!!action!!' target='_blank' >
	<h3>!!titre_form!!</h3>
	<!--	Contenu du form	-->
	<div class='form-contenu'>
		<div class='row'>
			<input type='checkbox' name='elt_flag' id='elt_flag' value='1'><label for='elt_flag'>$msg[caddie_item_marque]</label>
		</div>
		<div class='row'>
			<input type='checkbox' name='elt_no_flag' id='elt_no_flag' value='1'><label for='elt_no_flag'>$msg[caddie_item_NonMarque]</label>
		</div>
	</div>
	<!-- Boutons -->
	<div class='row'>
		<input type='button' class='bouton' value='$msg[76]' onClick='document.location=\"!!action_cancel!!\"' />&nbsp;
		<!-- !!boutons_supp!! -->
	</div>
</form>
";