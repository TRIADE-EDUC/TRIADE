<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RegistredPersonOrm.php,v 1.5 2020/10/02 15:24:05 qvarin Exp $

namespace Pmb\Animations\Orm;

use Pmb\Common\Orm\Orm;

class RegistredPersonOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "anim_registred_persons";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_person";
    
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;

    /**
     *
     * @var integer
     */
    protected $id_person = 0;

    /**
     * 
     * @var string
     */
    protected $person_name = "";
    
    /**
     *
     * @var integer
     */
    protected $num_empr = 0;

    /**
     * @Relation n0
     * @Orm Pmb\Common\Orm\EmprOrm
     * @Table empr
     * @ForeignKey num_empr
     * @RelatedKey id_empr
     */
    protected $empr = null;

    /**
     *
     * @var integer
     */
    protected $num_price = 0;

    /**
     * @Relation n0
     * @Orm Pmb\Animations\Orm\PriceOrm
     * @Table anim_price
     * @ForeignKey num_price
     * @RelatedKey id_price
     */
    protected $price = null;

    /**
     *
     * @var integer
     */
    protected $num_registration = 0;

    /**
     * @Relation n0
     * @Orm Pmb\Animations\Orm\RegistrationOrm
     * @Table anim_registrations
     * @ForeignKey num_registration
     * @RelatedKey id_registration
     */
    protected $registration = null;
}