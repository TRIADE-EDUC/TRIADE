<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EventOrm.php,v 1.8 2023/02/22 16:04:07 qvarin Exp $
namespace Pmb\Animations\Orm;

use Pmb\Common\Orm\Orm;

class EventOrm extends Orm
{

    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "anim_events";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_event";

    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;

    /**
     *
     * @var integer
     */
    protected $id_event = 0;

    /**
     *
     * @var \DateTime
     */
    protected $start_date = "";

    /**
     *
     * @var \DateTime
     */
    protected $end_date = "";

    /**
     *
     * @var integer
     */
    protected $num_config = 0;

    /**
     *
     * @Relation n0
     * @Orm Pmb\Animations\Orm\AnimationOrm
     * @Table anim_animations
     * @RelatedKey id_animation
     * @ForeignKey num_event
     */
    protected $animations = null;

    /**
     *
     * @var integer
     */
    protected $during_day = 0;
}