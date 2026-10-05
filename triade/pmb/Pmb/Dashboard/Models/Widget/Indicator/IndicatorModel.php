<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: IndicatorModel.php,v 1.1 2024/02/26 14:28:54 dbellamy Exp $

namespace Pmb\Dashboard\Models\Widget\Indicator;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

use Pmb\Dashboard\Models\WidgetModel;

class IndicatorModel extends WidgetModel
{}

