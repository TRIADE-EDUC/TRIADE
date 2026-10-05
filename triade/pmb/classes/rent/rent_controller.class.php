<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_controller.class.php,v 1.1.10.1 2024/07/19 07:01:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class rent_controller extends lists_controller {
	
    public static function proceed($id=0) {
        global $action;
        global $id_bibli;
        
        switch($action) {
            case 'list' :
                entites::setSessionBibliId($id_bibli);
                $list_ui_instance = static::get_list_ui_instance();
                print $list_ui_instance->get_display_list();
                break;
            case 'edit' :
                $model_instance = static::get_model_instance($id);
                print $model_instance->get_form();
                break;
            case 'update' :
                $model_instance = static::get_model_instance($id);
                $model_instance->set_properties_from_form();
                $model_instance->save();
                
                $list_ui_instance = static::get_list_ui_instance();
                print $list_ui_instance->get_display_list();
                break;
            case 'delete' :
                $model_instance = static::get_model_instance($id);
                $deleted = $model_instance->delete();
                $list_ui_instance = static::get_list_ui_instance();
                if(!empty($deleted['msg_to_display'])) {
                    $list_ui_instance->set_messages($deleted['msg_to_display']);
                }
                print $list_ui_instance->get_display_list();
                break;
            default:
                if(entites::is_selected_biblio('get_display_list', static::$list_ui_class_name) == false) {
                    print entites::show_list_biblio('get_display_list', static::$list_ui_class_name);
                } else {
                    parent::proceed($id);
                }
                break;
        }
    }
}