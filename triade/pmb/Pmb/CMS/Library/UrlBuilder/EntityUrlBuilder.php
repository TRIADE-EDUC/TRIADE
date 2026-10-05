<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EntityUrlBuilder.php,v 1.7 2023/02/24 11:05:39 qvarin Exp $
namespace Pmb\CMS\Library\UrlBuilder;

class EntityUrlBuilder extends RootUrlBuilder
{

    public const LVL = "";

    public static $entitiesIds = null;

    public static $acces = null;

    public static $domain = [];

    public function getEntityType()
    {
        return null;
    }
    
    /**
     * Permet de savoir si le module est activé
     *
     * @return bool
     */
    protected function active_module(): bool
    {
    	return true;
    }

    protected static function getAccesClass()
    {
        if (static::$acces === null) {
            if (!class_exists("acces")) {
                global $class_path;
                require_once($class_path."/acces.class.php");
            }
            static::$acces = new \acces();
        }
        return static::$acces;
    }

    protected function getAccesDomain(int $id)
    {
        if (empty(static::$domain[$id])) {
            static::getAccesClass();
            static::$domain[$id] = static::$acces->setDomain($id);
        }
        return static::$domain[$id];
    }
    
    protected function getQuery(): string
    {
        return "";
    }

    protected function getEntitiesIds(): array
    {
        if (static::$entitiesIds === null) {
            
            static::$entitiesIds = array();
            
            $query = $this->getQuery();
            if (empty($query)) {
                return static::$entitiesIds;
            }
            
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_object($result)) {
                    static::$entitiesIds[] = $row->entity_id;
                }
            }
        }
        return static::$entitiesIds;
    }

    public function getEntityId()
    {
        $this->getEntitiesIds();
        return (empty(static::$entitiesIds) || ! is_countable(static::$entitiesIds)) ? null : static::$entitiesIds[rand(0, (count(static::$entitiesIds) - 1))];
    }

    public function makeUrl(): string
    {
        $base_url = parent::makeUrl();
        if (!$this->active_module()) {
        	return $base_url;
        }
        
        $entityId = $this->getEntityId();
        
        if (empty(static::LVL) || empty($entityId)) {
            return $base_url;
        }
        return $base_url . "index.php?lvl=" . static::LVL . "&id=" . $entityId;
    }
}

