<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DashboardWidgetModel.php,v 1.5 2024/02/02 16:17:13 jparis Exp $

namespace Pmb\Dashboard\Models;

use Pmb\Common\Models\Model;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class DashboardWidgetModel extends Model
{
    protected $ormName = "Pmb\Dashboard\Orm\DashboardWidgetOrm";

    public $numDashboard = 0;
    public $numWidget = 0;
    public $dashboardWidgetSettings = "";

	public function __construct(int $numDashboard = 0, int $numWidget = 0)
	{
		$this->numDashboard = $numDashboard;
		$this->numWidget = $numWidget;

		$this->fetchData();
		
		$this->dashboardWidgetSettings = json_decode($this->dashboardWidgetSettings);
	}

	/**
	 * Update un DashboardWidget dans la base de données.
	 * 
	 * @return void
	 */
	public function update()
	{
		$orm = new $this->ormName();
		$orm->num_dashboard = $this->numDashboard;
		$orm->num_widget = $this->numWidget;
		$orm->dashboard_widget_settings = $this->dashboardWidgetSettings;
		$orm->save();
	}

	/**
	 * Supprime les DashboardWidget par l'id d'un dashboard.
	 * 
	 * @return void
	 */
	public function deleteByIdDashboard()
	{
		$this->ormName::deleteWhere("num_dashboard", $this->numDashboard);
	}
}

