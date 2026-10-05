<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EmprGroupeOrm.php,v 1.2 2024/01/03 11:24:13 gneveu Exp $

namespace Pmb\Payments\Orm;

use Pmb\Common\Orm\Orm;

class EmprGroupeOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "empr_groupe";
    
    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "empr_id";
    
    /**
     * Clé primaire supplémentaire
     *
     * @var array
     */
    public static $primaryKeyAditional = [
        "empr_id",
        "groupe_id"
    ];
    
    /**
     *
     * @var integer
     */
    public $empr_id = 0;
    
    /**
     *
     * @var integer
     */
    public $groupe_id = 0;
    
}