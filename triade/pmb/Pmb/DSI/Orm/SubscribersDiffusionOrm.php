<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubscribersDiffusionOrm.php,v 1.5.6.1 2025/03/21 08:32:08 rtigero Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\Orm;

class SubscribersDiffusionOrm extends Orm
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_subscribers_diffusion";

	/**
	 * Primary Key
	 *
	 * @var string
	 */
	public static $idTableName = "id_subscriber_diffusion";

	/**
	 *
	 * @var integer
	 */
	protected $id_subscriber_diffusion = 0;

	/**
	 *
	 * @var string
	 */
	public $name = "";

	/**
	 *
	 * @var string
	 */
	protected $settings = "";

	/**
	 *
	 * @var integer
	 */
	public $type = 0;

	/**
	 *
	 * @var integer
	 */
	public $update_type = 0;

	/**
	 *
	 * @var integer
	 */
	protected $num_diffusion = 0;

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;
}