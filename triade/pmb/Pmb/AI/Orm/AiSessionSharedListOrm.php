<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AiSessionSharedListOrm.php,v 1.1.2.1 2024/06/19 13:40:39 qvarin Exp $

namespace Pmb\AI\Orm;

use Pmb\Common\Orm\OrmManyToMany;

class AiSessionSharedListOrm extends OrmManyToMany
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "ai_session_shared_list";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "";

    /**
     *
     * @var array
     */
    public static $idsTableName = [
        'num_ai_session_semantique',
        'num_empr',
        'num_shared_list'
    ];

    /**
     *
     *
     * @var integer
     */
    protected $num_ai_session_semantique = 0;

    /**
     *
     *
     * @var integer
     */
    protected $num_empr = 0;

    /**
     *
     *
     * @var integer
     */
    protected $num_shared_list = 0;


    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;
}