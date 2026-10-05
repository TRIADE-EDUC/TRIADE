<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nav_history.inc.php,v 1.5 2022/02/07 09:01:58 jparis Exp $

use Pmb\Common\Opac\Views\VueJsView;
use Pmb\Common\Helper\Portal;

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $opac_nav_history_activated;

if ($opac_nav_history_activated) {

    if (strpos($_SERVER["REQUEST_URI"], "ajax.php") === false) {
        //nav_history
        $_SESSION["current_nav_page"] = Portal::getLabel(Portal::getTypePage());
        $_SESSION["current_nav_sub_page"] = Portal::getLabel(Portal::getSubTypePage());
    }
    
    global $id_empr, $navId;
    
    $opac_view = $_SESSION["opac_view"] ?? 0;
    if (empty($opac_view) || $_SESSION["opac_view"] == "default_opac") {
        $opac_view = 0;
    }
    
    $no_data = false;    
    if (empty($_SESSION["nav_history"]) || empty($_SESSION["nav_history"][$opac_view])) {
        $no_data = true;            
    }
    
    $VueJsView = new VueJsView("navHistory/navHistory", [
        'navId' => $navId ?? 0,
        'idEmpr' => $id_empr ?? 0,
        'opacView' => $opac_view ?? 0,
        'no_data' => $no_data,
        'img_expand_arrows' => get_url_icon("expand-arrows"),
    ]);
    echo $VueJsView ->render();
    
}