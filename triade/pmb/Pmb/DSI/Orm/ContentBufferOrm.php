<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ContentBufferOrm.php,v 1.2 2023/07/28 11:49:28 qvarin Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\Orm;

class ContentBufferOrm extends Orm
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_content_buffer";

	/**
	 * Primary Key
	 *
	 * @var string
	 */
	public static $idTableName = "id_content_buffer";

	/**
	 *
	 * @var integer
	 */
	protected $id_content_buffer = 0;
	
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
	protected $modified = 0;

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