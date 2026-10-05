<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PriceOrm.php,v 1.6 2020/10/06 08:39:19 qvarin Exp $

namespace Pmb\Animations\Orm;

use Pmb\Common\Orm\Orm;

class PriceOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "anim_prices";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_price";
    
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;

    /**
     *
     * @var integer
     */
    protected $id_price = 0;

    /**
     *
     * @var string
     */
    protected $name = "";

    /**
     *
     * @var float
     */
    protected $value = 0.0;
    
    /**
     *
     * @var integer
     */
    protected $num_price_type = 0;
    
    /**
     *
     * @var integer
     */
    protected $num_animation = 0;

    /**
     *
     * @Relation n0
     * @Orm Pmb\Animations\Orm\AnimationOrm
     * @Table anim_animations
     * @ForeignKey num_animation
     * @RelatedKey id_animation
     */
    protected $animation = null;
    
    /**
     *
     * @Relation n0
     * @Orm Pmb\Animations\Orm\PriceTypeOrm
     * @Table anim_prices_types
     * @ForeignKey num_price_type
     * @RelatedKey id_price_type
     */
    protected $priceType = null;
}