<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: shorturls.class.php,v 1.5 2023/12/01 08:51:24 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Common\Helper\Portal;

require_once("$class_path/shorturl/shorturl_type.class.php");

class shorturls {

    protected const RESULT_NOTICE = 3;
    protected const SEARCH_SEGMENT = 35;

    public static function proceed($hash)
    {
        $query = "select id_shorturl,shorturl_type from shorturls where shorturl_hash = '" . addslashes($hash) . "'";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_object($result);
            $id = $row->id_shorturl;
            $classname = self::get_class_name($row->shorturl_type);
            $class = new $classname($id);
            $class->proceed();
        } else {
            print "Hash not found";
            exit();
        }
    }

    protected static function get_class_name($type = "")
    {
        if ($type && class_exists("shorturl_type_" . $type)) {
            return "shorturl_type_" . $type;
        }
        throw new Exception("Class not found");
    }

    /**
     *
     * @param number $id
     * @return NULL|shorturl_type
     */
    public static function get_class_by_context_of_page($id = 0)
    {
        $type = intval(Portal::getTypePage());
        switch ($type) {
            case self::RESULT_NOTICE:
                $classname = self::get_class_name("search");
                break;

            case self::SEARCH_SEGMENT:
                $classname = self::get_class_name("segment");
                break;

            default:
                return null;
        }

        return new $classname($id);
    }
}
