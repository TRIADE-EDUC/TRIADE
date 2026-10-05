<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Orm.php,v 1.45.2.1.2.2 2025/06/04 12:39:32 qvarin Exp $

namespace Pmb\Common\Orm;

use Pmb\Common\Helper\Helper;

/**
 *
 * @author arenou
 *
 */
abstract class Orm
{
    // Declaration des proprietes obligatoires
    /**
     *
     * @var string
     */
    public static $tableName = "";

    /**
     *
     * @var string
     */
    public static $idTableName = "";

    /**
     * Clé primaire supplementaire
     *
     * @var array
     */
    public static $primaryKeyAdditional = [];

    /**
     * Cle primaire non supprimable
     *
     * @var array
     */
    public static $primaryKeyNotDeletable = [];

    protected $structure = [];

    protected static $relations = [];

    public static $instances = array();

    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = [];

    protected const DEFAULT_OPERATOR = "=";

    protected const ONE_TO_MANY = "1n";

    protected const MANY_TO_ONE = "n1";

    protected const MANY_TO_ZERO = "n0";

    protected const ZERO_TO_MANY = "0n";

    protected const MANY_TO_MANY = "nn";

    public function __construct(int $id = 0)
    {
        $this->initDataStructure();
        $this->initRelationsDefinition();
        if ($id > 0) {
            $this->fetchData($id);
        }
    }

    /**
     *
     * @param int $id
     * @throws \Exception
     * @return \Pmb\Common\Orm\Orm
     */
    protected function fetchData(int $id)
    {
        $query = 'select * from ' . static::$tableName . ' where ' . static::$idTableName . ' = ' . $id;
        $result = pmb_mysql_query($query);
        if (!pmb_mysql_num_rows($result)) {
            throw new \Exception("No record found");
        }
        $row = pmb_mysql_fetch_assoc($result);
        return $this->feedObject($row);
    }

    /**
     *
     * @param array $data
     * @return \Pmb\Common\Orm\Orm
     */
    public function feedObject(array $data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $this->formatValue($key, $value);
        }
        return $this;
    }

    private function formatValue($prop, $value)
    {
        switch (gettype($this->{$prop})) {
            case 'boolean':
                return boolval($value);
            case 'integer':
                return intval($value);
            case 'double':
            case 'float':
                return floatval($value);
        }
        return $value;
    }

    public function save()
    {
        $query = "replace into " . static::$tableName . " (" . implode(',', array_keys($this->structure)) . ") values ('";
        $values = [];
        foreach ($this->structure as $key => $val) {
            // TODO Addslashes plus fin...
            $values[] = addslashes($this->{$key} ?? '');
        }
        $query .= implode("','", $values) . "')";
        pmb_mysql_query($query);
        $this->{static::$idTableName} = pmb_mysql_insert_id();
    }

    public function delete()
    {
        if (in_array($this->{static::$idTableName}, static::$primaryKeyNotDeletable, true)) {
            throw new \Exception("Unable to delete !");
        }

        if (!$this->checkBeforeDelete()) {
            throw new \Exception("Unable to delete !");
        }

        $query = "delete from " . static::$tableName . " where " . static::$idTableName . " = " . $this->{static::$idTableName};
        pmb_mysql_query($query);
        $defaultProperties = static::$reflectionClass[static::class]->getDefaultProperties();
        foreach ($this->structure as $key => $val) {
            $this->{$key} = $defaultProperties[$key];
        }
    }

    public static function deleteWhere($field, $value)
    {
        $query = "delete from " . static::$tableName . " where " . $field . " = " . $value;
        pmb_mysql_query($query);
    }

    /**
     * Met a jour les donnees de l'objet selon l'Id defini
     *
     * @param int $id
     * @return \Pmb\Common\Orm\Orm
     */
    public function setId(int $id)
    {
        return $this->fetchData($id);
    }

    public function __set($label, $value)
    {
        if (static::$reflectionClass[static::class]->hasMethod(Helper::camelize("set " . $label))) {
            return $this->{Helper::camelize("set " . $label)}($value);
        }
        if (static::$reflectionClass[static::class]->hasProperty($label)) {
            $this->{$label} = $value;
            return $this;
        }
        throw new \Exception("Unknown property");
    }

    public function __get($label)
    {
        if (static::$reflectionClass[static::class]->hasMethod(Helper::camelize("get " . $label))) {
            return $this->{Helper::camelize("get " . $label)}();
        }
        if (isset(static::$relations[static::class]) && in_array($label, array_keys(static::$relations[static::class]), true)) {
            return $this->getRelated($label);
        }
        if (static::$reflectionClass[static::class]->hasProperty($label)) {
            return $this->{$label};
        }
        throw new \Exception("Unknown property");
    }

    protected function initDataStructure()
    {
        if (!isset(static::$reflectionClass[static::class])) {
            static::$reflectionClass[static::class] = new \ReflectionClass($this);
        }
        $query = "show columns from " . static::$tableName;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $this->structure[$row->Field] = $row;

                // On verifie que la propriete existe sur l'ORM
                $rowField = $row->Field;
                if (!empty(static::$tablePrefix)) {
                    $rowField = str_replace(static::$tablePrefix . '_custom_', '', $row->Field);
                }

                if (false === static::$reflectionClass[static::class]->hasProperty($rowField)) {
                    throw new \Exception("$rowField is missing");
                }

                // On verifie l'existence de la cle primaire
                if ('PRI' === $row->Key && (static::$idTableName !== $row->Field && !in_array($row->Field, static::$primaryKeyAdditional, true))) {
                    throw new \Exception("Wrong primary key");
                }
            }
        }
        return $this->structure;
    }

    private function initRelationsDefinition()
    {
        if (!empty(static::$relations[static::class])) {
            return static::$relations[static::class];
        }

        static::$relations[static::class] = [];

        // On reconstruit la structure decrivant les relations
        foreach (static::$reflectionClass[static::class]->getProperties() as $property) {
            $comment = $property->getDocComment();
            $matches = [];
            if (false !== $comment) {
                if (preg_match_all('/@(Relation|RelatedKey|ForeignKey|TableLink|Table|Orm)\s([^\s]+)/', $comment, $matches)) {
                    static::$relations[static::class][$property->getName()] = [];
                    for ($i = 0; $i < count($matches[0]); $i++) {
                        static::$relations[static::class][$property->getName()][$matches[1][$i]] = $matches[2][$i];
                    }
                }
            }
        }

        $this->checkRelationsDefinition();
        return static::$relations[static::class];
    }

    private function checkRelationsDefinition()
    {
        if (empty(static::$relations[static::class])) {
            return false;
        }

        foreach (static::$relations[static::class] as $definition) {
            if (empty($definition['Relation'])) {
                throw new \Exception("Relation required");
            }
            if (empty($definition['Orm'])) {
                throw new \Exception("Related ORM required");
            }
            switch ($definition['Relation']) {
                case static::ONE_TO_MANY:
                case static::ZERO_TO_MANY:
                    if (empty($definition['RelatedKey'])) {
                        throw new \Exception("RelatedKey required");
                    }
                    break;
                case static::MANY_TO_ONE:
                case static::MANY_TO_ZERO:
                    if (empty($definition['Table'])) {
                        throw new \Exception("Table required");
                    }
                    if (empty($definition['ForeignKey'])) {
                        throw new \Exception("ForeignKey required");
                    }
                    if (empty($definition['RelatedKey'])) {
                        throw new \Exception("RelatedKey required");
                    }
                    break;
                case static::MANY_TO_MANY:
                    if (empty($definition['RelatedKey'])) {
                        throw new \Exception("RelatedKey required");
                    }
                    if (empty($definition['ForeignKey'])) {
                        throw new \Exception("ForeignKey required");
                    }
                    if (empty($definition['TableLink'])) {
                        throw new \Exception("TableLink required");
                    }
                    break;
            }
        }
    }

    /**
     *
     * @param string $label
     * @return string
     */
    protected function getRelated(string $label)
    {
        // Si la property n'est pas a null, on y est deja passe, donc on evite le recalcul
        if ($this->{$label} !== null) {
            return $this->{$label};
        }
        $relation = static::$relations[static::class][$label];
        $result = pmb_mysql_query($this->getRelatedQuery($relation));
        $this->{$label} = false;
        if (static::ONE_TO_MANY !== $relation["Relation"]) {
            $this->{$label} = [];
        }
        if (pmb_mysql_num_rows($result)) {
            $ormClass = $relation['Orm'];
            while ($row = pmb_mysql_fetch_assoc($result)) {
                if (!empty($row['related_id'])) {
                    if ($relation['Relation'] == static::MANY_TO_MANY && is_subclass_of($ormClass, OrmManyToMany::class)) {
                        $obj = new $ormClass([
                            $relation['ForeignKey'] => $row['related_id'],
                            $relation['RelatedKey'] => $this->{static::$idTableName}
                        ]);
                    } else {
                        $obj = OrmCollection::getInstance($ormClass, $row['related_id']);
                    }
                    if (is_array($this->{$label})) {
                        $this->{$label}[] = $obj;
                    } else {
                        $this->{$label} = $obj;
                    }
                }
            }
        }
        return $this->{$label};
    }

    /**
     *
     * @param array $relation
     * @throws \Exception
     * @return string
     */
    private function getRelatedQuery($relation)
    {
        switch ($relation['Relation']) {
            case static::MANY_TO_ONE:
            case static::MANY_TO_ZERO:
                return "select {$relation['RelatedKey']} as related_id from {$relation['Table']} where  {$relation['ForeignKey']} = {$this->{static::$idTableName}}";
            case static::ONE_TO_MANY:
            case static::ZERO_TO_MANY:
                return "select {$relation['RelatedKey']} as related_id from " . static::$tableName . " where " . static::$idTableName . " = {$this->{static::$idTableName}}";
            case static::MANY_TO_MANY:
                return "select {$relation['ForeignKey']} as related_id from {$relation['TableLink']} join " . static::$tableName . " on {$relation['RelatedKey']} = " . static::$idTableName . " where " . static::$idTableName . " = {$this->{static::$idTableName}}";
            default:
                throw new \Exception("Unknown relation");
        }
    }

    /**
     *
     * @param int $id
     * @param boolean $fetchFlag
     * @return object|boolean
     */
    public static function findById(int $id)
    {
        try {
            $className = static::class;
            return new $className($id);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     *
     * @return array
     */
    public static function findAll($orderby = "")
    {
        $query = "SELECT * FROM " . static::$tableName . ($orderby ? " ORDER BY $orderby" : '');
        $result = pmb_mysql_query($query);
        $instances = array();
        if (pmb_mysql_num_rows($result)) {
            $className = static::class;
            foreach ($result as $row) {
                $instance = $className::getInstance(intval($row[static::$idTableName]));
                $instances[] = $instance;
            }

            pmb_mysql_free_result($result);
        }
        return $instances;
    }

    /**
     * Fetch list
     *
     * @param integer $start
     * @param integer $max (optional, default 20)
     * @param string $orderby (optional)
     * @return static[]
     */
    public static function fetchList(int $start, int $max = 20, $orderby = "")
    {
        $query = "SELECT * FROM " . static::$tableName;
        $query .= ($orderby ? " ORDER BY $orderby" : '');
        $query .= " LIMIT $start, $max";

        $result = pmb_mysql_query($query);
        $instances = array();
        if (pmb_mysql_num_rows($result)) {
            $className = static::class;
            foreach ($result as $row) {
                $instance = $className::getInstance(intval($row[static::$idTableName]));
                $instances[] = $instance;
            }

            pmb_mysql_free_result($result);
        }
        return $instances;
    }

    /**
     * Pagination
     *
     * @param integer $page
     * @param integer $max (optional, default 20)
     * @param string $orderby (optional)
     * @return array
     */
    public static function pagination(int $page, int $max = 20, $orderby = "")
    {
        $query = "SELECT count(*) as total FROM " . static::$tableName;
        $result = pmb_mysql_query($query);

        $pagination = [
            'currentPage' => $page + 1,
            'total' => 0,
            'nbPerPage' => $max,
            'instances' => []
        ];

        if (pmb_mysql_num_rows($result)) {
            $pagination['total'] = intval(pmb_mysql_result($result, 0));
            $maxPage = ceil($pagination['total'] / $max);
            if ($page > $maxPage) {
                $page = $maxPage;
            }

            $page = $page * $max;
            $pagination['instances'] = static::fetchList($page, $max, $orderby);
        }

        return $pagination;
    }

    /**
     * Pagination Where
     *
     * @param integer $page
     * @param integer $max (optional, default 20)
     * @param array $fieldsValues (optional)
     * @param string $orderby (optional)
     * @return array
     */
    public static function paginationWhere(int $page, int $max = 20, array $fieldsValues = [], string $orderby = "")
    {
        $query = "SELECT count(*) as total FROM " . static::$tableName;
        $query .= static::computeClause($fieldsValues, "AND");
        $result = pmb_mysql_query($query);

        $pagination = [
            'currentPage' => $page + 1,
            'total' => 0,
            'nbPerPage' => $max,
            'instances' => []
        ];

        if (pmb_mysql_num_rows($result)) {
            $pagination['total'] = intval(pmb_mysql_result($result, 0));
            $maxPage = ceil($pagination['total'] / $max);
            if ($page > $maxPage) {
                $page = $maxPage;
            }

            $page = $page * $max;
            $pagination['instances'] = static::finds($fieldsValues, $orderby, "AND", "$page, $max");
        }

        return $pagination;
    }

    /**
     * Effectuer une recherche sur une colonne
     *
     * @return array
     */
    public static function find($field, $value, $orderby = "")
    {
        $query = "SELECT * FROM " . static::$tableName . " WHERE $field = '" . addslashes($value) . "'" . ($orderby ? " ORDER BY $orderby" : '');
        $result = pmb_mysql_query($query);
        $instances = array();
        if (pmb_mysql_num_rows($result)) {
            $className = static::class;
            foreach ($result as $row) {
                $instances[] = new $className(intval($row[static::$idTableName]));
            }

            pmb_mysql_free_result($result);
        }
        return $instances;
    }

    /**
     * Effectuer une recherche sur plusieurs colonnes (Inter : AND)
     *
     * @return array
     */
    public static function finds(array $fieldsValues, $orderby = "", $inter = "AND", $limit = "")
    {
        $query = "SELECT * FROM " . static::$tableName;
        $query .= static::computeClause($fieldsValues, $inter);
        $query .= ($orderby ? " ORDER BY {$orderby}" : '');
        $query .= ($limit ? " LIMIT {$limit}" : '');

        $result = pmb_mysql_query($query);
        $instances = array();
        if (pmb_mysql_num_rows($result)) {
            $className = static::class;
            foreach ($result as $row) {
                $instances[] = $className::getInstance(intval($row[static::$idTableName]));
            }
            pmb_mysql_free_result($result);
        }
        return $instances;
    }

    public function toArray()
    {
        $object = array();
        if (!isset(static::$reflectionClass[static::class])) {
            static::$reflectionClass[static::class] = new \ReflectionClass($this);
        }
        foreach (static::$reflectionClass[static::class]->getProperties() as $property) {
            if (!$property->isStatic()) {
                if (is_array($this->{$property->name})) {
                    foreach ($this->{$property->name} as $property_array) {
                        if (is_object($property_array) && is_a($property_array, "\\Pmb\\Common\\Orm\\Orm")) {
                            $object[$property->name][] = $property_array->toArray();
                        } else {
                            $object[$property->name][] = $property_array;
                        }
                    }
                } elseif (is_object($this->{$property->name}) && is_a($this->{$property->name}, "\\Pmb\\Common\\Orm\\Orm")) {
                    $object[$property->name] = $this->{$property->name}->toArray();
                } else {
                    $object[$property->name] = $this->{$property->name};
                }
            }
        }
        return $object;
    }

    public function getInfos()
    {
        $infos = array();
        if (!isset(static::$reflectionClass[static::class])) {
            static::$reflectionClass[static::class] = new \ReflectionClass($this);
        }
        foreach (static::$reflectionClass[static::class]->getProperties() as $property) {
            if (!$property->isStatic()) {
                if (!is_a($this->{$property->name}, "\\Pmb\\Common\\Orm\\Orm")) {
                    $infos[$property->name] = $this->{$property->name};
                }
            }
        }
        return $infos;
    }

    public function getCmsStructure(string $prefixVar = "", bool $children = false)
    {
        global $msg;

        $cmsStructure = array();
        if (!$children) {
            $cmsStructure[0]['var'] = $msg['cms_module_common_datasource_main_fields'];
            $cmsStructure[0]['children'] = array();
        }

        foreach ($this->structure as $key => $val) {

            $var = addslashes($key);
            $msgVar = addslashes($key);
            if (!empty($prefixVar)) {
                $var = addslashes($prefixVar . "." . $key);
                $msgVar = addslashes($prefixVar . "_" . $key);
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

            switch (true) {
                case isset($msg['cms_module_common_datasource_desc_' . $msgVar]):
                    $desc = $msg['cms_module_common_datasource_desc_' . $msgVar];
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

        if (!empty(static::$relations[static::class]) && !$children) {
            foreach (static::$relations[static::class] as $key => $relation) {
                $length = count($cmsStructure[0]['children']);
                $cmsStructure[0]['children'][$length]['var'] = addslashes($key);
                $cmsStructure[0]['children'][$length]['desc'] = "";
                $cmsStructure[0]['children'][$length]['children'] = array();
                if (!empty($relation['Orm'])) {
                    $baseVar = $key;
                    if ($relation['Relation'] == static::MANY_TO_MANY) {
                        $baseVar .= '[i]';
                    }
                    $relation_orm = new $relation['Orm']();
                    $cmsStructure[0]['children'][$length]['children'] = $relation_orm->getCmsStructure($baseVar, true);
                }
            }
        }

        return $cmsStructure;
    }

    public function getCmsData()
    {
        $data = array();

        foreach ($this->structure as $key => $val) {
            $data[addslashes($key)] = $this->{$key};
        }

        if (!empty(static::$relations[static::class])) {
            foreach (static::$relations[static::class] as $key => $relation) {
                $data[addslashes($key)] = array();

                $relations = $this->getRelated($key);
                if (!empty($relations)) {
                    if ($relation['Relation'] == static::MANY_TO_MANY) {
                        $data[addslashes($key)] = $relations;
                    } else {
                        $data[addslashes($key)] = $relations[0];
                    }
                }
            }
        }

        return $data;
    }

    /**
     *
     * @param int $id
     * @return boolean
     */
    public static function exist(int $id)
    {
        $id = intval($id);

        if($id) {
            $query = "SELECT 1 FROM " . static::$tableName . " WHERE " . static::$idTableName . " = $id";
            $result = pmb_mysql_query($query);
            return pmb_mysql_num_rows($result) == 1;
        }

        return false;
    }

    protected function checkBeforeDelete()
    {
        return true;
    }

    public static function getInstance($id = 0)
    {
        $id = intval($id);
        static::$instances[static::class] = static::$instances[static::class] ?? [];

        if (isset(static::$instances[static::class][$id])) {
            $instance = static::$instances[static::class][$id];
        } else {
            $instance =  new static($id);
            static::$instances[static::class][$id] = $instance;
        }

        return $instance;
    }

    /**
     * La fonction "unsetStructure" définit la valeur de la propriété "structure" sur null.
     */
    public function unsetStructure()
    {
        $this->structure = null;
    }

    /**
     * computeClause
     *
     * @param array $fieldsValues
     * @param string $inter
     * @return string
     */
    protected static function computeClause(array $fieldsValues, string $inter)
    {
        $clause = "";
        foreach ($fieldsValues as $field => $data) {
            $field = trim($field);

            $operator = self::DEFAULT_OPERATOR;
            if (is_string($data) || is_numeric($data)) {
                $value = $data;
            } elseif (is_array($data)) {

                // Gestion des tableaux : associatif pour tester un champ
                // Sequentiel pour tester plusieurs fois le champ
                if (isset($data[0])) {
                    foreach ($data as $subData) {
                        if (isset($subData['value'], $subData['operator'])) {
                            $operator = trim($subData['operator']) ?? self::DEFAULT_OPERATOR;
                            $value = $subData['value'] ?? null;
                            if (!empty($clause)) {
                                $clause .= $subData["inter"] ?? "OR";
                            }

                            $format = " %s %s '%s'";
                            if (in_array(strtolower($operator), [
                                "in",
                                "not in"
                            ], true)) {
                                $format = " %s %s (%s)";
                                $value = is_string($value) ? $value : implode(",", $value);
                            }
                            $clause .= sprintf($format, $field, $operator, addslashes($value));
                        }
                    }
                    continue;
                } elseif (isset($data['value'], $data['operator'])) {
                    $operator = trim($data['operator']) ?? self::DEFAULT_OPERATOR;
                    $value = $data['value'] ?? null;
                }
            } else {
                continue;
            }

            if (!empty($clause)) {
                $clause .= " {$inter} ";
            }

            $format = " %s %s '%s'";
            if (in_array(strtolower($operator), [
                "in",
                "not in"
            ], true)) {
                $format = " %s %s (%s)";
                $value = is_string($value) ? $value : implode(",", $value);
            }
            $clause .= sprintf($format, $field, $operator, addslashes($value));
        }

        if (!empty($clause)) {
            $clause = ' WHERE ' . $clause;
        }

        return $clause;
    }
}
