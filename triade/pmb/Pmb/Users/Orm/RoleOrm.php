<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RoleOrm.php,v 1.1.2.3 2024/11/15 13:21:48 dgoron Exp $
namespace Pmb\Users\Orm;

use Pmb\Common\Orm\Orm;

class RoleOrm extends Orm
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "users_roles";

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
	protected $name = "";

	/**
	 * 
	 * @var string
	 */
	protected $comment = "";
	
}