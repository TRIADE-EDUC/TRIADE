<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: UsersAPIController.php,v 1.1.2.4 2024/11/29 07:40:46 dgoron Exp $
namespace Pmb\Users\Controller;

use Pmb\Common\Controller\Controller;
use Pmb\Users\Models\RoleModel;

class UsersAPIController extends Controller
{
    public function getMembersList() : void
    {
        $this->ajaxJsonResponse([
            'users' => \list_users_ui::get_instance()->get_objects_data(),
            'groups' => \list_configuration_users_groups_ui::get_instance()->get_objects_data()
        ]);
    }
    
    public function getModulesList() : void
    {
        \list_modules_ui::set_no_check_rights(true);
        $this->ajaxJsonResponse([
            'modules' => \list_modules_ui::get_instance([], [], ['by' => 'label'])->get_objects_data()
        ]);
    }

    /**
     * 
     * @param string $moduleName
     */
    public function getTabsList(string $moduleName) : void
    {
        $list_tabs_class_name = "\list_tabs_".$moduleName."_ui";
        if (class_exists($list_tabs_class_name)) {
            $list_tabs_class_name::set_no_check_rights(true);
            $this->ajaxJsonResponse([
                'tabs' => $list_tabs_class_name::get_instance()->get_objects_data()
            ]);
        } else {
            $this->ajaxJsonResponse([
                'tabs' => []
            ]);
        }
    }
    
    /**
     *
     * @param string $moduleName
     */
    public function getSubTabsList(string $moduleName) : void
    {
        $subTabs = array();
        $list_subtabs_class_name = "\list_subtabs_".$moduleName."_ui";
        if (class_exists($list_subtabs_class_name)) {
            $list_subtabs_class_name::set_no_check_rights(true);
            $list_subtabs_ui = $list_subtabs_class_name::get_instance();
            
            $list_tabs_class_name = "\list_tabs_".$moduleName."_ui";
            $list_tabs_class_name::set_no_check_rights(true);
            $objects_data = $list_tabs_class_name::get_instance()->get_objects_data();
            foreach ($objects_data as $objects) {
                foreach($objects as $object) {
                    $list_subtabs_class_name::set_categ($object['categ']);
                    $list_subtabs_class_name::set_sub($object['sub']);
                    $list_subtabs_ui->reload_data();
                    $subTabs[$object['label_code']] = $list_subtabs_ui->get_objects_data();
                }
                
            }
        }
        $this->ajaxJsonResponse([
            'subtabs' => $subTabs
        ]);
    }
    
    /**
     * enregistrement d'un rôle
     */
    public function saveRole() : void
    {
        global $msg;
        
        $roleModel = new RoleModel($this->data->id);
        $roleModel->setPropertiesFromForm($this->data);
        $succes = $roleModel->save();
        
        if ($succes) {
            $this->ajaxJsonResponse([
                'succes' => true
            ]);
        }
        $this->ajaxError($msg['common_failed_save']);
    }
    
    /**
     * suppresion d'un rôle
     */
    public function deleteRole() : void
    {
        global $msg;

        $roleModel = new RoleModel($this->data->id);
        $succes = $roleModel->delete();
        if ($succes) {
            $this->ajaxJsonResponse([
                'succes' => true
            ]);
        }
        $this->ajaxError($msg['common_failed_save']);
    }
}