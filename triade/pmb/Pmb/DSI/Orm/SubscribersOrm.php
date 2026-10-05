<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubscribersOrm.php,v 1.6 2023/02/02 09:52:22 rtigero Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\Orm;

class SubscribersOrm extends Orm
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_subscribers";

	/**
	 * Primary Key
	 *
	 * @var string
	 */
	public static $idTableName = "id_subscriber";

	/**
	 *
	 * @var integer
	 */
	public $id_subscriber = 0;

	/**
	 *
	 * @var string
	 */
	public $name = "";

	/**
	 *
	 * @var string
	 */
	public $settings = "";

	/**
	 *
	 * @var integer
	 */
	public $type = 0;

	/**
	 *
	 * @var integer
	 */
	protected $update_type = 0;

	/**
	 *
	 * @Relation nn
	 * @Orm Pmb\DSI\Orm\SubscriberListOrm
	 * @TableLink dsi_subscriber_list_content
	 * @ForeignKey num_subscriber_list
	 * @RelatedKey num_subscriber
	 */
	protected $subscriber_lists = null;

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;

	protected function checkBeforeDelete()
	{
		return true;
	}
}