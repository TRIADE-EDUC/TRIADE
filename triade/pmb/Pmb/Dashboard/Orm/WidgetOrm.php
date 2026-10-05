<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: WidgetOrm.php,v 1.5 2024/02/08 13:44:22 jparis Exp $

namespace Pmb\Dashboard\Orm;

use Pmb\Common\Orm\Orm;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class WidgetOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "widget";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_widget";

    /**
     *
     * @var integer
     */
    protected $id_widget = 0;

    /**
     *
     * @var string
     */
    protected $widget_name = "";

    /**
     *
     * @var boolean
     */
    protected $widget_editable = 0;

    /**
     *
     * @var string
     */
    protected $widget_type = "";

    /**
     *
     * @var integer
     */
    protected $num_user = 0;

    /**
     *
     * @var boolean
     */
    protected $widget_shareable = 0;

    /**
     *
     * @var string
     */
    protected $widget_settings = "";


    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;
}