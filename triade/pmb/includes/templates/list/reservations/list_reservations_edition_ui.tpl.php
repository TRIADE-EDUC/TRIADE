<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_reservations_edition_ui.tpl.php,v 1.2 2019/05/27 10:14:06 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $pmb_transferts_actif, $pmb_location_reservation, $list_reservations_ui_search_filters_form_tpl, $msg;

if ($pmb_transferts_actif || $pmb_location_reservation) {
	$list_reservations_ui_search_filters_form_tpl = "
		<div class='row'>
			<div class='colonne3'>
				<div class='row'>
					<label class='etiquette'>".$msg['edit_resa_expl_location_filter']."</label>
				</div>
				<div class='row'>
					!!removal_location!!
				</div>
			</div>
			<div class='colonne3'>
				<div class='row'>
					<label class='etiquette'>".$msg['edit_resa_expl_available_filter']."</label>
				</div>
				<div class='row'>
					!!available_location!!
				</div>
			</div>
		</div>";
} else {
	$list_reservations_ui_search_filters_form_tpl = "";
}
