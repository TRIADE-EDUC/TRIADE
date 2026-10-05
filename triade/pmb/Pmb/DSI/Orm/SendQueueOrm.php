<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SendQueueOrm.php,v 1.1.4.2.2.1 2025/01/31 11:15:26 jparis Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\Orm;

class SendQueueOrm extends Orm
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_send_queue";

	/**
	 * Primary Key
	 *
	 * @var string
	 */
	public static $idTableName = "id_send_queue";

	/**
	 *
	 * @var integer
	 */
	protected $id_send_queue = 0;

	/**
	 *
	 * @var integer
	 */
	protected $channel_type = 0;

	/**
	 *
	 * @var string
	 */
	protected $settings = "";

	/**
	 *
	 * @var integer
	 */
	protected $num_subscriber_diffusion = 0;

	/**
	 *
	 * @var integer
	 */
	protected $num_subscriber_empr = 0;

	/**
	 *
	 * @var integer
	 */
	protected $num_diffusion_history = 0;

	/**
	 *
	 * @var integer
	 */
	protected $flag = 0;

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;
}