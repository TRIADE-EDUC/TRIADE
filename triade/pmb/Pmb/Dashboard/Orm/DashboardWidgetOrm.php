<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DashboardWidgetOrm.php,v 1.1 2024/01/25 09:10:25 jparis Exp $
namespace Pmb\Dashboard\Orm;

use Pmb\Common\Orm\OrmManyToMany;

class DashboardWidgetOrm extends OrmManyToMany
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dashboard_widget";

    public static $idsTableName = ["num_dashboard", "num_widget"];

	/**
	 *
	 * @var integer
	 */
	protected $num_dashboard = 0;

	/**
	 *
	 * @var integer
	 */
	protected $num_widget = 0;

    /**
	 *
	 * @var string
	 */
	protected $dashboard_widget_settings = "";

	/**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;
}