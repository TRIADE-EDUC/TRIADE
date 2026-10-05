<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RoleMemberModel.php,v 1.1.2.5.2.4 2025/04/03 14:54:41 dgoron Exp $

namespace Pmb\Users\Models;

use Pmb\Common\Models\Model;

class RoleMemberModel extends Model
{
    protected $ormName = "Pmb\Users\Orm\RoleMemberOrm";
    
    /**
     *
     * @var string
     */
    public $typeMember = '';
    
    /**
     *
     * @var integer
     */
    public $numMember = 0;
    
    /**
     *
     * @var integer
     */
    public $numRole = 0;
    
    protected $numRoles = [];
    
    public function setPropertiesFromForm(object $data)
    {
    }
    
    public function save()
    {
        $orm = new $this->ormName($this->id);
        
        $orm->type_member = $this->typeMember;
        $orm->num_member = $this->numMember;
        $orm->num_role = $this->numRole;
        
        $orm->save();
        if(!$this->id) {
            $this->id = $orm->id;
        }
        return $orm;
    }
    
    public function delete()
    {
        $orm = new $this->ormName($this->id);
        $orm->delete();
    }
    
    public function getNumRoles($numMember)
    {
        $numMember = intval($numMember);
        if (!isset($this->numRoles[$numMember])) {
            $this->numRoles[$numMember] = [];
            $query = "SELECT num_role FROM users_roles_members WHERE num_member = ".$numMember." AND type_member = 'user'";
            $result = pmb_mysql_query($query);
            while ($row = pmb_mysql_fetch_object($result)) {
                if ($row->num_role) {
                    $this->numRoles[$numMember][] = $row->num_role;
                }
            }
            $numGroup = \user::get_grp_num($numMember);
            $query = "SELECT num_role FROM users_roles_members WHERE num_member = ".$numGroup." AND type_member = 'group'";
            $result = pmb_mysql_query($query);
            while ($row = pmb_mysql_fetch_object($result)) {
                if ($row->num_role) {
                    $this->numRoles[$numMember][] = $row->num_role;
                }
            }
            //Cela n'est pas censé arriver mais faisons en sorte que le rôle ne soit pas présent plusieurs fois
            $this->numRoles[$numMember] = array_unique($this->numRoles[$numMember]);
        }
        return $this->numRoles[$numMember];
    }
    
    /**
     * 
     * @param integer $numMember
     * @param array $roles
     */
    public static function saveRolesFromNumMember($numMember, $roles = [])
    {
        $numMember = intval($numMember);
        if (empty($numMember)) {
            return;
        }
        $rolesIds = [];
        if (!empty($roles)) {
            foreach ($roles as $role) {
                $rolesIds[] = $role->id;
                $query = "SELECT * FROM users_roles_members WHERE num_member = " . $numMember." AND type_member='user' AND num_role = " . $role->id;
                $result = pmb_mysql_query($query);
                if (!pmb_mysql_num_rows($result)) {
                    $roleMemberModel = new RoleMemberModel();
                    $roleMemberModel->typeMember = 'user';
                    $roleMemberModel->numMember = $numMember;
                    $roleMemberModel->numRole = $role->id;
                    $roleMemberModel->save();
                }
            }
        }
        // Suppression des rôles dissociés de l'utilisateur
        $query = "DELETE FROM users_roles_members WHERE num_member = " . $numMember." AND type_member='user'";
        if (!empty($rolesIds)) {
            $query .= " AND num_role NOT IN (".implode(',', $rolesIds).")";
        }
        pmb_mysql_query($query);
    }
}
