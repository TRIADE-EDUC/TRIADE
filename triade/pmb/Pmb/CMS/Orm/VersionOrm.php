<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: VersionOrm.php,v 1.2 2022/02/17 10:13:42 jparis Exp $
namespace Pmb\CMS\Orm;

use Pmb\Common\Orm\Orm;

class VersionOrm extends Orm
{

    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "portal_version";

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

    protected static $relations = [];

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
     * @var integer
     */
    protected $portal_num = 0;
    
    /**
     *
     * @var integer
     */
    protected $last_version_num = 0;

    /**
     *
     * @var \DateTime
     */
    protected $create_at = "";

    /**
     *
     * @var string
     */
    protected $properties = "";

    /**
     *
     * @Relation 0n
     * @Orm Pmb\CMS\Orm\PortalOrm
     * @RelatedKey portal_num
     * @var PortalOrm|null
     */
    protected $portal = null;
}