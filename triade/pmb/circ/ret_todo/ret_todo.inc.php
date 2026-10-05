<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ret_todo.inc.php,v 1.6.10.1 2025/05/02 12:27:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $form_cb_expl, $action_piege, $piege_resa, $dest;
global $database_window_title, $deflt_docs_location;

require_once("$class_path/expl_to_do.class.php");

$url="./circ.php?categ=ret_todo";

$expl=new expl_to_do($form_cb_expl,0,$url);
$expl->build_cb_tmpl($msg["alert_circ_retour"]." > ".$msg["alert_circ_retour_todo"], $msg[661], $msg["circ_tit_form_cb_expl"], $url);
if($form_cb_expl){
	$expl->do_form_retour($action_piege,$piege_resa);	
}
$deflt_docs_location = intval($deflt_docs_location);
if($deflt_docs_location) {
    $list_ui_instance = list_items_treat_ui::get_instance(array('expl_retloc' => $deflt_docs_location));
}
switch($dest) {
    case "TABLEAU":
        if($deflt_docs_location) {
            $list_ui_instance->get_display_spreadsheet_list();
        }
        break;
    case "TABLEAUHTML":
        if($deflt_docs_location) {
            print $list_ui_instance->get_display_html_list();
        }
        break;
    case "TABLEAUCSV":
        if($deflt_docs_location) {
            print $list_ui_instance->get_display_csv_list();
        }
        break;
    default:
        echo window_title($database_window_title.$msg["5"]." : ".$msg["circ_doc_a_traiter"]);
        print $expl->cb_tmpl.$expl->expl_form;
        if($deflt_docs_location) {
            print $list_ui_instance->get_display_list();
        }
        break;
}