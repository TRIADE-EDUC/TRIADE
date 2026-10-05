<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_search_perso.tpl.php,v 1.9 2021/10/13 13:16:27 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $charset;
global $segment_search_perso_list_form;
global $segment_search_perso_list_line_form, $msg;

$segment_search_perso_list_form = "

<hr />
<h3>".htmlentities($msg["search_persopac_list"], ENT_QUOTES, $charset)."</h3>

	<div class='row'>
		<table>
		<tr>
			<th></th>
			<th>".htmlentities($msg["search_persopac_table_name"], ENT_QUOTES, $charset)."</th>
			<th>".htmlentities($msg["search_persopac_table_shortname"], ENT_QUOTES, $charset)."</th>
			<th>".htmlentities($msg["search_persopac_table_humanquery"], ENT_QUOTES, $charset)."</th>
		</tr>
		!!search_perso_list!!
		</table>
	</div>		
<!--	Bouton Ajouter	-->
<!--
<div class='row'>
	<input class='bouton' value='".htmlentities($msg["search_persopac_add"], ENT_QUOTES, $charset)."' type='button'  data-pmb-evt='{\"class\":\"SegmentForm\", \"type\":\"click\", \"method\":\"loadSearchPersoDialog\", \"parameters\":{\"sub\" : \"search\", \"action\" : \"add\",\"entity_id\" : \"0\", \"entity_type\" : \"search\", \"segment_id\" : \"!!segment_id!!\", \"segment_type\" : \"!!segment_type!!\"}}' >
</div>
-->
";

$segment_search_perso_list_line_form = "
<tr class='!!search_perso_class!!'>
	<td class='center'>
		<input type='checkbox'  value='!!search_perso_id!!' name='!!search_perso_type!!' !!search_perso_checked!!/>
	</td>
	<td>
        <label data-entity-link='!!search_perso_link!!'>!!search_perso_name!!</label>
    </td>
	<td>!!search_perso_shortname!!</td>
	<td>!!search_perso_human!!</td>	
</tr>
";