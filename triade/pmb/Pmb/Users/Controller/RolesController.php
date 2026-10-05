<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RolesController.php,v 1.1.2.5 2024/11/29 07:40:46 dgoron Exp $
namespace Pmb\Users\Controller;

use Pmb\Common\Controller\Controller;
use Pmb\Common\Views\VueJsView;
use Pmb\Users\Models\RoleModel;
use Pmb\Users\Models\RoleMemberModel;
use Pmb\Users\Models\Roles;

class RolesController extends Controller
{

    /**
     *
     * @param string $action
     * @return
     */
    public $action;

    /**
     * 
     * @var array
     */
    private static $usersRights;
    
    public function proceed(string $action = "", $data = null)
    {
        $this->action = $action;
        switch ($action) {
            default:
            case "edit":
                return $this->editAction();
                break;
            case "save":
                return $this->saveAction();
                break;
        }
    }

    public function editAction()
    {
        global $pmb_url_base;
        
        $role = new RoleModel($this->data->id);
        if (empty($role->members)) {
            $role->members = new \stdClass();
        }
        if (empty($role->modules)) {
            $role->modules = new \stdClass();
        }
        if (empty($role->tabs)) {
            $role->tabs = new \stdClass();
        }
        if (empty($role->subTabs)) {
            $role->subTabs = new \stdClass();
        }
        $newVue = new VueJsView("users/roles", [
            "url_webservice" => $pmb_url_base . "rest.php/users/",
            "role" => $role,
        ]);
        print $newVue->render();
    }

    public function saveAction()
    {
        
        RoleModel::update($this->data);
    }
    
    public function getUserRights($component, $data)
    {
        if (empty($data['userId']) || empty($data['module'])) {
            return [];
        }
        $userId = $data['userId'];
        $module = $data['module'];
        
        if (!isset(static::$usersRights[$userId])) {
            static::$usersRights[$userId] = [];
        }
        if (!isset(static::$usersRights[$userId][$module])) {
            $roles = new Roles($userId, $module);
            static::$usersRights[$userId][$module] = $roles->getUserRights();
        }
        switch ($component) {
            case 'modules':
                return static::$usersRights[$userId][$module][$component][$data['module']] ?? [];
            case 'tabs':
            case 'subTabs':
            default:
                $keyTab = $data['categ'].'/'.$data['sub'].'/'.$data['urlExtra'];
                return static::$usersRights[$userId][$module][$component][$keyTab] ?? [];
            
        }
    }
    
    protected function checkReferer($data)
    {
        global $pmb_url_base;
        global $categ, $sub;
        
        if (!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $pmb_url_base) !== false) {
            return true;
        }
        
        // On est en accès direct..on vérifie les droits modules / menus / sous-menus
        $request_uri = "./".pmb_substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/')+1);
        
        //Modules
        $list_modules_ui = \list_modules_ui::get_instance();
        $objects = $list_modules_ui->get_objects();
        foreach ($objects as $object) {
            if($request_uri == $list_modules_ui->get_module_destination_link($object->get_name())) {
                return true;
            }
        }
        //Menus
        $list_tabs_ui_class_name = "\list_tabs_".$data['module']."_ui";
        $objects = $list_tabs_ui_class_name::get_instance()->get_objects();
        foreach ($objects as $object) {
            if($data['categ'] == $object->get_categ() && $data['sub'] == $object->get_sub() &&  strpos($request_uri, $object->get_destination_link()) !== false) {
                return true;
            }
        }
        //Sous-menus
        $list_subtabs_ui_class_name = "\list_subtabs_".$data['module']."_ui";
        $list_subtabs_ui_class_name::set_categ($categ);
        $list_subtabs_ui_class_name::set_sub($sub ?? '');
        $objects = $list_subtabs_ui_class_name::get_instance()->get_objects();
        foreach ($objects as $object) {
            if($data['sub'] == $object->get_sub() &&  strpos($request_uri, $object->get_destination_link()) !== false) {
                return true;
            }
        }
        return false;
    }
    public function checkUserAccess($data)
    {
        global $PMBuserid;
        
        if ($PMBuserid == 1) {
            return true;
        }
        
        //A activer plus tard pour contrôler l'acces direct
        /*if (!$this->checkReferer($data)) {
            return false;
        }*/
        
        $userRights = $this->getUserRights('modules', $data);
        if (isset($userRights['visible']) && $userRights['visible'] == 0) {
            return false;
        }
        $userRights = $this->getUserRights('tabs', $data);
        if (isset($userRights['visible']) && $userRights['visible'] == 0) {
            return false;
        }
        $userRights = $this->getUserRights('subTabs', $data);
        if (isset($userRights['visible']) && $userRights['visible'] == 0) {
            return false;
        }
        return true;
    }
}