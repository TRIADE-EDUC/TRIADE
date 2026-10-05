<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DocsLocationOrm.php,v 1.4 2020/09/14 09:03:43 btafforeau Exp $

namespace Pmb\Common\Orm;

class DocsLocationOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "docs_location";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "idlocation";

    /**
     *
     * @var integer
     */
    protected $idlocation = 0;

    /**
     *
     * @var string
     */
    protected $location_libelle = "";

    /**
     *
     * @var string
     */
    protected $locdoc_codage_import = "";

    /**
     *
     * @var integer
     */
    protected $locdoc_owner = 0;

    /**
     *
     * @var string
     */
    protected $location_pic = "";

    /**
     *
     * @var integer
     */
    protected $location_visible_opac = 0;
    
    /**
     *
     * @var string
     */
    protected $name = "";
    
    /**
     *
     * @var string
     */
    protected $adr1 = "";
    
    /**
     *
     * @var string
     */
    protected $adr2 = "";
    
    /**
     *
     * @var string
     */
    protected $cp = "";
    
    /**
     *
     * @var string
     */
    protected $town = "";
    
    /**
     *
     * @var string
     */
    protected $state = "";
    
    /**
     *
     * @var string
     */
    protected $country = "";
    
    /**
     *
     * @var string
     */
    protected $phone = "";
    
    /**
     *
     * @var string
     */
    protected $email = "";
    
    /**
     *
     * @var string
     */
    protected $website = "";
    
    /**
     *
     * @var string
     */
    protected $logo = "";
    
    /**
     *
     * @var string
     */
    protected $commentaire = "";
    
    /**
     *
     * @var integer
     */
    protected $transfert_ordre = 0;
    
    /**
     *
     * @var integer
     */
    protected $transfert_statut_defaut = 0;
    
    /**
     *
     * @var integer
     */
    protected $num_infopage = 0;
    
    /**
     *
     * @var string
     */
    protected $css_style = "";
    
    /**
     *
     * @var integer
     */
    protected $surloc_num = 0;
    
    /**
     *
     * @var integer
     */
    protected $surloc_used = 0;
    
    /**
     *
     * @var integer
     */
    protected $show_a2z = 0;
    
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;
}