<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubscribersProductOrm.php,v 1.5.2.1.2.1 2025/03/21 08:32:08 rtigero Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\Orm;

class SubscribersProductOrm extends Orm
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_subscribers_product";

	/**
	 * Primary Key
	 *
	 * @var string
	 */
	public static $idTableName = "id_subscriber_product";

	/**
	 *
	 * @var integer
	 */
	protected $id_subscriber_product = 0;

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
	protected $num_product = 0;

	/**
	 *
	 * @Relation 0n
	 * @Orm Pmb\DSI\Orm\ProductOrm
	 * @RelatedKey num_product
	 */
	protected $product = null;

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;
}