<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DashboardUsersGroupsOrm.php,v 1.1 2024/01/25 09:10:25 jparis Exp $
namespace Pmb\Dashboard\Orm;

use Pmb\Common\Orm\OrmManyToMany;

class DashboardUsersGroupsOrm extends OrmManyToMany
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dashboard_users_groups";

    public static $idsTableName = ["num_dashboard", "num_users_groups"];

	/**
	 *
	 * @var integer
	 */
	protected $num_dashboard = 0;

	/**
	 *
	 * @var integer
	 */
	protected $num_users_groups = 0;

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;
}