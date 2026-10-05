<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EventOrm.php,v 1.6 2023/02/02 09:52:22 rtigero Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\Orm;

class EventOrm extends Orm
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_event";

	/**
	 * Primary Key
	 *
	 * @var string
	 */
	public static $idTableName = "id_event";

	/**
	 *
	 * @var integer
	 */
	protected $id_event = 0;

	/**
	 *
	 * @var string
	 */
	protected $name = "";

	/**
	 *
	 * @var boolean
	 */
	protected $model = false;

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
	 * @var integer
	 */
	protected $num_model = 0;

	/**
	 *
	 * @Relation 0n
	 * @Orm Pmb\DSI\Orm\EventOrm
	 * @RelatedKey num_model
	 */
	protected $event_model = null;

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;
	
	protected static $relations = array();
}