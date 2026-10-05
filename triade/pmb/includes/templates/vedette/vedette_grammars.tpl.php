<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_grammars.tpl.php,v 1.2 2019/05/27 09:14:28 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg, $base_path, $vedette_grammars_by_entity_form, $vedette_grammars_by_entity_row, $vedette_grammars_by_entity_selector, $vedette_grammars_by_entity_selector_option;

$vedette_grammars_by_entity_form = '
	<h1>'.$msg['composed_vedettes_grammars_by_entity'].'</h1>
	<form action="'.$base_path.'/admin.php?categ=composed_vedettes&sub=param&action=save_grammars_by_entity" method="POST">
		<table>
			<tr>
				<th>'.$msg['frbr_cataloging_scheme_entity'].'</th>
				<th>'.$msg['composed_vedettes_grammar'].'</th>
			</tr>
			!!vedette_grammars_by_entity_rows!!
		</table>
		<input type="submit" class="bouton"/>
	</form>
';

$vedette_grammars_by_entity_row = '
			<tr>
				<td>!!entity_name!!</td>
				<td>!!grammars_selector!!</td>
			</tr>
';

$vedette_grammars_by_entity_selector = '
	<select multiple="multiple" name="!!grammar_selector_name!!">
		!!grammar_selector_options!!
	</select>
';

$vedette_grammars_by_entity_selector_option = '
	<option value="!!grammar_selector_option_value!!" !!selected!!>!!grammar_selector_option_label!!</option>
';