<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_accounting_ui.tpl.php,v 1.2 2019-05-27 10:13:11 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $list_accounting_ui_search_filters_form_tpl, $msg;

$list_accounting_ui_search_filters_form_tpl = "
<div class='row'>
	<div class='colonne2'>
		<div class='row'>
			<label class='etiquette'></label>
		</div>
		<div class='row'>
			<input type='text' id='!!objects_type!!_user_input' name='!!objects_type!!_user_input' value='!!user_input!!' class='saisie-30em' />
		</div>
	</div>
	<div class='colonne2'>
		<div class='row'>
			<label class='etiquette'>".$msg["acquisition_budg_exer"]."</label>
		</div>
		<div class='row'>
			!!exercices_selector!!
		</div>
	</div>
</div>
<div class='row'>
	<div class='colonne2'>
		<div class='row'>
			<label class='etiquette'>".$msg["acquisition_statut"]."</label>
		</div>
		<div class='row'>
			!!status_selector!!
		</div>
	</div>
	<div class='colonne2'>
		<div class='row'>
			<label class='etiquette'>".$msg["acquisition_coord_lib"]."</label>
		</div>
		<div class='row'>
			!!entites_selector!!
		</div>
	</div>
</div>
<script type='text/javascript'>
	document.getElementById('!!objects_type!!_user_input').focus();
</script>
";