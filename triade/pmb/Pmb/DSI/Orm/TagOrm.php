<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: TagOrm.php,v 1.5 2023/02/02 09:52:22 rtigero Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\Orm;

class TagOrm extends Orm
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_tag";

	/**
	 * Primary Key
	 *
	 * @var string
	 */
	public static $idTableName = "id_tag";

	/**
	 *
	 * @var integer
	 */
	public $id_tag = 0;

	/**
	 *
	 * @var string
	 */
	public $name = "";

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;
}