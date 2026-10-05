<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: 

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

//*******************************************************************
// Définition des templates
//*******************************************************************

global $search_segment_persopac_table;
global $search_segment_persopac_line;
global $msg;

$search_segment_persopac_table = "
    
<h3>".$msg["search_persopac_list"]."</h3>
    
	<div class='row'>
		<table style='width:100%; padding:2px'>
		<tr>
			<th>".$msg["search_persopac_table_name"]."</th>
			<th>".$msg["search_persopac_table_humanquery"]."</th>
		</tr>
		!!lignes_tableau!!
		</table>
	</div>
			    
<!--	Bouton	-->
<div class='row'>
</div>
";

$search_segment_persopac_line = "
<tr class='!!pair_impair!!' '!!tr_surbrillance!!'  style='cursor: pointer'>
	<td !!td_javascript!! >!!name!!</td>
	<td !!td_javascript!! >!!human!!</td>
</tr>
";