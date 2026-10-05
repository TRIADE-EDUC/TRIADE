<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SourcesORM.php,v 1.2 2022/12/07 16:32:48 qvarin Exp $
namespace Pmb\Thumbnail\Orm;

use Pmb\Common\Orm\Orm;

class SourcesORM extends Orm
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "thumbnail_sources";

	/**
	 * Primary Key
	 *
	 * @var string
	 */
	public static $idTableName = "id";

	/**
	 * 
	 * @var integer
	 */
	protected $id = 0;

	/**
	 * 
	 * @var string
	 */
	protected $class = "";

	/**
	 * 
	 * @var string
	 */
	protected $settings = "";

	/**
	 * 
	 * @var integer
	 */
	protected $active = 0;
	
	public function getSettings()
	{
		if (empty($this->settings)) {
			return [];
		}
		
		return \encoding_normalize::json_decode($this->settings, true);
	}
}