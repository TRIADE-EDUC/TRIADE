<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: more_results.tpl.php,v 1.11 2023/08/21 14:23:01 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $search_result_affiliate_lvl2_head;
global $search_result_extended_affiliate_lvl2_head;
global $search_result_affiliate_lvl2_footer;
global $tab, $msg, $opac_rgaa_active;
$startLink = $opac_rgaa_active ? "<button class='button-unstylized'" : "<a href='#'";
$search_result_affiliate_lvl2_head = "
<div id=\"resultatrech\">!!title!!
	<div id='search_onglet'>
        <br/>
		<ul id='search_tabs' class='search_tabs'>
			<li id='search_tabs_catalog' ".(($tab == "catalog") || (empty($tab)) ? "class='current'" : "").">". $startLink ." onclick='showSearchTab(\"catalog\",false);return false;'>".$msg['in_catalog']."</a></li>
			<li id='search_tabs_affiliate' ".($tab == "affiliate" ? "class='current'" : "").">". $startLink ." onclick='showSearchTab(\"affiliate\",false);return false;'>".$msg['in_affiliate_source']."</a></li>
		</ul>
	</div>";

$search_result_extended_affiliate_lvl2_head = "
<div id=\"resultatrech\">!!title!!
	<div id='search_onglet'>
        <br/>
		<ul id='search_tabs' class='search_tabs'>
			<li id='search_tabs_catalog' ".(($tab == "catalog") || (empty($tab)) ? "class='current'" : "").">". $startLink ." onclick='showSearchTab(\"catalog\",true);return false;'>".$msg['in_catalog']."</a></li>
			<li id='search_tabs_affiliate' ".($tab == "affiliate" ? "class='current'" : "").">". $startLink ." onclick='showSearchTab(\"affiliate\",true);return false;'>".$msg['in_affiliate_source']."</a></li>
		</ul>
	</div>";
	
$search_result_affiliate_lvl2_footer = "
</div>";