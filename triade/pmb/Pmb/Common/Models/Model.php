<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Model.php,v 1.33 2024/01/25 16:21:34 jparis Exp $

namespace Pmb\Common\Models;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Orm\OrmManyToMany;
use Pmb\Dashboard\Models\WidgetModel;
use Pmb\Dashboard\Orm\WidgetOrm;

class Model
{
    protected static $antiLoopToArray = [];

    /**
     * Correspond a l'identifiant de model et ORM
     *
     * @var integer
     */
    public $id = 0;

    /**
     * Correspond au namespace de l'ORM
     *
     * @var string
     */
    protected $ormName = "";

    /**
     * Permet de savoir si les donnees de l'ORM sont recuperees
     *
     * @var boolean
     */
    protected $datafetch = false;

    /**
     * Contient la liste des proprietes recuperees par l'ORM
     *
     * @var array
     */
    protected $structure = [];

    /**
     * Contient la liste des proprietes a ignorer pour le toArray
     *
     * @var array
     */
    public const IGNORE_PROPS_TOARRAY = [
        "structure",
        "datafetch",
        "ormName",
    ];

    /**
     * Contient la liste des proprietes a ne pas convertir en tableau
     *
     * @var array
     */
    public const IGNORE_CONVERT_PROPS_TOARRAY = [];

    public function __construct(int $id = 0)
    {
        $this->id = $id;
        $this->fetchData();
    }

    protected function fetchData()
    {
        $id = $this->id;

        $class = new $this->ormName();
        if(is_a($class, OrmManyToMany::class) && isset($class::$idsTableName) && !empty($class::$idsTableName)) {
            $id = [];
            foreach ($class::$idsTableName as $value) {
                $id[$value] = $this->{Helper::camelize($value)};
            }
        }

        if (!$this->datafetch && $this->ormName::exist($id)) {
            if(is_array($id)) {
                $orm = $this->ormName::finds($id)[0];
            } else {
                $orm = $this->ormName::findById($id);
            }

            $reflect = new \ReflectionClass($orm);
            $props   = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);

            foreach ($props as $prop) {
                if (in_array($prop->getName(), ['structure'])) {
                    continue;
                }

                if (!$prop->isStatic() && !method_exists($this, Helper::camelize("fetch_".$prop->getName()))) {
                    $this->structure[] = Helper::camelize($prop->getName());
                    $this->{Helper::camelize($prop->getName())} = $orm->{$prop->getName()};
                }
            }

            $this->datafetch = true;
        }
    }

    public static function toArray($data, $default = null)
    {
        if (
            is_a($data, self::class) &&
            !empty(static::$antiLoopToArray[strval($data)])
        ) {
            return strval($data);
        }

        if (is_a($data, self::class)) {
            static::$antiLoopToArray[strval($data)] = true;
        }

        $result = [];
        if (is_a($data, self::class)) {
            $result['__class'] = static::class;
        }

        foreach ($data as $key => $value) {

            if (
                is_a($data, self::class) &&
                in_array($key, static::getIgnorePropsToArray())
            ) {
                continue;
            }

            if (
                is_a($data, self::class) &&
                in_array($key, static::getIgnoreConvertPropsToArray())
            ) {
                $result[$key] = $value;
                continue;
            }

            if (is_object($value) && is_a($value, "\\Pmb\\Common\\Orm\\Orm")) {
                $result[$key] = $value->getInfos();
            } elseif (is_object($value) || is_array($value)) {
                $result[$key] = static::toArray($value, $default);
            } else {
                $result[$key] = is_null($value) ? $default : $value;
            }
        }

        if (is_a($data, self::class)) {
            static::$antiLoopToArray[strval($data)] = false;
        }

        return $result;
    }

    protected static function getRelations(array $relations, $object): array
    {
        $tab_relations = [];
        if (!empty($relations)) {
            foreach ($relations as $property => $relation) {
                $orm = $object->{$property};
                if (!empty($orm)) {
                    if (is_object($orm)) {
                        $tab_relations[$property] = $orm->getInfos();
                        if (is_array($relation) && count($relation)) {
                            $tab_relations[$property] = array_merge($tab_relations[$property], static::getRelations($relation, $orm));
                        }
                    } elseif (is_array($orm)) {
                        for ($i = 0; $i < count($orm); $i++) {
                            $orm[$i] = $orm[$i]->getInfos();
                            if (is_array($relation) && count($relation)) {
                                // $orm[$i] devrait etre un objet dans ce cas :(
                                $tab_relations[$property] = array_merge($tab_relations[$property], static::getRelations($relation, $orm[$i]));
                            }
                        }
                        $tab_relations[$property] = $orm;
                    }
                }
            }
        }
        return $tab_relations;
    }

    public function getCmsStructure(string $prefixVar = "", bool $children = false)
    {
        global $msg;

        $this->generateStructure();
        $cmsStructure = [];

        if ($this->structure && !empty($this->structure)) {
            if (!$children) {
                $cmsStructure[0]['var'] = $msg['cms_module_common_datasource_main_fields'];
                $cmsStructure[0]['children'] = [];
            }

            foreach ($this->structure as $prop) {
                if (isset($this->{$prop}) && (is_object($this->{$prop}) || is_array($this->{$prop}))) {
                    continue;
                }

                $var = addslashes($prop);
                if (!empty($prefixVar)) {
                    $var = addslashes($prefixVar.".".$prop);
                }


                if (!$children) {
                    $length = count($cmsStructure[0]['children']);
                    $cmsStructure[0]['children'][$length]['var'] = $var;
                    $cmsStructure[0]['children'][$length]['desc'] = "";
                } else {
                    $length = count($cmsStructure);
                    $cmsStructure[$length]['var'] = $var;
                    $cmsStructure[$length]['desc'] = "";
                }

                $msgVar = str_replace(".", "_", $var);
                switch (true) {
                    case isset($msg['cms_module_common_datasource_desc_'.$msgVar]):
                        $desc = $msg['cms_module_common_datasource_desc_'.$msgVar];
                        break;

                    case isset($msg[$msgVar]):
                        $desc = $msg[$msgVar];
                        break;

                    default:
                        $desc = addslashes($msgVar);
                        break;
                }

                if (!$children) {
                    $cmsStructure[0]['children'][$length]['desc'] = $desc;
                } else {
                    $cmsStructure[$length]['desc'] = $desc;
                }
            }



            if (!$children) {
                $reflect = new \ReflectionClass($this);
                $methods = $reflect->getMethods();

                foreach ($methods as $method) {
                    if (substr($method->name, 0, 5) == "fetch") {
                        $prop = $this->{$method->name}();

                        if (!empty($prop)) {
                            $key = strtolower(str_replace("fetch", "", $method->name));

                            $baseVar = "";
                            if (!empty($prefixVar)) {
                                $baseVar = $prefixVar.".";
                            }

                            $length = count($cmsStructure[0]['children']);
                            $cmsStructure[0]['children'][$length]['var'] = addslashes($baseVar.$key);
                            $cmsStructure[0]['children'][$length]['desc'] = "";
                            $cmsStructure[0]['children'][$length]['children'] = [];

                            $baseVar .= $key;
                            $class = $prop;

                            if (is_array($prop)) {
                                $baseVar .= '[i]';
                                $class = $prop[array_key_first($prop)];
                            }

                            if (is_object($class) && method_exists($class, "getCmsStructure")) {
                                $cmsStructure[0]['children'][$length]['children'] = $class->getCmsStructure($baseVar, true);
                            }

                            if (empty($cmsStructure[0]['children'][$length]['children'])) {
                                unset($cmsStructure[0]['children'][$length]);
                            }
                        }
                    }
                }
            }

            if (isset($cmsStructure[0]['children'])) {
                // Dojo attend un tableau indexer et non associatif
                $cmsStructure[0]['children'] = array_values($cmsStructure[0]['children']);
            }
        }

        return $cmsStructure;
    }

    public function getCmsData()
    {
        $data = [
            'id' => $this->id,
        ];

        if (!empty($this->structure)) {
            foreach ($this->structure as $prop) {
                $data[addslashes($prop)] = Helper::toCmsData($this->{$prop});
            }
        }

        $reflect = new \ReflectionClass($this);
        $methods = $reflect->getMethods();
        if (!empty($methods)) {
            foreach ($methods as $method) {
                if (substr($method->name, 0, 5) == "fetch") {
                    $prop = $this->{$method->name}();
                    if (!empty($prop)) {
                        $key = strtolower(str_replace("fetch", "", $method->name));
                        if (method_exists($prop, "getCmsData")) {
                            $data[addslashes($key)] = $prop->getCmsData();
                        } else {
                            $data[addslashes($key)] = Helper::toCmsData($prop);
                        }
                    }
                }
            }
        }

        return $data;
    }

   /**
    * Retourne la liste d'elements du modele
    *
    * @param array $fields : tableau de parametres pour la requete
    * @param bool $toArray : indique si le resultat doit etre converti en tableau
    */
    public function getList($fields = null, $toArray = false)
    {
        $list = [];
        if(! empty($fields)) {
            $result = $this->ormName::finds($fields);
        } else {
            $result = $this->ormName::findAll();
        }
        foreach ($result as $element) {
            if (method_exists(static::class, "getInstance")) {
                $list[] = call_user_func_array(
                    [static::class, "getInstance"],
                    [$element->{$this->ormName::$idTableName}]
                );
            } else {
                if($element instanceof OrmManyToMany) {
                    $props = [];
                    foreach($this->ormName::$idsTableName as $prop) {
                        $value = $element->{$prop};
                        if(!empty($value)) {
                            $props[] = $element->{$prop};
                        }
                    }

                    $list[] = new static(...$props);

                } else {
                    $list[] = new static($element->{$this->ormName::$idTableName});
                }
            }
        }
        return $toArray ? Helper::toArray($list) : $list;
    }

    protected function generateStructure()
    {
        if (empty($this->structure)) {
            $reflect = new \ReflectionClass(new $this->ormName());
            $props   = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);

            foreach ($props as $prop) {
                if (in_array($prop->getName(), ['structure'])) {
                    continue;
                }

                if (!$prop->isStatic() && !method_exists($this, Helper::camelize("fetch_".$prop->getName()))) {
                    $this->structure[] = Helper::camelize($prop->getName());
                }
            }
        }
        return $this->structure;
    }

    /**
     * Retourne la liste des proprietes a ignorer pour le toArray
     *
     * @return array
     */
    protected static function getIgnorePropsToArray()
    {
        return static::IGNORE_PROPS_TOARRAY;
    }

    /**
     * Retourne la liste des proprietes a ne pas convertir en tableau pour le toArray
     *
     * @return array
     */
    protected static function getIgnoreConvertPropsToArray()
    {
        return static::IGNORE_CONVERT_PROPS_TOARRAY;
    }

    /**
     * Retourne une chaine contenant le namespace suivi de son identifiant
     *
     * @return string
     */
    public function __toString()
    {
        return static::class . "_" . $this->id;
    }
}
