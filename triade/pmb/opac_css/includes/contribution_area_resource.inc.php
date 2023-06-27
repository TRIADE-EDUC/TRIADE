<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_resource.inc.php,v 1.3 2019-02-22 10:16:53 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!$opac_contribution_area_activate || !$allow_contribution) {
	die();
}

require_once($class_path.'/notice_affichage.class.php');
require_once($class_path.'/authority.class.php');
require_once($class_path.'/contribution_area/contribution_area_store.class.php');

$template = "";
if (!is_numeric($id)) {
    $contribution_area_store = new contribution_area_store();
    //on stocke l'id de l'entité en base SQL s'il existe
    $query = "select ?pmb_id where {
					<$id> pmb:identifier ?pmb_id
				}";
    $contribution_area_store->get_datastore()->query($query);
    if ($contribution_area_store->get_datastore()->num_rows()) {
        $id = $contribution_area_store->get_datastore()->get_result()[0]->pmb_id;
    }
}
if (!empty($type) && !empty($id) && is_numeric($id)) {
    switch ($type) {
        case 'categories':
            $authority = new authority(0, $id, AUT_TABLE_CATEG);
            $template = $authority->get_isbd();
            break;
        case 'authors':
            $authority = new authority(0, $id, AUT_TABLE_AUTHORS);
            $template = $authority->get_isbd();
            break;
        case 'publishers':
            $authority = new authority(0, $id, AUT_TABLE_PUBLISHERS);
            $template = $authority->get_isbd();
            break;
        case 'titres_uniformes':
            $authority = new authority(0, $id, AUT_TABLE_TITRES_UNIFORMES);
            $template = $authority->get_isbd();
            break;
        case 'collections':
            $authority = new authority(0, $id, AUT_TABLE_COLLECTIONS);
            $template = $authority->get_isbd();
            break;
        case 'subcollections':
            $authority = new authority(0, $id, AUT_TABLE_SUB_COLLECTIONS);
            $template = $authority->get_isbd();
            break;
        case 'indexint':
            $authority = new authority(0, $id, AUT_TABLE_INDEXINT);
            $template = $authority->get_isbd();
            break;
        case 'serie':
            $authority = new authority(0, $id, AUT_TABLE_SERIES);
            $template = $authority->get_isbd();
            break;
        case 'notice':
            if (!empty($id)) {
                $template = record_display::get_display_in_contribution($id);
//                 $notice = new notice_affichage($id);
//                 $notice->do_header();
//                 $template = $notice->notice_header;
            }
    		break;
    }
}
print $template;