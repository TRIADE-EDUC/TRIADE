<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DashboardOrm.php,v 1.2 2024/01/25 09:10:25 jparis Exp $

namespace Pmb\Dashboard\Orm;

use Pmb\Common\Orm\Orm;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class DashboardOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "dashboard";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_dashboard";

    /**
     *
     * @var integer
     */
    protected $id_dashboard = 0;

    /**
     *
     * @var string
     */
    protected $dashboard_name = "";

    /**
     *
     * @var boolean
     */
    protected $dashboard_editable = 0;

    /**
     *
     * @var integer
     */
    protected $num_user = 0;


    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;
}