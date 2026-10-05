<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: TransactionsOrm.php,v 1.2 2024/01/03 11:24:13 gneveu Exp $

namespace Pmb\Payments\Orm;

use Pmb\Common\Orm\Orm;

class TransactionsOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "transactions";
    
    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_transaction";
    
    /**
     * Clé primaire supplémentaire
     *
     * @var array
     */
    public static $primaryKeyAditional = [
        "id_transaction",
    ];
    
    /**
     *
     * @var integer
     */
    public $id_transaction = 0;
    
    /**
     *
     * @var integer
     */
    public $compte_id = 0;
    
    /**
     *
     * @var integer
     */
    public $user_id = 0;
    
    /**
     *
     * @var string
     */
    public $user_name = "";
    
    /**
     *
     * @var string
     */
    public $machine = "";

    /**
     *
     * @var string
     */
    public $date_enrgt = "";

    /**
     *
     * @var string
     */
    public $date_prevue = "";

    /**
     *
     * @var string
     */
    public $date_effective = "";

    /**
     *
     * @var float
     */
    public $montant = 0.0;

    /**
     *
     * @var integer
     */
    public $sens = 0;

    /**
     *
     * @var integer
     */
    public $realisee = 0;

    /**
     *
     * @var string
     */
    public $commentaire = "";

    /**
     *
     * @var string
     */
    public $encaissement  = 0;

    /**
     *
     * @var string
     */
    public $transactype_num  = 0;

    /**
     *
     * @var string
     */
    public $cashdesk_num = 0;

    /**
     *
     * @var string
     */
    public $transacash_num  = 0;

    /**
     *
     * @var string
     */
    public $transaction_payment_method_num  = 0;
}