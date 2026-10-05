<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_account_types.tpl.php,v 1.3 2019/05/27 10:05:16 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $rent_request_type_pref_account_list_tpl, $msg, $charset, $rent_request_type_pref_account_tpl, $rent_account_types_list_tpl, $rent_account_type_line_tpl;

$rent_request_type_pref_account_list_tpl = "
<div class='row'>
	<span id='request_type_pref_account_messages' class='erreur'>!!messages!!</span>
</div>
<form name='request_type_pref_account_form' method='post' action='./admin.php?categ=acquisition&sub=account_types&action=save_request_type_pref_account&id_entity=!!id_entity!!&id_exercice=!!id_exercice!!'>
<table>
	<tr>
		<th>".htmlentities($msg['request_type_label'], ENT_QUOTES, $charset)."</th>
 		<th>".htmlentities($msg['request_type_pref_account'], ENT_QUOTES, $charset)."</th>
	</tr>
	!!rent_request_type_pref_account_lines!!
</table>
<input class='bouton' type='submit' value=' ".$msg['request_type_pref_account_save']." ' />
</form>		
<hr />
";

$rent_request_type_pref_account_tpl = "
<tr class='!!odd_even!!' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='!!odd_even!!'\">
	<td>!!request_type_label!!</td>
	<td>!!accounts!!</td>
</tr>";

$rent_account_types_list_tpl = "
<div class='row'>
	<label class='etiquette' for='account_types_exercices'>".htmlentities($msg['account_type_exercices'], ENT_QUOTES, $charset)."</label>!!exercices!!
</div>
<div class='row'>&nbsp;</div>
<div class='row'>
	<span id='account_types_messages' class='erreur'>!!messages!!</span>
</div>
<form name='account_types_form' method='post' action='./admin.php?categ=acquisition&sub=account_types&id_entity=!!id_entity!!&action=save&id_exercice=!!id_exercice!!'>
<table>
	<tr>
		<th>".htmlentities($msg['account_type_label'], ENT_QUOTES, $charset)."</th>
		<th>".htmlentities($msg['account_type_associated_section'], ENT_QUOTES, $charset)."</th>
	</tr>
	!!account_types_lines!!
</table>
<input class='bouton' type='submit' value=' ".$msg['account_type_save']." ' />
</form>";

$rent_account_type_line_tpl = "
<tr class='!!odd_even!!' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='!!odd_even!!'\">
	<td>!!label!!</td>
	<td>!!sections!!</td>
</tr>";
