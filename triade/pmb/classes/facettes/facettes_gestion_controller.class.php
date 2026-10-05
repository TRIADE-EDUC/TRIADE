<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes_gestion_controller.class.php,v 1.4 2024/02/02 08:03:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class facettes_gestion_controller extends facettes_controller {
    
    protected static function init_list_ui_class_name() {
        global $sub;
        
        switch ($sub) {
            case 'facettes_authorities':
                static::$list_ui_class_name = 'list_configuration_gestion_facettes_authorities_ui';
                break;
            case 'facettes_external':
                static::$list_ui_class_name = 'list_configuration_gestion_facettes_external_ui';
                break;
            default:
                static::$list_ui_class_name = 'list_configuration_gestion_facettes_ui';
                break;
        }
        static::$list_ui_class_name::set_num_facettes_set(static::$num_facettes_set);
    }
    
    public static function has_rights($id=0) {
        global $PMBuserid;
        
        $id = intval($id);
        //Aucun identifiant : on peut être en affiche de liste ou en création par exemple
        if($id == 0) {
            return true;
        }
        if (!empty(static::$num_facettes_set)) {
            $id = static::$num_facettes_set;
        }
        $query = "SELECT num_user, users_groups FROM facettes_sets WHERE id_set =".$id;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_object($result);
            $num_user = $row->num_user;
            $users_groups = encoding_normalize::json_decode($row->users_groups, true);
            $grp_num = user::get_param($PMBuserid, 'grp_num');
            if($num_user == $PMBuserid || in_array($grp_num, $users_groups)) {
                return true;
            }
        }
        return false;
    }
}

