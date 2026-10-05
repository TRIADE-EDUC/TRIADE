<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DiffusionDescriptorsOrm.php,v 1.1 2023/05/24 12:48:26 qvarin Exp $

namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\OrmManyToMany;

class DiffusionDescriptorsOrm extends OrmManyToMany
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "dsi_diffusion_descriptors";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "num_diffusion";

    /**
     * Clé primaire supplémentaire
     *
     * @var array
     */
    public static $primaryKeyAditional = ["num_noeud"];

    /**
     *
     * @var integer
     */
    public $num_diffusion = 0;

    /**
     *
     * @var integer
     */
    public $num_noeud = 0;

    /**
     *
     * @var integer
     */
    public $diffusion_descriptor_order = 0;

    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;
}
