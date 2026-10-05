<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MailingListOrm.php,v 1.3 2021/03/11 16:45:28 jlaurent Exp $

namespace Pmb\Animations\Orm;

use Pmb\Common\Orm\Orm;

class MailingListOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "anim_mailing_list";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_mailing_list";
    
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;

    /**
     *
     * @var integer
     */
    protected $id_mailing_list = 0;

    /**
     *
     * @var string
     */
    protected $send_at = "";

    /**
     *
     * @var boolean
     */
    protected $auto_send = false;

    /**
     *
     * @var integer
     */
    protected $nb_success_mails = 0;

    /**
     *
     * @var integer
     */
    protected $nb_error_mails = 0;

    /**
     *
     * @var string
     */
    protected $mailing_content = "";

    /**
     *
     * @var string
     */
    protected $response_content = "";

    /**
     *
     * @var integer
     */
    protected $num_animation = 0;
    
    /**
     *
     * @var integer
     */
    protected $num_user = 0;
    
    /**
     *
     * @var integer
     */
    protected $num_campaign = 0;
    
    /**
     * @Relation n0
     * @Orm Pmb\Animations\Orm\AnimationOrm
     * @Table anim_animations
     * @ForeignKey num_animation
     * @RelatedKey id_animation
     */
    protected $animation = null;
}