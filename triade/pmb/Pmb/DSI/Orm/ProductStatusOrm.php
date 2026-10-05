<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ProductStatusOrm.php,v 1.5 2022/10/19 15:14:21 rtigero Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\Orm;

class ProductStatusOrm extends Orm
{

	/**
	 * Clé primaire non supprimable
	 *
	 * @var array
	 */
	public static $primaryKeyNotDeletable = [
		1
	];

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_product_status";

	/**
	 * Primary Key
	 *
	 * @var string
	 */
	public static $idTableName = "id_product_status";

	/**
	 *
	 * @var int
	 */
	protected $id_product_status = 0;

	/**
	 *
	 * @var string
	 */
	protected $name = "";

	/**
	 *
	 * @var boolean
	 */
	protected $active = false;

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;
}