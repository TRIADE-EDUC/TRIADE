<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_ui.tpl.php,v 1.4 2019/10/14 09:28:44 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl,$msg,$base_path;

$ontology_tpl['list']='
<div class="row">
	<script type="javascript" src="./javascript/sorttable.js"></script>
	<table class="sortable">
		<tr>
			<th>!!list_header!!</th>
		</tr>
		!!list_content!!
	</table>
	<div class="row">
		<input type="button" class="bouton" onclick="!!list_onclick!!" value="!!list_value!!"/>	
	</div>
	!!list_pagination!!
</div>	
';

$ontology_tpl['list_line']='
<tr>
	<td>
		<a href="!!list_line_href!!">!!list_line_libelle!!</a>
	</td>
</tr>
';

$ontology_tpl['list_assertions'] = "
<br />
<div class='row'>
	<div class='colonne10'>
		<img src='".get_url_icon('alert.gif')."' class='align_left'/>
	</div>
	<div class='erreur colonne80'>".$msg["onto_common_warning_object_in_assertions"]."</div>
	<table>
		<tr>
			<th>".$msg["onto_common_assertion_subject"]."</th>
			<th>".$msg["onto_common_assertion_predicate"]."</th>
			<th>".$msg["onto_common_assertion_object"]."</th>
		</tr>
		!!list_content!!
	</table>
</div>
<div class='row'>
	<input type='button' class='bouton' value='".$msg["76"]."' onClick='document.location=\"!!href_cancel!!\"'>
	<input type='button' class='bouton' value='".$msg["autorite_suppr_categ_forcage_button"]."' onClick='document.location=\"!!href_continue!!\"'>
</div>
";

$ontology_tpl['list_assertions_line'] = '
<tr>
	<td>!!assertion_subject!!</td>
	<td>!!assertion_predicate!!</td>
	<td>!!assertion_object!!</td>
</tr>
';