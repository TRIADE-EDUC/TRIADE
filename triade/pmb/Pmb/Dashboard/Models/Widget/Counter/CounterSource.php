<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CounterSource.php,v 1.4 2024/02/15 15:45:46 jparis Exp $

namespace Pmb\Dashboard\Models\Widget\Counter;
use Pmb\Common\Models\DocsLocationModel;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class CounterSource
{
    public function getData($params = null)
    {
        if(!isset($params->location)) {
            return [];
        }

        $visitsStatistics = new \visits_statistics($params->location);
        return $visitsStatistics->get_statistics();
    }

    public function getConfiguration()
    {
        $config = \visits_statistics::get_config();
        
        return [
            "services" => array_merge($config['main'], $config['services']),
            "locations" => DocsLocationModel::getLocationList()
        ];
    }

    public function updateData($data = null)
    {
        if(!empty($data)) {
            $visits_statistics = new \visits_statistics($data->location);
            
            switch ($data->value) {
                case "add":
                    $visits_statistics->add_visit($data->counter_type);
                    return true;

                case "remove":
                    $visits_statistics->remove_visit($data->counter_type);
                    return true;
                    
                default:
                    if(filter_var($data->value, FILTER_VALIDATE_INT, FILTER_REQUIRE_SCALAR) !== false) {
                        $visits_statistics->update_visits($data->counter_type, intval($data->value));
                        return true;
                    }
                    break;
            }
        }

        return false;
    }
}

