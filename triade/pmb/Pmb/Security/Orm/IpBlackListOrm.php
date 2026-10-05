<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: IpBlackListOrm.php,v 1.2.4.2 2025/05/20 14:00:08 qvarin Exp $

namespace Pmb\Security\Orm;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

use Pmb\Common\Orm\Orm;

class IpBlackListOrm extends Orm
{
    public static $instances = [];

    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;

    /**
     *
     * @var string
     */
    public static $tableName = "ip_blacklist";

    /**
     *
     * @var string
     */
    public static $idTableName = "id_ip_blacklist";

    /**
     * ID
     *
     * @var int
     */
    public $id_ip_blacklist = 0;

    /**
     * Address IP
     *
     * @var string
     */
    public $ip_blacklist_ip = "";

    /**
     * Date d'ajout
     *
     * @var string
     */
    public $ip_blacklist_time = "";

    /**
     * Renvoie le nombre d'enregistrement
     *
     * @return int
     */
    public static function count()
    {
        $result = pmb_mysql_query("SELECT COUNT(".self::$idTableName.") FROM ".self::$tableName);
        if (pmb_mysql_num_rows($result)) {
            return pmb_mysql_result($result, 0, 0);
        }

        return 0;
    }
}
