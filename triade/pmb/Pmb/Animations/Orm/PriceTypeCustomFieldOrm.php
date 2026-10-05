<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PriceTypeCustomFieldOrm.php,v 1.4 2020/10/01 07:56:12 gneveu Exp $

namespace Pmb\Animations\Orm;

use Pmb\Common\Orm\CustomFieldOrm;

class PriceTypeCustomFieldOrm extends CustomFieldOrm
{
    /**
     * Table name
     * 
     * @var string
     */
    public static $tableName = "anim_price_type_custom";
    
    
    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "idchamp";
    
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;
}