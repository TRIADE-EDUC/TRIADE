<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DashboardModel.php,v 1.5 2024/01/31 10:17:12 jparis Exp $

namespace Pmb\Dashboard\Models;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Models\Model;
use Pmb\Common\Orm\UsersGroupsOrm;
use Pmb\Dashboard\Orm\DashboardUsersGroupsOrm;
use Pmb\Dashboard\Orm\DashboardWidgetOrm;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class DashboardModel extends Model
{
    protected $ormName = "Pmb\Dashboard\Orm\DashboardOrm";

    public $idDashboard = 0;
    public $dashboardName = "";
    public $dashboardEditable = 0;
    public $numUser = 0;

    public function __construct(int $id = 0)
    {
        $this->id = intval($id);
        $this->fetchData();
    }

    /**
     * Crée un nouveau dashboard dans la base de données.
     * 
     * @return void
     */
    public function create() 
    {
        $orm = new $this->ormName();

        $orm->dashboard_name = $this->dashboardName;
        $orm->dashboard_editable = $this->dashboardEditable;
        $orm->num_user = $this->numUser;

        $orm->save();

        $this->idDashboard = $orm->id_dashboard;
    }

    /**
     * Met à jour les informations du dashboard dans la base de données.
     * 
     * @return void
     */
    public function update() 
    {
        $orm = new $this->ormName($this->idDashboard);

        $orm->dashboard_name = $this->dashboardName;
        $orm->dashboard_editable = $this->dashboardEditable;
        $orm->num_user = $this->numUser;

        $orm->save();
    }

    /**
     * Supprime le dashboard de la base de données.
     * 
     * @return array
     */
    public function delete()
    {
        global $PMBuserid;

        try {
            if($this->numUser != intval($PMBuserid)) {
                return [
                    'error' => true,
                    'errorMessage' => 'msg:form_not_allowed',
                ];
            }

            $orm = new $this->ormName($this->idDashboard);
            $orm->delete();

            DashboardUsersGroupsOrm::deleteWhere("num_dashboard", $this->idDashboard);
            DashboardWidgetOrm::deleteWhere("num_dashboard", $this->idDashboard);

        } catch (\Exception $e) {
            return [
                'error' => true,
                'errorMessage' => $e->getMessage(),
            ];
        }

        return [
            'error' => false,
            'errorMessage' => '',
        ];
    }

    /**
     * Vérifies les données du formulaire
     *
     * @param object $data Données vérifiées
     * @return array
     */
    public function check(object $data)
    {
        global $PMBuserid;

        if(empty($data->dashboardName) || !is_string($data->dashboardName)) {
            return [
                'error' => true,
                'errorMessage' => 'msg:form_data_errors',
            ];
        }

        if($data->numUser != intval($PMBuserid)) {
            return [
                'error' => true,
                'errorMessage' => 'msg:form_not_allowed',
            ];
        }

        return [
            'error' => false,
            'errorMessage' => '',
        ];
    }

    /**
     * Définit les propriétés de l'objet à partir des données du formulaire.
     *
     * @param mixed $data Les données du formulaire à définir
     */
    public function setFromForm($data)
    {
        global $PMBuserid;

        $this->dashboardName = $data->dashboardName;
        $this->dashboardEditable = $data->dashboardEditable;
        $this->numUser = intval($PMBuserid);
    }

    /**
     * Obtenir la liste des tableaux de bord pour l'utilisateur actuel.
     *
     * @return array Liste de tableaux de bord.
     */
    public function getListByCurrentUserId()
    {
        global $PMBuserid;

        $usersGroupsId = intval(\user::get_param(intval($PMBuserid), "grp_num"));
        $dashboardList = [];

        $list = $this->getList();
        foreach($list as $dashboard) {
            // Si $PMBuserid est le propriétaire
            if($dashboard->numUser == intval($PMBuserid)) {
                $dashboardList[] = $dashboard;
                continue;
            }

            $dashboardUsersGroups = new DashboardUsersGroupsModel();
            $dashboardUsersGroupsList = $dashboardUsersGroups->getList(["num_dashboard" => $dashboard->idDashboard]);
            
            foreach($dashboardUsersGroupsList as $usersGroups) {
                // Si le dashboard est partagé avec tous les groupes
                if($usersGroups->numUsersGroups == -1) {
                    $dashboardList[] = $dashboard;
                    continue;
                }

                // Si le dashboard est partagé avec le groupe $usersGroupsId
                if($usersGroups->numUsersGroups == $usersGroupsId) {
                    $dashboardList[] = $dashboard;
                    continue;
                }
            }
        }

        return $dashboardList;
    }

    /**
     * Obtenir la liste des widgets de l'utilisateur actuel.
     *
     * @return array Liste de widgets.
     */
    public function getWidgetList() 
    {
        $dashboardWidget = new DashboardWidgetModel();
        $dashboardWidgetList = $dashboardWidget->getList(["num_dashboard" => $this->idDashboard], true);

        $widgetList = [];
        foreach ($dashboardWidgetList as $element) {
            $widgetList[] = Helper::toArray(new WidgetModel($element["numWidget"]));
        }

        return $widgetList;
    }
}

