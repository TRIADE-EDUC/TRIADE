<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MailingAnimationOrm.php,v 1.1.10.1 2025/06/04 12:39:32 qvarin Exp $

namespace Pmb\Animations\Orm;

use Pmb\Common\Orm\Orm;

class MailingAnimationOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "anim_mailings";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_mailing";

    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;

    /**
     *
     * @var integer
     */
    protected $id_mailing = 0;

    /**
     *
     * @var integer
     */
    protected $num_animation = 0;

    /**
     *
     * @var integer
     */
    protected $num_mailing_type = 0;

    /**
     *
     * @var integer
     */
    protected $already_mail = 0;

    /**
     *
     * @Relation n0
     * @Orm Pmb\Animations\Orm\AnimationOrm
     * @Table anim_animations
     * @ForeignKey id_animation
     * @RelatedKey id_animation
     */
    protected $animation = null;

    /**
     *
     * @Relation n0
     * @Orm Pmb\Animations\Orm\MailingTypeOrm
     * @Table anim_mailing_types
     * @ForeignKey num_mailing_type
     * @RelatedKey id_mailing_type
     */
    protected $mailingType = null;
}