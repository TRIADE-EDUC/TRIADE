<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationStatusOrm.php,v 1.7.10.1 2025/03/24 12:40:04 jparis Exp $

namespace Pmb\Animations\Orm;

use Pmb\Common\Orm\Orm;

class AnimationStatusOrm extends Orm
{

    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "anim_status";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_status";
    
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;

    /**
     *
     * @var integer
     */
    protected $id_status = 0;

    /**
     *
     * @var string
     */
    protected $label = "";
    
    /**
     *
     * @var string
     */
    protected $color = "";
}