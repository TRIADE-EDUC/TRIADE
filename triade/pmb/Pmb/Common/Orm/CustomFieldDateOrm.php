<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomFieldDateOrm.php,v 1.2 2020/08/31 10:26:09 gneveu Exp $

namespace Pmb\Common\Orm;

abstract class CustomFieldDateOrm extends Orm
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
     * @var integer
     */
    protected $origine = 0;
    
    /**
     *
     * @var integer
     */
    protected $date_type = 0;
    
    /**
     *
     * @var integer
     */
    protected $date_start = 0;
    
    /**
     *
     * @var integer
     */
    protected $date_end = 0;
    
    /**
     *
     * @var integer
     */
    protected $order = 0;
    
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;
}