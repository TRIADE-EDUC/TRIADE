<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: UsersGroupsOrm.php,v 1.1 2024/01/23 13:34:36 dbellamy Exp $

namespace Pmb\Common\Orm;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class UsersGroupsOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "users_groups";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "grp_id";

    /**
     *
     * @var integer
     */
    protected $grp_id = 0;

    /**
     *
     * @var string
     */
    protected $grp_name = "";


    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;
}