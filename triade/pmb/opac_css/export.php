<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: export.php,v 1.34.4.1 2025/02/07 13:49:04 qvarin Exp $

use Pmb\DSI\Models\Channel\Export\ExportChannel;
use Pmb\DSI\Models\Diffusion;
use Pmb\DSI\Orm\DiffusionOrm;

$base_path = ".";
require_once($base_path."/includes/init.inc.php");

global $include_path, $action, $msg, $popup_header, $popup_footer;
global $action, $typeexport, $notice_id, $select_item, $opac_export_allow, $opac_export_allow_expl;

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

require_once('./includes/templates/common.tpl.php');
require_once('./includes/notice_categories.inc.php');

// classe de gestion des catégories
require_once($base_path.'/classes/categorie.class.php');
require_once($base_path.'/classes/notice.class.php');
require_once($base_path.'/classes/notice_display.class.php');

// classe indexation interne
require_once($base_path.'/classes/indexint.class.php');

// classe d'affichage des tags
require_once($base_path.'/classes/tags.class.php');

// pour l'affichage correct des notices
require_once($base_path."/includes/templates/notice.tpl.php");
require_once($base_path."/includes/navbar.inc.php");
require_once($base_path."/includes/explnum.inc.php");

require_once('./classes/notice_affichage.class.php');
require_once('./classes/notice_affichage.ext.class.php');

require_once($include_path."/mail.inc.php") ;

// pour export panier
require_once("$base_path/admin/convert/start_export.class.php");

// si paramétrage authentification particulière et pour la re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) {
    require_once($base_path.'/includes/ext_auth.inc.php');
}

global $lvl, $id;
if ($lvl == "dsi") {
    $id = intval($id);
    if (DiffusionOrm::exist($id)) {
        $diffusion = new Diffusion($id);
        $history = $diffusion->getLastHistorySent(ExportChannel::class);
        if (!empty($history)) {
            $history->send();
        }
    }
    die();
}

switch(true) {
    case isset($notice_id):
        $items = [$notice_id];
        break;
    case (isset($select_item) && $select_item):
        $items = explode(",", $select_item);
        break;
    case (isset($_SESSION["cart"])):
        $items = $_SESSION["cart"];
        break;
    default:
        $items = [];
        break;
}

if (($opac_export_allow == '1') || (($opac_export_allow == '2') && ($_SESSION["user_code"]))) {
    switch ($opac_export_allow_expl) {
        case '1':
            $keep_expl = 1 ;
            $keep_explnum = 0 ;
            break;
        case '2':
            $keep_expl = 0 ;
            $keep_explnum = 1 ;
            break;
        case '3':
            $keep_expl = 1 ;
            $keep_explnum = 1 ;
            break;
        case '0':
        default:
            $keep_expl = 0 ;
            $keep_explnum = 0 ;
            break;
    }
    if($action == 'select') {
        print $popup_header;
        $form = "
            <div id='att' style='z-Index:1000; width=200px'></div>
            <div id='titre-popup'>".$msg['notice_title_export_record']."</div>
            <div>
                <form name='export_form' action='./export.php'><br />";

        $exp = start_export::get_exports();
        $form .= "<select id='typeexport' name='typeexport'>" ;
        for ($i = 0; $i < count($exp); $i++) {
            $form .= "<option value='".$exp[$i]['ID']."'>".$exp[$i]['NAME']."</option>";
        }
        $form .= "</select>" ;
        $form .= "<input type='hidden' name='action' value='export' />";
        $form .= "<input type='hidden' name='notice_id' value='". htmlentities($notice_id, ENT_QUOTES, $charset) ."' />";

        $form .= "<span class=\"export_notice_button\">&nbsp;</span><input type='submit' class='bouton' value=\"{$msg['notice_title_export_record_button']}\" />";
        $form .= '</form>
                    <div class="close_export_popup"><a href="#" onclick="window.close()">'.$msg['close_export_record_popup'].'</a></div>
                    </div>';
        print $form;
        print $popup_footer;
    } elseif ($action == "export") {
        $exportation = "";
        $nb_fiche = 0;
        $nb_fiche_total = count($items);
        $n_notices = $nb_fiche_total;
        $_SESSION["param_export"]["notice_exporte"] = [];
        for ($z = 0; $z < $nb_fiche_total; $z++) {
            $id_externe = 0;
            $is_externe = false;
            $sql = '';
            if (substr($items[$z], 0, 2) != "es") {
                // Exclure de l'export (opac, panier) les fiches interdites de diffusion dans administration, Notices > Origines des notices NG72
                $sql = "select 1 from origine_notice,notices where notice_id = '$items[$z]' and origine_catalogage = orinot_id and orinot_diffusion='1'";
            } else {
                $id_externe = intval(substr($items[$z], 2));
                if($id_externe) {
                    $is_externe = true;
                    $requete = "SELECT source_id FROM external_count WHERE rid=".$id_externe;
                    $myQuery = pmb_mysql_query($requete);
                    $source_id = pmb_mysql_result($myQuery, 0, 0);
                    $sql = "select 1 from entrepot_source_$source_id where recid='".$id_externe."' group by ufield,usubfield,field_order,subfield_order,value";
                }
            }
            if($sql) {
                $res = pmb_mysql_query($sql);
                if (pmb_mysql_fetch_array($res)) {
                    $nb_fiche++;
                    $export = new start_export(($id_externe ? $id_externe : $items[$z]), $typeexport, $is_externe, $keep_expl, $keep_explnum) ;
                    $exportation .= $export->output_notice;
                }
            }
        }

        if ($nb_fiche > 0) {
            $exportation = $export->get_header().$exportation.$export->get_footer();
            header("Content-type: ".$export->get_mime_type());
            header('Content-Disposition: attachment; filename="export.'.$export->get_suffix().'"');

            print $exportation;

            generate_log('export', [], true);
        } else {
            print "<script>alert(\"".$msg['export_aucune_notice']."\"); history.go(-1);</script>";
        }
    }
}
