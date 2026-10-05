<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PriceTypeCustomFieldValueOrm.php,v 1.3 2020/10/01 07:56:12 gneveu Exp $

namespace Pmb\Animations\Orm;

use Pmb\Common\Orm\CustomFieldValueOrm;

class PriceTypeCustomFieldValueOrm extends CustomFieldValueOrm
{
    /**
     * Table name
     * 
     * @var string
     */
    public static $tableName = "anim_price_type_custom_values";
    
    /**
     *
     * @var string
     */
    public static $tablePrefix = "anim_price_type";
    
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;
}