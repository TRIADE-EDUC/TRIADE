<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Roles.php,v 1.1.2.2 2024/11/28 09:03:10 dgoron Exp $

namespace Pmb\Users\Models;

class Roles
{
    protected $numUser = 0;
    
    protected $module = '';
    
    private $rights;
    
    public function __construct($numUser=0, $module='')
    {
        $this->numUser = intval($numUser);
        $this->module = $module;
    }
    
    protected function mergePropertyRights($property, $name = '', $data = [])
    {
        if (!empty($data['visible'])) {
            $this->rights[$property][$name]['visible'] = $data['visible'];
        }
        if ($data['privilege'] == 0) {
            $this->rights[$property][$name]['privilege'] = $data['privilege'];
        }
        if (!empty($data['log'])) {
            $this->rights[$property][$name]['log'] = $data['log'];
        }
    }
    
    protected function mergeModulesRights($roleData = [])
    {
        if (empty($this->rights['modules'])) {
            $this->rights['modules'] = $roleData;
        } else {
            foreach ($roleData as $name=>$module) {
                $this->mergePropertyRights('modules', $name, $module);
            }
        }
    }
    
    protected function mergeTabsRights($roleData = [])
    {
        if (empty($this->rights['tabs'])) {
            $this->rights['tabs'] = $roleData;
        } else {
            foreach ($roleData as $name=>$tab) {
                $this->mergePropertyRights('tabs', $name, $tab);
            }
        }
    }
    
    protected function mergeSubTabsRights($roleData = [])
    {
        if (empty($this->rights['subTabs'])) {
            $this->rights['subTabs'] = $roleData;
        } else {
            foreach ($roleData as $name=>$subTab) {
                $this->mergePropertyRights('subTabs', $name, $subTab);
            }
        }
    }
    
    public function getUserRights()
    {
        if (!isset($this->rights)) {
            $this->rights = [];
            $roleMemberModel = new RoleMemberModel();
            $numRoles = $roleMemberModel->getNumRoles($this->numUser);
            foreach ($numRoles as $numRole) {
                $roleModel = RoleModel::getInstance($numRole);
                $this->mergeModulesRights($roleModel->modules ?? []);
                $this->mergeTabsRights($roleModel->tabs[$this->module] ?? []);
                $this->mergeSubTabsRights($roleModel->subTabs[$this->module] ?? []);
            }
        }
        return $this->rights;
    }
    
}
