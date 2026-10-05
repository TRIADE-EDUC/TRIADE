<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: TransactionPaymentsOrm.php,v 1.2 2024/01/03 11:24:13 gneveu Exp $

namespace Pmb\Payments\Orm;

use Pmb\Common\Orm\Orm;

class TransactionPaymentsOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "transaction_payments";

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
    public $order_number = 0;

    /**
     *
     * @var string
     */
    public $payment_date = "";

    /**
     *
     * @var int
     */
    public $payment_status = 0;

    /**
     *
     * @var string
     */
    public $payment_organization_status = "";

    /**
     *
     * @var integer
     */
    public $num_user = 0;

    /**
     *
     * @var integer
     */
    public $num_organization = 0;

    protected $organization = null;

    /**
     * The function returns the payment organization associated with the current object.
     *
     * @return the value of the `organization` property.
     */
    public function getOrganization()
    {
        if (!isset($this->organization)) {
            $this->organization = PaymentOrganizationOrm::findById($this->num_organization);
        }
        unset($this->organization->structure);
        return $this->organization;
    }

    /**
     * The function "unsetStructure" sets the value of the "structure" property to null.
     */

    public function unsetStructure()
    {
        $this->structure = null;
    }

    /**
     * The function retrieves the order number of the last entry in a database table.
     *
     * @return the order number of the last entry in the database table.
     */

    public static function getLastEntryOrderNumber()
    {
        $query = "SELECT order_number FROM " . self::$tableName . " ORDER BY id DESC LIMIT 1";
        $result = pmb_mysql_query($query);
        return pmb_mysql_result($result, 0, 0);
    }
}
