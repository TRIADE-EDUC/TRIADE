<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EmprCategOrm.php,v 1.1.2.2 2024/10/31 08:20:26 gneveu Exp $

namespace Pmb\Common\Orm;

class EmprCategOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "empr_categ";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_categ_empr";

    /**
     *
     * @var integer
     */
    protected $id_categ_empr = 0;

    /**
     *
     * @var string
     */
    protected $libelle = "";

    /**
     *
     * @var int
     */
    protected $duree_adhesion = 365;

    /**
     *
     * @var float
     */
    protected $tarif_abt = 0.0;

    /**
     *
     * @var int
     */
    protected $age_min = 0;

    /**
     *
     * @var int
     */
    protected $age_max = 0;

    /**
     *
     * @var int
     */
    protected $pret_already_loaned_active = 0;

    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;

    public function getEmprCategList()
    {
        $return = [];
        $emprCategList = $this->findAll();
        foreach ($emprCategList as $emprCateg) {
            $return[$emprCateg->id_categ_empr] = $emprCateg->libelle;
        }
        return $return;
    }
}
