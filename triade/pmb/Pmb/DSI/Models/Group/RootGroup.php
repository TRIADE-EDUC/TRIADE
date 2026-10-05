<?php

namespace Pmb\DSI\Models\Group;

use Pmb\DSI\Models\Root;

class RootGroup extends Root
{
    public const EMPTY_GROUP_KEY = "notfound";

    public const RESULT_KEY = "values";

    public const IDS_TYPE = [
        \Pmb\DSI\Models\Group\Facets\Entities\RecordFacets\RecordFacets::class => 1,
        \Pmb\DSI\Models\Group\CustomFields\Entities\RecordCustomFields\RecordCustomFields::class => 2
    ];

    /**
     * Contient la liste des entites
     *
     * @var array
     */
    protected $entities = [];

    /**
     * Correspond au type d'entitie
     *
     * @var integer
     */
    protected $entityType;

    /**
     * Contient un sous groupe
     *
     * @var RootGroup
     */
    protected $subGroup;

    /**
     * Contient le parametrage
     *
     * @var object
     */
    protected $settings;

    public function __construct(?object $settings = null)
    {
        $this->settings = $settings ?? new \stdClass();
    }

    /**
     * Permet d'ajouter des entites au groupe
     *
     * @param integer $entityType
     * @param array $items
     * @return void
     */
    public function addItems(int $entityType, array $entities)
    {
        if (!in_array($entityType, $this->getAvailableEntitieTypes())) {
            throw new \InvalidArgumentException("Entity type '$entityType' not compatible");
        }
        $this->entities = $entities;
        $this->entityType = $entityType;
    }

    /**
     * Permet de grouper les items
     *
     * @return array
     */
    public function group()
    {
        $item = $this->entities;

        if (isset($this->subGroup)) {
            $this->subGroup->addItems($this->entityType, $item);
            $item = $this->subGroup->group();
        }

        return [
            RootGroup::EMPTY_GROUP_KEY => [
                RootGroup::RESULT_KEY => $item,
            ]
        ];
    }

    /**
     * Retourne la liste des type d'entites compatibles
     *
     * @return array
     */
    protected function getAvailableEntitieTypes()
    {
        $availableTypes = $this->getAvailableTypes() ?? [];
        return array_map(function ($item) {
            return $item::TYPE;
        }, $availableTypes['item'] ?? []);
    }

    /**
     * Permet de fournir des donnees pour le formulaire
     *
     * @return array
     */
    public function getFormData()
    {
        return [
            "messages" => static::getMessages(),
            "availableTypes" => $this->getAvailableTypes(),
            "availableItems" => $this->getAvailableEntitieTypes(),
        ];
    }

    /**
     * Retourne un parametre en fonction de son nom
     *
     * @param string $settingName
     * @param mixed $default
     * @return mixed
     */
    public function getSetting(string $settingName, $default = null)
    {
        if (empty($this->settings)) {
            return $default;
        }
        return $this->settings->{$settingName} ?? $default;
    }

    /**
     * Permet de définir une sous groupe
     *
     * @param RootGroup $group
     * @return void
     */
    public function setSubGroup(RootGroup $group)
    {
        $this->subGroup = $group;
    }
}