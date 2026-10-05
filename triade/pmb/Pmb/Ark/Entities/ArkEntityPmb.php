<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ArkEntityPmb.php,v 1.4 2022/09/09 15:23:32 tsamson Exp $
namespace Pmb\Ark\Entities;

use Pmb\Ark\Ark;

class ArkEntityPmb extends ArkEntity
{

    /**
     *
     * @const string
     */
    protected const ENTITY_TYPE = "pmb";

    /**
     *
     * @var int
     */
    protected $arkTypeObject;
    
    /**
     *
     * @var string
     */
    protected $lvl;
    
    /**
     * 
     * @var string
     */
    const OPAC_ENTRY_POINT = "index.php";
    
    /**
     * Pour stocker les instances
     * @var array
     */
    protected static $instances = [];
    
    const AUTHORITY_TYPES = [
        AUT_TABLE_AUTHORS,
        AUT_TABLE_PUBLISHERS,
        AUT_TABLE_COLLECTIONS,
        AUT_TABLE_SUB_COLLECTIONS,
        AUT_TABLE_SERIES,
        AUT_TABLE_TITRES_UNIFORMES,
        AUT_TABLE_INDEXINT,
        AUT_TABLE_CONCEPT,
        AUT_TABLE_AUTHPERSO
    ];
    
    const OTHER_TYPES = [
        TYPE_NOTICE,
        TYPE_BULLETIN
    ];
    /**
     *
     * @param int $entityId
     */
    public function __construct(int $entityId)
    {
        parent::__construct($entityId);
    }

    /**
     */
    protected function fetchData()
    {
        $this->id = 0;
        $this->arkId = 0;
        $this->metadata = [];
        $query = "
            SELECT metadata, num_ark, ark_entity_" . self::ENTITY_TYPE . ".id as id
            FROM ark
            JOIN ark_entity_" . self::ENTITY_TYPE . " ON num_ark = ark.id 
            WHERE ark_entity_" . self::ENTITY_TYPE . ".entity_type = '" . $this->arkTypeObject . "' 
            AND entity_id = '$this->entityId'";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_assoc($result);
            $this->id = $row['id'];
            $this->arkId = $row['num_ark'];
            $this->metadata = \encoding_normalize::json_decode(stripslashes($row['metadata']), true);
        }
    }

    /**
     *
     * @return mixed
     */
    protected function deleteEntity()
    {
        $query = "
            DELETE
            FROM ark_entity_" . self::ENTITY_TYPE . "
            WHERE entity_id = '$this->entityId'
            AND entity_type = '" . $this->arkTypeObject . "'";
        $result = pmb_mysql_query($query);
        return $result;
    }

    /**
     *
     * @param string $arkIdentifier
     * @return boolean
     */
    public function save()
    {
        $this->updateMetadata();
        if (0 == $this->id) {
            $query = "
                INSERT INTO ark_entity_" . self::ENTITY_TYPE . "
                (entity_type, entity_id, num_ark)
                VALUES
                ('" . $this->arkTypeObject . "', '{$this->entityId}', '$this->arkId')";
            $result = pmb_mysql_query($query);
            if ($result) {
                $this->id = pmb_mysql_insert_id();
            }
        }
        return $this->register();
    }

    /**
     *
     * @return number
     */
    public function getArkId()
    {
        if (isset($this->arkId)) {
            return $this->arkId;
        }
        $query = "
            SELECT num_ark 
            FROM ark_entity_" . self::ENTITY_TYPE . "
            WHERE entity_type = '" . $this->arkTypeObject . "' 
            AND entity_id = '$this->entityId'";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            return pmb_mysql_result($result, 0, 0);
        }
        return 0;
    }

    /**
     */
    protected function updateMetadata()
    {
        $date = date("Y-m-d H:i:s");
        if ($this->id > 0) {
            $this->metadata['last_updated'] = $date;
        } else {
            $this->metadata = [
                "created" => date("Y-m-d H:i:s"),
                "last_updated" => $date
            ];
        }
    }

    /**
     *
     * @return int
     */
    public function getArkTypeObject()
    {
        return $this->arkTypeObject;
    }

    public final static function getEntityFromArkId(int $arkId)
    {
        $query = "SELECT entity_type, entity_id FROM ark_entity_" . self::ENTITY_TYPE . " WHERE num_ark = '$arkId'";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_assoc($result);
            return self::getEntityClassFromType($row['entity_type'], $row['entity_id']);
        }
        return null;
    }

    public static function getEntityClassFromType(string $entityType, int $entityId)
    {
        if (isset(self::$instances[$entityType][$entityId])) {
            return self::$instances[$entityType][$entityId];
        }
        switch ($entityType) {
            case TYPE_NOTICE:
                self::$instances[$entityType][$entityId] = new ArkRecord($entityId);
                break;
            case TYPE_BULLETIN:
                self::$instances[$entityType][$entityId] =  new ArkBulletin($entityId);
                break;
            case TYPE_AUTHOR:
            case TYPE_CATEGORY:
            case TYPE_PUBLISHER:
            case TYPE_COLLECTION:
            case TYPE_SUBCOLLECTION:
            case TYPE_SERIE:
            case TYPE_TITRE_UNIFORME:
            case TYPE_INDEXINT:
            case TYPE_AUTHORITY:
            case TYPE_AUTHPERSO:
            case TYPE_CONCEPT:
                self::$instances[$entityType][$entityId] = new ArkAuthority($entityId);
                break;
            default :
                return null;
        }
        return self::$instances[$entityType][$entityId];
    }
    
    public function getOpacUrl()
    {
        if ($this->qualifiers) {
            return $this->generateQualifiedURL();
        }
        return self::OPAC_ENTRY_POINT."?lvl=".$this->lvl."&id=".$this->entityId;
    }
    
    public static function getEntitiesWithoutArk(int $lot = 0)
    {
        $count = 0;
        $tab_result = array();
        //authorities
        $formated_array = array_map(function($a){return \authority::aut_const_to_type_const($a);},self::AUTHORITY_TYPES);
        
        $subQuery = "SELECT entity_id FROM ark_entity_pmb WHERE entity_type IN (".implode(', ', $formated_array).")";
        $query = "SELECT id_authority, type_object FROM authorities WHERE id_authority NOT IN (" . $subQuery . ") AND type_object IN(".implode(', ', self::AUTHORITY_TYPES).") LIMIT $lot";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $type = \authority::aut_const_to_type_const($row["type_object"]);
                $tab_result[$type][] = $row['id_authority'];
                $count++;
                if ($lot && $count == $lot) {
                    return $tab_result;
                }
            }
        }
        foreach (self::OTHER_TYPES as $type) {
            $subQuery = "SELECT entity_id FROM ark_entity_pmb WHERE entity_type = '" . $type . "'";
            $query = "SELECT notice_id as entity_id FROM notices WHERE notice_id NOT IN (" . $subQuery . ") AND notice_id NOT IN (SELECT num_notice FROM bulletins WHERE num_notice > 0) LIMIT $lot";
            if($type == TYPE_BULLETIN) {
                $query = "SELECT bulletin_id as entity_id FROM bulletins WHERE bulletin_id NOT IN (" . $subQuery . ")";
            }
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_assoc($result)) {
                    $tab_result[$type][] = $row['entity_id'];
                    $count++;
                    if ($lot && $count == $lot) {
                        return $tab_result;
                    }
                }
            }
        }
        return $tab_result;
    }
    
    public static function massArkInsert(array $arks)
    {
        foreach($arks as $type => $entities) {
            foreach($entities as $entity_id) {
                $arkEntity = ArkEntityPmb::getEntityClassFromType($type, $entity_id);
                if ($arkEntity instanceof ArkEntity) {
                    $ark = new Ark();
                    $ark->setArkEntity($arkEntity);
                    $ark->getArkIdentifier();
                    $ark->save();
                    unset($ark);
                }
                unset($arkEntity);
            }
        }
    }
    
    public static function getNbEntitiesWithoutArk() {
        $nb = 0;
        foreach (self::AUTHORITY_TYPES as $autType) {
            $type = \authority::aut_const_to_type_const($autType);
            $subQuery = "SELECT entity_id FROM ark_entity_pmb WHERE entity_type = '" . $type . "'";
            $query = "SELECT id_authority FROM authorities WHERE id_authority NOT IN (" . $subQuery . ") AND type_object = '$autType'";
            $result = pmb_mysql_query($query);
            $nb += pmb_mysql_num_rows($result);
        }
        
        foreach (self::OTHER_TYPES as $type) {
            $subQuery = "SELECT entity_id FROM ark_entity_pmb WHERE entity_type = '" . $type . "'";
            $query = "SELECT notice_id as entity_id FROM notices WHERE notice_id NOT IN (" . $subQuery . ") AND notice_id NOT IN (SELECT num_notice FROM bulletins WHERE num_notice > 0)";
            if($type == TYPE_BULLETIN) {
                $query = "SELECT bulletin_id as entity_id FROM bulletins WHERE bulletin_id NOT IN (" . $subQuery . ")";
            }
            $result = pmb_mysql_query($query);
            $nb += pmb_mysql_num_rows($result);
        }
        return $nb;
    }
}
