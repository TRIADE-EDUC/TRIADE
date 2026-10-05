<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomFieldListOrm.php,v 1.2 2020/08/31 10:26:09 gneveu Exp $

namespace Pmb\Common\Orm;

abstract class CustomFieldListOrm extends Orm
{
    /**
     *
     * @var string
     */
    public static $tableName = "";
    
    /**
     * 
     * @var string
     */
    public static $tablePrefix = "";
    
    /**
     *
     * @var integer
     */
    protected $champ = 0;
    
    /**
     *
     * @var string
     */
    protected $list_value = "";
    
    /**
     *
     * @var string
     */
    protected $list_lib = 0;
    
    /**
     *
     * @var integer
     */
    protected $ordre = 0;
    
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;
}