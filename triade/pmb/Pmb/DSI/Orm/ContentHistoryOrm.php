<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ContentHistoryOrm.php,v 1.4 2023/03/03 13:50:43 rtigero Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\Orm;

class ContentHistoryOrm extends Orm
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_content_history";

	/**
	 * Primary Key
	 *
	 * @var string
	 */
	public static $idTableName = "id_content_history";

	/**
	 *
	 * @var integer
	 */
	protected $id_content_history = 0;
	
	/**
	 *
	 * @var integer
	 */
	protected $type = 0;

	/**
	 *
	 * @var string
	 */
	protected $content = "";

	/**
	 *
	 * @var integer
	 */
	protected $num_diffusion_history = 0;

	/**
	 *
	 * @Relation 0n
	 * @Orm Pmb\DSI\Orm\DiffusionHistoryOrm
	 * @RelatedKey num_diffusion_history
	 */
	protected $diffusion_history = null;

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;
}