<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id$

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $msg, $tab, $lvl, $dest;
global $allow_loan_hist;
global $opac_cart_allow, $opac_empr_export_loans, $id_empr;

require_once($include_path."/notice_authors.inc.php");

if (!$allow_loan_hist) {
    die();
}

//Récupération des variables postées, on en aura besoin pour les liens
$page=$_SERVER['SCRIPT_NAME'];

$id_empr = intval($id_empr);
$list_opac_loans_archives_reader_ui = list_opac_loans_archives_reader_ui::get_instance(array('arc_id_empr' => $id_empr));
$nb_elements = count($list_opac_loans_archives_reader_ui->get_objects());
if (!$dest) {
    if ($opac_empr_export_loans) {
        echo "<input class=\"bouton\" type=\"button\" value=\"".$msg["print_loans_bt"]."\" name=\"print_loans_bt\" id=\"print_loans_bt\" onClick=\"location.href='empr.php?tab=".$tab."&lvl=".$lvl."&dest=TABLEAU'\">";
    }
    if ($opac_empr_export_loans && $nb_elements) {
        echo "&nbsp;";
    }
    if ($nb_elements && $opac_cart_allow) {
        echo "<span class='addCart'><input type=\"button\" class=\"bouton\" id=\"add_cart_loans_bt\" value=\"".$msg["add_cart_loans_bt"]."\" onClick=\"javascript:document.add_cart_loans.submit();\"></span>";
        echo "<form name='add_cart_loans' method='post' action='cart_info.php?lvl=loans_".$lvl."' target='cart_info' style='display:none'></form>";
    }
    print $list_opac_loans_archives_reader_ui->get_display_list();
} elseif ($dest=="TABLEAU") {
    $list_opac_loans_archives_reader_ui->get_display_spreadsheet_list();
    die();
}