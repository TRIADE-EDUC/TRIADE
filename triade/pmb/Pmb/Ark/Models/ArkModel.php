<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ArkModel.php,v 1.9 2024/03/06 15:56:04 jparis Exp $
namespace Pmb\Ark\Models;

use Pmb\Ark\Ark;
use Pmb\Ark\Entities\ArkEntity;
use Pmb\Common\Helper\Helper;
use Pmb\Ark\Entities\ArkEntityPmb;

class ArkModel
{

    /**
     *
     * @var int
     */
    protected $type;

    /**
     *
     * @var array
     */
    protected $types;

    /**
     *
     * @var int
     */
    protected $naan;

    /**
     *
     * @var string
     */
    protected $shoulder;
    
    const ARK_ENTITIES = [
        "Pmb"
    ];

    /**
     */
    public function __construct()
    {}

    /**
     *
     * @return int
     */
    private function getNaan()
    {}

    /**
     *
     * @param int $typeObject
     * @param int $numObject
     * @return string
     */
    private function getShoulder(int $typeObject, int $numObject)
    {
        return "";
    }

    /**
     *
     * @param ArkEntity $arkEntity
     * @return \Pmb\Ark\Ark
     */
    public static function getArkFromEntity(ArkEntity $arkEntity)
    {
        $ark = new Ark();
        $ark->setArkEntity($arkEntity);
        return $ark;
    }

    /**
     *
     * @param mixed $entity
     * @return boolean|\Pmb\Ark\Ark
     */
    public static function saveArkFromEntity($entity)
    {
        $arkEntity = self::getArkEntityFromEntity($entity);
        if ($arkEntity instanceof ArkEntity) {
            $arkId = $arkEntity->getArkId();

            if ($arkId > 0) {
                return $arkEntity->save();
            } else {
                $ark = new Ark();
                $ark->setArkEntity($arkEntity);
                $ark->getArkIdentifier();
                $ark->save();
                return $ark;
            }
        }
    }

    /**
     *
     * @param string $arkUrl
     */
    public static function resolve($naan, $identifier, $qualifiers)
    {
        $ark = self::getArkInstance($identifier);
        if (is_null($ark) || is_null($ark->getId())) {
            return "";
        }
        $arkId = $ark->getId();
        $className = "\Pmb\Ark\Entities\ArkEntity" . ucfirst(Helper::camelize($ark->getEntityType()));
        if (class_exists($className)) {
            $entity = $className::getEntityFromArkId($arkId);
            if (! isset($entity)) {
                return $ark->getOpacUrl();
            }
            $entity->setQualifiers($qualifiers);
            return $entity->getOpacUrl();
        }
        return "";
    }

    /**
     *
     * @param
     *            $entity
     * @return \Pmb\Ark\Entities\ArkEntity|boolean
     */
    public static function getArkEntityFromEntity($entity)
    {
        switch (true) {
            case get_class($entity) == 'notice':
            case get_class($entity) == 'analysis':
            case get_class($entity) == 'serial':
                return ArkEntityPmb::getEntityClassFromType(TYPE_NOTICE, $entity->id);
            case get_class($entity) == 'authority':
                return ArkEntityPmb::getEntityClassFromType(TYPE_AUTHORITY, $entity->id);
            case get_class($entity) == 'bulletinage':
                return ArkEntityPmb::getEntityClassFromType(TYPE_BULLETIN, $entity->bulletin_id);
            default:
                return false;
        }
    }

    /**
     *
     * @param string $arkIdentifier
     * @param array $arkIdentifiers
     * @return NULL|NULL|\Pmb\Ark\Ark|\Pmb\Ark\Ark
     */
    private static function getArkInstance(string $arkIdentifier, array $arkIdentifiers = [])
    {
        if (in_array($arkIdentifier, $arkIdentifiers)) {
            return null;
        }
        $ark = new Ark($arkIdentifier);
        if ($ark->getReplacedBy()) {
            $arkIdentifiers[] = $arkIdentifier;
            return self::getArkInstance($ark->getReplacedBy(), $arkIdentifiers);
        }
        return $ark;
    }
    
    public static function generateMassArk(int $lot = 0) {
        foreach (self::ARK_ENTITIES as $type) {
            $className = "\Pmb\Ark\Entities\ArkEntity".$type;
            if (class_exists($className)) {
                $listEntites = $className::getEntitiesWithoutArk($lot);
                $className::massArkInsert($listEntites);
            }
        }
    }
    
    public static function getNbEntitiesWithoutArk() {
        $nb = 0;
        foreach (self::ARK_ENTITIES as $type) {
            $className = "\Pmb\Ark\Entities\ArkEntity".$type;
            if (class_exists($className)) {
                $nb += $className::getNbEntitiesWithoutArk();
            }
        }
        return $nb;
    }
}