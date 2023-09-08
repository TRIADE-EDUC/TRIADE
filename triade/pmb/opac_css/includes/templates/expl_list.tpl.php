<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl_list.tpl.php,v 1.20 2019-05-29 11:23:32 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $expl_list_header;
global $expl_list_footer;
global $expl_list_header_loc_tpl;
global $msg;

// template for PMB OPAC

$expl_list_header = "
<h3><span id='titre_exemplaires' class='titre_exemplaires'>".$msg["exemplaries"]."<!--nb_expl_visible--></span></h3>
<table cellpadding='2' class='exemplaires' style='width:100%'>
";

$expl_list_footer ="
</table>";

$expl_list_header_loc_tpl="
<h3><span id='titre_exemplaires' class='titre_exemplaires'>".$msg["exemplaries"]."<!--nb_expl_visible--></span></h3>
<ul id='onglets_isbd_public!!id!!' class='onglets_isbd_public'>  
  	<li id='onglet_expl_loc!!id!!' class='isbd_public_active'><a href='#' onclick=\"show_what('EXPL_LOC', '!!id!!'); return false;\">!!mylocation!!</a></li>
	<li id='onglet_expl!!id!!' class='isbd_public_inactive'><a href='#' onclick=\"show_what('EXPL', '!!id!!'); return false;\">".$msg['onglet_expl_alllocation']."</a></li>
</ul>
<div id='div_expl_loc!!id!!' style='display:block;'><table cellpadding='2' class='exemplaires' style='width:100%'>!!EXPL_LOC!!</table></div>
<div id='div_expl!!id!!' style='display:none;'><table cellpadding='2' class='exemplaires' style='width:100%'>!!EXPL!!</table></div>
";
