<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: WidgetModel.php,v 1.12 2024/02/26 14:28:55 dbellamy Exp $

namespace Pmb\Dashboard\Models;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Helper\ParserMessage;
use Pmb\Common\Models\Model;
use Pmb\Dashboard\Orm\DashboardOrm;
use Pmb\Dashboard\Orm\DashboardWidgetOrm;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class WidgetModel extends Model
{
    use ParserMessage;

    protected $ormName = "Pmb\Dashboard\Orm\WidgetOrm";

    public $idWidget = 0;
    public $widgetName = "";
    public $widgetEditable = 0;
    public $widgetType = "";
    public $numUser = "";
    public $widgetShareable = 0;
    public $widgetShared = 0;
    public $widgetSettings = "";

    public function __construct(int $id = 0)
    {
        $this->id = intval($id);
        $this->fetchData();

        $this->widgetShared = $this->isShared();
        $this->widgetSettings = json_decode($this->widgetSettings, true);

    }

    /**
     * Crée un nouveau widget dans la base de données.
     *
     * @return void
     */
    public function create()
    {
        $orm = new $this->ormName();

        $orm->widget_name = $this->widgetName;
        $orm->widget_editable = $this->widgetEditable;
        $orm->widget_type = $this->widgetType;
        $orm->num_user = $this->numUser;
        $orm->widget_shareable = $this->widgetShareable;
        $orm->widget_settings = json_encode($this->widgetSettings, true);

        $orm->save();

        $this->idWidget = $orm->id_widget;
    }

    /**
     * Met à jour les informations du widget dans la base de données.
     *
     * @return void
     */
    public function update()
    {
        $orm = new $this->ormName($this->idWidget);

        $orm->widget_name = $this->widgetName;
        $orm->widget_editable = $this->widgetEditable;
        $orm->widget_type = $this->widgetType;
        $orm->num_user = $this->numUser;
        $orm->widget_shareable = $this->widgetShareable;
        $orm->widget_settings = json_encode($this->widgetSettings, true);

        $orm->save();
    }

    /**
     * Supprime le widget de la base de données.
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

            // Si un widget est utilise par un tableau de bord qui n'appartient pas à l'utilisateur courant
            $query = 'SELECT id_dashboard FROM dashboard JOIN dashboard_widget ON dashboard.id_dashboard = dashboard_widget.num_dashboard
                      WHERE dashboard_widget.num_widget = ' . $this->idWidget . ' AND num_user != ' . $PMBuserid;

            $result = pmb_mysql_query($query);
            if(pmb_mysql_num_rows($result)) {
                return [
                    'error' => true,
                    'errorMessage' => 'msg:form_widget_used',
                ];
            }

            $orm = new $this->ormName($this->idWidget);
            $orm->delete();

            DashboardWidgetOrm::deleteWhere("num_widget", $this->idWidget);

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
     * Vérifie les données du formulaire
     *
     * @param object $data Données vérifiées
     * @return array
     */
    public function check(object $data)
    {
        global $PMBuserid;

        if(empty($data->widgetName) || !is_string($data->widgetName)) {
            return [
                'error' => true,
                'errorMessage' => 'msg:form_data_errors',
            ];
        }

        // Si le widget ne m'appartient pas
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
     * @return void
     */
    public function setFromForm($data)
    {
        global $PMBuserid;

        $this->widgetName = $data->widgetName;
        $this->widgetEditable = $data->widgetEditable;
        $this->widgetType = $data->widgetType;
        $this->widgetSettings = $data->widgetSettings;
        if(!$this->idWidget) {
            $this->numUser = intval($PMBuserid);
        }

        if(!$this->widgetShared) {
            $this->widgetShareable = $data->widgetShareable;
        }
    }

    /**
     * Recupere la liste des types de widgets
     *
     * @return array
     */
    public static function getWidgetTypeList() 
    {
        $widgets = [];

        $manifests = DashboardParserDirectory::getInstance()->getManifests("Pmb/Dashboard/Models/Widget/");

        foreach ($manifests as $manifest) {
            $messages = $manifest->namespace::getMessages();

            $display_formats = $manifest->displayFormats["displayFormat"] ?? [];
            if (is_string($display_formats)) {
                $display_formats = [$display_formats];
            }

            $widgets[] = [
                "id" => md5($manifest->namespace),
                "type" => $manifest->type,
                "namespace" => $manifest->namespace,
                "source" => $manifest->source ?? "",
                "display_formats" => $display_formats,
                "msg" => $messages
            ];
        }

        return $widgets;
    }

    /**
     * Recupere la liste des widgets de l'utilisateur courant
     *
     * @return array
     */
    public function getListByCurrentUserId()
    {
        global $PMBuserid;

        $widgetList = [];

        $list = $this->getList();
        foreach($list as $widget) {
            if($widget->numUser == intval($PMBuserid) || $widget->widgetShareable == 1) {
                $widgetList[] = $widget;
                continue;
            }

        }

        return $widgetList;
    }

    protected function isShared() 
    {
        global $PMBuserid;

        $query = 'SELECT count(*) FROM dashboard
                  JOIN dashboard_widget ON dashboard.id_dashboard = dashboard_widget.num_dashboard
                  WHERE num_widget = ' . $this->idWidget . ' AND num_user != ' . $PMBuserid;

        $result = pmb_mysql_query($query);
        if(pmb_mysql_result($result, 0, 0)) {
            return true;
        }

        return false;
    }
}

