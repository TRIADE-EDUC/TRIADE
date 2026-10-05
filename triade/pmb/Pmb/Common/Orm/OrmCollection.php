<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: OrmCollection.php,v 1.1 2022/11/09 08:10:04 rtigero Exp $
namespace Pmb\Common\Orm;

/**
 */
class OrmCollection
{

	private static $orm = array();

	private function __construct()
	{}

	public static function getInstance(string $ormName, int $id)
	{
		if (! isset(static::$orm[$ormName])) {
			static::$orm[$ormName] = array();
		}
		if (! isset(static::$orm[$ormName][$id])) {
			static::$orm[$ormName][$id] = new $ormName($id);
		}
		return static::$orm[$ormName][$id];
	}
}