<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationCustomFieldOrm.php,v 1.3 2020/09/15 15:28:09 btafforeau Exp $

namespace Pmb\Animations\Orm;

use Pmb\Common\Orm\CustomFieldOrm;

class AnimationCustomFieldOrm extends CustomFieldOrm
{
    /**
     * Table name
     * 
     * @var string
     */
    public static $tableName = "anim_animation_custom";
    
    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "idchamp";
    
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;
}