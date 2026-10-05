<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ConceptOrm.php,v 1.3 2021/03/31 08:47:34 qvarin Exp $

namespace Pmb\Autorities\Orm;

use Pmb\Common\Orm\Orm;

class ConceptOrm extends Orm
{

    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "index_concept";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "num_concept";

    /**
     * Clé primaire supplémentaire
     *
     * @var array
     */
    public static $primaryKeyAditional = [
        "num_object",
        "type_object"
    ];

    /**
     *
     * @var integer
     */
    protected $num_object = 0;

    /**
     *
     * @var integer
     */
    protected $type_object = 0;

    /**
     *
     * @var integer
     */
    protected $num_concept = 0;

    /**
     *
     * @var integer
     */
    protected $order_concept = 0;

    /**
     *
     * @var string
     */
    protected $comment = "";

    /**
     *
     * @var integer
     */
    protected $comment_visible_opac = 0;
}