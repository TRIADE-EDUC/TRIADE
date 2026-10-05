<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubscriberListOrm.php,v 1.9 2023/02/02 09:52:22 rtigero Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\Orm;

class SubscriberListOrm extends Orm
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_subscriber_list";

	/**
	 * Primary Key
	 *
	 * @var string
	 */
	public static $idTableName = "id_subscriber_list";

	/**
	 *
	 * @var integer
	 */
	protected $id_subscriber_list = 0;

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
	protected $num_parent = 0;

	/**
	 *
	 * @Relation 0n
	 * @Orm Pmb\DSI\Orm\SubscriberListOrm
	 * @RelatedKey num_parent
	 */
	protected $parent_subscriber_list = null;

	/**
	 *
	 * @var integer
	 */
	protected $num_model = 0;

	/**
	 *
	 * @Relation 0n
	 * @Orm Pmb\DSI\Orm\SubscriberListOrm
	 * @RelatedKey num_model
	 */
	protected $subscriber_list_model = null;



	/**
	 *
	 * @Relation nn
	 * @Orm Pmb\DSI\Orm\SubscribersOrm
	 * @TableLink dsi_subscriber_list_content
	 * @ForeignKey num_subscriber
	 * @RelatedKey num_subscriber_list
	 */
	protected $subscribers = null;

	/**
	 *
	 * @Relation nn
	 * @Orm Pmb\DSI\Orm\SubscriberListContentOrm
	 * @TableLink dsi_subscriber_list_content
	 * @ForeignKey num_subscriber
	 * @RelatedKey num_subscriber_list
	 */
	protected $subscriberListContent = null;

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;

	/**
	 */
	protected static $relations = array();
}