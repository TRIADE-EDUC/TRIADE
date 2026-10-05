<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: TransactionComptePaymentsOrm.php,v 1.2 2024/01/03 11:24:13 gneveu Exp $

namespace Pmb\Payments\Orm;

use Pmb\Common\Orm\Orm;

class TransactionComptePaymentsOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "transaction_compte_payments";

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
     * @var integer
     */
    public $transaction_num = 0;

    /**
     *
     * @var int
     */
    public $compte_num = 0;

    /**
     *
     * @var integer
     */
    public $amount = 0;

}
