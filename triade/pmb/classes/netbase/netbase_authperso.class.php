<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: netbase_authperso.class.php,v 1.1.2.3 2024/10/15 15:41:23 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path . "/indexations_collection.class.php");

class netbase_authperso extends netbase_authorities {
    
    protected static $object_type = AUT_TABLE_AUTHPERSO;
    
    protected static $id_authperso = 0;
    
    protected static $indexation_authpersos = [];
    
    public static function set_id_authperso($id_authperso) {
        static::$id_authperso = $id_authperso;
    }
    
    public static function get_index_query_count() {
        return "SELECT count(1) FROM authperso_authorities";
    }
    
    public static function get_index_query($start, $lot) {
        $start = intval($start);
        $lot = intval($lot);
        
        return "SELECT id_authperso_authority as id, authperso_authority_authperso_num from authperso_authorities ORDER BY authperso_authority_authperso_num LIMIT $start, $lot";
    }
    
    public static function index_from_interval($start, $count) {
        global $include_path;
        
        $lot = static::get_lot($count);
        $query = static::get_index_query($start, $lot);
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $id_authperso = 0;
            while($row = pmb_mysql_fetch_object($result)) {
                if(!$id_authperso || ($id_authperso != $row->authperso_authority_authperso_num)) {
                    $indexation_authperso = new indexation_authperso($include_path."/indexation/authorities/authperso/champs_base.xml", "authorities", (1000+$row->authperso_authority_authperso_num), $row->authperso_authority_authperso_num);
                    $indexation_authperso->set_deleted_index(true);
                    $id_authperso = $row->authperso_authority_authperso_num;
                }
                $indexation_authperso->maj($row->id);
            }
            pmb_mysql_free_result($result);
            return ($start + $lot);
        }
        return 0;
    }
    
    public static function index_from_interface($start, $count) {
        $temp_indexation_by_fields = static::$indexation_by_fields;
        
        static::$indexation_by_fields = false;
        $next = parent::index_from_interface($start, $count);
        static::$indexation_by_fields = $temp_indexation_by_fields;
        return $next;
    }
    
    public static function get_query_unrelated_authorities() {
        return "SELECT id_authority FROM authorities LEFT JOIN authperso_authorities ON num_object=id_authperso_authority WHERE type_object ='".static::$object_type."' AND id_authperso_authority IS NULL";
    }
    
    public static function get_indexation_authorities() {
        global $include_path;
        if(!isset(static::$indexation_authorities[static::$object_type][static::$id_authperso]) || static::$indexation_authorities[static::$object_type][static::$id_authperso] == null) {
            static::$indexation_authorities[static::$object_type][static::$id_authperso] = new indexation_authpersos($include_path."/indexation/authorities/authperso/champs_base.xml", "authorities", (1000+static::$id_authperso), static::$id_authperso);
        }
        return static::$indexation_authorities[static::$object_type][static::$id_authperso];
    }
    
}