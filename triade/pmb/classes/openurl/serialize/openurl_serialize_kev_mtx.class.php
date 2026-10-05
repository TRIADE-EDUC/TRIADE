<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: openurl_serialize_kev_mtx.class.php,v 1.3.18.1 2025/03/04 10:45:25 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}
require_once $class_path . "/openurl/serialize/openurl_serialize.class.php";

class openurl_serialize_kev_mtx extends openurl_serialize
{

    public function __construct()
    {
        parent::__construct();
        $this->uri = $this->uri . ":kev";
    }

    public static function serialize($tab)
    {
        $serialized_object = "";
        foreach ($tab as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $val) {
                    if ($serialized_object != "") {
                        $serialized_object .= "&";
                    }
                    $serialized_object .= "$key=" . rawurlencode($val);
                }
            } else {
                if ($serialized_object != "") {
                    $serialized_object .= "&";
                }
                $serialized_object .= "$key=" . rawurlencode($value);
            }
        }
        return $serialized_object;
    }

    public static function unserialize($str)
    {
        $value_name = $value = $tmp = "";
        $params = array();
        for ($i = 0; $i < strlen($str); $i ++) {
            switch ($str[$i]) {
                case "=":
                    $value_name = $tmp;
                    $tmp = "";
                    break;
                case "&":
                    $value = $tmp;
                    $tmp = '';
                    if (! isset($params[$value_name])) {
                        $params[$value_name] = array(
                            rawurldecode($value)
                        );
                    } else {
                        $params[$value_name][] = rawurldecode($value);
                    }
                    $value = $value_name = "";
                    break;
                default:
                    $tmp .= $str[$i];
                    break;
            }
        }
        if ($value_name != "" && $tmp != "") {
            if (! isset($params[$value_name])) {
                $params[$value_name] = array(
                    rawurldecode($tmp)
                );
            } else {
                $params[$value_name][] = rawurldecode($tmp);
            }
        }
        return $params;
    }
}