<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ProductOrm.php,v 1.8 2023/02/02 09:52:22 rtigero Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\Orm;

class ProductOrm extends Orm
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_product";

	/**
	 * Primary Key
	 *
	 * @var string
	 */
	public static $idTableName = "id_product";

	/**
	 *
	 * @var integer
	 */
	protected $id_product = 0;

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
	protected $num_status = 1;

	/**
	 *
	 * @Relation 0n
	 * @Orm Pmb\DSI\Orm\ProductStatusOrm
	 * @RelatedKey num_status
	 */
	protected $status = null;

	/**
	 *
	 * @var integer
	 */
	protected $num_subscriber_list = 0;

	/**
	 *
	 * @Relation 0n
	 * @Orm Pmb\DSI\Orm\SubscriberListOrm
	 * @RelatedKey num_subscriber_list
	 * @Table dsi_subscriber_list
	 * @ForeignKey num_subscriber_list
	 */
	protected $subscriber_list = null;

	/**
	 *
	 * @Relation nn
	 * @Orm Pmb\DSI\Orm\EventProductOrm
	 * @TableLink dsi_event_product
	 * @ForeignKey num_event
	 * @RelatedKey num_product
	 */
	protected $events = null;
	
	/**
	 *
	 * @Relation nn
	 * @Orm Pmb\DSI\Orm\DiffusionProductOrm
	 * @TableLink dsi_diffusion_product
	 * @ForeignKey num_diffusion
	 * @RelatedKey num_product
	 */
	protected $productDiffusions = null;

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;
	
}