<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: LayoutElementModel.php,v 1.10 2022/06/07 09:49:58 qvarin Exp $
namespace Pmb\CMS\Models;

use Pmb\CMS\Semantics\RootSemantic;

class LayoutElementModel extends PortalRootModel
{

    public static $nbInstance = 0;

    /**
     * 
     * @var LayoutElementModel[]
     */
    public static $instances = array();
    
    protected $name = "";
    
    /**
     *
     * @var \Pmb\CMS\Semantics\RootSemantic|null
     */
    protected $semantic = null;
    
    protected $classement = "";
        
    public static $classements = [];
    
    public $isHidden = false;
    
    /**
     *
     * @return \Pmb\CMS\Semantics\RootSemantic|null
     */
    public function getSemantic()
    {
        return $this->semantic;
    }

    /**
     * 
     * @param RootSemantic $semantic
     */
    public function setSemantic(RootSemantic $semantic)
    {
        $this->semantic = $semantic;
    }
    
    public function getClassement()
    {
        return static::getClassementOfFrame($this->getSemantic()->getIdTag());
    }
    
    public function setClassement(string $classement) 
    {
        if (!empty($this->getSemantic()) && static::addFrameClassement($classement, $this->getSemantic()->getIdTag())) {            
            $this->classement = $classement;
            return true;
        }
        return false;
    }
    
    public static function getClassementOfFrame(string $idTagFrame)
    {
        return static::$classements[$idTagFrame] ?? "";
    }
    
    public static function addFrameClassement(string $classement, string $idTag)
    {
        if ($classement == static::getClassementOfFrame($idTag)) {
            return false;
        }
        
        if (!isset(static::$classements[$idTag])) {
            static::$classements[$idTag] = "";
        }
        
        static::$classements[$idTag] = $classement;
        return true;
    }
    
    public function init()
    {
        if (!empty($this->getSemantic())) {
            $this->classement = $this->getClassement();
        }
    }

    public function setClassements(array $classements)
    {
    	// Use unsetClassements() for reset $classements
    	if (empty($classements)) return false; 
    	static::$classements = $classements;
    }

    public function unsetClassements()
    {
    	static::$classements = [];
    }
}