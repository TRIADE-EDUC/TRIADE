<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AlertSource.php,v 1.1 2024/02/21 10:26:27 jparis Exp $

namespace Pmb\Dashboard\Models\Widget\Alert;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class AlertSource
{
    public function getData($params = null)
    {
        global $base_path, $msg;

        if(!isset($params->modules)) {
            return [];
        }

        $data = [];

        foreach($params->modules as $module) {
            $className = "alerts_" . $module;

            $alertModule = new $className();
            $alertsData = $alertModule->get_data();

            if(empty($alertsData)) {
                continue;
            }

            $formatedAlerts = [];
            foreach($alertsData as $alertData) {

                if(!isset($formatedAlerts[$module]["label"])) {
                    $formatedAlerts[$module]["label"] = $msg[$alertData["section"]];
                }

                $destinationLink = $base_path . "/" . $alertData["module"] . ".php";
                $destinationLink .= $alertData["categ"] ? "?categ=" . $alertData["categ"] : "";
                $destinationLink .= $alertData["sub"] ? "&sub=" . $alertData["sub"] : "";
                $destinationLink .= $alertData["url_extra"];

                $formatedAlerts[$module]["alerts"][] = [
                    "label" => $msg[$alertData["label_code"]],
                    "number" => intval($alertData["number"]),
                    "destination_link" => $destinationLink
                ];
            }

            if(!empty($formatedAlerts)) {
                $data = array_merge($data, $formatedAlerts);
            }
        }

        return $data;
    }

    public function getConfiguration()
    {
        $modules = \alerts::get_module_list();

        return [
            "modules" => $modules
        ];
    }
}

