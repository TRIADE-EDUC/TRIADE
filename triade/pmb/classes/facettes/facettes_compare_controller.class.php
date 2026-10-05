<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes_compare_controller.class.php,v 1.1 2024/02/02 08:03:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class facettes_compare_controller extends lists_controller {
    
    public static function proceed($id=0) {
        global $action;
        
        $id = intval($id);
        $facette_compare = new facette_search_compare();
        switch($action) {
            case "update":
            case "save":
                $facette_compare->save_form();
                print $facette_compare->get_display_parameters();
                break;
            case "modify":
                print $facette_compare->get_form();
                break;
            case "display":
            default:
                print $facette_compare->get_display_parameters();
                break;
        }
    }
}

