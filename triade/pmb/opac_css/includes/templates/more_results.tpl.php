<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: more_results.tpl.php,v 1.6 2019-05-29 11:23:32 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $search_result_affiliate_lvl2_head;
global $search_result_extended_affiliate_lvl2_head;
global $search_result_affiliate_lvl2_footer;
global $tab, $msg;

$search_result_affiliate_lvl2_head = "
<div id=\"resultatrech\"><h3>".$msg['resultat_recherche']."</h3>
	<div class='row'>&nbsp;</div>
	<div id='search_onglet'>
		<ul id='search_tabs' class='search_tabs'>
			<li id='search_tabs_catalog' ".($tab == "catalog" ? "class='current'" : "")."><a href='#' onclick='showSearchTab(\"catalog\",false);return false;'>".$msg['in_catalog']."</a></li>
			<li id='search_tabs_affiliate' ".($tab == "affiliate" ? "class='current'" : "")."><a href='#' onclick='showSearchTab(\"affiliate\",false);return false;'>".$msg['in_affiliate_source']."</a></li>
		</ul>
	</div>";

$search_result_extended_affiliate_lvl2_head = "
<div id=\"resultatrech\"><h3>".$msg['resultat_recherche']."</h3>
	<div class='row'>&nbsp;</div>
	<div id='search_onglet'>
		<ul id='search_tabs' class='search_tabs'>
			<li id='search_tabs_catalog' ".($tab == "catalog" ? "class='current'" : "")."><a href='#' onclick='showSearchTab(\"catalog\",true);return false;'>".$msg['in_catalog']."</a></li>
			<li id='search_tabs_affiliate' ".($tab == "affiliate" ? "class='current'" : "")."><a href='#' onclick='showSearchTab(\"affiliate\",true);return false;'>".$msg['in_affiliate_source']."</a></li>
		</ul>
	</div>";
	
$search_result_affiliate_lvl2_footer = "
</div>";	
?>