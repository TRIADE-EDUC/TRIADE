<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationTypesOrm.php,v 1.2.8.1 2025/03/24 12:40:04 jparis Exp $

namespace Pmb\Animations\Orm;

use Pmb\Common\Orm\Orm;

class AnimationTypesOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "anim_types";
    
    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_type";
    
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;
    
    /**
     *
     * @var integer
     */
    protected $id_type = 0;
    
    /**
     *
     * @var string
     */
    protected $label = "";
}