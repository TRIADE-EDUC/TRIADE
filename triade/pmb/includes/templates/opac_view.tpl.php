<?php
// +-------------------------------------------------+
// Â© 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: opac_view.tpl.php,v 1.10 2022/05/12 07:50:04 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg, $tpl_opac_view_content_form, $charset, $tpl_opac_view_create_content_form, $tpl_opac_view_list_sel_tableau, $tpl_opac_view_list_sel_tableau_ligne;

//*******************************************************************
// Définition des templates pour les listes en edition
//*******************************************************************
$tpl_opac_view_content_form = "
<!--	nom	-->
<div class='row'>
	<label class='etiquette' for='name'>".$msg["opac_view_form_name"]."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-80em' id='name' name='name' value=\"!!name!!\" />
</div>

<!--	Multi critère	-->
<div class='row'>
	<input type='radio' name='opac_view_wo_query' id='opac_view_w_query' value='0' !!opac_view_w_query_checked!! />
	<label class='etiquette' for='opac_view_w_query' >".$msg["opac_view_form_search"]."</label>
	<input type='radio' name='opac_view_wo_query' id='opac_view_wo_query' value='1' !!opac_view_wo_query_checked!! />
	<label class='etiquette' for='opac_view_wo_query' >".$msg['opac_view_wo_query']."</label>
</div>
<div class='row'>
	!!requete_human!!
	<input type='hidden' name='requete' value='!!requete!!' />
	<input type='button' id='search_bt' class='bouton' value='".$msg["opac_view_form_add_search"]."' !!search_build!! />
</div>

<!--	Paramètres de la vue	-->
<div class='row'>
	<label class='etiquette' >".$msg["opac_view_form_parameters"]."</label>
</div>
<div class='row'>
	!!parameters!!
</div>

<!--	Filtres de la vue	-->
<div class='row'>
	<label class='etiquette' >".$msg["opac_view_form_filters"]."</label>
</div>
<div class='row'>
	!!filters!!
</div>

<!--	visibilité Opac	-->
<div class='row'>
	<label class='etiquette' >".$msg["opac_view_form_opac_visible_title"]."</label>
</div>
<div class='row'>
	<select name='opac_view_form_visible' id='opac_view_form_visible' onchange=''>
		<option value='0' !!opac_visible_selected_0!!>".$msg["opac_view_form_opac_visible_no"]."</option>
		<option value='1' !!opac_visible_selected_1!!>".$msg["opac_view_form_opac_visible"]."</option>
		<option value='2' !!opac_visible_selected_2!!>".$msg["opac_view_form_opac_visible_connected"]."</option>
	</select>
</div>

<!--	commentaire de la vue	-->
<div class='row'>
	<label class='etiquette' >".$msg["opac_view_form_comment"]."</label>
</div>
<div class='row'>
	<textarea name='comment' rows='3' cols='75' wrap='virtual'>!!comment!!</textarea>
</div>

<!-- date maj et validite -->
<div class='row'>
	<div class='colonne5'>
		<label class='etiquette' >".htmlentities($msg['opac_view_form_last_gen'],ENT_QUOTES, $charset)."</label>
	</div>
	<div class='colonne_suite'>
		!!last_gen!!
	</div>
</div>
<div class='row'>
	<div class='colonne5'>
		<label class='etiquette' >".htmlentities($msg['opac_view_form_ttl'],ENT_QUOTES, $charset)."</label>
	</div>
	<div class='colonne_suite'>
		<input type='text' class='saisie-5em' name='ttl' value='!!ttl!!' />
	</div>
</div>
";

$tpl_opac_view_create_content_form = "
<!--	nom	-->
<div class='row'>
	<label class='etiquette' for='name'>".$msg["opac_view_form_name"]."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-80em' id='name' name='name' value=\"!!name!!\" />
</div>
";

$tpl_opac_view_list_sel_tableau = "
<div class='row'>
	!!forcage!!
</div>
<table>
	<tr>
		<th>".$msg["opac_view_list_select"]."</th>
		<th>".$msg["opac_view_list_default"]."</th>
		<th>".$msg["opac_view_list_name"]."</th>
		<th>".$msg["opac_view_list_comment"]."</th>
	</tr>
	!!lignes_tableau!!
</table>
";

$tpl_opac_view_list_sel_tableau_ligne = "
<tr class='!!class!!' !!tr_surbrillance!!>
	<td><input type=checkbox name='form_empr_opac_view[]' value='!!opac_view_id!!' !!checked!! class='checkbox' !!disabled!!/></td>
	<td><input type='radio' name='form_empr_opac_view_default' value='!!opac_view_id!!' !!radio_checked!! !!disabled!!></td>
	<td>!!name!!</td>
	<td>!!comment!!</td>
</tr>
";