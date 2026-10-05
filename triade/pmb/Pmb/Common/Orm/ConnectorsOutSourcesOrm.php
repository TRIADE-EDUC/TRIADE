<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ConnectorsOutSourcesOrm.php,v 1.1 2024/01/24 09:43:13 gneveu Exp $

namespace Pmb\Common\Orm;

use Pmb\Common\Orm\Orm;

class ConnectorsOutSourcesOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "connectors_out_sources";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "connectors_out_source_id";

    /**
     *
     * @var integer
     */
    public $connectors_out_source_id = 0;

    /**
     *
     * @var integer
     */
    public $connectors_out_sources_connectornum = 0;

    /**
     *
     * @var string
     */
    public $connectors_out_source_name = "";

    /**
     *
     * @var string
     */
    public $connectors_out_source_comment = "";

    /**
     *
     * @var string
     */
    public $connectors_out_source_config = "";

}