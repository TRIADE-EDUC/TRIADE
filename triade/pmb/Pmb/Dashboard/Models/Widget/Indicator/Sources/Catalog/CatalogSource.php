<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CatalogSource.php,v 1.1 2024/02/26 14:28:53 dbellamy Exp $

namespace Pmb\Dashboard\Models\Widget\Indicator\Sources\Catalog;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

use Pmb\Dashboard\Models\Widget\Common\AbstractSource;

class CatalogSource extends AbstractSource
{

    protected static $configuration_filename = "CatalogSource";
    protected static $memo_abts_pointage_calc_alert = null;

    public function __construct()
    {
        static::$configuration_filename = __DIR__ . DIRECTORY_SEPARATOR . static::$configuration_filename;
    }

    protected function getBulletinsToReceive()
    {
		if(is_null(static::$memo_abts_pointage_calc_alert)) {	
			static::$memo_abts_pointage_calc_alert = \abts_pointage::get_dashboard_info();			
		}
        
		return static::$memo_abts_pointage_calc_alert["a_recevoir"];
    }

    protected function getBulletinsNext()
    {
		if(is_null(static::$memo_abts_pointage_calc_alert)) {	
			static::$memo_abts_pointage_calc_alert = \abts_pointage::get_dashboard_info();			
		}
        
		return static::$memo_abts_pointage_calc_alert["prochain_numero"];
    }

    protected function getBulletinsLate()
    {
		if(is_null(static::$memo_abts_pointage_calc_alert)) {	
			static::$memo_abts_pointage_calc_alert = \abts_pointage::get_dashboard_info();			
		}
        
		return static::$memo_abts_pointage_calc_alert["en_retard"];
    }

    protected function getBulletinsAlert()
    {
		if(is_null(static::$memo_abts_pointage_calc_alert)) {	
			static::$memo_abts_pointage_calc_alert = \abts_pointage::get_dashboard_info();			
		}
        
		return static::$memo_abts_pointage_calc_alert["en_alerte"];
    }


}

