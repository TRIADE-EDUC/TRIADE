<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RoleRightModel.php,v 1.1.2.3.2.1 2025/03/28 07:51:15 dgoron Exp $

namespace Pmb\Users\Models;

use Pmb\Common\Models\Model;

class RoleRightModel extends Model
{
    protected $ormName = "Pmb\Users\Orm\RoleRightOrm";
    
    /**
     *
     * @var integer
     */
    public $numRole = 0;
    
    /**
     *
     * @var string
     */
    public $component = '';
    
    /**
     *
     * @var string
     */
    public $module = '';
    
    /**
     *
     * @var string
     */
    public $categ = '';
    
    /**
     *
     * @var string
     */
    public $sub = '';
    
    /**
     *
     * @var string
     */
    public $urlExtra = '';
    
    /**
     *
     * @var string
     */
    public $action = '';
    
    /**
     *
     * @var integer
     */
    public $visible = 1;
    
    /**
     *
     * @var integer
     */
    public $privilege = 0;
    
    /**
     *
     * @var integer
     */
    public $log = 0;
    
    public function setPropertiesFromForm(object $data)
    {
        $this->visible = $data->visible;
        $this->privilege = $data->privilege;
        $this->log = $data->log;
    }
    
    public function save()
    {
        if (!$this->id) {
            $ormClass = $this->ormName;
            $instances = $ormClass::finds([
                'num_role' => $this->numRole,
                'component' => $this->component,
                'module' => $this->module,
                'categ' => $this->categ,
                'sub' => $this->sub,
                'url_extra' => $this->urlExtra,
            ]);
            if (!empty($instances[0])) {
                $this->id = $instances[0]->id;
            }
        }
        $orm = new $this->ormName($this->id);
        $orm->component = $this->component;
        $orm->module = $this->module;
        $orm->categ = $this->categ;
        $orm->sub = $this->sub;
        $orm->url_extra = $this->urlExtra;
        $orm->action = $this->action;
        $orm->visible = $this->visible;
        $orm->privilege = $this->privilege;
        $orm->log = $this->log;
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
}
