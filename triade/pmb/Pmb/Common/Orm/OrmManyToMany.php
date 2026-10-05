<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: OrmManyToMany.php,v 1.13.2.2 2024/10/07 15:26:53 tsamson Exp $
namespace Pmb\Common\Orm;

abstract class OrmManyToMany extends Orm
{

    protected $ids = array();

    public static $instances = array();

    /**
     * Respecter le format de tableau : [ nom_du_champ => id ]
     *
     * @param array $ids
     */
    public function __construct(array $ids = array())
    {
        $this->ids = $ids;
        $this->initDataStructure();
        if (! empty($ids)) {
            $this->fetchData();
        }
    }

    /**
     *
     * @param int $id
     * @throws \Exception
     * @return \Pmb\Common\Orm\Orm
     */
    protected function fetchData(int $id = 0)
    {
        $query = 'SELECT * FROM ' . static::$tableName . ' WHERE ';
        $i = 0;
        foreach ($this->ids as $field => $value) {
            if ($i) {
                $query .= " AND ";
            }
            $query .= "$field = $value";
            $i ++;
        }
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_assoc($result);
            return $this->feedObject($row);
        }
    }

    /**
     *
     * @param array $ids
     * @return boolean
     */
    public static function exist($ids = [])
    {
        if (! is_array($ids)) {
            return false;
        }
        $query = "SELECT 1 FROM " . static::$tableName . " WHERE ";
        $i = 0;
        foreach ($ids as $field => $value) {
            if ($i) {
                $query .= " AND ";
            }
            $query .= "$field = $value";
            $i ++;
        }
        $result = pmb_mysql_query($query);
        return pmb_mysql_num_rows($result) == 1;
    }

    public function saveRelations()
    {
        foreach (static::$relations[static::class] as $name => $relation) {
            switch ($relation['Relation']) {
                case static::MANY_TO_MANY:
                    foreach ($this->$name as $relatedObject) {
                        $query = "REPLACE INTO {$relation['TableLink']} ({$relation['ForeignKey']}, {$relation['RelatedKey']}) VALUES ('{$this->{static::$idTableName}}', '{$relatedObject->{Helper::camelize($relation['Orm']::$idTableName)}}')";
                        @pmb_mysql_query($query);
                    }
                    break;
                default:
                    break;
            }
        }
    }

    public function save()
    {
        $query = "REPLACE INTO " . static::$tableName . " (" . implode(',', array_keys($this->structure)) . ") VALUES ('";
        $values = [];
        foreach ($this->structure as $key => $val) {
            // TODO Addslashes plus fin...
            $values[] = addslashes($this->{$key});
        }
        $query .= implode("','", $values) . "')";

        pmb_mysql_query($query);
    }

    public function delete()
    {
        if (! is_array($this->ids) && ! count($this->ids)) {
            throw new \Exception("Unable to delete !");
        }

        if (! $this->checkBeforeDelete()) {
            throw new \Exception("Unable to delete !");
        }

        $query = "DELETE FROM " . static::$tableName . " WHERE ";

        $i = 0;
        foreach ($this->ids as $field => $value) {
            if ($i) {
                $query .= " AND ";
            }
            $query .= "$field = $value";
            $i ++;
        }
        pmb_mysql_query($query);
        $defaultProperties = static::$reflectionClass[static::class]->getDefaultProperties();
        foreach ($this->structure as $key => $val) {
            $this->{$key} = $defaultProperties[$key];
        }
    }

    public static function find($field, $value, $orderby = "")
    {
        $query = "SELECT * FROM " . static::$tableName . " WHERE $field = '" . addslashes($value) . "'" . ($orderby ? " ORDER BY $orderby" : '');
        $result = pmb_mysql_query($query);
        $instances = array();
        if (pmb_mysql_num_rows($result)) {
            $className = static::class;
            foreach ($result as $row) {
                $ids = $row;

                if(isset($className::$idsTableName) && !empty($className::$idsTableName)) {
                    foreach($ids as $prop => $value) {
                        if(!in_array($prop, $className::$idsTableName)) {
                            unset($ids[$prop]);
                        }
                    }
                }

                $instances[] = new $className($ids);
            }
        }
        return $instances;
    }

    /**
     * Effectuer une recherche sur plusieurs colonnes (Inter : AND)
     *
     * @param array $fieldsValues
     * @param string $orderby
     * @param string $inter
     * @param string $limit
     *
     * @return array
     */
    public static function finds(array $fieldsValues, $orderby = "", $inter = "AND", $limit = "")
    {
        $query = "SELECT * FROM " . static::$tableName . " WHERE ";
        $clause = "";
        foreach ($fieldsValues as $field => $data) {
            $operator = self::DEFAULT_OPERATOR;
            if (is_string($data) || is_numeric($data)) {
                $value = $data;
            } elseif (is_array($data) && isset($data['value']) && isset($data['operator'])) {
                $value = $data['value'] ?? null;
                $operator = $data['operator'] ?? self::DEFAULT_OPERATOR;
            } else {
                continue;
            }

            if (! empty($clause)) {
                $clause .= " {$inter} ";
            }
            $clause .= "{$field} {$operator} '" . addslashes($value) . "'";
        }
        $query .= $clause;
        $query .= ($orderby ? " ORDER BY {$orderby}" : '');
        $query .= ($limit ? " LIMIT {$limit}" : '');

        $result = pmb_mysql_query($query);
        $instances = array();
        if (pmb_mysql_num_rows($result)) {
            $className = static::class;
            foreach ($result as $row) {
                $ids = $row;

                if(isset($className::$idsTableName) && !empty($className::$idsTableName)) {
                    foreach($ids as $prop => $value) {
                        if(!in_array($prop, $className::$idsTableName)) {
                            unset($ids[$prop]);
                        }
                    }
                }

                $instances[] = new $className($ids);
            }
        }
        return $instances;
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

                // On vérifie que la propriété existe sur l'ORM
                $rowField = $row->Field;
                if (! empty(static::$tablePrefix)) {
                    $rowField = str_replace(static::$tablePrefix . '_custom_', '', $row->Field);
                }

                if (false === static::$reflectionClass[static::class]->hasProperty($rowField)) {
                    throw new \Exception("$rowField is missing");
                }
            }
        }
        return $this->structure;
    }

    public static function getInstance($ids = array())
    {
        $ids = is_array($ids) ? $ids : [$ids];
        static::$instances[static::class] = static::$instances[static::class] ?? [];
        $id = implode(',', $ids);

        if (isset(static::$instances[static::class][$id])) {
            $instance = static::$instances[static::class][$id];
        } else {
            $instance =  new static($ids);
            static::$instances[static::class][$id] = $instance;
        }

        return $instance;
    }
}