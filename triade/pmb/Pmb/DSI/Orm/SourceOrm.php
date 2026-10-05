<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SourceOrm.php,v 1.4 2022/10/26 15:02:04 jparis Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\Orm;

class SourceOrm extends Orm
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_source";

	/**
	 * Primary Key
	 *
	 * @var string
	 */
	public static $idTableName = "id_source";

	/**
	 *
	 * @var integer
	 */
	protected $id_source = 0;
	
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
	protected $type = 0;

	/**
	 *
	 * @var integer
	 */
	protected $num_model = 0;

	/**
	 *
	 * @Relation 0n
	 * @Orm Pmb\DSI\Orm\SourceOrm
	 * @RelatedKey num_model
	 */
	protected $source_model = null;

	/**
	 *
	 * @var integer
	 */
	protected $num_tag = 0;

	/**
	 *
	 * @Relation 0n
	 * @Orm Pmb\DSI\Orm\TagOrm
	 * @RelatedKey num_tag
	 */
	protected $tag = null;

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;
}