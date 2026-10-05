<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RoleRightOrm.php,v 1.1.2.3 2024/11/15 13:21:48 dgoron Exp $
namespace Pmb\Users\Orm;

use Pmb\Common\Orm\Orm;

class RoleRightOrm extends Orm
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "users_roles_rights";

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
	protected $component = "";

	/**
	 * 
	 * @var string
	 */
	protected $module = "";
	
	/**
	 *
	 * @var string
	 */
	protected $categ = "";
	
	/**
	 *
	 * @var string
	 */
	protected $sub = "";
	
	/**
	 *
	 * @var string
	 */
	protected $url_extra = "";
	
	/**
	 *
	 * @var string
	 */
	protected $action = "";
	
	/**
	 *
	 * @var integer
	 */
	protected $visible = 1;
	
	/**
	 *
	 * @var integer
	 */
	protected $privilege = 0;
	
	/**
	 *
	 * @var integer
	 */
	protected $log = 0;
	
	/**
	 *
	 * @var integer
	 */
	protected $num_role = 0;
	
}