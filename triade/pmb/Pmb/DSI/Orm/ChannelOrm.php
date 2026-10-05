<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ChannelOrm.php,v 1.6 2023/02/02 09:52:22 rtigero Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\Orm;

class ChannelOrm extends Orm
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_channel";

	/**
	 * Primary Key
	 *
	 * @var string
	 */
	public static $idTableName = "id_channel";

	/**
	 *
	 * @var integer
	 */
	protected $id_channel = 0;

	/**
	 *
	 * @var string
	 */
	protected $name = "";

	/**
	 *
	 * @var string
	 */
	protected $settings = "";

	/**
	 *
	 * @var integer
	 */
	protected $type = 0;
	
	/**
	 *
	 * @var boolean
	 */
	protected $model = false;
	
	/**
	 *
	 * @var integer
	 */
	protected $num_model = 0;

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;
	
	protected static $relations = array();
}