<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PortalOrm.php,v 1.1 2022/02/07 09:01:59 jparis Exp $
namespace Pmb\CMS\Orm;

use Pmb\Common\Orm\Orm;

class PortalOrm extends Orm
{

    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "portal_portal";

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
    protected $is_default = 0;

    /**
     *
     * @var integer
     */
    protected $version_num = 0;

    /**
     *
     * @Relation 0n
     * @Orm Pmb\CMS\Orm\VersionOrm
     * @RelatedKey version_num
     */
    protected $version = null;

    /**
     *
     * @Relation n0
     * @Orm Pmb\CMS\Orm\VersionOrm
     * @Table portal_verions
     * @ForeignKey portal_num
     * @RelatedKey id
     */
    protected $versions = null;
}