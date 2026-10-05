<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchPersoOrm.php,v 1.1 2024/01/24 09:43:13 gneveu Exp $

namespace Pmb\Common\Orm;

use Pmb\Common\Orm\Orm;

class SearchPersoOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "search_perso";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "search_id";

    /**
     *
     * @var integer
     */
    public $search_id = 0;

    /**
     *
     * @var string
     */
    public $search_type = "";

    /**
     *
     * @var integer
     */
    public $num_user = 0;

    /**
     *
     * @var string
     */
    public $search_name = "";

    /**
     *
     * @var string
     */
    public $search_shortname = "";

    /**
     *
     * @var string
     */
    public $search_query = "";

    /**
     *
     * @var string
     */
    public $search_human = "";

    /**
     *
     * @var integer
     */
    public $search_directlink = 0;

    /**
     *
     * @var string
     */
    public $autorisations = "";

    /**
     *
     * @var integer
     */
    public $search_order = "";

    /**
     *
     * @var string
     */
    public $search_comment = "";
}