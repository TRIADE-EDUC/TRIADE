<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl_list.tpl.php,v 1.24 2023/12/14 15:31:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $expl_list_header;
global $expl_list_footer;
global $expl_list_header_loc_tpl;
global $msg;
global $opac_rgaa_active;

// template for PMB OPAC
$expl_list_header = "";
if ($opac_rgaa_active) {
    $expl_list_header .= "
    <h2>
        <span class='titre_exemplaires'>".$msg["exemplaries"]."<!--nb_expl_visible--></span>
    </h2>";
} else {
    $expl_list_header .= "
    <h3>
        <span id='titre_exemplaires' class='titre_exemplaires'>".$msg["exemplaries"]."<!--nb_expl_visible--></span>
    </h3>";
}
$expl_list_header .= "
    <table class='exemplaires' style='width:100%; padding:2px'>
        <caption class='visually-hidden'>".htmlentities($msg['list_opac_items_ui_dataset_title'], ENT_QUOTES, $charset)."</caption>
";


$expl_list_footer ="
</table>";

$expl_list_header_loc_tpl = "";
if ($opac_rgaa_active) {
    $expl_list_header_loc_tpl .= "
    <h2>
        <span class='titre_exemplaires'>".$msg["exemplaries"]."<!--nb_expl_visible--></span>
    </h2>";
} else {
    $expl_list_header_loc_tpl .= "
    <h3>
        <span id='titre_exemplaires' class='titre_exemplaires'>".$msg["exemplaries"]."<!--nb_expl_visible--></span>
    </h3>";
}
$expl_list_header_loc_tpl .= "
    <ul id='onglets_isbd_public!!id!!' class='onglets_isbd_public'>
        <li id='onglet_expl_loc!!id!!' class='isbd_public_active'>
            <a href='#' onclick=\"show_what('EXPL_LOC', '!!id!!'); return false;\">!!mylocation!!</a>
        </li>
        <li id='onglet_expl!!id!!' class='isbd_public_inactive'>
            <a href='#' onclick=\"show_what('EXPL', '!!id!!'); return false;\">".$msg['onglet_expl_alllocation']."</a>
        </li>
    </ul>
    <div id='div_expl_loc!!id!!' style='display:block;'><table class='exemplaires' style='width:100%; padding:2px'>!!EXPL_LOC!!</table></div>
    <div id='div_expl!!id!!' style='display:none;'><table class='exemplaires' style='width:100%; padding:2px'>!!EXPL!!</table></div>
";

