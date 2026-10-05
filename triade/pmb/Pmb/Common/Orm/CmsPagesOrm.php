<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CmsPagesOrm.php,v 1.1.4.2 2024/06/27 09:55:04 qvarin Exp $

namespace Pmb\Common\Orm;

class CmsPagesOrm extends Orm
{
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;

    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "cms_pages";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_page";

    /**
     *
     * @var integer
     */
    protected $id_page = 0;

    /**
     *
     * @var string
     */
    protected $page_hash = "";

    /**
     *
     * @var string
     */
    protected $page_name = "";

    /**
     *
     * @var string
     */
    protected $page_description = "";

    /**
     *
     * @var string
     */
    protected $page_classement = "";

}