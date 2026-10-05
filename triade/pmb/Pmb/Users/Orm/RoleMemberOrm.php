<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RoleMemberOrm.php,v 1.1.2.3 2024/11/15 13:21:48 dgoron Exp $
namespace Pmb\Users\Orm;

use Pmb\Common\Orm\Orm;

class RoleMemberOrm extends Orm
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "users_roles_members";

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
	protected $type_member = "";

	/**
	 *
	 * @var integer
	 */
	protected $num_member = 0;
	
	/**
	 *
	 * @var integer
	 */
	protected $num_role = 0;
}