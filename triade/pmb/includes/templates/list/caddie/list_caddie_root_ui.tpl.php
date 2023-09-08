<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_caddie_root_ui.tpl.php,v 1.3 2019-05-27 10:15:53 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $list_caddie_root_ui_search_filters_form_tpl, $msg;

$list_caddie_root_ui_search_filters_form_tpl = "
<div class='row'>
	<div class='colonne3'>
		<div class='row'>
			<input type='checkbox' name='!!objects_type!!_elt_flag' id='!!objects_type!!_elt_flag' value='1' !!elt_flag!!><label for='!!objects_type!!_elt_flag'>".$msg['caddie_item_marque']."</label>
		</div>
		<div class='row'>
			<input type='checkbox' name='!!objects_type!!_elt_no_flag' id='!!objects_type!!_elt_no_flag' value='1' !!elt_no_flag!!><label for='!!objects_type!!_elt_no_flag'>".$msg['caddie_item_NonMarque']."</label>
		</div>		
	</div>
</div>";
