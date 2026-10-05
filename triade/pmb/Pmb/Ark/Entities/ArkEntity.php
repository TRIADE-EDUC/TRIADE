<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ArkEntity.php,v 1.4 2022/09/02 13:22:44 rtigero Exp $
namespace Pmb\Ark\Entities;

use Pmb\Common\Helper\Helper;
use Pmb\Ark\Ark;

class ArkEntity
{

    /**
     *
     * @var array
     */
    protected $metadata;

    /**
     *
     * @var int
     */
    protected $id;

    /**
     *
     * @var int
     */
    protected $entityId;

    /**
     *
     * @var int
     */
    protected $arkId;
    
    /**
     *
     * @var string
     */
    protected $qualifiers;

    /**
     *
     * @param int $entityId
     */
    public function __construct(int $entityId)
    {
        $this->entityId = $entityId;
        $this->fetchData();
    }

    /**
     *
     * @return string
     */
    public function getOpacUrl()
    {
        return "";
    }

    /**
     *
     * @return string
     */
    protected function fetchData()
    {}

    /**
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     *
     * @param string $arkIdentifier
     * @return boolean
     */
    public function save()
    {}

    /**
     *
     * @return number
     */
    public function getArkId()
    {}

    /**
     *
     * @param int $arkId
     */
    public function setArkId($arkId)
    {
        $this->arkId = intval($arkId);
    }

    /**
     */
    protected function updateMetadata()
    {}

    /**
     *
     * @return bool
     */
    protected function register()
    {
        $query = "
            UPDATE ark
            SET metadata = '" . addslashes(\encoding_normalize::json_encode($this->metadata)) . "', entity_type = '" . $this::ENTITY_TYPE . "'
            WHERE id = '$this->arkId'";
        $result = pmb_mysql_query($query);
        return $result;
    }
    
    /**
     *
     * @param string $qualifiers
     */
    public function setQualifiers(string $qualifiers) {
        $this->qualifiers = $qualifiers;
    }
    
    /**
     *
     * @return string $qualifiers
     */
    public function getQualifiers() {
        return $this->qualifiers;
    }
    
    
    protected function generateQualifiedURL()
    {
        $tabQualifiers = explode("/", $this->qualifiers);
        if (empty($tabQualifiers)) {
            return "";
        }
        $method = "";
        if ($tabQualifiers[0]) {
            $method = $tabQualifiers[0];
        }
        array_shift($tabQualifiers);
        return $this->{$method}($tabQualifiers);
    }
    
    public function __call($name, $arguments)
    {
        $method = "";
        if (method_exists($this, $name)) {
            $method = $name;
        } else if (method_exists($this, Helper::camelize($name))) {
            $method = Helper::camelize($name);
        } else if (method_exists($this, Helper::camelize("get_" . $name))) {
            $method = Helper::camelize("get_" . $name);
        }
        if ($method) {
            return call_user_func_array([$this, $method], $arguments);
        }
        return "";
    }
    
    /**
     *
     * @return bool
     */
    public function markAsDeleted()
    {
        if (isset($this->metadata['replaced'])) {
            return;
        }
        if (empty($this->arkId)) {
            $this->createArk($this);
        }
        $result = $this->deleteEntity();
        if ($result) {
            $this->metadata['deleted'] = date("Y-m-d H:i:s");
            return $this->register();
        }
        return false;
    }
    
    /**
     *
     * @return bool
     */
    public function markAsReplaced(ArkEntity $replacedBy)
    {
        if (empty($this->arkId)) {
            $this->createArk($this);
        }
        if (empty($replacedBy->getArkId())) {
            $this->createArk($replacedBy);
        }
        $ark = new Ark();
        $ark->setArkEntity($replacedBy);
        //si le $replacedBy n'a pas d'identifiant ARK
        $result = $this->deleteEntity();
        if ($result) {
            $this->metadata['replaced']['date'] = date("Y-m-d H:i:s");
            $this->metadata['replaced']['replaced_by'] = $ark->getArkIdentifier();
            return $this->register();
        }
        return false;
    }
    
    /**
     * creation du numero ark si besoin
     * @param ArkEntity $arkEntity
     */
    protected function createArk(ArkEntity &$arkEntity) {
        $ark = new Ark();
        $ark->setArkEntity($arkEntity);
        $ark->getArkIdentifier();
        $ark->save();
    }
    
    /**
     * fonction pour l'insertion en masse des numeros ark, a deriver
     * @param array $arks
     */
    public static function massArkInsert(array $arks)
    {
        //
    }
    
    /**
     * fonction pour recuperer les entites sans numero ark, a deriver
     */
    public static function getEntitiesWithoutArk(int $lot = 0)
    {
        
    }
}