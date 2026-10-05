<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_renewal.tpl.php,v 1.5 2019/07/30 11:57:24 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $empr_renewal_form, $msg, $empr_renewal_form_row;

$empr_renewal_form = "
<form class='form-admin' name='empr_renewal_form' method='post' id='empr_renewal_form' action='./admin.php?categ=empr&sub=renewal_form&action=save'>
	<h3>".$msg['empr_renewal_form']."</h3>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='code'>" . $msg['admin_opac_renewal_activate'] . "</label>
		</div>
		<div class='row'>
			<input type='checkbox' id='renewal_activate' name='renewal_activate' class='switch' value='1' !!renewal_activate_checked!!>
			<label for='renewal_activate'>&nbsp;</label>
		</div>
		<table class='modern'>
			<thead id='empr_renewal_form_fixed_header'>
				<tr>
					<th>".$msg['empr_renewal_form_fields']."</th>
					<th>".$msg['empr_renewal_form_display']."</th>
					<th>".$msg['empr_renewal_form_mandatory']."</th>
					<th>".$msg['empr_renewal_form_alterable']."</th>
					<th>".$msg['empr_renewal_form_explanation']."</th>
				</tr>
			</thead>
			<tbody>
				!!empr_renewal_form_rows!!
			</tbody>
		</table>
	</div>
	<div class='row'>
		<input class='bouton' type='submit' value='".$msg['77']."' />
	</div>
</form>";

$empr_renewal_form_row = "
			<tr>
				<td>!!empr_renewal_form_fieldname!!</td>
				<td>
					<input type='checkbox' name='!!empr_renewal_form_field_code!![display]' value='1' !!empr_renewal_form_display!! />
				</td>
				<td>
					<input type='checkbox' name='!!empr_renewal_form_field_code!![mandatory]' value='1' !!empr_renewal_form_mandatory!! !!empr_renewal_form_force_mandatory!! />
				</td>
				<td>
					<input type='checkbox' name='!!empr_renewal_form_field_code!![alterable]' value='1' !!empr_renewal_form_alterable!! />
				</td>
				<td>
					<input type='text' name='!!empr_renewal_form_field_code!![explanation]' value='!!empr_renewal_form_explanation!!' size='50' />
				</td>
			</tr>";