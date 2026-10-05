<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RoleModel.php,v 1.1.2.5 2025/01/08 09:08:46 dgoron Exp $

namespace Pmb\Users\Models;

use Pmb\Common\Models\Model;
use Pmb\Users\Orm\RoleRightOrm;
use Pmb\Users\Orm\RoleMemberOrm;

class RoleModel extends Model
{
    protected $ormName = "Pmb\Users\Orm\RoleOrm";
    
    /**
     * 
     * @var string
     */
    public $name ='';
    
    /**
     *
     * @var string
     */
    public $comment ='';
    
    /**
     *
     * @var array
     */
    public $members = [];
    
    /**
     *
     * @var array
     */
    public $modules = [];
    
    /**
     *
     * @var array
     */
    public $tabs = [];
    
    /**
     *
     * @var array
     */
    public $subTabs = [];
    
    /**
     *
     * @var array
     */
    public $actions = [];
    
    protected static $instances = [];
    
    protected function fetchData()
    {
        parent::fetchData();
        $this->fetchMembers();
        $this->fetchModules();
        $this->fetchTabs();
        $this->fetchSubTabs();
    }
    
    protected function getInstanceRightModel($component, $module, $key='')
    {
        $roleRightModel = new RoleRightModel();
        $roleRightModel->component = $component;
        $roleRightModel->module = $module;
        if ($key) {
            $explodedKey = explode('/', $key);
            $roleRightModel->categ = $explodedKey[0];
            $roleRightModel->sub = $explodedKey[1];
            $roleRightModel->urlExtra = $explodedKey[2];
        }
        $roleRightModel->numRole = $this->id;
        return $roleRightModel;
    }
    
    public function setPropertiesFromForm(object $data)
    {
        $this->name = $data->name;
        $this->comment = $data->comment;
        $this->members = [];
        if (!empty($data->members)) {
            foreach ($data->members as $typeMember=>$members) {
                foreach ($members as $numMember) {
                    $roleMemberModel = new RoleMemberModel();
                    $roleMemberModel->typeMember = $typeMember;
                    $roleMemberModel->numMember = $numMember;
                    $roleMemberModel->numRole = $this->id;
                    $this->members[] = $roleMemberModel;
                }
            }
        }
        $this->modules = [];
        if (!empty($data->modules)) {
            foreach ($data->modules as $moduleName=>$module) {
                $roleRightModel = $this->getInstanceRightModel('module', $moduleName);
                $roleRightModel->setPropertiesFromForm($module);
                $this->modules[] = $roleRightModel;
            }
        }
        $this->tabs = [];
        if (!empty($data->tabs)) {
            foreach ($data->tabs as $moduleName=>$tabs) {
                foreach ($tabs as $keyTab=>$tab) {
                    $roleRightModel = $this->getInstanceRightModel('tab', $moduleName, $keyTab);
                    $roleRightModel->setPropertiesFromForm($tab);
                    $this->tabs[] = $roleRightModel;
                }
            }
        }
        $this->subTabs = [];
        if (!empty($data->subTabs)) {
            foreach ($data->subTabs as $moduleName=>$subTabs) {
                foreach ($subTabs as $keySubTab=>$subTab) {
                    $roleRightModel = $this->getInstanceRightModel('subtab', $moduleName, $keySubTab);
                    $roleRightModel->setPropertiesFromForm($subTab);
                    $this->subTabs[] = $roleRightModel;
                }
            }
        }
    }
    
    public function save()
    {
        $orm = new $this->ormName($this->id);
        
        $orm->name = $this->name;
        $orm->comment = $this->comment;
        
        $orm->save();
        if(!$this->id) {
            $this->id = $orm->id;
        }
        
        // Suppression des lignes associées au rôle
        $query = "DELETE FROM users_roles_members WHERE num_role = " . $this->id;
        pmb_mysql_query($query);
        
        foreach ($this->members as $member) {
            $member->save();
        }
        foreach ($this->modules as $module) {
            $module->save();
        }
        foreach ($this->tabs as $tab) {
            $tab->save();
        }
        foreach ($this->subTabs as $subTab) {
            $subTab->save();
        }
        
        //Mise à jour des droits utilisateurs
        foreach ($this->members as $member) {
            if($member->typeMember == 'user') {
                $numRoles = $member->getNumRoles($member->numMember);
                if (!empty($numRoles)) {
                    $user = new \user($member->numMember);
                    $user->adjustment_rights_from_roles($numRoles);
                    $rights =intval($user->get_rights());
                    if ($rights) {
                        $query = "UPDATE users SET rights='".$rights."' WHERE userid=".$member->numMember;
                        pmb_mysql_query($query);
                    }
                }
            }
        }
        return $orm;
    }
    
    public function delete()
    {
        //TODO : supprimer les entrees dans les autres tables 
        
        $orm = new $this->ormName($this->id);
        $orm->delete();
        return true;
    }
    
    public function fetchMembers()
    {
        if (empty($this->members)) {
            $members = RoleMemberOrm::finds(['num_role' => $this->id]);
            foreach ($members as $member) {
//                 $this->members[$member->type_member][$member->num_member] = [
//                     'visible' => 1
//                 ];
                $this->members[$member->type_member][] = $member->num_member;
            }
        }
    }
    
    public function fetchModules()
    {
        if (empty($this->modules)) {
            $this->modules = [];
            $modules = RoleRightOrm::finds(['num_role' => $this->id, 'component' => 'module']);
            foreach ($modules as $module) {
                $this->modules[$module->module] = [
                    'visible' => $module->visible,
                    'privilege' => $module->privilege,
                    'log' => $module->log,
                ];
            }
        }
    }
    
    public function fetchTabs()
    {
        if (empty($this->tabs)) {
            $this->tabs = [];
            $tabs = RoleRightOrm::finds(['num_role' => $this->id, 'component' => 'tab']);
            foreach ($tabs as $tab) {
                $this->tabs[$tab->module][$tab->categ.'/'.$tab->sub.'/'.$tab->url_extra] = [
                    'visible' => $tab->visible,
                    'privilege' => $tab->privilege,
                    'log' => $tab->log,
                ];
            }
        }
    }
    
    public function fetchSubTabs()
    {
        if (empty($this->subTabs)) {
            $this->subTabs = [];
            $subTabs = RoleRightOrm::finds(['num_role' => $this->id, 'component' => 'subtab']);
            foreach ($subTabs as $subTab) {
                $this->subTabs[$subTab->module][$subTab->categ.'/'.$subTab->sub.'/'.$subTab->url_extra] = [
                    'visible' => $subTab->visible, 
                    'privilege' => $subTab->privilege,
                    'log' => $subTab->log,
                ];
            }
        }
    }
    
    public static function getInstance($id)
    {
        $id = intval($id);
        if(!isset(static::$instances[$id])) {
            static::$instances[$id] = new RoleModel($id);
        }
        return static::$instances[$id];
    }
}
