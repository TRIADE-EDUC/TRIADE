<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_resource.inc.php,v 1.7 2021/07/05 12:50:32 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $opac_contribution_area_activate, $allow_contribution, $class_path, $id, $type, $area_id, $sub;

if (!$opac_contribution_area_activate || !$allow_contribution) {
    die();
}

require_once($class_path.'/notice_affichage.class.php');
require_once($class_path.'/authority.class.php');
require_once($class_path.'/entities.class.php');
require_once($class_path.'/contribution_area/contribution_area_store.class.php');
require_once "$class_path/contribution_area/contribution_area.class.php";

$template = "";
if (!is_numeric($id)) {
    $contribution_area_store = new contribution_area_store();
    $id = $contribution_area_store->get_pmb_identifier_from_uri($id);
}
if (!empty($type) && !empty($id) && is_numeric($id)) {
    $contribution_area = new contribution_area($area_id);
    
    switch($sub) {
        case "get_resource_display_label":
            if ($type != 'notice') {
                $type = authority::get_const_type_object($type);
            }
            $template = entities::get_label_from_entity($id, $type);
            break;
        default :
            $repo_template =  $contribution_area->get_repo_template_records();
            if ($type != 'notice') {
                $repo_template = $contribution_area->get_repo_template_authorities();
            }
            $template = entities::get_entity_template($id, $type, $repo_template);
            break;
    }
}

print $template;