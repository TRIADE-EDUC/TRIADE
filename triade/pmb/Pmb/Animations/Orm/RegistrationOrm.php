<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RegistrationOrm.php,v 1.11 2023/02/20 14:07:00 qvarin Exp $
namespace Pmb\Animations\Orm;

use Pmb\Common\Orm\Orm;

class RegistrationOrm extends Orm
{

    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "anim_registrations";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_registration";

    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;

    /**
     *
     * @var integer
     */
    protected $id_registration = 0;

    /**
     *
     * @var integer
     */
    protected $nb_registered_persons = "";

    /**
     *
     * @var string
     */
    protected $name = "";

    /**
     *
     * @var string
     */
    protected $email = "";

    /**
     *
     * @var string
     */
    protected $phone_number = "";

    /**
     *
     * @var integer
     */
    protected $num_animation = 0;

    /**
     *
     * @Relation 0n
     * @Orm Pmb\Animations\Orm\AnimationOrm
     * @RelatedKey num_animation
     */
    protected $animation = null;

    /**
     *
     * @var integer
     */
    protected $num_registration_status = 0;

    /**
     *
     * @Relation n0
     * @Orm Pmb\Animations\Orm\RegistrationStatusOrm
     * @Table anim_registration_status
     * @ForeignKey anim_registration_status
     * @RelatedKey id_registration_status
     *
     */
    protected $registrationStatus = null;

    /**
     *
     * @var integer
     */
    protected $num_empr = 0;

    /**
     *
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
    protected $num_origin = 0;

    /**
     *
     * @Relation n0
     * @Orm Pmb\Animations\Orm\RegistrationOriginOrm
     * @Table anim_registration_origins
     * @ForeignKey num_origin
     * @RelatedKey id_registration_origin
     */
    // protected $registrationOrigin = null;

    /**
     *
     * @var boolean
     */
    protected $validated = false;

    /**
     *
     * @var datetime
     */
    protected $date = "";

    /**
     *
     * @var string
     */
    protected $hash = "";
}