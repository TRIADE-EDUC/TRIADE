<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Helper.php,v 1.15 2023/11/09 08:14:28 gneveu Exp $

namespace Pmb\Common\Helper;

class Helper
{
    public static function camelize(string $string): string
    {
        return lcfirst(str_replace(' ', '', ucwords(preg_replace('/[^a-zA-Z0-9\x7f-\xff]++/', ' ', $string))));
    }

    public static function pascalize(string $string): string
    {
        return ucfirst(str_replace(' ', '', ucwords(preg_replace('/[^a-zA-Z0-9\x7f-\xff]++/', ' ', $string))));
    }

    public static function snakelize(string $string, string $delimiter = '_'): string
    {
        return trim(strtolower(preg_replace('/[^a-zA-Z0-9\x7f-\xff]++/', $delimiter, $string)), $delimiter);
    }

    public static function array_camelize_key(array $array): array
    {
        $new_array = [];
        foreach ($array as $key => $value) {
            $key = lcfirst(str_replace(' ', '', ucwords(preg_replace('/[^a-zA-Z0-9\x7f-\xff]++/', ' ', $key))));
            $new_array[$key] = $value;
        }
        return $new_array;
    }

    public static function array_camelize_key_recursive(array $array): array
    {
        return array_map(function ($item) {
            if (is_array($item)) {
                $item = self::array_camelize_key_recursive($item);
            }
            return $item;
        }, self::array_camelize_key($array));
    }

    public static function array_change_key_case_recursive(array $array): array
    {
        return array_map(function ($item) {
            if (is_array($item)) {
                $item = self::array_change_key_case_recursive($item);
            }
            return $item;
        }, array_change_key_case($array));
    }

    public static function camelize_to_snake($string)
    {
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
    }

    public static function camelize_to_kebab($string)
    {
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^-])([A-Z][a-z])/'], '$1-$2', $string));
    }

    /**
     * Test la validité d'un email
     *
     * @param string $mail
     * @return boolean
     */
    public static function isValidMail(string $mail): string
    {
        $regex = "/(^(([^<>()\[\]\\.,;:\s@\"]+(\.[^<>()\[\]\\.,;:\s@\"]+)*)|(\"\.+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$)/";
        return pmb_preg_match($regex, $mail);
    }

    /**
     * Test la validité d'un numéro de téléphone
     *
     * @param string $phone
     * @return boolean
     */
    public static function isValidPhone(string $phone): string
    {
        $phoneTemp = preg_replace("/[\W\s]/", '', $phone);
        if (is_numeric($phoneTemp)) {
            return true;
        }
        return false;
    }

    /**
     * Récupère les informations de l'utilisateur en Gestion
     *
     * @param int $id
     * @return array $user
     */

    public static function getUser(int $id)
    {
        $user = [];

        $query = "SELECT * FROM users WHERE userid = $id";
        $result = pmb_mysql_query($query);

        if (pmb_mysql_num_rows($result)) {
            $user = pmb_mysql_fetch_assoc($result, 0, 0);
        }

        return $user;
    }

    /**
     * Récupère les informations de l'utilisateur en Gestion
     *
     * @param int $id
     * @return array $user
     */

    public static function getUsers()
    {
        $users = [];

        $query = "SELECT * FROM users";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $users[] = $row;
            }
        }
        return $users;
    }

    /**
     * Récupère les informations de l'utilisateur en Gestion sur un ou plusieurs champs
     * 
     * @param array $fields
     * @param array $condition
     * @param string $operator
     * @return array $user
     */

    public static function getUsersByFields($fields = array(), $conditions = array(), $operator = "AND")
    {
        $users = [];
        $searchFields = implode(',', $fields);
        if (empty($fields)) {
            $searchFields = "*";
        }
        
        $query = "SELECT $searchFields FROM users";
        
        if (!empty($conditions)) {
            $queryConditions = "";
            foreach ($conditions as $field => $value) {
                if (!empty($queryConditions)) {
                    $queryConditions .= " $operator $field = '$value'";
                } else {
                    $queryConditions = " WHERE $field = '$value'";
                }
            }
            $query .= $queryConditions;
        }
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $users[] = $row;
            }
        }
        return $users;
    }

    public static function toArray($data, $default = null)
    {
        if (!is_array($data) && !is_object($data)) {
            return is_null($data) ? $default : $data;
        }

        if (is_object($data) && method_exists($data, "toArray")) {
            return call_user_func_array([$data, "toArray"], [$data, $default]);
        }

        $result = [];
        foreach ($data as $key => $value) {
            if (is_object($value) && method_exists($value, "toArray")) {
                $result[$key] = call_user_func_array([$value, "toArray"], [$value, $default]);
            } elseif (is_array($value) || is_object($value)) {
                $result[$key] = self::toArray($value, $default);
            } else {
                $result[$key] = is_null($value) ? $default : $value;
            }
        }
        return $result;
    }

    public static function toCmsData($data)
    {
        if (!is_array($data) && !is_object($data)) {
            return $data;
        }

        $result = [];
        foreach ($data as $key => $value) {
            if (is_object($value) && method_exists($value, "toCmsData")) {
                $result[$key] = call_user_func_array([$value, "toCmsData"], [$value]);
            } elseif (is_array($value) || is_object($value)) {
                $result[$key] = self::toArray($value);
                if (empty($result[$key])) {
                    $result[$key] = $value;
                }
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    public static function toObject($data)
    {
        if (!is_array($data) && !is_object($data)) {
            return $data;
        }

        $result = new \StdClass();
        foreach ($data as $key => $value) {
            if (is_object($value) && method_exists($value, "toObject")) {
                $result->{$key} = call_user_func_array([$value, "toObject"], [$value]);
            } elseif (is_array($value) || is_object($value)) {
                $result->{$key} = self::toObject($value);
            } else {
                $result->{$key} = $value;
            }
        }
        return $result;
    }
}
