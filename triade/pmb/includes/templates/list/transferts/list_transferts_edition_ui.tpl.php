<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_transferts_edition_ui.tpl.php,v 1.3 2020/10/31 10:09:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg, $list_transferts_edition_ui_search_order_form_tpl;

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
