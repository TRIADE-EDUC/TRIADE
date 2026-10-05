<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DashboardController.php,v 1.21 2024/03/14 10:24:27 jparis Exp $
namespace Pmb\Dashboard\Controller;

use Pmb\Common\Controller\Controller;
use Pmb\Common\Helper\Helper;
use Pmb\Common\Orm\UsersGroupsOrm;
use Pmb\Dashboard\Models\DashboardModel;
use Pmb\Dashboard\Models\DashboardUsersGroupsModel;
use Pmb\Dashboard\Models\DashboardWidgetModel;
use Pmb\Dashboard\Models\WidgetModel;
use Pmb\Dashboard\Views\DashboardView;

class DashboardController extends Controller
{
    /**
     * Affiche la vue du tableau de bord.
     *
     * @return void
     */
    public function proceed()
    {
        $data = $this->getDataView();

        $view = new DashboardView("dashboard/dashboard", $data);
        print $view->render();
    }

    /**
     * Récupère les données pour la vue
     *
     * @return array
     */
    private function getDataView()
    {
        global $msg, $PMBuserid, $pmb_url_base;

        $data = array();

        $widget = new WidgetModel();
        $data["widgets"] = Helper::toArray($widget->getListByCurrentUserId());
        $data["dashboards"] = $this->getFormatedDashboardList();

        $data["groups"] = [
            ["grp_id" => -1, "grp_name" => $msg["form_share_all_dashboard"]],
            ["grp_id" => 0, "grp_name" => $msg["admin_usr_grp_non_aff"]]
        ];

        $data["users"] = $this->getUserList();

        $usersOrm = UsersGroupsOrm::findAll("grp_name");

        for ($index = 0; $index < count($usersOrm); $index++) {
            $data["groups"][] = ["grp_id" => $usersOrm[$index]->grp_id, "grp_name" => $usersOrm[$index]->grp_name];
        }

        $data["current_user"] = intval($PMBuserid);
        $data["current_group"] = intval(\user::get_param($data["current_user"], "grp_num"));

        $data["widget_types"] = WidgetModel::getWidgetTypeList();

        $data["url_webservice"] = $pmb_url_base . "rest.php/dashboard/";
        $data["pmb_url_base"] = $pmb_url_base;
        $data["widget_refresh_time"] = 60;

        return $data;
    }

    public function getUserList() 
    {
        $users = [];

        $query = "SELECT userid, username FROM users";
        $result = pmb_mysql_query($query);

        while ($row = pmb_mysql_fetch_assoc($result)) {
            $users[$row["userid"]] = $row["username"];
        }

        return $users;
    }

    public function getFormatedDashboardList()
    {
        $dashboard = new DashboardModel();
        $dashboards = Helper::toArray($dashboard->getListByCurrentUserId());

        $dashboardUsersGroups = new DashboardUsersGroupsModel();
        $dashboardWidget = new DashboardWidgetModel();

        foreach ($dashboards as &$element) {

            if (!isset($element["layout"])) {
                $element["layout"] = [];
            }
            if (!isset($element["dashboardUsersGroups"])) {
                $element["dashboardUsersGroups"] = [];
            }
            if (!isset($element["widgets"])) {
                $element["widgets"] = [];
            }

            $tempDashboardUsersGroups = $dashboardUsersGroups->getList(["num_dashboard" => $element["idDashboard"]], true);

            foreach ($tempDashboardUsersGroups as $value) {
                $element["dashboardUsersGroups"][] = $value["numUsersGroups"];
            }

            $tempDashboardWidget = $dashboardWidget->getList(["num_dashboard" => $element["idDashboard"]], true);

            foreach ($tempDashboardWidget as $value) {
                $element["layout"][] = $value["dashboardWidgetSettings"]["position"];

                $tempWidget = new WidgetModel($value["dashboardWidgetSettings"]["position"]["i"]);

                $dashboardWidgetSettings = ["dashboardWidgetSettings" => $value["dashboardWidgetSettings"]];
                $element["widgets"][] = array_merge(Helper::toArray($tempWidget), $dashboardWidgetSettings);
            }
        }

        return $dashboards;
    }

    /**
     * Récupère la liste des dashboards formatés
     *
     * @return void
     */
    public function getList()
    {
        $this->ajaxJsonResponse($this->getFormatedDashboardList());
    }

    /**
     * Sauvegarde les données et renvoie une réponse JSON.
     *
     * @return void
     */
    public function save()
    {
        $this->data->idDashboard = intval($this->data->idDashboard);

        $dashboard = new DashboardModel($this->data->idDashboard);

        $check = $dashboard->check($this->data);


        if ($check['error']) {
            $this->ajaxError($check['errorMessage']);
            exit();
        }

        $dashboard->setFromForm($this->data);

        if ($dashboard->idDashboard) {
            $dashboard->update();
        } else {
            $dashboard->create();
        }

        $dashboardUsersGroups = new DashboardUsersGroupsModel();
        $dashboardUsersGroups->numDashboard = $dashboard->idDashboard;

        $dashboardUsersGroups->deleteByIdDashboard();

        if (in_array(-1, $this->data->dashboardUsersGroups)) {
            $this->data->dashboardUsersGroups = [-1];
        }

        foreach ($this->data->dashboardUsersGroups as $usersGroups) {
            $dashboardUsersGroups = new DashboardUsersGroupsModel();

            $dashboardUsersGroups->numDashboard = $dashboard->idDashboard;
            $dashboardUsersGroups->numUsersGroups = intval($usersGroups);

            $dashboardUsersGroups->create();
        }

        $this->ajaxJsonResponse($dashboard);
    }

    /**
     * Supprime un dashboard de la base de données.
     *
     * @return void
     */
    public function delete()
    {
        $this->data->idDashboard = intval($this->data->idDashboard);

        $dashboard = new DashboardModel($this->data->idDashboard);
        $result = $dashboard->delete();

        if ($result['error']) {
            $this->ajaxError($result['errorMessage']);
            exit();
        }
        $this->ajaxJsonResponse([
            'success' => true
        ]);
    }

    /**
     * Duplique un dashboard de la base de données.
     *
     * @return void
     */
    public function duplicate()
    {
        global $PMBuserid;

        $this->data->idDashboard = intval($this->data->idDashboard);

        // On duplique le dashboard
        $dashboardModel = new DashboardModel($this->data->idDashboard);
        $dashboardModel->idDashboard = 0;
        $dashboardModel->numUser = intval($PMBuserid);
        $dashboardModel->dashboardName = "Copy of " . $dashboardModel->dashboardName;

        $dashboardModel->create();

        // On duplique les liens entre le dashboard et les widgets
        $dashboardWidgetModel = new DashboardWidgetModel();
        $dashboardWidgetList = $dashboardWidgetModel->getList(["num_dashboard" => $this->data->idDashboard], false);

        foreach($dashboardWidgetList as $dashboardWidget) {
            $dashboardWidget->numDashboard = $dashboardModel->idDashboard;
            $dashboardWidget->dashboardWidgetSettings = json_encode($dashboardWidget->dashboardWidgetSettings);
            $dashboardWidget->update();
        }

        // On duplique les liens entre le dashboard et les groupes d'utilisateurs
        $dashboardUserGroupModel = new DashboardUsersGroupsModel();
        $dashboardUserGroupList = $dashboardUserGroupModel->getList(["num_dashboard" => $this->data->idDashboard], false);

        foreach($dashboardUserGroupList as $dashboardUserGroup) {
            $dashboardUserGroup->numDashboard = $dashboardModel->idDashboard;
            $dashboardUserGroup->create();
        }

        $this->ajaxJsonResponse([
            'success' => true
        ]);
    }

    /**
     * Sauvegarde le layout du tableau de bord.
     *
     * @return void
     */
    public function saveLayout()
    {
        global $PMBuserid;

        $this->data->idDashboard = intval($this->data->idDashboard);

        $dashboard = new DashboardModel($this->data->idDashboard);

        // Si on est pas propriétaire du dashboard et qu'il n'est pas editable, on renvoi une erreur
        if ($dashboard->numUser != $PMBuserid && !$dashboard->dashboardEditable) {
            $this->ajaxError("msg:form_not_allowed");
            exit();
        }

        $dashboardWidget = new DashboardWidgetModel();
        $dashboardWidget->numDashboard = $this->data->idDashboard;
        $dashboardWidget->deleteByIdDashboard();

        foreach ($this->data->layout as $value) {
            $dashboardWidget->numDashboard = $this->data->idDashboard;
            $dashboardWidget->numWidget = $value->i;

            foreach ($this->data->dashboardWidgets as $element) {
                if ($element->numWidget == $value->i) {
                    $dashboardWidget->dashboardWidgetSettings = $element->dashboardWidgetSettings;
                    $dashboardWidget->dashboardWidgetSettings->position = $value;
                    $dashboardWidget->dashboardWidgetSettings = json_encode($dashboardWidget->dashboardWidgetSettings, true);
                    break;
                }
            }

            $dashboardWidget->update();
        }

        $this->ajaxJsonResponse([
            'success' => true
        ]);
    }

    public function saveDashboardWidget()
    {
        global $PMBuserid;

        $this->data->idDashboard = intval($this->data->idDashboard);

        $dashboard = new DashboardModel($this->data->idDashboard);

        // Si on est pas propriétaire du dashboard et qu'il n'est pas editable, on renvoi une erreur
        if ($dashboard->numUser != $PMBuserid && !$dashboard->dashboardEditable) {
            $this->ajaxError("msg:form_not_allowed");
            exit();
        }

        $widget = new WidgetModel(intval($this->data->widget->idWidget));

        // Si on est propriétaire ou que le widget est editable alors on peut modifier le contenu
        if ($widget->numUser == $PMBuserid || $widget->widgetEditable) {
            $widget->setFromForm($this->data->widget);

            if ($widget->idWidget) {
                $widget->update();
            } else {
                $widget->create();
            }
        }

        $dashboardWidget = new DashboardWidgetModel();
        $dashboardWidget->numDashboard = $this->data->idDashboard;
        $dashboardWidget->numWidget = $this->data->widget->idWidget;
        $dashboardWidget->dashboardWidgetSettings = $this->data->widget->dashboardWidgetSettings;
        $dashboardWidget->dashboardWidgetSettings = json_encode($dashboardWidget->dashboardWidgetSettings, true);

        $dashboardWidget->update();

        $this->ajaxJsonResponse([
            'success' => true
        ]);
    }

    public function refreshWidget()
    {
        $this->data->idWidget = intval($this->data->idWidget);
        $this->data->idDashboard = intval($this->data->idDashboard);

        $widget = new WidgetModel($this->data->idWidget);
        $dashboardWidget = new DashboardWidgetModel($this->data->idDashboard, $this->data->idWidget);

        if (empty($dashboardWidget->dashboardWidgetSettings)) {
            $this->ajaxJsonResponse([
                'error' => true,
                'errorMessage' => '',
            ]);
            exit();
        }

        $this->ajaxJsonResponse(
            array_merge(
                Helper::toArray($widget),
                ["dashboardWidgetSettings" => $dashboardWidget->dashboardWidgetSettings]
            )
        );
    }
}
