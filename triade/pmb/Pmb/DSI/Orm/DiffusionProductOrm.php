<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DiffusionProductOrm.php,v 1.1.8.1 2025/05/26 12:13:32 rtigero Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\OrmManyToMany;

class DiffusionProductOrm extends OrmManyToMany
{

	/**
	 *
	 * @var array
	 */
	public static $idsTableName = [
		'num_diffusion',
		'num_product'
	];

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_diffusion_product";

	/**
	 *
	 * @var integer
	 */
	protected $num_diffusion = 0;

	/**
	 *
	 * @var integer
	 */
	protected $num_product = 0;

	/**
	 *
	 * @var integer
	 */
	protected $active = 0;

	/**
	 *
	 * @var \DateTime
	 */
	protected $last_diffusion = "";

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;
}
