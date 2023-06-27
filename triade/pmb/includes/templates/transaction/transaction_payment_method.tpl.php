<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transaction_payment_method.tpl.php,v 1.2 2019-05-27 10:16:50 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $transaction_payment_method_form, $msg, $current_module, $transaction_payment_method_list_form;

$transaction_payment_method_form="

<script type='text/javascript'>
<!--
	function test_form(form) {
		
		if((form.f_name.value.replace(/^\s+|\s+$/g,'').length == 0) ) {
			alert(\"".$msg["transaction_payment_method_form_name_no"]."\");
			return false;
		}
		return true;
	}

-->
</script>
<form class='form-$current_module' name='transaction_payment_method' method='post' action='!!action!!' >
<h3>!!titre!!</h3>
<div class='form-contenu' >
	<div class='row'>
		<label class='etiquette' for='f_name'>".$msg["transaction_payment_method_form_name"]."</label>
		<div class='row'>
			<input type='text' class='saisie-50em' id=\"f_name\" value='!!name!!' name='f_name'  />				
		</div>
	</div>		
	<div class='row'></div>
</div>
<div class='row'>
	<div class='left'>
		<input type='button' class='bouton' value=' $msg[76] ' onClick=\"history.go(-1);\" />
		<input type='submit' class='bouton' value=' ".$msg["transaction_payment_method_form_save"]." ' onclick=\"return test_form(this.form)\" />
		</div>
	<div class='right'>
		!!supprimer!!
		</div>
	</div>
<div class='row'></div>
</form>		
<script>document.forms['transaction_payment_method'].elements['f_name'].focus();</script>
";


$transaction_payment_method_list_form="
<table>
	<tr>
		<th>".$msg["transaction_payment_method_list_libelle"]."</th>
	</tr>
		!!transaction_payment_method_list!!
</table>
<input class='bouton' type='button' value=\" ".$msg["transaction_payment_method_list_add"]." \" onClick=\"document.location='./admin.php?categ=finance&sub=transaction_payment_method&action=edit'\" />
";



