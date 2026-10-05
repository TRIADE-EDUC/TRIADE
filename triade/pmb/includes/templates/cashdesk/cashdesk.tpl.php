<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cashdesk.tpl.php,v 1.15.8.1 2024/09/11 12:23:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $cashdesk_content_form, $msg;

$cashdesk_content_form="
<div class='row'>
	<label class='etiquette' for='f_name'>".$msg["cashdesk_form_name"]."</label>
	<div class='row'>
		<input type='text' class='saisie-50em' id=\"f_name\" value='!!name!!' name='f_name'  />				
	</div>
</div>
<div class='row'>
	<label class='etiquette' for='f_ex_section'>".$msg["cashdesk_form_affectation"]."</label>
	<div class='row'>
		!!location_section!!
	</div>
</div>
<div class='row'>
	<label class='etiquette' for='form_type'>".$msg["cashdesk_autorisations_transaction"]."</label>
	<input type='button' class='bouton_small align_middle' value='".$msg['tout_cocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list_transactypes\").value,1);'>
	<input type='button' class='bouton_small align_middle' value='".$msg['tout_decocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list_transactypes\").value,0);'>
</div>
<div class='row'>
	!!transactypes!!
</div>
<div class='row'>
	<label class='etiquette' for='form_type'>".$msg["cashdesk_autorisations"]."</label>
	<input type='button' class='bouton_small align_middle' value='".$msg['tout_cocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,1);'>
	<input type='button' class='bouton_small align_middle' value='".$msg['tout_decocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,0);'>
</div>
<div class='row'>
	!!autorisations_users!!
</div>	
<div class='row'>
	<input type='checkbox' !!cashbox_checked!! class='checkbox' id=\"f_cashbox\" value='1' name='f_cashbox'  />	<label class='etiquette' for='f_cashbox'>".$msg["cashdesk_cashbox"]."</label>			
</div>
";

