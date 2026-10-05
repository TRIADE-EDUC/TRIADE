<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_collstate.inc.php,v 1.1 2023/11/17 09:34:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $action, $object_type;

switch($action) {
    case "list":
        if(strpos($object_type, 'opac_collstate_ui') !== false) {
            lists_controller::proceed_ajax($object_type);
        }
        break;
}

