<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EventProductOrm.php,v 1.2 2023/02/01 10:53:10 jparis Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\OrmManyToMany;

class EventProductOrm extends OrmManyToMany
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_event_product";

	/**
	 *
	 * @var integer
	 */
	public $num_event = 0;

	/**
	 *
	 * @var integer
	 */
	public $num_product = 0;

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;
}