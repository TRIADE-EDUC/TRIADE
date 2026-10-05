<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AuthenticationModelsOrm.php,v 1.6 2023/07/11 08:49:01 dbellamy Exp $
namespace Pmb\Authentication\Orm;

use Pmb\Common\Orm\Orm;
if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class AuthenticationModelsOrm extends Orm
{

    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "authentication_models";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id";

    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;

    /**
     *
     * @var integer
     */
    protected $id = 0;

    /**
     *
     * @var string
     */
    protected $name = "";

    /**
     *
     * @var string
     */
    protected $source_name = "";

    /**
     *
     * @var string
     */
    protected $settings = "";

    /**
     *
     * @var integer
     */
    protected $context = 0;

    public function getSettings()
    {
        if (empty($this->settings)) {
            return [];
        }

        return \encoding_normalize::json_decode($this->settings, true);
    }

    public static function findByContext(int $context, string $order_by = 'name' )
    {
        if (! property_exists(static::class, $order_by)) {
            $order_by = '';
        }
        $query = "SELECT * FROM " . static::$tableName . " WHERE (context & $context) = $context";
        if($order_by) {
            $query.= " ORDER BY $order_by";
        }
        $result = pmb_mysql_query($query);
        $instances = [];

        if (pmb_mysql_num_rows($result)) {
            $className = static::class;
            foreach ($result as $row) {
                $instances[] = new $className(intval($row[static::$idTableName]));
            }
        }

        return $instances;
    }
}



