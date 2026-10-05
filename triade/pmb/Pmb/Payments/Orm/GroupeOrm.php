<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: GroupeOrm.php,v 1.2 2024/01/03 11:24:13 gneveu Exp $

namespace Pmb\Payments\Orm;

use Pmb\Common\Orm\Orm;

class GroupeOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "groupe";
    
    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_groupe";
    
    /**
     * Clé primaire supplémentaire
     *
     * @var array
     */
    public static $primaryKeyAditional = [
        "id_groupe",
    ];
    
    /**
     *
     * @var integer
     */
    public $id_groupe = 0;
    
    /**
     *
     * @var string
     */
    public $libelle_groupe = "";
    
    /**
     *
     * @var integer
     */
    public $resp_groupe = 0;

    /**
     *
     * @var integer
     */
    public $lettre_rappel = 0;

    /**
     *
     * @var integer
     */
    public $mail_rappel = 0;

    /**
     *
     * @var integer
     */
    public $lettre_rappel_show_nomgroup = 0;
    
    /**
     *
     * @var string
     */
    public $comment_gestion = "";
    
    /**
     *
     * @var string
     */
    public $comment_opac = "";
    
    /**
     *
     * @var integer
     */
    public $lettre_resa = 0;
    
    /**
     *
     * @var integer
     */
    public $mail_resa = 0;
    
    /**
     *
     * @var integer
     */
    public $lettre_resa_show_nomgroup = 0;
    
}