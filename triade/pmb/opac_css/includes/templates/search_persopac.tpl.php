<?php
// +-------------------------------------------------+
// Â© 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_persopac.tpl.php,v 1.4 2019-05-29 11:23:32 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

//*******************************************************************
// Définition des templates 
//*******************************************************************

global $tpl_search_persopac_liste_tableau;
global $tpl_search_persopac_liste_tableau_ligne;
global $msg;

$tpl_search_persopac_liste_tableau = "

<h3>".$msg["search_persopac_list"]."</h3>

	<div class='row'>
		<table cellpadding='2' style='width:100%'>
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

$tpl_search_persopac_liste_tableau_ligne = "
<tr class='!!pair_impair!!' '!!tr_surbrillance!!'  style='cursor: pointer'>
	<td !!td_javascript!! >!!name!!</td>
	<td !!td_javascript!! >!!human!!</td>	
</tr>
";
?>
