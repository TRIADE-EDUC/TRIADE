<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: HarvestController.php,v 1.6 2024/01/03 14:21:36 qvarin Exp $

namespace Pmb\Harvest\Controller;

use Pmb\Common\Controller\Controller;
use Pmb\Harvest\Models\HarvestModel;
use Pmb\Harvest\Views\HarvestFormView;

class HarvestController extends Controller
{
    public $action;

    public const HARVEST_IMPORT_NO_MODIFICATION_FLAG = 0;

    public const HARVEST_IMPORT_FLAGS = [
        [
            "value" => 0,
            "onlyRepeatable" => false,
            "labelCode" => "harvest_import_flag_no_modify"
        ],
        [
            "value" => 1,
            "onlyRepeatable" => false,
            "labelCode" => "harvest_import_flag_replace"
        ],
        [
            "value" => 2,
            "onlyRepeatable" => true,
            "labelCode" => "harvest_import_flag_add"
        ]
    ];

    protected $model = null;

    public function __construct(object $data = null)
    {
        global $id;
        parent::__construct($data);
        if (! $id) {
            $id = $data->id ?? 0;
        }
        $this->model = new HarvestModel($id);
    }

    public function proceedProfile(string $action = "", $data = array())
    {
        global $pmb_url_base;

        $this->action = $action;
        $data = array();
        $data["url_webservice"] = $pmb_url_base . "rest.php/";
        $data["url"] = $pmb_url_base . "admin.php?categ=harvest&sub=profil";
        $data["action"] = $action ?? "";

        switch ($action) {
            case "modif":
            case "add":
                global $id;
                $id = intval($id);
                $data["id"] = $id;
                $data["profil"] = $this->model->getProfile();
                break;
            default:
                $data["list"] = $this->model->getProfiles();
                break;
        }

        $view = new HarvestFormView("harvest/profil", $data);
        print $view->render();
    }

    public function proceedProfileImport(string $action = "", $data = array())
    {
        global $pmb_url_base;

        $this->action = $action;
        $data = array();
        $data["url_webservice"] = $pmb_url_base . "rest.php/";
        $data["url"] = $pmb_url_base . "admin.php?categ=harvest&sub=profil_import";
        $data["action"] = $action ?? "";

        switch ($action) {
            case "modif":
            case "add":
                global $id;
                $id = intval($id);
                $data["id"] = $id;
                $data["profil"] = $this->model->getImportProfile();
                $data["flags"] = static::HARVEST_IMPORT_FLAGS;
                break;
            default:
                $data["list"] = $this->model->getImportProfiles();
                break;
        }
        $view = new HarvestFormView("harvest/import", $data);
        print $view->render();
    }

    /**
     * Enregistrement d'un profil
     */
    public function saveProfile()
    {
        $id = $this->model->saveProfile($this->data);
        if (! $id) {
            $this->ajaxError("");
            exit();
        }
        $this->ajaxJsonResponse(array(
            "id" => $id,
            "groups" => $this->data->groups,
            "name" => $this->data->name
        ));
    }

    /**
     * Enregistrement d'un profil d'import
     */
    public function saveImportProfile()
    {
        foreach ($this->data->groups ?? [] as $ufield => $group) {
            if ($group->repeatable !== "no") {
                continue;
            }

            foreach (static::HARVEST_IMPORT_FLAGS as $flag) {
                if ($flag['onlyRepeatable'] && $flag['value'] == $group->flag) {
                    // Nous avons un flag réservé aux champs répétables, on le remplace
                    $this->data->groups->{$ufield}->flag = static::HARVEST_IMPORT_NO_MODIFICATION_FLAG;
                    break;
                }
            }
        }

        $id = $this->model->saveImportProfile($this->data);
        if (! $id) {
            $this->ajaxError("");
            exit();
        }

        $this->ajaxJsonResponse([
            "id" => $id
        ]);
    }

    public function deleteProfile()
    {
        if (! $this->data->id) {
            $this->ajaxError("");
            exit();
        }
        $this->ajaxJsonResponse($this->model->deleteProfile());
    }

    public function deleteImportProfile()
    {
        if (! $this->data->id) {
            $this->ajaxError("");
            exit();
        }
        $this->ajaxJsonResponse($this->model->deleteImportProfile());
    }
}
