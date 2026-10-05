<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationCalendarOrm.php,v 1.2 2022/10/06 07:32:19 gneveu Exp $

namespace Pmb\Animations\Orm;

use Pmb\Common\Orm\Orm;

class AnimationCalendarOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "anim_calendar";
    
    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_calendar";
    
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;
    
    /**
     *
     * @var integer
     */
    protected $id_calendar = 0;
    
    /**
     *
     * @var string
     */
    protected $name = "";

    /**
     *
     * @var string
     */
    protected $color = "";
    
}