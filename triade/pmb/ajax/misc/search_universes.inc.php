<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_universes.inc.php,v 1.1.2.2 2024/12/17 10:41:55 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php"))
    die("no access");

global $class_path, $sub, $action, $categ, $object_type, $filters;

require_once ($class_path . "/facettes/facettes_opac_controller.class.php");
require_once ($class_path . "/analytics_services/analytics_services.class.php");

switch ($sub) {
    default:
        $is_external = false;
        $temporary_variable_filters = (! empty($filters) ? encoding_normalize::json_decode(stripslashes($filters), true) : array());
        if (! isset($temporary_variable_filters['type'])) {
            $temporary_variable_filters['type'] = 'notices';
        }
        if ('notices_externes' == $temporary_variable_filters['type']) {
            $is_external = true;
        }
        facettes_opac_controller::set_object_id(0);
        facettes_opac_controller::set_type($temporary_variable_filters['type']);
        facettes_opac_controller::set_is_external($is_external);
        facettes_opac_controller::proceed_ajax($object_type, 'configuration/opac');
        break;
}