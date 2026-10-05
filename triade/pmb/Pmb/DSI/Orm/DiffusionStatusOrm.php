<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DiffusionStatusOrm.php,v 1.5 2022/10/19 15:14:21 rtigero Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\Orm;

class DiffusionStatusOrm extends Orm
{

	/**
	 * Clé primaire non supprimable
	 *
	 * @var array
	 */
	public static $primaryKeyNotDeletable = [
		1
	];

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_diffusion_status";

	/**
	 * Primary Key
	 *
	 * @var string
	 */
	public static $idTableName = "id_diffusion_status";

	/**
	 *
	 * @var int
	 */
	protected $id_diffusion_status = 0;

	/**
	 *
	 * @var string
	 */
	protected $name = "";

	/**
	 *
	 * @var boolean
	 */
	protected $active = false;

	/**
	 *
	 * @Relation n0
	 * @Orm Pmb\DSI\Orm\DiffusionOrm
	 * @RelatedKey id_diffusion
	 * @Table dsi_diffusion
	 * @ForeignKey num_status
	 */
	protected $diffusions = null;

	protected function checkBeforeDelete()
	{
		$this->diffusions = null;
		return empty($this->getRelated('diffusions'));
	}

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;
}