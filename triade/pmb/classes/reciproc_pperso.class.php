<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reciproc_pperso.class.php,v 1.3 2021/12/06 11:09:01 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
global $class_path;

require_once($class_path."/authorities_collection.class.php");
require_once($class_path."/notice.class.php");

class reciproc_pperso {
    
    /**
     * (Utilisée en callback par l'indexation)
     */
    public static function get_reciproc_pperso_from_entity($entity_id, $id_champ, $prefix) {
        $datatype = "integer";
        $labels = array();
        
        $query = "
                SELECT " . $prefix . "_custom_origine
                FROM " . $prefix . "_custom_values
                WHERE " . $prefix . "_custom_champ = " . $id_champ . "
                AND " . $prefix . "_custom_" . $datatype . " = " . $entity_id . "
                ORDER BY " . $prefix . "_custom_order
            ";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $label = self::get_entity_label_from_prefix($prefix, $row[$prefix . "_custom_origine"]);
                if ($label) {
                    $labels[] = $label;
                }
            }
        }
        return $labels;
    }
    
    public static function get_entity_label_from_prefix($prefix, $id) {
        switch ($prefix) {
            case "notices" ;
                return notice::get_notice_title($id);
            case "author" ;
                $authority = authorities_collection::get_authority(AUT_TABLE_AUTHORS, $id);
                return $authority->get_isbd();
            case "categ" ;
                $authority = authorities_collection::get_authority(AUT_TABLE_CATEG, $id);
                return $authority->get_isbd();
            case "publisher" ;
                $authority = authorities_collection::get_authority(AUT_TABLE_PUBLISHERS, $id);
                return $authority->get_isbd();
            case "collection" ;
                $authority = authorities_collection::get_authority(AUT_TABLE_COLLECTIONS, $id);
                return $authority->get_isbd();
            case "subcollection" ;
                $authority = authorities_collection::get_authority(AUT_TABLE_SUB_COLLECTIONS, $id);
                return $authority->get_isbd();
            case "serie" ;
                $authority = authorities_collection::get_authority(AUT_TABLE_SERIES, $id);
                return $authority->get_isbd();
            case "tu" ;
                $authority = authorities_collection::get_authority(AUT_TABLE_TITRES_UNIFORMES, $id);
                return $authority->get_isbd_without_responsabilites();
            case "indexint" ;
                $authority = authorities_collection::get_authority(AUT_TABLE_INDEXINT, $id);
                return $authority->get_isbd();
            case "authperso" ;
                $authority = authorities_collection::get_authority(AUT_TABLE_AUTHPERSO, $id);
                return $authority->get_isbd();
            case "skos" ;
                $authority = authorities_collection::get_authority(AUT_TABLE_INDEX_CONCEPT, $id);
                return $authority->get_isbd();
            case "expl" ;
            case "explnum" ;
            case "cms_editorial" ;
            default:
                return "";
        }
    }
}