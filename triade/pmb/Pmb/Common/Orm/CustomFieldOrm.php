<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomFieldOrm.php,v 1.3 2020/09/15 15:28:09 btafforeau Exp $

namespace Pmb\Common\Orm;

abstract class CustomFieldOrm extends Orm
{
   /**
    *
    * @var integer
    */
    protected $idchamp = 0;
    
    /**
     *
     * @var integer
     */
    protected $num_type = 0;
    
    /**
     *
     * @var string
     */
    protected $name = "";
    
    /**
     *
     * @var string
     */
    protected $titre = "";
    
    /**
     *
     * @var string
     */
    protected $type = "";
    
    /**
     *
     * @var string
     */
    protected $datatype = "";
    
    /**
     *
     * @var string
     */
    protected $options = "";
    
    /**
     *
     * @var integer
     */
    protected $multiple = 0;
    
    /**
     *
     * @var integer
     */
    protected $obligatoire = 0;
    
    /**
     *
     * @var integer
     */
    protected $ordre = 0;
    
    /**
     *
     * @var integer
     */
    protected $search = 0;
    
    /**
     *
     * @var integer
     */
    protected $export = 0;
    
    /**
     *
     * @var integer
     */
    protected $filters = 0;
    
    /**
     *
     * @var integer
     */
    protected $exclusion_obligatoire = 0;
    
    /**
     *
     * @var integer
     */
    protected $pond = 0;
    
    /**
     *
     * @var integer
     */
    protected $opac_sort = 0;
    
    /**
     *
     * @var string
     */
    protected $comment = "";
    
    /**
     *
     * @var string
     */
    protected $custom_classement = "";
    
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;
}