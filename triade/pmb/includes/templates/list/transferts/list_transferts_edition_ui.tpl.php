<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_transferts_edition_ui.tpl.php,v 1.2 2019-05-27 10:07:02 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $list_transferts_edition_ui_search_filters_form_tpl, $msg, $list_transferts_edition_ui_search_order_form_tpl;

$list_transferts_edition_ui_search_filters_form_tpl = "
<div class='row'>
	<div class='colonne3'>
		<div class='row'>
			<label class='etiquette'>".$msg["transferts_edition_filtre_origine"]."</label>
		</div>
		<div class='row'>
			<select name='!!objects_type!!_site_origine'>!!liste_sites_origine!!</select>
		</div>
	</div>
	<div class='colonne3'>
		<div class='row'>
			<label class='etiquette'>".$msg["transferts_edition_filtre_destination"]."</label>
		</div>
		<div class='row'>
			<select name='!!objects_type!!_site_destination'>!!liste_sites_destination!!</select>
		</div>
	</div>
	<div class='colonne3'>
		!!retour_filtre_etat!!
	</div>
</div>";

$list_transferts_edition_ui_search_order_form_tpl = "
<div class='row'>
	<div class='colonne3'>
		<div class='row'>
			<label class='etiquette'>".$msg["transferts_edition_order"]."</label>
		</div>
		<div class='row'>
			<select name='!!objects_type!!_select_order'>!!list_order!!</select>
		</div>
	</div>
</div>
";
