<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DiffusionHistoryOrm.php,v 1.5 2023/03/03 13:50:43 rtigero Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\Orm;

class DiffusionHistoryOrm extends Orm
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_diffusion_history";

	/**
	 * Primary Key
	 *
	 * @var string
	 */
	public static $idTableName = "id_diffusion_history";

    /**
     *
     * @var integer
     */
    protected $id_diffusion_history = 0;

	/**
	 *
	 * @var \DateTime
	 */
	protected $date = "";

	/**
	 *
	 * @var integer
	 */
	protected $total_recipients = 0;

	/**
	 *
	 * @var integer
	 */
	protected $num_diffusion = 0;
	
	/**
	 *
	 * @var integer
	 */
	protected $state = 0;

	/**
	 *
	 * @Relation 0n
	 * @Orm Pmb\DSI\Orm\DiffusionOrm
	 * @RelatedKey num_diffusion
	 */
	protected $diffusion = null;

	/**
	 *
	 * @Relation n0
	 * @Orm Pmb\DSI\Orm\ContentHistoryOrm
	 * @RelatedKey id_content_history
	 * @Table dsi_content_history
	 * @ForeignKey num_diffusion_history
	 */
	protected $contents = null;

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;
}