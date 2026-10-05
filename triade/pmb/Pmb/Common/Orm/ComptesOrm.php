<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ComptesOrm.php,v 1.1 2023/03/21 13:35:34 gneveu Exp $

namespace Pmb\Common\Orm;

class ComptesOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "comptes";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_compte";

    /**
     *
     * @var integer
     */
    protected $id_compte = 0;

    /**
     *
     * @var string
     */
    protected $libelle = "";

    /**
     *
     * @var integer
     */
    protected $type_compte_id = 0;

    /**
     *
     * @var float
     */
    protected $solde = 0.0;

    /**
     *
     * @var float
     */
    protected $prepay_mnt = 0.0;

    /**
     *
     * @var integer
     */
    protected $proprio_id = 0;

    /**
     *
     * @var string
     */
    protected $droits = "";
    
    

    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;
}