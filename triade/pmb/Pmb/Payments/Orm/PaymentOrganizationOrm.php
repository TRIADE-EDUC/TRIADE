<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PaymentOrganizationOrm.php,v 1.2 2024/01/03 11:24:13 gneveu Exp $

namespace Pmb\Payments\Orm;

use Pmb\Common\Orm\Orm;

class PaymentOrganizationOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "payment_organization";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id";

    /**
     *
     * @var integer
     */
    public $id = 0;

    /**
     *
     * @var string
     */
    public $name = "";

    /**
     *
     * @var string
     */
    public $data = "";

}
