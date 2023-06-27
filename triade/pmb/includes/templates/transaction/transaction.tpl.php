<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transaction.tpl.php,v 1.3 2019-05-27 10:25:29 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $transactype_form, $msg, $current_module, $transactype_list_form;

$transactype_form="

<script type='text/javascript'>
<!--
	function test_form(form) {
		
		if((form.f_name.value.replace(/^\s+|\s+$/g,'').length == 0) ) {
			alert(\"".$msg["transactype_form_name_no"]."\");
			return false;
		}
		return true;
	}

-->
</script>
<form class='form-$current_module' name='transactype' method='post' action='!!action!!' >
<h3>!!titre!!</h3>
<div class='form-contenu' >
	<div class='row'>
		<label class='etiquette' for='f_name'>".$msg["transactype_form_name"]."</label>
		<div class='row'>
			<input type='text' class='saisie-50em' id=\"f_name\" value='!!name!!' name='f_name'  />				
		</div>
	</div>
	
	
	<div class='row'>
		<label class='etiquette' for='f_unit_price'>".$msg["transactype_form_unit_price"]."</label>
		<div class='row'>
			<input type='text' class='saisie-50em' id=\"f_unit_price\" value='!!unit_price!!' name='f_unit_price'  />				
		</div>
	</div>
	
	<div class='row'>
		<label class='etiquette' for='f_quick_allowed'>".$msg["transactype_form_quick_allowed"]."</label>
		<div class='row'>
			<input type='checkbox' !!quick_allowed_checked!! class='checkbox' id=\"f_quick_allowed\" value='1' name='f_quick_allowed'  />				
		</div>
	</div>
	
	<div class='row'></div>
</div>
<div class='row'>
	<div class='left'>
		<input type='button' class='bouton' value=' $msg[76] ' onClick=\"history.go(-1);\" />
		<input type='submit' class='bouton' value=' ".$msg["transactype_form_save"]." ' onclick=\"return test_form(this.form)\" />
		</div>
	<div class='right'>
		!!supprimer!!
		</div>
	</div>
<div class='row'></div>
</form>		
<script>document.forms['transactype'].elements['f_name'].focus();</script>
";


$transactype_list_form="
<table>
	<tr>
		<th>".$msg["transactype_list_libelle"]."</th>
		<th>".$msg["transactype_form_unit_price"]."</th>
		<th>".$msg["transactype_form_quick_allowed"]."</th>
	</tr>
		!!transactype_list!!
</table>
<input class='bouton' type='button' value=\" ".$msg["transactype_list_add"]." \" onClick=\"document.location='./admin.php?categ=finance&sub=transactype&action=edit'\" />
";



