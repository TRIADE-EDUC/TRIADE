<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_pricing_systems.tpl.php,v 1.4 2019-05-27 10:00:50 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $rent_pricing_systems_list_tpl, $msg, $charset, $rent_pricing_system_line_tpl;

$rent_pricing_systems_list_tpl = "
<script src='javascript/pricing_systems.js'></script>
<div class='row'>
	<span id='pricing_systems_messages' class='erreur'>!!messages!!</span>
</div>
<table>
	<tr>
		<th width='5px'></th>
		<th>".htmlentities($msg['pricing_system_label'], ENT_QUOTES, $charset)."</th>
		<th>".htmlentities($msg['pricing_system_associated_exercice'], ENT_QUOTES, $charset)."</th>
		<th></th>
	</tr>
	!!pricing_systems_lines!!
</table>
<input class='bouton' type='button' value=' ".$msg['pricing_system_add']." ' onClick=\"document.location='./admin.php?categ=acquisition&sub=pricing_systems&id_entity=!!id_entity!!&action=edit&id=0'\" />";

$rent_pricing_system_line_tpl = "
<tr class='!!odd_even!!' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='!!odd_even!!'\">
	<td>
		<img class='img_plus' id='pricing_system_img_!!id!!' onclick='display_grid(!!id!!);' src='".get_url_icon('plus.gif')."' />
	</td>
	<td !!onmousedown!! style='cursor: pointer'>!!label!!</td>
	<td !!onmousedown!! style='cursor: pointer'>!!associated_exercice!!</td>
	<td>
		<input type='button' class='bouton' onclick=\"document.location='./admin.php?categ=acquisition&sub=pricing_systems&id_entity=!!id_entity!!&action=grid_edit&id=!!id!!'\" value=\"".htmlentities($msg['pricing_system_edit_grid'], ENT_QUOTES, $charset)."\" />
	</td>		
</tr>
<tr id='pricing_system_grid_!!id!!' class='!!odd_even!!' style='display : none;'>
	<td></td>
	<td colspan='3'>!!grid!!</td>
</tr>";
