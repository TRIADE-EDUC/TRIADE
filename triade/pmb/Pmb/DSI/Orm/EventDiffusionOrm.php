<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EventDiffusionOrm.php,v 1.1 2022/12/16 10:54:06 jparis Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\OrmManyToMany;

class EventDiffusionOrm extends OrmManyToMany
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_event_diffusion";

	/**
	 *
	 * @var integer
	 */
	protected $num_event = 0;

	/**
	 *
	 * @var integer
	 */
	protected $num_diffusion = 0;

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;
}