<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_schemes.tpl.php,v 1.2 2020/12/22 16:55:43 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg, $base_path, $vedette_scheme_by_entity_form, $vedette_scheme_by_entity_row, $vedette_scheme_by_entity_selector, $vedette_scheme_by_entity_selector_option;

$vedette_scheme_by_entity_form = '
	<h1>'.$msg['composed_vedettes_schemes_by_entity'].'</h1>
	<form action="'.$base_path.'/admin.php?categ=composed_vedettes&sub=schemes&action=save_schemes_by_entity" method="POST">
		<table>
			<tr>
				<th>'.$msg['frbr_cataloging_scheme_entity'].'</th>
				<th>'.$msg['deflt_concept_scheme'].'</th>
			</tr>
			!!vedette_scheme_by_entity_rows!!
		</table>
		<input type="submit" class="bouton"/>
	</form>
';

$vedette_scheme_by_entity_row = '
			<tr>
				<td>!!entity_name!!</td>
				<td>!!schemes_selector!!</td>
			</tr>
';

$vedette_scheme_by_entity_selector = '
	<select name="!!scheme_selector_name!!">
		!!scheme_selector_options!!
	</select>
';

$vedette_scheme_by_entity_selector_option = '
	<option value="!!scheme_selector_option_value!!" !!selected!!>!!scheme_selector_option_label!!</option>
';