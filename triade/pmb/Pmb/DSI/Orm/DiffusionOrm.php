<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DiffusionOrm.php,v 1.14 2023/05/24 12:48:26 qvarin Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\Orm;

class DiffusionOrm extends Orm
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_diffusion";

	/**
	 * Primary Key
	 *
	 * @var string
	 */
	public static $idTableName = "id_diffusion";

	/**
	 *
	 * @var integer
	 */
	protected $id_diffusion = 0;

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
	 * @Orm Pmb\DSI\Orm\DiffusionStatusOrm
	 * @RelatedKey num_status
	 */
	protected $status = null;

    /**
     *
     * @var integer
     */
    protected $automatic = 0;

	/**
	 *
	 * @var integer
	 */
	protected $num_subscriber_list = 0;

	/**
	 *
	 * @Relation 1n
	 * @Orm Pmb\DSI\Orm\SubscriberListOrm
	 * @RelatedKey num_subscriber_list
	 * @Table dsi_subscriber_list
	 * @ForeignKey num_subscriber_list
	 */
	protected $subscriber_list = null;

	/**
	 *
	 * @var integer
	 */
	protected $num_item = 0;

	/**
	 *
	 * @Relation 1n
	 * @Orm Pmb\DSI\Orm\ItemOrm
	 * @RelatedKey num_item
	 */
	protected $item = null;

	/**
	 *
	 * @var integer
	 */
	protected $num_view = 0;

	/**
	 *
	 * @Relation 1n
	 * @Orm Pmb\DSI\Orm\ViewOrm
	 * @RelatedKey num_view
	 */
	protected $view = null;

	/**
	 *
	 * @var integer
	 */
	protected $num_channel = 0;

	/**
	 *
	 * @Relation 1n
	 * @Orm Pmb\DSI\Orm\ChannelOrm
	 * @RelatedKey num_channel
	 */
	protected $channel = null;


	/**
	 *
	 * @Relation nn
	 * @Orm Pmb\DSI\Orm\EventDiffusionOrm
	 * @TableLink dsi_event_diffusion
	 * @ForeignKey num_event
	 * @RelatedKey num_diffusion
	 */
	protected $events = null;

	/**
	 *
	 * @Relation nn
	 * @Orm Pmb\DSI\Orm\DiffusionProductOrm
	 * @TableLink dsi_diffusion_product
	 * @ForeignKey num_product
	 * @RelatedKey num_diffusion
	 */
	protected $diffusionProducts = null;

	/**
	 *
	 * @Relation nn
	 * @Orm Pmb\DSI\Orm\DiffusionDescriptorsOrm
	 * @TableLink dsi_diffusion_descriptors
	 * @ForeignKey num_noeud
	 * @RelatedKey num_diffusion
	 */
	protected $diffusionDescriptors = null;

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;

	protected static $relations = array();
}