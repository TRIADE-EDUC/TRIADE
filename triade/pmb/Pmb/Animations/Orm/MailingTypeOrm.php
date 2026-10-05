<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MailingTypeOrm.php,v 1.5 2021/03/18 12:01:13 gneveu Exp $

namespace Pmb\Animations\Orm;

use Pmb\Common\Orm\Orm;

class MailingTypeOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "anim_mailing_types";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_mailing_type";
    
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;

    /**
     *
     * @var integer
     */
    protected $id_mailing_type = 0;

    /**
     *
     * @var string
     */
    protected $name = "";

    /**
     *
     * @var integer
     */
    protected $delay = 0;

    /**
     *
     * @var integer
     */
    protected $periodicity = 0;

    /**
     *
     * @var integer
     */
    protected $auto_send = 0;

    /**
     *
     * @var integer
     */
    protected $num_template = 0;

    /**
     *
     * @var integer
     */
    protected $campaign = 0;

    /**
     *
     * @var integer
     */
    protected $num_sender = 0;

    /**
     * @Relation 0n
     * @Orm Pmb\Common\Orm\MailtplOrm
     * @RelatedKey num_template
     */
    protected $mailtpl = null;
    
    /**
     *
     * @Relation n0
     * @Orm Pmb\Animations\Orm\MailingAnimationOrm
     * @Table anim_mailings
     * @ForeignKey num_mailing_type
     * @RelatedKey id_mailing
     */
    protected $mailings = null;
}