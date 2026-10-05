<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AccountsOrm.php,v 1.2 2024/01/03 11:24:13 gneveu Exp $

namespace Pmb\Payments\Orm;

use Pmb\Common\Orm\Orm;

class AccountsOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "comptes";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_compte";

    /**
     * Clé primaire supplémentaire
     *
     * @var array
     */
    public static $primaryKeyAditional = [
        "id_compte",
        "proprio_id"
    ];

    /**
     *
     * @var integer
     */
    public $id_compte = 0;

    /**
     *
     * @var string
     */
    public $libelle = "";

    /**
     *
     * @var integer
     */
    public $type_compte_id = 0;

    /**
     *
     * @var float
     */
    public $solde = 0.0;

    /**
     *
     * @var float
     */
    public $prepay_mnt = 0.0;

    /**
     *
     * @var integer
     */
    public $proprio_id = 0;

    /**
     *
     * @var integer
     */
    public $droits = 0;

}
