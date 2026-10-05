<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EntitiesTagsOrm.php,v 1.1 2023/02/02 09:52:22 rtigero Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\OrmManyToMany;

class EntitiesTagsOrm extends OrmManyToMany
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_entities_tags";

	/**
	 *
	 * @var integer
	 */
	protected $num_tag = 0;

	/**
	 *
	 * @var integer
	 */
	protected $num_entity = 0;

	/**
	 *
	 * @var integer
	 */
	protected $type = 0;

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;
}