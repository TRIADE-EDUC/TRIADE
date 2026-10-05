<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PortalRootModel.php,v 1.21.2.1 2025/01/08 09:51:43 jparis Exp $

namespace Pmb\CMS\Models;

use Pmb\Common\Helper\Helper;

class PortalRootModel
{
    public $id = 0;

    public $portal = null;

    public $serialised = false;

    public static $nbInstance = 0;
    
    public $pageId;

    public $versionNum;

    public $structure;

    /**
     * Contient la liste des proprietes a ignorer pour le serialize
     *
     * @var array
     */
    public const IGNORE_PROPS_SERIALISE = [
        "portal"
    ];

    /**
     *
     * @var PortalRootModel[]
     */
    public static $instances = [];

    public function __construct(PortalModel $portal, array $data = [])
    {
        $this->portal = $portal;
        if (! empty($data)) {
            $this->unserialize($data, $portal);
        } else {
            static::$nbInstance ++;
            $this->id = static::$nbInstance;
            $this->init();
        }
        static::$instances[$this->id] = $this;
    }

    /**
     *
     * @param int $id
     * @throws \Exception
     * @return mixed
     */
    public static function getInstance(int $id)
    {
        if (empty(static::$instances[$id])) {
            throw new \Exception("unknown instance");
        }
        return static::$instances[$id];
    }

    /**
     *
     * @param int $id
     * @return mixed
     */
    public static function exist(int $id)
    {
        return ! empty(static::$instances[$id]);
    }

    /**
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function serialize()
    {
        $serialize = [ 
            'class' => static::class,
        ];

        foreach ($this as $prop => $value) {


            if (in_array($prop, static::getIgnorePropsSerialise())) {
                continue;
            }

            if (method_exists($this, Helper::camelize("get_" . $prop))) {
                $value = $this->{Helper::camelize("get_" . $prop)}();
            }

            if ($value instanceof PortalRootModel) {
                if ($value->serialised) {
                    // On évite de dupliquer les informations dans le json
                    $value = [
                        "class" => get_class($value),
                        "id" => $value->getId(),
                    ];
                } else {
                    $value = $value->serialize();
                }
            } elseif (is_array($value)) {
                $value = $this->arraySerialize($value);
            }

            $serialize[Helper::camelize_to_snake($prop)] = $value;
        }

        $this->serialised = true;

        return $serialize;
    }

    /**
     *
     * @param array $data
     */
    protected function unserialize(array $data, PortalModel $portal)
    {
        foreach ($data as $prop => $value) {
            if ($prop == "class") {
                continue;
            }

            if ($prop == "id" && $value > static::$nbInstance) {
                static::$nbInstance = $value;
            }

            if (! empty($value) && is_array($value)) {
                if (! empty($value['class'])) {
                    $value = $value['class']::makeInstance($value, $portal);
                } else {
                    $value = $this->arrayUnserialize($value, $portal);
                }
            }

            if (method_exists($this, Helper::camelize("set_" . $prop))) {
                $this->{Helper::camelize("set_" . $prop)}($value);
            } else {
                $this->{Helper::camelize($prop)} = $value;
            }
        }

        if ($this->id == 0) {
            static::$nbInstance ++;
            $this->id = static::$nbInstance;
            $this->init();
        }
    }

    /**
     *
     * @param array $instanceData
     * @return mixed
     */
    private static function makeInstance(array $instanceData, PortalModel $portal)
    {
        $instance = null;
        if (! empty($instanceData['class']) && class_exists($instanceData['class'])) {
            if (! empty($instanceData['id']) && ! empty(static::$instances[intval($instanceData['id'])])) {
                $instance = $instanceData['class']::getInstance(intval($instanceData['id']));
            } else {
                $instance = new $instanceData['class']($portal, $instanceData);
            }
        } else {
            $instance = $instanceData;
        }
        return $instance;
    }

    public function __get(string $prop)
    {
        if (method_exists($this, Helper::camelize("get_" . $prop))) {
            return $this->{Helper::camelize("get_" . $prop)}();
        }
        return property_exists($this, $prop) ? $this->{$prop} : null;
    }

    public function __set(string $prop, $value)
    {
        if (method_exists($this, Helper::camelize("set_" . $prop))) {
            return $this->{Helper::camelize("set_" . $prop)}($value);
        } else {
            $this->{$prop} = $value;
        }
    }

    public function __clone()
    {
        static::$nbInstance ++;
        $this->id = static::$nbInstance;

        foreach ($this as $prop => $element) {
            if ($prop == "portal") {
                continue;
            }
            if ($element instanceof PortalRootModel) {
                $this->{$prop} = clone $element;
            } elseif (is_array($element)) {
                foreach ($element as $index => $instance) {
                    if ($instance instanceof PortalRootModel) {
                        $element[$index] = clone $instance;
                    }
                }
                $this->{$prop} = $element;
            }
        }
    }

    /**
     * À dériver
     * Méthode appelée lors d'une nouvelle instance
     */
    public function init()
    {
    }

    /**
     *
     * @param array $array
     * @return \Pmb\CMS\Models\PortalRootModel[]|array[]
     */
    protected function arraySerialize(array $array)
    {
        $valueSerialize = [];
        foreach ($array as $index => $element) {
            if ($element instanceof PortalRootModel) {
                $valueSerialize[$index] = $element->serialize();
            } else {
                if (is_array($element)) {
                    $valueSerialize[$index] = $this->arraySerialize($element);
                } else {
                    $valueSerialize[$index] = $element;
                }
            }
        }
        return $valueSerialize;
    }

    /**
     *
     * @param array $array
     * @return \Pmb\CMS\Models\PortalRootModel[]|array[]
     */
    protected function arrayUnserialize(array $array, PortalModel $portal)
    {
        $arrayUnserialize = [];
        foreach ($array as $index => $element) {
            if (! empty($element['class'])) {
                $arrayUnserialize[$index] = $element['class']::makeInstance($element, $portal);
            } else {
                if (is_array($element)) {
                    $arrayUnserialize[$index] = $this->arrayUnserialize($element, $portal);
                } else {
                    $arrayUnserialize[$index] = $element;
                }
            }
        }
        return $arrayUnserialize;
    }

    /**
     * Retourne la liste des proprietes a ignorer pour le serialise
     *
     * @return array
     */
    protected static function getIgnorePropsSerialise()
    {
        return static::IGNORE_PROPS_SERIALISE;
    }
}
