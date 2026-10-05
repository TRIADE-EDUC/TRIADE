<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationCustomFieldValueOrm.php,v 1.2 2020/08/31 10:26:09 gneveu Exp $

namespace Pmb\Animations\Orm;

use Pmb\Common\Orm\CustomFieldValueOrm;

class AnimationCustomFieldValueOrm extends CustomFieldValueOrm
{
    /**
     * Table name
     * 
     * @var string
     */
    public static $tableName = "anim_animation_custom_values";
    
    /**
     *
     * @var string
     */
    public static $tablePrefix = "anim_animation";
    
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;
}