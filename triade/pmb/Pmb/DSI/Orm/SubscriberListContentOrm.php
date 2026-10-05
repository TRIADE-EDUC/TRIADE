<?php
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\OrmManyToMany;

class SubscriberListContentOrm extends OrmManyToMany
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_subscriber_list_content";

	/**
	 *
	 * @var integer
	 */
	protected $num_subscriber = 0;

	/**
	 *
	 * @var integer
	 */
	protected $num_subscriber_list = 0;

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;
}

